<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticateController extends Controller
{
    public function login(Request $request)
    {
        $validates = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validates->fails()) {
            return response()->json($validates->errors(), 400);
        }

        try {
            $userCheck = User::where('username', $request->username)->first();
            if (!$userCheck) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 400);
            }

            if (!$token = Auth::guard('api')->attempt($validates->validated())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            if ($userCheck->roles == 'atasan') {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Authentication as Atasan successfully',
                    'data' => [
                        'token' => $token,
                        'atasan' => [
                            'id' => $userCheck->id,
                            'name' => $userCheck->name,
                            'username' => $userCheck->username,
                            'phone' => $userCheck->phone,
                            'email' => $userCheck->email
                        ]
                    ]
                ], 200);
            }

            if ($userCheck->roles == 'admin') {
                return response()->json([
                    'success' => 'success',
                    'message' => 'Authentication as Admin successfully',
                    'data' => [
                        'token' => $token,
                        'admin' => [
                            'id' => $userCheck->id,
                            'name' => $userCheck->name,
                            'username' => $userCheck->username,
                            'phone' => $userCheck->phone,
                            'email' => $userCheck->email
                        ]
                    ]

                ]);
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json([
            'success' => "success",
            'message' => 'Successfully logged out',
        ]);
    }
}
