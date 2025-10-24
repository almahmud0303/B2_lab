<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();

        // Get the authenticated user
        $user = auth()->user();
        
        // Log successful login
        \Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role,
        ]);

        // Always redirect to the main dashboard which will handle role-based redirection
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log out from all guards
        Auth::guard('web')->logout();
        Auth::logout();
        
        // Invalidate the session completely
        $request->session()->invalidate();
        
        // Regenerate the CSRF token
        $request->session()->regenerateToken();
        
        // Clear all session data
        $request->session()->flush();
        
        // Clear any cached data
        if (method_exists($request, 'clearCache')) {
            $request->clearCache();
        }

        // Redirect to login with a special parameter to prevent back button access
        return redirect()->route('login')->with('logout', 'true');
    }
}
