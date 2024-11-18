<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GeneralWarning extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'status' => 'warning',
            'message' => $this->getMessage() ? $this->getMessage() : 'Something broken in the process!',
        ], Response::HTTP_CREATED);
    }
}
