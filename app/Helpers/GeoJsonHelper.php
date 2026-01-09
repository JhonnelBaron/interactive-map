<?php

function project($lon, $lat, $width = 1000, $height = 1200)
{
    $minLon = 116.0;
    $maxLon = 127.0;
    $minLat = 5.0;
    $maxLat = 19.0;

    $x = ($lon - $minLon) / ($maxLon - $minLon) * $width;
    $y = $height - (($lat - $minLat) / ($maxLat - $minLat) * $height);

    return [$x, $y];
}

function geojsonToSvgPath(array $coordinates, $width = 1000, $height = 1200)
{
    $paths = [];

    // Check if the first element is a number (single polygon), wrap it
    if (isset($coordinates[0][0][0]) && is_float($coordinates[0][0][0])) {
        // Polygon instead of MultiPolygon
        $coordinates = [$coordinates];
    }

    foreach ($coordinates as $polygon) { // MultiPolygon level
        foreach ($polygon as $ring) { // Polygon level
            $d = '';
            foreach ($ring as $i => $point) {
                if (!isset($point[0], $point[1])) continue; // safety check
                [$x, $y] = project($point[0], $point[1], $width, $height);
                $d .= ($i === 0 ? "M" : "L") . $x . " " . $y . " ";
            }
            $d .= "Z";
            $paths[] = $d;
        }
    }

    return implode(" ", $paths);
}
