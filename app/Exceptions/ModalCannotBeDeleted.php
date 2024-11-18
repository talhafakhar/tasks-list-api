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

class ModalCannotBeDeleted extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->getMessage() ? $this->getMessage() : 'Modal cannot be deleted',
        ], Response::HTTP_NOT_ACCEPTABLE);
    }
}
