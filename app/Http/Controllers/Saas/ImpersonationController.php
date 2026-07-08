<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a tenant user.
     */
    public function impersonate(User $user)
    {
        // Store the original admin ID in the session
        session()->put('impersonated_by', auth('admin')->id());
        
        // Log in as the target user using the web guard
        Auth::guard('web')->login($user);
        
        return redirect()->route('dashboard')->with('success', "You are now impersonating {$user->name}.");
    }

    /**
     * Stop impersonating and return to the Super Admin panel.
     */
    public function leave()
    {
        // Only allow leaving if actually impersonating
        if (!session()->has('impersonated_by')) {
            return redirect()->route('dashboard');
        }

        // Log out of the web guard
        Auth::guard('web')->logout();
        
        // Forget the impersonation flag
        session()->forget('impersonated_by');
        
        return redirect()->route('saas.users.index')->with('success', 'You have returned to the Super Admin panel.');
    }
}
