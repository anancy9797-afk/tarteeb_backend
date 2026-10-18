<?php

namespace Modules\Student\App\Http\Requests;

use Modules\Student\App\Rules\MaxStudents;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Modules\Class\App\Rules\ClassBelongToSchool;
use Modules\Subject\App\Rules\GradeBelongToSchool;
use Illuminate\Http\Exceptions\HttpResponseException;

class StudentUpdateRequest extends FormRequest
{
    public $student;
    public $user;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $studentRoute = $this->route('student');
        $this->student = $studentRoute;

        // Load user relationship if student exists
        if ($this->student) {
            $this->student->load('user');
            $this->user = $this->student->user;
        }

        // Build email validation rules
        $emailRules = ['nullable', 'email', 'unique:students,email,' . ($this->student?->id ?? ''), 'unique:students,parent_email,' . ($this->student?->id ?? ''), 'different:parent_email'];

        // Add user email uniqueness check only if user exists
        if ($this->user) {
            $emailRules[] = 'unique:users,email,' . $this->user->id;
        }

        // Build phone validation rules
        $phoneRules = ['nullable', 'string'];
        if ($this->user) {
            $phoneRules[] = 'unique:users,phone,' . $this->user->id;
        } else {
            $phoneRules[] = 'unique:users,phone';
        }

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => $emailRules,
            'phone' => $phoneRules,
            'identity_number' => ['nullable', 'string', 'unique:students,identity_number,' . ($this->student?->id ?? '')],
            'gender' => ['nullable', 'in:m,f'],
            'parent_email' => ['nullable', 'email', 'unique:students,parent_email,' . ($this->student?->id ?? ''), 'unique:students,email,' . ($this->student?->id ?? ''), 'different:email'],
            'parent_phone' => ['nullable', 'string'],
            'school_id' => ['nullable', 'exists:schools,id'],
            'grade_id' => ['nullable', 'exists:grades,id', new GradeBelongToSchool($this->input('school_id'))],
            'password' => ['nullable', 'string', 'min:6'],
            'password_confirmation' => ['nullable', 'string', 'min:6', 'same:password'],
            'application_form' => ['nullable', 'file', 'mimes:pdf', 'max:1024'],
            'address' => ['nullable', 'string'],
            'state_id' => ['nullable', 'exists:states,id'],
            'region' => ['nullable', 'string'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'name_en' => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date'],
            'education_level' => ['nullable', 'in:excellent,normal,needs_follow_up'],
            'has_learning_difficulties' => ['nullable', 'boolean'],
            'educational_system' => ['nullable', 'in:monolingual,bilingual'],
            'behavioral_data' => ['nullable', 'array'],
            'behavioral_data.*' => ['nullable', 'string', 'in:articulate,sociable,introvert,shy,unresponsive,inflexible,irritable,hyperactive,stubbon,normal,aggressive'],
            'pronunciation' => ['nullable', 'in:excellent,good,needs_follow_up'],
            'chronic_diseases' => ['nullable', 'string'],
            'food_allergies' => ['nullable', 'string'],
            'other_notes' => ['nullable', 'string'],
            'transport' => ['nullable', 'in:school_bus,private_bus'],
            'street_number' => ['nullable', 'string'],
            'house_number' => ['nullable', 'string'],
            'nearest_landmark' => ['nullable', 'string'],
            'home_location_url' => ['nullable', 'string', 'url'],
            'siblings_count' => ['nullable', 'string'],
            'parent_identity_card_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'],
            'student_residence_card_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'],
            'student_passport_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'],
            'student_birth_certificate_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'],
            'student_health_card_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'],
            'home_map_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:1024'],
            'parent_name' => ['nullable', 'string'],
            'parent_nationality' => ['nullable', 'string'],
            'parent_identity_number' => ['nullable', 'string'],
            'parent_job' => ['nullable', 'string'],
            'parent_job_address' => ['nullable', 'string'],
            'parent_education_level' => ['nullable', 'string'],
            'mother_name' => ['nullable', 'string'],
            'mother_nationality' => ['nullable', 'string'],
            'mother_identity_number' => ['nullable', 'string'],
            'mother_job' => ['nullable', 'string'],
            'mother_job_address' => ['nullable', 'string'],
            'mother_education_level' => ['nullable', 'string'],
            'mother_phone' => ['nullable', 'string'],
            'parents_status' => ['nullable', 'in:together,separated,widower,widow,father_dead,mother_dead'],
            'relative_name' => ['nullable', 'string'],
            'relative_relation' => ['nullable', 'string'],
            'relative_phone' => ['nullable', 'string'],
            'distinguished_skills' => ['nullable', 'string'],
            'has_previous_education' => ['nullable', 'boolean'],
            'previous_school_data' => ['nullable', 'string'],
            'can_distinguish_letters_randomly' => ['nullable', 'boolean'],
            'reads_short_words' => ['nullable', 'in:excellent,very_good,good,cannot_read'],
            'reads_short_sentences' => ['nullable', 'in:excellent,very_good,good,cannot_read'],
            'memorizes_quran_surahs' => ['nullable', 'boolean'],
            'memorizes_quran_from' => ['nullable', 'string'],
            'memorizes_quran_to' => ['nullable', 'string'],
            'additional_educational_notes' => ['nullable', 'string'],
            'sibling_order' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email Address',
            'identity_number' => 'Identity Number',
            'gender' => 'Gender',
            'class_id' => 'Class',
            'grade_id' => 'Grade',
            'school_id' => 'School',
            'parent_email' => 'Parent Email',
            'parent_phone' => 'Parent Phone',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('PUT')) {
            $admin = auth('user')->user();
            if ($admin && $admin->hasRole('School Manager')) {
                // Check if the student's school ID matches the authenticated user's school ID
                return $admin->school_id == $this->student?->school_id;
            }
        }
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            returnValidationMessage(
                false,
                trans('validation.rules_failed'),
                $validator->errors()->messages(),
                'unprocessable_entity'
            )
        );
    }
}
