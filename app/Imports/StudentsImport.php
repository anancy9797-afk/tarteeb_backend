<?php

declare(strict_types=1);

namespace App\Imports;

use Exception;
use Modules\User\App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Student\App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Modules\Student\App\Rules\MaxStudents;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Class\App\Rules\ClassBelongToSchool;
use Modules\Subject\App\Rules\GradeBelongToSchool;
use Illuminate\Http\Exceptions\HttpResponseException;

final class StudentsImport implements ToCollection, WithHeadingRow
{
    private array $pdfMapping;
    private array $createdStudentIds = [];
    private array $usedPdfFiles = [];

    public function __construct(array $pdfMapping = [])
    {
        $this->pdfMapping = $pdfMapping;

        Log::info("========== StudentsImport Constructor ==========");
        Log::info("Received pdfMapping with " . count($pdfMapping) . " entries");
        foreach ($pdfMapping as $row => $file) {
            Log::info("  Constructor received: Row {$row} => {$file}");
        }
        Log::info("================================================");
    }

    public function collection(Collection $rows): void
    {
        Log::info("========== Starting Import ==========");
        Log::info("Total rows in Excel: " . $rows->count());

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // This is the Excel row number (with header)
            $dataRowNumber = $index + 1; // This is the data row number (1, 2, 3...)

            Log::info("--- Processing Excel Row {$rowNumber} (Data Row {$dataRowNumber}, Index {$index}) ---");
            Log::info("Student name: " . ($row['name'] ?? 'N/A'));

            $this->validateRow($row, $index, $rowNumber);
            $data = $this->prepareStudentData($row, $dataRowNumber); // Use dataRowNumber instead

            Log::info("Data prepared with application_form: " . ($data['application_form'] ?? 'NULL'));

            $student = $this->createStudent($data);

            $this->createdStudentIds[] = $student->id;

            if (isset($data['application_form'])) {
                $this->usedPdfFiles[] = $data['application_form'];
                Log::info("Added to usedPdfFiles: " . $data['application_form']);
            }
        }

        Log::info("========== Import Complete ==========");
    }

    public function getCreatedStudentIds(): array
    {
        return $this->createdStudentIds;
    }

    public function getUsedPdfFiles(): array
    {
        return $this->usedPdfFiles;
    }

    private function validateRow($row, int $index, int $rowNumber): void
    {
        $rules = $this->getValidationRules($row);
        $validator = Validator::make($row->toArray(), $rules);

        if ($validator->fails()) {
            throw new HttpResponseException(
                returnValidationMessage(
                    false,
                    trans('validation.rules_failed'),
                    ['row' => $rowNumber, 'errors' => $validator->errors()->messages()],
                    'unprocessable_entity'
                )
            );
        }
    }

    private function getValidationRules($row): array
    {
        $rules = [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:students,email', 'unique:students,parent_email', 'different:parent_email', 'unique:users,email'],
            'identity_number' => ['nullable'],
            'gender' => ['required', 'in:m,f'],
            'parent_email' => ['nullable', 'email', 'unique:students,parent_email', 'unique:students,email', 'different:email'],
            'parent_phone' => ['required'],
            'address' => ['nullable', 'string'],
            'is_fee_paid' => ['required', 'in:0,1'],
            'password' => ['required', 'min:6'],
        ];

        if (auth('user')->user()->hasRole('Super Admin')) {
            $rules['school_id'] = ['required', 'exists:schools,id'];
            $rules['grade_id'] = ['required', 'exists:grades,id', new GradeBelongToSchool($row['school_id'])];
            $rules['class_id'] = ['required', 'exists:classes,id', new ClassBelongToSchool($row['class_id'], $row['school_id']), new MaxStudents($row['class_id'])];
        } elseif (auth('user')->user()->hasRole('School Manager')) {
            $schoolId = auth('user')->user()->school_id;
            $rules['school_id'] = ['prohibited'];
            $rules['grade_id'] = ['required', 'exists:grades,id', new GradeBelongToSchool($schoolId)];
            $rules['class_id'] = ['required', 'exists:classes,id', new ClassBelongToSchool($row['class_id'], $schoolId), new MaxStudents($row['class_id'])];
        }

        return $rules;
    }

    private function prepareStudentData($row, int $rowNumber): array
    {
        Log::info("prepareStudentData for row {$rowNumber}");
        Log::info("Checking if pdfMapping[{$rowNumber}] exists...");
        Log::info("pdfMapping keys: " . implode(', ', array_keys($this->pdfMapping)));
        Log::info("isset(pdfMapping[{$rowNumber}])? " . (isset($this->pdfMapping[$rowNumber]) ? 'YES' : 'NO'));

        $data = [
            'name' => $row['name'],
            'gender' => $row['gender'],
            'email' => $row['email'],
            'identity_number' => $row['identity_number'] ?? null,
            'parent_email' => $row['parent_email'] ?? null,
            'parent_phone' => $row['parent_phone'],
            'grade_id' => $row['grade_id'],
            'class_id' => $row['class_id'],
            'is_fee_paid' => $row['is_fee_paid'],
            'is_graduated' => 0,
            'is_active' => 1,
            'is_register' => 0,
            'address' => $row['address'] ?? null,
            'password' => (string) $row['password'],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (isset($this->pdfMapping[$rowNumber])) {
            $data['application_form'] = $this->pdfMapping[$rowNumber];
            Log::info("✓ Row {$rowNumber} assigned PDF: {$this->pdfMapping[$rowNumber]}");
        } else {
            Log::warning("✗ Row {$rowNumber} NO PDF assigned");
        }

        if (auth('user')->user()->hasRole('Super Admin')) {
            $data['school_id'] = $row['school_id'];
        } elseif (auth('user')->user()->hasRole('School Manager')) {
            $data['school_id'] = auth('user')->user()->school_id;
        }

        return $data;
    }

    private function createStudent(array $data): Student
    {
        $user = $this->createUser($data);
        return $user->student()->create($data);
    }

    private function createUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'Student',
            'school_id' => $data['school_id'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at'],
        ]);

        $user->assignRole('Student');

        return $user;
    }
}
