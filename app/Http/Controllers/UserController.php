<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function remove(Request $request): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $id = $request->post('id');
        var_dump($request->all());

        User::whereId($id)->firstOrFail()->delete();

        return redirect(route('register', absolute: false));
    }
}
