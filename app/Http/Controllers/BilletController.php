<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Billet;

class BilletController extends Controller
{
    public function getAll(Request $request)
    {
        $array = ['error' => ''];
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'property' => 'required'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $property = $request->input('property');

        $unit = Unit::where('id', $property)->where('id_owner', $user->id)->first();

        if (!$unit) {
            $array['error'] = 'Acesso negado';
            return $array;
        }

        $billets = Billet::where('id_unit', $property)->get();
        foreach ($billets as $bKey => $bValue) {
            $billets[$bKey]['file_url'] = asset('storage/'.$bValue['file_url']);
        }
        
        $array['list'] = $billets;
        
        return $array;
    }
}
