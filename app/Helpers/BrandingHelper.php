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
                'color' => '#000000',
            ];
        }

        return [
            'name' => $organisation->name,
            'logo' => $organisation->logo,
            'gst' => $organisation->gst_number,
            'address' => $organisation->address,
            'footer' => $organisation->quotation_footer,
            'branding' => $organisation->pdf_branding ?? [],
        ];
    }
}
