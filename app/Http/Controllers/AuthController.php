<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'number' => 'required|string|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'number' => $fields['number'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }
    public function login(Request $request)
    {
        $fields = $request->validate([
            'number' => 'required|string',
            'password' => 'required|string'
        ]);

        //check the number
        $user = User::where('number', $fields['number'])->first();

        //check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logged out'
        ];
    }
    public function depot(Request $request, $id)
    {
        $request
        if ($id == auth()->user()->id) {
            $request->validate([
                'value' => 'required'
            ]);
            $user = User::find($id);

            $user->value = $user->value + $request->input("value");
            $user->save();

            return $user;
        }
        echo 'session unavailable';
    }
    public function update(Request $request, $id)
    {
        if ($id == auth()->user()->id) {
            $user = User::find($id);
            $user->value = $request->input("value");
            $user->save();

            return $user;
        }
        echo 'you are not authenticated';
    }
    public function transfert(Request $request, $id)
    {
        if ($id == auth()->user()->id) {
            $request->validate([
                'number' => 'required',
                'value' => 'required',
                'password' => 'required'
            ]);
            $user = User::find($id);

            //search the number in the user database
            $user_destination = User::where('number', $request->input("number"))->first();

            //check if the use user exists and if the password is true
            if (!$user_destination || !Hash::check($request->input("password"), $user->password)) {
                return response([
                    'message' => 'try again, password false or destination do not exist'
                ], 401);
            }

            //check if the value is less than the transfert value
            if ($user->value < $request->input("value")) {
                return response([
                    'message' => 'the amount in your account is not enough'
                ], 401);
            }

            //update the value in the user account
            $user->value = $user->value - $request->input("value");
            $user->save();


            //update the value in user destination account
            $user_destination->value = $user_destination->value + $request->input("value");
            $user_destination->save();

            return $user;
        }
        echo 'you are not authenticated';
    }
    public function show($id)
    {
        if ($id == auth()->user()->id) {
            $user = User::find($id);

            // echo $user->value('value');
            echo "hello world";
            var_dump($user->value);
            return $user;
        }
        echo 'you are not authenticated';
    }
}
