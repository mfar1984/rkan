<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
	/**
	 * Handle an incoming authentication request.
	 */
	public function __invoke(Request $request): RedirectResponse
	{
		$credentials = $request->validate([
			'email' => ['required', 'string', 'email'],
			'password' => ['required', 'string'],
		]);

		if (Auth::attempt($credentials, false)) {
			$request->session()->regenerate();
			return redirect()->intended('/dashboard');
		}

		return back()->withErrors([
			'email' => 'The provided credentials are incorrect.',
		])->onlyInput('email');
	}
} 