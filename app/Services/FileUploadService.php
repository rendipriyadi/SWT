<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * FileUploadService
 * 
 * Handles file uploads for reports and completions.
 * All files are stored in storage/app/public/images/
 */
class FileUploadService
{
    /**
     * Base path for images in storage
     */
    private const BASE_PATH = 'images';
    private const REPORTS_PATH = 'images/reports';
    private const COMPLETIONS_PATH = 'images/completions';

    /**
     * Upload report photos
     * 
     * @param array $files Array of UploadedFile instances
     * @return array Array of uploaded filenames
     */
    public function uploadReportPhotos(array $files): array
    {
        return $this->uploadFiles($files, self::REPORTS_PATH);
    }

    /**
     * Upload completion photos
     * 
     * @param array $files Array of UploadedFile instances
     * @return array Array of uploaded filenames
     */
    public function uploadCompletionPhotos(array $files): array
    {
        return $this->uploadFiles($files, self::COMPLETIONS_PATH);
    }

    /**
     * Upload files to specified directory in storage
     * 
     * @param array $files Array of UploadedFile instances
     * @param string $directory Directory path relative to storage/app/public/
     * @return array Array of uploaded filenames
     */
    private function uploadFiles(array $files, string $directory): array
    {
        $uploadedFiles = [];
        $destinationPath = storage_path('app/public/' . $directory);

        // Create directory if not exists
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        foreach ($files as $file) {
            if (!($file instanceof UploadedFile) || !$file->isValid()) {
                continue;
            }

            try {
                // Generate unique filename
                $filename = $this->generateUniqueFilename($file);
                
                // Move file to storage
                $file->move($destinationPath, $filename);
                
                $uploadedFiles[] = $filename;
                
                Log::info("File uploaded successfully", [
                    'filename' => $filename,
                    'path' => $directory
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to upload file", [
                    'error' => $e->getMessage(),
                    'path' => $directory
                ]);
            }
        }

        return $uploadedFiles;
    }

    /**
     * Generate unique filename for uploaded file
     * 
     * @param UploadedFile $file
     * @return string
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $timestamp = time();
        $uniqueId = uniqid();
        $extension = $file->getClientOriginalExtension();
        
        return "{$timestamp}-{$uniqueId}.{$extension}";
    }

    /**
     * Delete a file from storage
     * 
     * @param string $filename Filename to delete
     * @param string $type Type of file ('reports' or 'completions')
     * @return bool True if deleted successfully
     */
    public function deleteFile(string $filename, string $type): bool
    {
        // Determine directory based on type
        $directory = $type === 'reports' ? self::REPORTS_PATH : self::COMPLETIONS_PATH;
        
        // Try storage path first (new location)
        $storagePath = storage_path('app/public/' . $directory . '/' . $filename);
        if (file_exists($storagePath)) {
            $deleted = @unlink($storagePath);
            if ($deleted) {
                Log::info("File deleted from storage", [
                    'filename' => $filename,
                    'path' => $directory
                ]);
            }
            return $deleted;
        }

        // Fallback to old public path for backward compatibility
        $publicPath = public_path('images/' . $type . '/' . $filename);
        if (file_exists($publicPath)) {
            $deleted = @unlink($publicPath);
            if ($deleted) {
                Log::info("File deleted from public (legacy)", [
                    'filename' => $filename,
                    'path' => 'public/images/' . $type
                ]);
            }
            return $deleted;
        }

        Log::warning("File not found for deletion", [
            'filename' => $filename,
            'type' => $type
        ]);

        return false;
    }

    /**
     * Delete multiple files from storage
     * 
     * @param array $filenames Array of filenames to delete
     * @param string $type Type of files ('reports' or 'completions')
     * @return int Number of files deleted successfully
     */
    public function deleteFiles(array $filenames, string $type): int
    {
        $deleted = 0;

        foreach ($filenames as $filename) {
            if ($this->deleteFile($filename, $type)) {
                $deleted++;
            }
        }

        Log::info("Batch file deletion completed", [
            'total' => count($filenames),
            'deleted' => $deleted,
            'type' => $type
        ]);

        return $deleted;
    }

    /**
     * Check if file exists in storage
     * 
     * @param string $filename
     * @param string $type Type of file ('reports' or 'completions')
     * @return bool
     */
    public function fileExists(string $filename, string $type): bool
    {
        $directory = $type === 'reports' ? self::REPORTS_PATH : self::COMPLETIONS_PATH;
        $storagePath = storage_path('app/public/' . $directory . '/' . $filename);
        
        return file_exists($storagePath);
    }

    /**
     * Get full storage path for a file
     * 
     * @param string $filename
     * @param string $type Type of file ('reports' or 'completions')
     * @return string
     */
    public function getFilePath(string $filename, string $type): string
    {
        $directory = $type === 'reports' ? self::REPORTS_PATH : self::COMPLETIONS_PATH;
        return storage_path('app/public/' . $directory . '/' . $filename);
    }
}
