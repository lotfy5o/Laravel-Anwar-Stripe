<?php

namespace App\Http\Controllers\adminAuth;

use App\Models\Cart;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

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
        $cart = Cart::session()->first();

        $request->authenticate();

        $request->session()->regenerate();

        if ($cart) {
            $cart->update([
                'session_id' => session()->getId(),
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $cart = Cart::session()->first();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($cart) {
            $cart->delete();
        }

        return redirect('/');
    }
}
