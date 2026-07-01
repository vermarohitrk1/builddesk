<?php

namespace App\Helpers;

use App\Models\Organisation;
use Illuminate\Support\Facades\Auth;

class BrandingHelper
{
    /**
     * Get the current organisation's branding details.
     */
    public static function getBranding(): array
    {
        $organisation = Auth::user()?->organisation;

        if (!$organisation) {
            return [
                'name' => 'Default Company',
                'logo' => null,
                'theme_color' => '#fc720d',
                'theme_color_rgb' => self::hexToRgbString('#fc720d'),
                'theme_color_hover' => self::adjustColorBrightness('#fc720d', -15),
                'theme_color_active' => self::adjustColorBrightness('#fc720d', -25),
            ];
        }

        $theme_color = $organisation->theme_color ?? '#fc720d';

        return [
            'name' => $organisation->name,
            'logo' => $organisation->logo,
            'gst' => $organisation->gst_number,
            'address' => $organisation->address,
            'footer' => $organisation->quotation_footer,
            'branding' => $organisation->pdf_branding ?? [],
            'theme_color' => $theme_color,
            'theme_color_rgb' => self::hexToRgbString($theme_color),
            'theme_color_hover' => self::adjustColorBrightness($theme_color, -15),
            'theme_color_active' => self::adjustColorBrightness($theme_color, -25),
        ];
    }
    
    public static function hexToRgbString($hex) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "$r, $g, $b";
    }
    

    public static function adjustColorBrightness($hex, $steps) {
        // Steps range from -255 (darkest) to 255 (lightest)
        $steps = max(-255, min(255, $steps));
        $hex = ltrim($hex, '#');

        if (strlen($hex) == 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Adjust brightness channels safely between 0 and 255
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . 
                        str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . 
                        str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
    
}
