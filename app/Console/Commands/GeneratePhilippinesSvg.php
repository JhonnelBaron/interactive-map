<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GeneratePhilippinesSvg extends Command
{
    protected $signature = 'map:generate-svg';
    protected $description = 'Generate cached SVG paths from ph.json';

    public function handle()
    {
        $geojsonContent = Storage::disk('public')->get('maps/ph.json');
        $geojson = json_decode($geojsonContent, true);

        $svgPaths = '';

        foreach ($geojson['features'] as $feature) {
            $props = $feature['properties'] ?? [];
            $id = $props['id'] ?? 'unknown';
            $region = $props['region'] ?? 'unknown';
            $code = $props['psgc'] ?? 'unknown';
            $name = $props['name'] ?? 'Unknown Region';

            $coordinates = $feature['geometry']['coordinates'];
            $pathString = geojsonToSvgPath($coordinates, 1000, 1200);

            $svgPaths .= "<path 
                d=\"{$pathString}\" 
                class=\"region\" 
                data-id=\"{$id}\"
                data-code=\"{$code}\" 
                data-region=\"{$region}\" 
                data-name=\"{$name}\"></path>";
        }

        // Save the result to storage/app/public/maps/cache.svg
        Storage::disk('public')->put('maps/cache.svg', $svgPaths);

        $this->info('SVG paths generated and cached successfully.');
    }
}
// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use Illuminate\Support\Facades\Storage;

// class GeneratePhilippinesSvg extends Command
// {
//     protected $signature = 'map:generate-svg';
//     protected $description = 'Generate cached SVG paths from ph.json and ph_provinces.json';

//     public function handle()
//     {
//         // --- Regions ---
//         $regionsGeojson = Storage::disk('public')->get('maps/ph.json');
//         $regionsGeojson = json_decode($regionsGeojson, true);
//         $regionsSvg = '';

//         foreach ($regionsGeojson['features'] as $feature) {
//             $props = $feature['properties'] ?? [];
//             $code = $props['id'] ?? 'unknown';
//             $name = $props['name'] ?? 'Unknown Region';
//             $coordinates = $feature['geometry']['coordinates'];
//             $path = geojsonToSvgPath($coordinates, 1000, 1200);

//             $regionsSvg .= "<path d=\"{$path}\" class=\"region\" data-region=\"{$code}\" data-name=\"{$name}\"></path>";
//         }

//         // --- Provinces ---
//         $provincesGeojson = Storage::disk('public')->get('maps/ph_provinces.json');
//         $provincesGeojson = json_decode($provincesGeojson, true);
//         $provincesSvg = '';

//         foreach ($provincesGeojson['features'] as $feature) {
//             $props = $feature['properties'] ?? [];
//             $code = $props['id'] ?? 'unknown';
//             $name = $props['name'] ?? 'Unknown Province';
//             $regionCode = $props['region_id'] ?? 'unknown'; // Adjust depending on your province ID structure
//             $coordinates = $feature['geometry']['coordinates'];
//             $path = geojsonToSvgPath($coordinates, 1000, 1200);

//             // Provinces hidden initially
//             $provincesSvg .= "<path d=\"{$path}\" class=\"province\" data-region=\"{$regionCode}\" data-province=\"{$code}\" data-name=\"{$name}\" style=\"display:none\"></path>";
//         }

//         // Save combined SVG
//         $cachedSvg = $regionsSvg . $provincesSvg;
//         Storage::disk('public')->put('maps/cache.svg', $cachedSvg);

//         $this->info('SVG paths (regions + provinces) generated and cached successfully.');
//     }
// }
