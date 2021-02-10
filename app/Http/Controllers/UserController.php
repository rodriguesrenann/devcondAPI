<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function getInfo()
    {
        $array = ['error' => ''];

        $user = Auth::user();

        $info = User::where('id', $user->id)->first();

        if (!$info) {
            $array['error'] = 'Ocorreu algum erro';
            return $array;
        }

        $array['user'] = $info;

        return $array;
    }

    public function updateUser(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'cpf' => 'required|digits:11',
            'old_password' => 'required|string',
            'new_password' => 'required|min:4|string|same:new_password_confirm',
            'new_password_confirm' => 'required|min:4|string'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $cpf = $request->input('cpf');
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');

        $user = User::where('cpf', $cpf)->first();

        if (!$user) {
            $array['error'] = 'Usuário não registrado no sistema';
            return $array;
        }

        if (!Hash::check($old_password, $user->password)) {
            $array['error'] = 'Senha antiga incorreta';
            return $array;
        }

        $user->password = password_hash($new_password, PASSWORD_DEFAULT);
        $user->save();
        
        return $array;
    }
}