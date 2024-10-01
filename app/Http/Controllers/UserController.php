<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('username', 'ASC')->paginate(10);
        return response()->json([
            'data' => $users,
            'success' => true,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        return response()->json([
            'data' => $user,
            'success' => true,
            'message' => 'Get user successfully',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $admin = $request->user();
        $rules = [];
        if ($request->first_name) {
            $rules['first_name'] = 'required';
        }
        if ($request->last_name) {
            $rules['last_name'] = 'required';
        }
        if ($request->username) {
            $rules['username'] = 'required';
        }
        if ($request->password) {
            $rules['password'] = 'required';
        }
        if ($request->email) {
            $rules['email'] = 'required|email|unique:users,' . $id;
        }
        $request->validate(
            $rules
        );

        if ($admin->is_admin != 1) {
            //Chỉ được phép sửa user chính mình
            if ($admin->id != $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                ], 403);
            }
        }
        $user = User::find($id);
        $user->fill($request->all());
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Update user successfully',
            'data' => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $admin = $request->user();
        if ($admin->is_admin != 1) {
            //Chỉ được phép xóa user chính mình
            if ($admin->id != $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                ], 403);
            }
        }
        $user = User::find($id);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delete user successfully',
        ]);
    }

    public function orders(Request $request)
    {
        $user = $request->user();
        $user->orders = $user->orders()->with('details')->select('id', 'total')->orderBy('total', 'DESC')->get();
        return response()->json([
            'data' => $user,
            'success' => true,
            'message' => 'Get order successfully',
        ]);
    }
}
