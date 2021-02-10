<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function getAllDates()
    {
        $array = ['error' => ''];

        $areas = Area::where('allowed', 1)->get();
        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];

        foreach ($areas as $area) {
            $daysList = explode(',', $area['days']);
            $daysGroup = [];
            $lastDay = intval(current($daysList));
            array_shift($daysList);

            //Inserir primeiro dia
            $daysGroup[] = $daysHelper[$lastDay];

            //Inserir dias que não são sequencia
            foreach ($daysList as $day) { // 1 3 5
                if (intval($day) !== $lastDay + 1) {

                    $daysGroup[] = $daysHelper[$lastDay]; // 1
                    $daysGroup[] = $daysHelper[$day]; // 3
                }

                $lastDay = intval($day);
            }

            //Inserir ultimo dia
            $daysGroup[] = $daysHelper[end($daysList)];

            $dates = '';
            $close = 0;

            foreach ($daysGroup as $day) {
                if ($close == 0) {
                    $dates .= $day;
                } else {
                    $dates .= '-' . $day . ',';
                }

                $close = 1 - $close;
            }
            print_r($daysGroup);
            $dates = explode(',', $dates);
            array_pop($dates);
            $start = $area['start_time'];
            $end = $area['end_time'];

            foreach ($dates as $dKey => $dValue) {
                $dates[$dKey] .= ' ' . $start . ' as ' . $end;
            }

            $array['list'][] = [
                'id_area' => $area['id'],
                'title' => $area['title'],
                'cover' => asset('storage/' . $area['cover']),
                'dates' => $dates
            ];
        }

        return $array;
    }

    public function getDisabledDates($id)
    {
        $array = ['error' => ''];

        $area = Area::find($id);

        if (!$area) {
            $array['error'] = 'Area não encontrada';
            return $array;
        }

        $allowedDays = explode(',', $area['days']);
        $offDays = [];

        for ($q = 0; $q < 7; $q++) {
            if (!in_array($q, $allowedDays)) {
                $offDays[] = $q;
            }
        }

        $start = time();
        $end = strtotime('+3 months');

        for (
            $current = $start;
            $current < $end;
            $current = strtotime('+1 day', $current)
        ) {
            $weekday = date('w', $current);
            if (in_array($weekday, $offDays)) {
                $array['list'][] = date('Y-m-d', $current);
            }
        }

        return $array;
    }

    public function getTimes(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $date = $request->input('date');
        $area = Area::find($id);

        if (!$area) {
            $array['error'] = 'Area não encontrada';
            return $array;
        }

        $isDisabled = AreaDisabledDay::where('id_unit', $id)->where('day', $date)->first();
        if ($isDisabled) {
            $array['error'] = 'Dia indisponível!';
            return $array;
        }

        $allowedDays = explode(',', $area['days']);
        $weekday = date('w', strtotime($date));

        if (!in_array($weekday, $allowedDays)) {
            $array['error'] = 'Area não está em funcionamento neste dia!';
            return $array;
        }

        $start = strtotime($area['start_time']);
        $end = strtotime($area['end_time']);
        $timeList = [];

        for (
            $lastTime = $start;
            $lastTime < $end;
            $lastTime = strtotime('+1 hour', $lastTime)
        ) {
            $timeList[] = [
                'id' => date('H:i:s', $lastTime),
                'title' => date('H:i', $lastTime) . ' ' . date('H:i', strtotime('+1 hour', $lastTime))
            ];
        }

        $toRemove = [];
        $reservations = Reservation::where('id_unit', $id)->whereBetween('reservation_date', [
            $date . ' 00:00:00',
            $date . ' 23:59:59'
        ])->get();

        foreach ($reservations as $reservation) {
            $toRemove[] = date('H:i:s', strtotime($reservation['reservation_date']));
        }

        foreach ($timeList as $time) {
            if (!in_array($time['id'], $toRemove)) {
                $array['list'][] = $time;
            }
        }


        return $array;
    }
}
