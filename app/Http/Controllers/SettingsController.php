<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $organisation = Auth::user()->organisation;
        return view('pages.settings.index', compact('organisation'));
    }

    public function updateOrganisationLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $organisation = Auth::user()->organisation;

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($organisation->logo) {
                Storage::disk('public')->delete($organisation->logo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $organisation->update(['logo' => $path]);
        }

        return $this->ajaxResponse('success', 'Logo updated successfully!', [
        ]);
    }
}
