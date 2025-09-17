<?php

namespace App\Utils;

use Illuminate\Http\JsonResponse;

class ResponseCode
{
    /**
     * Success GET (200)
     */
    public static function successGet($data = null, string $message = 'Data berhasil diambil.'): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], 200);
    }

    /**
     * Success POST (201)
     */
    public static function successPost($data = null, string $message = 'Data berhasil disimpan.'): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], 201);
    }

    // ============================
    // Error Responses
    // ============================

    /**
     * Bad Request (400)
     */
    public static function badRequest(string $message = 'Permintaan tidak valid.', $data = null): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'data'    => $data,
        ], 400);
    }

    /**
     * Unauthorized (401)
     */
    public static function unauthorized(string $message = 'Autentikasi gagal.', $data = null): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'data'    => $data,
        ], 401);
    }

    /**
     * Forbidden (403)
     */
    public static function forbidden(string $message = 'Akses ditolak.', $data = null): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'data'    => $data,
        ], 403);
    }

    /**
     * Not Found (404)
     */
    public static function notFound(string $message = 'Data tidak ditemukan.', $data = null): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'data'    => $data,
        ], 404);
    }

    /**
     * Internal Server Error (500)
     */
    public static function internalError(string $message = 'Terjadi kesalahan pada server.', $data = null): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'data'    => $data,
        ], 500);
    }
}
