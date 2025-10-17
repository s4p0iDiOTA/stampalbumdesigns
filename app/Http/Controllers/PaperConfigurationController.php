<?php

namespace App\Http\Controllers;

use App\Models\PaperConfiguration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * PaperConfigurationController
 *
 * Handles API endpoints for paper configurations and calculations.
 */
class PaperConfigurationController extends Controller
{
    /**
     * Calculate price, weight, and other specs for a configuration
     *
     * POST /api/paper-configurations/calculate
     *
     * Body: {
     *   "size": "8.5x11",
     *   "options": {
     *     "paper_weight": "67lb",
     *     "color": "cream",
     *     "punches": "3-hole",
     *     "corners": "square",
     *     "protection": "none"
     *   },
     *   "pages": 50
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'size' => 'required|string',
            'options' => 'required|array',
            'options.paper_weight' => 'required|string',
            'options.color' => 'required|string',
            'options.punches' => 'required|string',
            'options.corners' => 'required|string',
            'options.protection' => 'required|string',
            'pages' => 'sometimes|integer|min:1',
        ]);

        try {
            $config = new PaperConfiguration(
                $validated['size'],
                $validated['options']
            );

            $pages = $validated['pages'] ?? null;
            $specs = $config->getSpecifications($pages);

            return response()->json([
                'success' => true,
                'data' => $specs,
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Validate a paper configuration
     *
     * POST /api/paper-configurations/validate
     *
     * Body: {
     *   "size": "8.5x11",
     *   "options": {...}
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'size' => 'required|string',
            'options' => 'required|array',
        ]);

        try {
            $config = new PaperConfiguration(
                $validated['size'],
                $validated['options']
            );

            $validation = $config->validate();

            return response()->json([
                'success' => true,
                'data' => $validation,
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'valid' => false,
                    'errors' => [$e->getMessage()],
                ],
            ], 400);
        }
    }

    /**
     * Get specifications for a configuration
     *
     * POST /api/paper-configurations/specifications
     *
     * Body: {
     *   "size": "8.5x11",
     *   "options": {...},
     *   "pages": 50
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function specifications(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'size' => 'required|string',
            'options' => 'required|array',
            'pages' => 'sometimes|integer|min:1',
        ]);

        try {
            $config = new PaperConfiguration(
                $validated['size'],
                $validated['options']
            );

            $pages = $validated['pages'] ?? null;
            $specs = $config->getSpecifications($pages);

            return response()->json([
                'success' => true,
                'data' => $specs,
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get display name for a configuration
     *
     * POST /api/paper-configurations/display-name
     *
     * Body: {
     *   "size": "8.5x11",
     *   "options": {...}
     * }
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function displayName(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'size' => 'required|string',
            'options' => 'required|array',
        ]);

        try {
            $config = new PaperConfiguration(
                $validated['size'],
                $validated['options']
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'display_name' => $config->getDisplayName(),
                    'sku' => $config->generateSku(),
                ],
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
