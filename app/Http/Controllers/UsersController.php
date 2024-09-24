<?php

namespace App\Http\Controllers;

use App\Http\Resources\UsersResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

use function Laravel\Prompts\password;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username"=> ["required", "unique:users,username"],
            "email"=> ["required","email", 'unique:users,email'],
            "password"=> ["required", Password::min(6)->letters()->symbols()],
        ],[
            "username.required"=> "Username Wajib Diisi",
            "username.lowercase"=> ":attribute harus mengandung Huruf Kecil",
            "email"=> ":attribute Wajib diisi",
            "password.min"=> ":attribute minimal :min karakter",
            "password.letters"=> ":attribute harus mengandung :letters",
            "password.symbols"=> ":attribute harus mengandung :symbols",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'=> false,
                "message"=> $validator->errors()->first(),
            ],422);
        }
        $user = User::create([
            "username"=> $request->username,
            "email"=> $request->email,
            "password"=> bcrypt($request->password)
        ]);
        if ($user) {
            return response([
                "status"=> true,
                "message"=> "Berhasil Registrasi",
                'data'=> $user
            ],200);
        }
        return response()->json([
            'status'=> false,
            'message'=> 'gagal registrasi'
        ]);
    }
    public function login(Request $request)
{
    // Validasi input
    $request->validate([
        "email" => ['required', 'email'],
        "password" => ['required'],
    ]);

    // Mendapatkan credentials
    $credentials = $request->only('email', 'password');

    // Jika autentikasi gagal
    if (!Auth::attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah',
        ], 401); // Unauthorized
    }

    // Jika autentikasi berhasil
    $user = Auth::user();

    // Generate token menggunakan Sanctum
    $token = $user->createToken('token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Berhasil Login',
        'token' => $token,
        'user' => $user,
    ], 200);
}

    public function getUser(string $id)
    {
        $user = User::where("id", $id)->first();
        if (!$user) {
            return response()->json([
                'status'=> 'false',
                "message"=> "Users Tidak DItemukan"
                ], Response::HTTP_NOT_FOUND);
        }else{
            return response()->json([
                "status"=> 'success',
                'message'=> 'Users Ditemukan',
                'data'=> new UsersResource(true, 'Users Ditemukan', $user)
            ]);
        }
    }

    public function createUser(Request $request)
    {
        $request->validate([
            "username"=> ["required", "lowercase"],
            "email"=> ["required", "email"],
            "password"=> ["required"],
        ],[
            "username.required"=> "Username Wajib Diisi",
            "username.lowercase"=> ":attribute harus mengandung Huruf Kecil",
            "email"=> ":attribute Wajib diisi",
            "password.min"=> ":attribute minimal :min karakter",
            "password.letters"=> ":attribute harus mengandung :letters",
            "password.symbols"=> ":attribute harus mengandung :symbols",
        ]);
        $user = User::create([
            "username"=> $request->username,
            "email"=> $request->email,
            "password"=> bcrypt($request->password),
            "email_verified_at"=> Carbon::now(),
        ]);

        if ($user) {
            return response()->json([
                "status"=> "success",
                'message' => 'Berhasil Membuat User',
                'data' => $user,
            ],Response::HTTP_OK);
        }else{
            return response()->json([
                'status' => 'false',
                "message"=> "Gagal Membuat User"
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
