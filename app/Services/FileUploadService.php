<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * FileUploadService
 * 
 * Handles file uploads for reports and completions
 */
class FileUploadService
{
    /**
     * Upload report photos
     * 
     * @param array $files
     * @return array Array of uploaded filenames
     */
    public function uploadReportPhotos(array $files): array
    {
        return $this->uploadFiles($files, 'images/reports');
    }

    /**
     * Upload completion photos
     * 
     * @param array $files
     * @return array Array of uploaded filenames
     */
    public function uploadCompletionPhotos(array $files): array
    {
        return $this->uploadFiles($files, 'images/completions');
    }

    /**
     * Upload files to specified directory
     * 
     * @param array $files
     * @param string $directory
     * @return array Array of uploaded filenames
     */
    private function uploadFiles(array $files, string $directory): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                try {
                    $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($directory), $filename);
                    $uploadedFiles[] = $filename;
                } catch (\Exception $e) {
                    Log::error("Failed to upload file: " . $e->getMessage());
                }
            }
        }

        return $uploadedFiles;
    }

    /**
     * Delete a file from storage
     * 
     * @param string $filename
     * @param string $directory
     * @return bool
     */
    public function deleteFile(string $filename, string $directory): bool
    {
        $path = public_path($directory . '/' . $filename);
        
        // Try primary directory first
        if (file_exists($path)) {
            return @unlink($path);
        }

        // Fallback to legacy images folder for old reports
        if ($directory === 'images/reports') {
            $legacyPath = public_path('images/' . $filename);
            if (file_exists($legacyPath)) {
                return @unlink($legacyPath);
            }
        }

        return false;
    }

    /**
     * Delete multiple files from storage
     * 
     * @param array $filenames
     * @param string $directory
     * @return int Number of files deleted
     */
    public function deleteFiles(array $filenames, string $directory): int
    {
        $deleted = 0;

        foreach ($filenames as $filename) {
            if ($this->deleteFile($filename, $directory)) {
                $deleted++;
            }
        }

        return $deleted;
    }
}
