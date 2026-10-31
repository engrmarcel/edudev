<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out. Staff go back to the staff sign-in page;
     * everyone else goes to the public homepage.
     */
    public function __invoke()
    {
        $wasStaff = (bool) Auth::user()?->isStaff();

        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect($wasStaff ? route('admin.login') : '/');
    }
}
