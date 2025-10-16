<?php

namespace App\Http\Controllers;

use App\Models\PaperType;
use Illuminate\Http\Request;

class PaperTypeController extends Controller
{
    /**
     * Get all available paper types
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $paperTypes = PaperType::all()->map(fn($type) => $type->toJson());

        return response()->json([
            'success' => true,
            'paper_types' => $paperTypes,
            'default' => PaperType::default()->getId(),
        ]);
    }

    /**
     * Get a specific paper type
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $paperType = PaperType::find($id);

        if (!$paperType) {
            return response()->json([
                'success' => false,
                'message' => 'Paper type not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'paper_type' => $paperType->toJson(),
            'specifications' => $paperType->getSpecifications(),
        ]);
    }

    /**
     * Calculate pricing for given pages and paper type
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'paper_type_id' => 'required|string',
            'pages' => 'required|integer|min:1',
        ]);

        $paperType = PaperType::find($validated['paper_type_id']);

        if (!$paperType) {
            return response()->json([
                'success' => false,
                'message' => 'Paper type not found',
            ], 404);
        }

        $pages = $validated['pages'];

        return response()->json([
            'success' => true,
            'paper_type' => $paperType->getName(),
            'pages' => $pages,
            'price_per_page' => $paperType->getPricePerPage(),
            'total_price' => $paperType->calculatePrice($pages),
            'weight_oz' => $paperType->calculateWeight($pages),
            'thickness_inches' => $paperType->calculateThickness($pages),
        ]);
    }

    /**
     * Get paper types compatible with a specific album type
     *
     * @param string $albumType
     * @return \Illuminate\Http\JsonResponse
     */
    public function forAlbum(string $albumType)
    {
        $paperTypes = PaperType::forAlbum($albumType)->map(fn($type) => $type->toJson());

        if ($paperTypes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Album type not found or no compatible paper types',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'album_type' => $albumType,
            'paper_types' => $paperTypes,
        ]);
    }
}
