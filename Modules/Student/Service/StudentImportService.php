<?php

declare(strict_types=1);

namespace Modules\Student\Service;

use Exception;
use ZipArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

final class StudentImportService
{
    private array $uploadedFiles = [];
    private ?string $tempDirectory = null;
    private array $createdStudentIds = [];
    private array $usedPdfFiles = [];

    public function importStudents($excelFile, $zipFile): bool
    {
        try {
            DB::beginTransaction();

            $this->tempDirectory = $this->createTempDirectory();
            $this->extractZipFile($zipFile, $this->tempDirectory);

            $pdfMapping = $this->processPdfFiles($this->tempDirectory);
            $this->importFromExcel($excelFile, $pdfMapping);
            $this->deleteUnusedPdfFiles();

            DB::commit();
            $this->cleanup();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $this->rollbackAll();
            throw $e;
        }
    }

    private function createTempDirectory(): string
    {
        $uniqueId = uniqid('student_import_', true) . '_' . auth('user')->id();
        $tempPath = storage_path("app/temp/{$uniqueId}");

        if (!File::exists($tempPath)) {
            File::makeDirectory($tempPath, 0755, true);
        }

        return $tempPath;
    }

    private function extractZipFile($zipFile, string $extractPath): void
    {
        if ($zipFile->getClientOriginalExtension() !== 'zip') {
            throw new Exception('PDF file must be a ZIP archive');
        }

        $zip = new ZipArchive();

        if ($zip->open($zipFile->getRealPath()) !== true) {
            throw new Exception('Failed to open ZIP file');
        }

        $zip->extractTo($extractPath);
        $zip->close();
    }

    private function processPdfFiles(string $directory): array
    {
        $files = File::files($directory);
        $pdfMapping = [];

        foreach ($files as $file) {
            if (strtolower($file->getExtension()) === 'pdf') {
                $originalName = $file->getFilename();
                $rowNumber = $this->extractRowNumber($originalName);

                if ($rowNumber !== null) {
                    $uniqueFileName = $this->generateUniqueFileName();
                    $this->moveToStorage($file, $uniqueFileName);

                    $pdfMapping[$rowNumber] = $uniqueFileName;
                    $this->uploadedFiles[] = $uniqueFileName;
                }
            }
        }

        return $pdfMapping;
    }

    private function extractRowNumber(string $filename): ?int
    {
        if (preg_match('/^(\d+)\.pdf$/i', $filename, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    private function generateUniqueFileName(): string
    {
        $timestamp = time();
        $microtime = (int) (microtime(true) * 10000);
        $randomString = bin2hex(random_bytes(8));

        return "{$timestamp}_{$microtime}_{$randomString}.pdf";
    }

    private function moveToStorage($file, string $newFileName): void
    {
        $destinationPath = public_path('uploads/student/application_form');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $sourcePath = $file->getPathname();
        $destinationFile = "{$destinationPath}/{$newFileName}";

        if (!File::copy($sourcePath, $destinationFile)) {
            throw new Exception("Failed to copy file: {$file->getFilename()}");
        }
    }

    private function importFromExcel($excelFile, array $pdfMapping): void
    {
        $import = new StudentsImport($pdfMapping);
        Excel::import($import, $excelFile);

        $this->createdStudentIds = $import->getCreatedStudentIds();
        $this->usedPdfFiles = $import->getUsedPdfFiles();
    }

    private function deleteUnusedPdfFiles(): void
    {
        $basePath = public_path('uploads/student/application_form');
        $unusedFiles = array_diff($this->uploadedFiles, $this->usedPdfFiles);

        foreach ($unusedFiles as $fileName) {
            $filePath = "{$basePath}/{$fileName}";
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
    }

    private function cleanup(): void
    {
        if ($this->tempDirectory && File::exists($this->tempDirectory)) {
            File::deleteDirectory($this->tempDirectory);
        }
    }

    private function rollbackAll(): void
    {
        $this->deleteUploadedFiles();
        $this->deleteCreatedStudents();
        $this->cleanup();
    }

    private function deleteUploadedFiles(): void
    {
        $basePath = public_path('uploads/student/application_form');

        foreach ($this->uploadedFiles as $fileName) {
            $filePath = "{$basePath}/{$fileName}";
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
    }

    private function deleteCreatedStudents(): void
    {
        if (!empty($this->createdStudentIds)) {
            DB::table('users')
                ->whereIn('id', function ($query) {
                    $query->select('user_id')
                        ->from('students')
                        ->whereIn('id', $this->createdStudentIds);
                })
                ->delete();

            DB::table('students')->whereIn('id', $this->createdStudentIds)->delete();
        }
    }
}
