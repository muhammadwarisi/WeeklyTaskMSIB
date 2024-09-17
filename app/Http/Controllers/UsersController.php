<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function Laravel\Prompts\password;
use Illuminate\Validation\Rules\Password;

class UsersController extends Controller
{
    public function loginUser(Request $request)
    {
        $request->validate([
            "email"=> ["required"],
            "password"=> ["required"],
        ]);
        $data = [
            "email"=> $request->email,
            "password"=> $request->password,
        ];

        if(!$token = auth()->guard('api')->attempt($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password Anda salah'
            ], 401);
        }

        //if auth success
        return response()->json([
            'success' => true,
            'user'    => auth()->guard('api')->user(),    
            'token'   => $token   
        ], 200);
        // GENERATE TOKEN LOGIN
        // $token = RandomString::numeric(6);

        // if ($data) {
        //     return response()->json([
        //         'data' => $data,
        //         'message' => 'Berhasil Login'
        //     ]);
        // }

        // if ($data['email'] == $user['email']) {
        //     if ($user['password'] != $data['password']) {
        //         return response()->json([
        //             'status' => 'Success',
        //             'message' => 'berhasil login'
        //         ]);
        //     }
        // }
    }

    public function getUser(string $id)
    {
        $user = User::where("id", $id)->first();
        if ($user) {
            return response()->json([$user],200);
        }else{
            return response()->json(["message"=> "Users Tidak DItemukan"]);
        }
    }

    public function createUser(Request $request)
    {
        $request->validate([
            "username"=> ["required", "lowercase"],
            "email"=> ["required", "email"],
            "password"=> ["required", Password::min(6)->letters()->symbols()],
        ],[
            "username.required"=> "Username Wajib Diisi",
            "username.lowercase"=> ":attribute harus mengandung Huruf Kecil",
            "email"=> "Email Wajib diisi",
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
            return response()->json([$user],200);
        }else{
            return response()->json(["message"=> "Users Tidak DItemukan"]);
        }
    }
}
