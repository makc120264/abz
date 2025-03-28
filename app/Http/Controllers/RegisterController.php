<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * @return Factory|View|Application
     */
    public function create(): Factory|View|Application
    {
        $positions = Position::all();

        return view('register', compact('positions'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:60',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'phone' => ['required', 'regex:/^\+380\d{9}$/'],
            'photo' => 'required|image|mimes:jpeg,jpg|max:5120|dimensions:min_width=70,min_height=70',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $photoPath = $request->file('photo')->store('photos', 'public');

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position_id' => $request->position_id,
            'photo' => $photoPath,
            'password' => Hash::make('password123')
        ]);

        return redirect()->route('register.form')->with('success', 'User registered successfully!');
    }
}

