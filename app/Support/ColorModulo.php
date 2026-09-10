<?php

namespace App\Support;

class ColorModulo
{
    public static function fondo(mixed $color): string
    {
        return is_string($color) && preg_match('/^#[0-9a-f]{6}$/i', $color)
            ? strtolower($color)
            : '#ffffff';
    }

    public static function requiereTextoClaro(string $color): bool
    {
        $hexadecimal = ltrim(self::fondo($color), '#');
        $rojo = hexdec(substr($hexadecimal, 0, 2));
        $verde = hexdec(substr($hexadecimal, 2, 2));
        $azul = hexdec(substr($hexadecimal, 4, 2));

        return (($rojo * 299) + ($verde * 587) + ($azul * 114)) / 1000 < 150;
    }
}
