<?php

namespace App\Http\Controllers;

use App\Models\PaperSize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * PaperSizeController
 *
 * Handles API endpoints for paper sizes and their available options.
 */
class PaperSizeController extends Controller
{
    /**
     * Get all available paper sizes
     *
     * GET /api/paper-sizes
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $sizes = PaperSize::all()->map(fn($size) => $size->toArray());

        return response()->json([
            'success' => true,
            'data' => $sizes,
        ]);
    }

    /**
     * Get a specific paper size
     *
     * GET /api/paper-sizes/{id}
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $size = PaperSize::find($id);

        if (!$size) {
            return response()->json([
                'success' => false,
                'message' => "Paper size '{$id}' not found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $size->toArray(),
        ]);
    }

    /**
     * Get available options for a specific paper size
     *
     * GET /api/paper-sizes/{id}/options
     *
     * @param string $id
     * @return JsonResponse
     */
    public function options(string $id): JsonResponse
    {
        $size = PaperSize::find($id);

        if (!$size) {
            return response()->json([
                'success' => false,
                'message' => "Paper size '{$id}' not found",
            ], 404);
        }

        $options = $size->getAllAvailableOptions();

        return response()->json([
            'success' => true,
            'data' => $options,
        ]);
    }

    /**
     * Get the default paper size
     *
     * GET /api/paper-sizes/default
     *
     * @return JsonResponse
     */
    public function getDefault(): JsonResponse
    {
        $size = PaperSize::default();

        return response()->json([
            'success' => true,
            'data' => $size->toArray(),
        ]);
    }
}
