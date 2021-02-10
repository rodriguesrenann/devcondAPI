<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Unit;
use App\Models\Warning;

class WarningController extends Controller
{
    public function getMyWarnings(Request $request)
    {
        $array = ['error' => ''];

        $property = $request->input('property');
        $user = Auth::user();

        if (empty($property)) {
            $array['error'] = 'Informe a sua unidade';
            return $array;
        }

        $unit = Unit::where('id_owner', $user->id)->first();

        if (!$unit) {
            $array['error'] = 'Acesso negado';
            return $array;
        }

        $warnings = Warning::where('id_unit', $property)
            ->orderBy('date_created', 'DESC')
            ->orderBy('id')
            ->get();

        foreach ($warnings as $wKey => $wValue) {
            $warnings[$wKey]['date_created'] = date('d/m/Y H:i', strtotime($wValue['date_created']));
            $photos = explode(',', $wValue['photos']);
            $photos2 = [];

            foreach ($photos as $photo) {
                $photos2[] = asset('storage/' . $photo);
            }

            $warnings[$wKey]['photos'] = $photos2;
        }
        $array['list'] = $warnings;
        return $array;
    }

    public function addWarningFile(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'photo' => 'required|mimes:jpeg,png,jpg'
        ]);

        if($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $file = $request->file('photo')->store('public');
        $array['photo'] = asset(Storage::url($file));

        return $array;
    }

    public function setWarning(Request $request)
    {
        $array = ['error' => ''];

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'property' => 'required',
            'title' => 'required|string'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $title = $request->input('title');
        $property = $request->input('property');
        $list = $request->input('list');

        $unit = Unit::where('id_owner', $user->id)->where('id', $property)->first();
        if (!$unit) {
            $array['error'] = 'Informe uma unidade que pertença a voce';
            return $array;
        }

        $newWarning = new Warning();
        $newWarning->id_unit = $property;
        $newWarning->title = $title;
        $newWarning->date_created = date('Y-m-d');
        $newWarning->photos = '';

        if(!empty($list) && is_array($list)) {
            $photos = [];
            foreach($list as $item) {
                $photo = explode('/', $item);
                $photos[] = end($photo);
            }
            $photos = implode(',', $photos);
            
            $newWarning->photos = $photos;
        }

        $newWarning->save();

        return $array;
    }
}
