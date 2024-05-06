<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function Store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5',
            "name" => "required"
        ]);
        try {
            DB::beginTransaction();
            $user = new User();
            $user->name = $request->name;
            $user->password = bcrypt($request->password);
            $user->email = $request->email;
            $user->save();
            DB::commit();
            return response()->json(["code" => 200, "message" => "Account created succesfully"]);
        } catch (\Exception $err) {
            DB::rollBack();
            return abort(500, $err);
        }
    }

    public function Login(Request $request): JsonResponse
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
            "device_name" => "required"
        ]);

        $user = User::where("email", $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken($request->device_name)->plainTextToken;
        return response()->json(["token" => $token]);
    }
}
