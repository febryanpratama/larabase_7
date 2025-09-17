<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadHelper
{
    /**
     * Upload file base64 ke storage
     *
     * @param string|null $base64File   base64 string
     * @param string $directory         folder tujuan, default "attachments"
     * @param string $fileType          "all" | "image" (default: all)
     * @param int $maxSize              max size dalam KB (default 2048 = 2MB)
     * @return array                    ['status' => bool, 'message' => string, 'path' => string|null]
     */
    public static function uploadBase64File(
        ?string $base64File,
        string $directory = 'attachments',
        string $fileType = 'all',
        int $maxSize = 2048
    ): array {
        if (!$base64File) {
            return [
                'status'  => false,
                'message' => 'File tidak ditemukan.',
                'path'    => null,
            ];
        }

        // cek format base64
        if (!preg_match('/^data:(.*);base64,/', $base64File, $match)) {
            return [
                'status'  => false,
                'message' => 'Format base64 tidak valid.',
                'path'    => null,
            ];
        }

        $mimeType = $match[1];
        $fileData = substr($base64File, strpos($base64File, ',') + 1);
        $fileData = base64_decode($fileData);

        if ($fileData === false) {
            return [
                'status'  => false,
                'message' => 'Gagal decode base64.',
                'path'    => null,
            ];
        }

        // cek ukuran file
        $fileSize = strlen($fileData) / 1024; // KB
        if ($fileSize > $maxSize) {
            return [
                'status'  => false,
                'message' => "Ukuran file melebihi batas {$maxSize}KB.",
                'path'    => null,
            ];
        }

        // mapping mime ke ekstensi
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        ];

        $extension = $mimeToExt[$mimeType] ?? null;
        if (!$extension) {
            return [
                'status'  => false,
                'message' => 'Tipe file tidak didukung.',
                'path'    => null,
            ];
        }

        // filter berdasarkan tipe
        if ($fileType === 'image' && !in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return [
                'status'  => false,
                'message' => 'Hanya file gambar yang diperbolehkan.',
                'path'    => null,
            ];
        }

        // generate nama file unik
        $fileName = Str::uuid()->toString() . '.' . $extension;

        // path folder + simpan
        $path = $directory . '/' . $fileName;
        Storage::disk('public')->put($path, $fileData);

        return [
            'status'  => true,
            'message' => 'Upload berhasil.',
            'path'    => 'storage/' . $path,
        ];
    }
}