<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Unit;

class AuthController extends Controller
{
    public function unauthorized()
    {
        return response()->json([
            'error' => 'Não autorizado'
        ], 401);
    }

    public function register(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'cpf' => 'required|string|max:11|unique:users',
            'name' => 'required|string',
            'email' => 'required|email|unique:users|string',
            'password' => 'required|min:4|same:password_confirmation',
            'password_confirmation' => 'required|min:4'
        ]);

        if($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $cpf = $request->input('cpf');
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $newUser = new User();
        $newUser->cpf = $cpf;
        $newUser->name = $name;
        $newUser->email = $email;
        $newUser->password = password_hash($password, PASSWORD_DEFAULT);
        $newUser->save();

        $token = Auth::attempt([
            'cpf' => $cpf,
            'password' => $password
        ]);

        if(!$token) {
            $array['error'] = 'Ocorreu algum erro!';
            return $array;
        }

        $user = Auth::user();
        $properties = Unit::select(['id', 'name'])->where('id_owner', $user->id)->get();

        $array['token'] = $token;
        $array['user'] = $user;
        $array['user']['properties'] = $properties;

        return $array;
    }

    public function login(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'cpf' => 'required|digits:11',
            'password' => 'required|string'
        ]);
            
        if($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        $cpf = $request->input('cpf');
        $password = $request->input('password');

        $token = Auth::attempt([
            'cpf' => $cpf,
            'password' => $password
        ]);

        if(!$token) {
            $array['error'] = 'Cpf e/ou senha incorretos!';
            return $array;
        }

        $user = Auth::user();
        $properties = Unit::select(['id', 'name'])->where('id_owner', $user->id)->get();

        $array['token'] = $token;
        $array['user'] = $user;
        $array['properties'] = $properties;

        return $array;
    }

    public function validateToken()
    {
        $array = ['error' => ''];

        $user = Auth::user();
        $properties = Unit::select(['id', 'name'])->where('id_owner', $user->id)->get();

        $array['user'] = $user;
        $array['user']['properties'] = $properties;

        return $array;
    }

    public function logout()
    {
        $array = ['error' => ''];

        Auth::logout();

        return $array;
    }
}
