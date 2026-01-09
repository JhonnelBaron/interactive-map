<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class MapController extends Controller
{
    public function regions()
    {
        // Check if cached SVG exists
        if (!Storage::disk('public')->exists('maps/cache.svg')) {
            abort(500, 'Cached SVG not found. Run php artisan map:generate-svg');
        }

        $svgPaths = Storage::disk('public')->get('maps/cache.svg');

        return view('maps.pages.philippines', compact('svgPaths'));
        // return view('map.sampregions', compact('svgPaths'));
    }

}
