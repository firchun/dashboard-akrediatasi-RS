<?php

if (!function_exists('ringSVG')) {
    function ringSVG($p, $size = 50, $stroke = 6, $color = '#94a3ad', $trackColor = '#e6edee')
    {
        $r = ($size - $stroke) / 2;
        $c = 2 * M_PI * $r;
        $off = $c * (1 - min(100, max(0, $p)) / 100);
        $cx = $size / 2;

        return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '">'
            . '<circle cx="' . $cx . '" cy="' . $cx . '" r="' . $r . '" fill="none" stroke="' . $trackColor . '" stroke-width="' . $stroke . '"/>'
            . '<circle cx="' . $cx . '" cy="' . $cx . '" r="' . $r . '" fill="none" stroke="' . $color . '" stroke-width="' . $stroke . '" '
            . 'stroke-linecap="round" stroke-dasharray="' . number_format($c, 2, '.', '') . '" stroke-dashoffset="' . number_format($off, 2, '.', '') . '" '
            . 'transform="rotate(-90 ' . $cx . ' ' . $cx . ')"/>'
            . '</svg>';
    }
}
