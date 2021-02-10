<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Unit;
use App\Models\UnitPeople;
use App\Models\UnitPet;
use App\Models\UnitVehicle;

class UnitController extends Controller
{
    public function getInfo($id)
    {
        $array = ['error' => ''];

        $user = Auth::user();

        $unit = Unit::where('id', $id)->where('id_owner', $user->id)->first();

        if (!$unit) {
            $array['error'] = 'Essa unidade não é sua!';
            return $array;
        }

        $unitpeoples = UnitPeople::where('id_unit', $id)->get();
        foreach ($unitpeoples as $pKey => $pValue) {
            $unitpeoples[$pKey]['birthdate'] = date('d/m/Y', strtotime($pValue['birthdate']));
        }
        $unitpets = UnitPet::where('id_unit', $id)->get();
        $unitvehicles = UnitVehicle::where('id_unit', $id)->get();

        $array['unit'][] = [
            'id' => $unit['id'],
            'ap' => $unit['name'],
            'peoples' => $unitpeoples,
            'pets' => $unitpets,
            'vehicles' => $unitvehicles
        ];

        return $array;
    }

    public function addPerson($id, Request $request)
    {
        $array = ['error' => ''];

        $user = Auth::user();
        $unit = Unit::where('id', $id)->where('id_owner', $user->id)->first();

        if (!$unit) {
            $array['error'] = 'Acesso negado';
            return $array;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'birthdate' => 'required|date_format:d/m/Y'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        $name = $request->input('name');
        $birthdate = $request->input('birthdate');

        $birthdate = explode('/', $birthdate);
        $birthdate = $birthdate[2] . '-' . $birthdate[1] . '-' . $birthdate[0];

        $newPeople = new UnitPeople();
        $newPeople->id_unit = $id;
        $newPeople->name = $name;
        $newPeople->birthdate = $birthdate;
        $newPeople->save();

        return $array;
    }

    public function addPet(Request $request, $id)
    {
        $array = ['error' => ''];

        $user = Auth::user();
        $unit = Unit::where('id', $id)->where('id_owner', $user->id)->first();

        if (!$unit) {
            $array['error'] = 'Acesso negado';
            return $array;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'race' => 'required|string'
        ]);

        if($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $name = $request->input('name');
        $race = $request->input('race');

        $newPet = new UnitPet();
        $newPet->id_unit = $id;
        $newPet->name = $name;
        $newPet->race = $race;
        $newPet->save();

        return $array;
    }

    public function addVehicle(Request $request, $id)
    {
        $array = ['error' => ''];

        $user = Auth::user();
        $unit = Unit::where('id', $id)->where('id_owner', $user->id)->first();

        if (!$unit) {
            $array['error'] = 'Acesso negado';
            return $array;
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:20',
            'plate' => 'required|string',
            'color' => 'required|string'
        ]);

        if($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $title = $request->input('title');
        $plate = $request->input('plate');
        $color = $request->input('color');

        $newVehicle = new UnitVehicle();
        $newVehicle->id_unit = $id;
        $newVehicle->title = $title;
        $newVehicle->plate = $plate;
        $newVehicle->color = $color;
        $newVehicle->save();

        return $array;
    }

    public function removePerson($id, Request $request)
    {
        $array = ['error' => ''];
        $user = Auth::user();

        $idItem = $request->input('id');

        $unit = Unit::where('id', $id)->where('id_owner', $user->id)->first();

        if (!$unit) {
            $array['error'] = 'Acesso negado';
            return $array;
        }

        $people = UnitPeople::where('id', $idItem)->where('id_unit', $id)->first();

        if (!$people) {
            $array['error'] = 'Pessoa não encontrada no sistema!';
            return $array;
        }

        $people->delete();

        return $array;
    }

    public function removePet($id, Request $request)
    {
        $array = ['error' => ''];

        $user = Auth::user();
        $unit = Unit::where('id', $id)->where('id_owner', $user->id)->first();

        if(!$unit) {
            $array['error'] = 'Acesso negado!';
            return $array;
        }

        $idItem = $request->input('id');

        $pet = UnitPet::where('id_unit', $id)->where('id', $idItem)->first();

        if(!$pet) {
            $array['error'] = 'Pet não encontrado no sistema!';
            return $array;
        }

        $pet->delete();

        return $array;
    }

    public function removeVehicle($id, Request $request)
    {
        $array = ['error' => ''];

        $user = Auth::user();
        $unit = Unit::where('id', $id)->where('id_owner', $user->id)->first();

        if(!$unit) {
            $array['error'] = 'Acesso negado';
            return $array;
        }
        $idItem = $request->input('id');

        $vehicle = UnitVehicle::where('id_unit', $id)->where('id', $idItem)->first();

        if(!$vehicle) {
            $array['error'] = 'Veículo não encontrado no sistema!';
            return $array;
        }

        $vehicle->delete();

        return $array;
    }
}
