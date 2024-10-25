<?php

namespace App\Http\Controllers;

use App\Http\Resources\UsersResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;



class UsersController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => ["required", "unique:users,username"],
            "firstname" => ['required'],
            "lastname" => ['required'],
            "email" => ["required", "email", 'unique:users,email'],
            "password" => ['required'],
        ], [
            "username.required" => "Username Wajib Diisi",
            "username.lowercase" => ":attribute harus mengandung Huruf Kecil",
            "firstname.required" => ":attribute harus diisi",
            "firstname.string" => ":attribute harus berupa string",
            "lastname.required" => ":attribute harus diisi",
            "lastname.string" => ":attribute harus berupa string",
            "email" => ":attribute harus merupakan email yang valid",
            "password.min" => ":attribute minimal :min karakter",
            "password.letters" => ":attribute harus mengandung :letters",
            "password.symbols" => ":attribute harus mengandung :symbols",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                "message" => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $user = User::create([
            "username" => $request->username,
            "email" => $request->email,
            "firstname" => $request->firstname,
            "lastname" => $request->lastname,
            "password" => bcrypt($request->password)
        ]);
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => "Berhasil Registrasi",
                'data' => new UsersResource($user)
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'gagal registrasi'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
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
            ], Response::HTTP_UNAUTHORIZED); // Unauthorized
        }

        // // Jika autentikasi berhasil
        $user = Auth::user();

        // Update semua Status token yang ada sebelumnya
        $user->tokens()->update(['status' => false]);

        // Buat token baru
        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(60))->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => "Berhasil Login",
            'data' => new UsersResource($user),
            'token' => $token,
        ],Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->update(['status' => false]);
        return response()->json([
            'success' => true,
            'message' => 'Anda Berhasil Logout'
        ]);
    }

    public function getUser(string $id)
    {
        $user = User::where("id", $id);
        $user = $user->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                "message" => "Users Tidak DItemukan"
            ], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json([
                "success" => true,
                'message' => 'Users Ditemukan',
                'data' => new UsersResource($user)
            ],Response::HTTP_OK);
        }
    }

    public function getAllUser(Request $request)
    {
        // Ambil parameter pencarian dari request
        $username = $request->input('username');
        $email = $request->input('email');
        $name = $request->input('name');

        // Query dasar untuk mengambil semua user
        $query = User::query();

        // Tambahkan filter berdasarkan username jika ada
        if ($username) {
            $query->where('username', 'LIKE', "%$username%");
        }

        // Tambahkan filter berdasarkan email jika ada
        if ($email) {
            $query->where('email', 'LIKE', "%$email%");
        }

        // Tambahkan filter berdasarkan firstname jika ada
        if ($name) {
            $query->where(function ($query) use ($name) {
                $query->where('firstname', 'LIKE', "%$name%")
                      ->orWhere('lastname', 'LIKE', "%$name%")
                      ->orWhere(DB::raw("CONCAT(firstname, ' ', lastname)"), 'LIKE', "%$name%");
            });
        }

        // Ambil hasil query
        $user = $query->latest()->get(); // Ambil semua user yang sesuai filter dan urutkan
        
        if ($user->isEmpty()) {
            return response()->json([
                'success' => false,
                "message" => "Users Tidak DItemukan"
            ], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json([
                "success" => true,
                'message' => 'Users Ditemukan',
                'data' => UsersResource::collection($user)
            ],Response::HTTP_OK);
        }
    }
}
