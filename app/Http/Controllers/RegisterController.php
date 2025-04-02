<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse as IlluminateJsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;

use function Tinify\fromFile;
use function Tinify\setKey;

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
     * @return RedirectResponse|IlluminateJsonResponse
     */
    public function store(Request $request): RedirectResponse|IlluminateJsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:60',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'phone' => ['required', 'regex:/^\+380\d{9}$/'],
            'photo' => 'required|image|mimes:jpeg,jpg|max:5120|dimensions:min_width=70,min_height=70',
        ]);

        if ($validator->fails()) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $validator->getMessageBag(),
                ]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $photoPath = $request->file('photo')->store('photos', 'public');

        $this->imageOptimization($photoPath);

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position_id' => $request->position_id,
            'photo' => $photoPath,
            'password' => Hash::make('password123')
        ]);

        if ($request->is('api/*')) {
            return response()->json([
                "success" => true,
                "user_id" => $newUser->id,
                "message" => "New user successfully registered"
            ]);
        } else {
            return redirect()->route('users.index')->with('success', 'User registered successfully!');
        }
    }

    /**
     * @param $photoPath
     * @return void
     */
    private function imageOptimization($photoPath): void
    {
        setKey(env('TINYPNG_API_KEY'));

        $absFilePath = public_path('storage') . "/" . $photoPath;

        $source = fromFile($absFilePath);

        $resizedImage = $source->resize([
            "method" => "cover",
            "width" => 70,
            "height" => 70
        ]);

        $resizedImage->toFile($absFilePath);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return redirect()->back()->withErrors(['email' => 'Invalid credentials']);
        }

        return redirect()->route('register.form');
    }

}

