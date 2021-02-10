<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\FoundAndLost;

class FoundAndLostController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];

        $losts = FoundAndLost::where('status', 'lost')->orderBy('date_created')->get();

        foreach ($losts as $fKey => $fValue) {
            $losts[$fKey]['date_created'] = date('d/m/Y', strtotime($fValue['date_created']));
            $losts[$fKey]['photo'] = asset('storage/' . $fValue['photo']);
        }

        $founds = FoundAndLost::where('status', 'found')->orderBy('date_created')->get();

        foreach ($founds as $fKey => $fValue) {
            $founds[$fKey]['date_created'] = date('d/m/Y', strtotime($fValue['date_created']));
            $founds[$fKey]['photo'] = asset('storage/' . $fValue['photo']);
        }
        $array['losts'] = $losts;
        $array['founds'] = $founds;
        return $array;
    }

    public function insert(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'photo' => 'required|file|mimes:jpeg,jpg,png',
            'title' => 'required|string',
            'where' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $title = $request->input('title');
        $where = $request->input('where');
        $description = $request->input('description');
        $photo = $request->file('photo')->store('public');
        $photo = explode('/', $photo);
        $photo = $photo[1];

        $newFound = new FoundAndLost();
        $newFound->title = $title;
        $newFound->description = $description;
        $newFound->status = 'lost';
        $newFound->date_created = date('Y-m-d');
        $newFound->where = $where;
        $newFound->photo = $photo;
        $newFound->save();

        return $array;
    }

    public function update($id, Request $request)
    {
        $array = ['error' => ''];

        $foundandlost = FoundAndLost::where('id', $id)->first();

        if ($foundandlost) {
            $status = $request->input('status');

            if ($status && in_array($status, ['found', 'lost'])) {
                $foundandlost->status = $status;
                $foundandlost->save();
            } else {
                $array['error'] = 'Status válidos found ou lost';
                return $array;
            }
        } else {
            $array['error'] = 'ID inválido!';
            return $array;
        }

        return $array;
    }
}
