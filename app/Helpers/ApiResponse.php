<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    // Başarılı response
    public static function success($data = null, string $message = 'Başarılı', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null
        ], $status);
    }

    // Hata response
    public static function error($message = 'Hata oluştu', $errors = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors
        ], $status);
    }

    // Yetkisiz / Unauthorized
    public static function unauthorized($message = 'Yetkisiz işlem', int $status = 401): JsonResponse
    {
        return self::error($message, null, $status);
    }
    public static function accessDenied(string $message = 'Bu işlemi yapmaya yetkiniz yok'): JsonResponse
    {
        return self::error($message, null, 403);
    }
    // Silme işlemi başarılı
    public static function deleted(string $message = 'Kayıt silindi', int $status = 200): JsonResponse
    {
        return self::success(null, $message, $status);
    }

    // Validation hatası
    public static function validationError($errors, string $message = 'Validation hatası', int $status = 422): JsonResponse
    {
        return self::error($message, $errors, $status);
    }
}
