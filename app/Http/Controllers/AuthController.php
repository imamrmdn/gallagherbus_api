<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\User;

use DB;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'alamat' => 'string',
            'tanggal_lahir' => 'required|date',
            'password' => 'required|string|min:6',
        ]);

        $name = $request->input('name');
        $email = $request->input('email');

        //
        try {
            //code...

            $cekUsername = DB::table('users')->where('name', $name)->get();

            if ($cekUsername->isEmpty()) {
                $user = new User([
                    'name' => $request->name,
                    'email' => $request->email,
                    'alamat' => $request->alamat,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'password' => bcrypt($request->password),
                ]);

                $user->save();

                $response = [
                    "success" => true,
                    "message" => "Registration successfuly"
                ];

                return response()->json($response, 201);
            } else {
                return response()->json(['error' => 'username already exist'], 500);
            }



        } catch (\Throwable $th) {
            //throw $th;
            return [
                "success" => false,
                "message" => $th->getMessage()
            ];
        }
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        //
        try {
            //code...
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('MyApp')->accessToken;

                $newUser = [
                    "nama" => $user->name,
                    "email" => $user->email,
                    "alamat" => $user->alamat,
                    "tanggal_lahir" => $user->tanggal_lahir
                ];

                $response = [
                    "success" => true,
                    "status" => 200,
                    "message" => 'Successfuly Login',
                    "data" => [
                        "user" => $newUser,
                        "token" => $token
                    ]
                ];

                return response()->json($response, 200);
            }

            $errorResponse = [
                'status' => 401,
                'message' => 'User Not Found',
                'error' => 'Unauthorized'
            ];

            return response()->json($errorResponse, 401);

        } catch (\Throwable $th) {
            //throw $th;
            return [
                "success" => false,
                "message" => $th->getMessage()
            ];
        }
    }

    public function edit_profile(Request $request){

        $request->validate([
            'nama' => 'required|string',
        ]);

        //
        try {
            //code...
            $user = Auth::user();
            $user->name = $request->nama;
            $user->save();

            $response = [
                'success' => true,
                'message' => 'Profile updated successfully'
            ];
        
            return response()->json($response, 200);

        } catch (\Throwable $th) {
            //throw $th;
            return [
                "success" => false,
                "message" => $th->getMessage()
            ];
        }
    }
}