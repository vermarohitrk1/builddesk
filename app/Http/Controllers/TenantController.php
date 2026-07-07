<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function impersonate($id)
    {
        if (!Auth::guard('super_admin')->check()) {
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

        Auth::guard('web')->login($oa);

        return redirect('/dashboard')->with('success', 'Logged in as ' . $organisation->name . ' Admin');
    }

    public function stopImpersonation()
    {
        Auth::guard('web')->logout();
        return redirect()->route('admin.dashboard')->with('success', 'Returned to Super Admin panel');
    }
}
