<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function impersonate($id)
    {
        if (Auth::user()->role !== 'super_admin') {
            abort(403);
        }

        $organisation = \App\Models\Organisation::findOrFail($id);
        
        // Find the first admin of this organisation
        $oa = \App\Models\User::where('organisation_id', $id)
            ->where('role', 'org_admin')
            ->first();

        if (!$oa) {
            return redirect()->back()->with('error', 'This organisation has no active admin account.');
        }

        // Store original SA ID in session
        $saId = Auth::id();
        
        Auth::login($oa);
        
        session(['impersonated_by' => $saId]);

        return redirect()->intended('/dashboard')->with('success', 'Logged in as ' . $organisation->name . ' Admin');
    }

    public function stopImpersonation()
    {
        if (!session()->has('impersonated_by')) {
            return redirect('/dashboard');
        }

        $saId = session()->pull('impersonated_by');
        $sa = \App\Models\User::find($saId);

        if ($sa) {
            Auth::login($sa);
            return redirect()->route('organisations.index')->with('success', 'Returned to Super Admin panel');
        }

        return redirect('/login')->with('success', 'Session expired');
    }
}
