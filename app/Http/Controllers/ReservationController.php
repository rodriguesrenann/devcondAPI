<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Area;
use App\Models\AreaDisabledDay;
use App\Models\Unit;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function getMyReservations(Request $request)
    {
        $array = ['error' => ''];

        $property = $request->input('property');

        if(empty($property)) {
            $array['error'] = 'Envie a propriedade';
            return $array;
        }

        $user = Auth::user();

        $unit = Unit::where('id', $property)->where('id_owner', $user->id)->first();

        if(!$unit) {
            $array['error'] = 'Essa unidade não é sua!';
            return $array;
        }

        $reservations = Reservation::where('id_unit', $property)->orderBy('reservation_date', 'DESC')->get();
        foreach($reservations as $reservation) {
            $area = Area::find($reservation['id_area']);
            $resDate = date('d/m/Y H:i', strtotime($reservation['reservation_date']));
            $afterHour = date('H:i', strtotime('+1 hour', strtotime($reservation['reservation_date'])));
            $resDate .= ' as '.$afterHour;

            $array['list'][] = [
                'id' => $reservation['id'],
                'area' => $area['title'],
                'cover' => asset('storage/'.$area['cover']),
                'date' => $resDate
                
            ];
        }

        return $array;
    }

    public function setReservation(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required',
            'property' => 'required',
        ]);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $user = Auth::user();

        $date = $request->input('date');
        $time = $request->input('time');
        $property = $request->input('property');

        $area = Area::find($id);
        $unit = Unit::where('id', $property)->where('id_owner', $user->id)->first();

        if(!$area && !$unit) {
            $array['error'] = 'Dados inválidos';
            return $array;
        }

        $timeRes = strtotime($time);
        $start = strtotime($area['start_time']);
        $end = strtotime($area['end_time']);

        if($timeRes < $start || $timeRes > $end) {
            $array['error'] = 'Horário de reserva inválido';
            return $array;
        }

        $allowedDays = explode(',', $area['days']);
        $weekday = date('w', strtotime($date));

        if(!in_array($weekday, $allowedDays)) {
            $array['error'] = 'Dia inválido';
            return $array;
        }

        $isDisabled = AreaDisabledDay::where('id_area', $id)->where('day', $date)->first();
        if($isDisabled) {
            $array['error'] = 'Dia indiponivel';
            return $array;
        }

        $dateRes = $date.' '.$time;
        $isReserved = Reservation::where('id_area', $id)->where('reservation_date', $dateRes)->first();
        if($isReserved) {
            $array['error'] = 'Horário já reservado neste dia';
            return $array;
        }

        $newRes = new Reservation();
        $newRes->id_area = $id;
        $newRes->id_unit = $property;
        $newRes->reservation_date = $dateRes;
        $newRes->save();

        $array['list'][] = [
            'id_area' => $area['id'],
            'title' => $area['title'],
            'cover' => asset('storage/'.$area['cover']),
            'date' => $dateRes.' as '.date('H:i', strtotime('+1 hour', strtotime($dateRes)))
        ];

        return $array;
    }
}
