<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(
            [
                'username' => 'required',
                'password' => 'required',
            ],
            [
                'username.required' => 'Tên đăng nhập không đươc để trống.',
                'password.required' => 'Mật khẩu không được để trống.',
            ]
        );
        
        $username = $request->username;
        $password = $request->password;

        $status = Auth::attempt(['username' => $username, 'password' => $password]);
        if ($status) {
            $token = $request->user()->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'token' => $token,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tên đăng nhập hoặc mật khẩu không đúng',
        ], 401);
    }

    public function register(Request $request)
    {
        $admin = $request->user();
        if ($admin->is_admin != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền',
            ], 401);
        }
        $request->validate(
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required|unique:users',
                'password' => 'required',
                'email' => 'required|email|unique:users',
            ],
            [
                'first_name.required' => 'Tên là bắt buộc.',
                'last_name.required' => 'Họ là bắt buộc.',
                'username.required' => 'Tên đăng nhập là bắt buộc.',
                'username.unique' => 'Tên đăng nhập đã được sử dụng.',
                'password.required' => 'Mật khẩu là bắt buộc.',
                'email.required' => 'Email là bắt buộc.',
                'email.email' => 'Email không hợp lệ.',
                'email.unique' => 'Email đã được sử dụng.',
            ]
        );
        
        
        $user = new User();
        $user->fill($request->all());
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Thêm người dùng thành công',
            'user' => $user,
        ]);
    }
}
