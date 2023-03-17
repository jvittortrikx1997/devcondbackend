<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\AreaDisabledDay;
use App\Models\Reservation;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function getReservation(){
        $array = ['error' => '', 'list' => []];
        $daysHelper = ['Dom', 'Seg', 'Ter', 'Quar', 'Quin', 'Sex', 'Sab'];

        $areas = Area::where('allowed', 1)->get();

        foreach($areas as $area){
            $dayList = explode(',', $area['days']);
            $dayGroups = [];

            $lastDay = intval(current($dayList));
            $dayGroups[] = $daysHelper[$lastDay];
            array_shift($dayList);

            foreach($dayList as $day){
                if(intval($day) != $lastDay+1){
                    $dayGroups[] = $daysHelper[$lastDay];
                    $dayGroups[] = $daysHelper[$day];
                }
                $lastDay = intval($day);
            }
            $dayGroups[] = $daysHelper[end($dayList)];
            $dates = '';
            $close = 0;
            foreach($dayGroups as $group){
                if($close == 0){
                    $dates .=$group;
                }else{
                    $dates .= '-'.$group.',';
                }
                $close = 1 - $close;
            }

            $dates = explode(',', $dates);
            array_pop($dates);

            $startTime = date('H:i', strtotime($area['start_time']));
            $endTime = date('H:i', strtotime($area['end_time']));

            foreach($dates as $dKey => $dValue){
                $dates[$dKey] .= ' '.$startTime.' às '.$endTime;
            }

            $array['list'][] = [
                'id' => $area['id'],
                'cover' => asset('storage/'.$area['cover']),
                'tile' => $area['title'],
                'dates' => $dates
            ];
        }
        return $array;
    }

    public function setReservation($id, Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
            'property' => 'required'
        ]);

        if(!$validator->fails()){
            $date = $request->input('date');
            $time = $request->input('time');
            $property = $request->input('property');

            $unit = Unit::find($property);
            $area = Area::find($id);

            if($unit && $area){
                $can = true;
                $weekDay = date('w', strtotime($date));
                $allowedDays = explode(',', $area['days']);
                if(!in_array($weekDay, $allowedDays)){
                    $can = false;
                }else{
                    $start = strtotime($area['start_time']);
                    $end = strtotime('-1 hour', strtotime($area['end_time']));
                    $revtime = strtotime($time);
                    if($revtime < $start || $revtime > $end){
                        $can = false;
                    }
                }
                $existingDisabledDay = AreaDisabledDay::where('id_area', $id)
                ->where('day', $date)
                ->count();
                if($existingDisabledDay > 0){
                    $can = false;
                }
                $existingReservation = Reservation::where('id_area', $id)
                ->where('reservation_date', $date.' '.$time)
                ->count();
                if($existingReservation){
                    $can = false;
                }

                if($can == true){
                    $reservation = new Reservation();
                    $reservation->id_unit = $property;
                    $reservation->id_area = $id;
                    $reservation->reservation_date = $date.' '.$time;
                    $reservation->save();
                }else{
                    $array['error'] = 'Reserva não permitida nesse período.';
                    return $array;
                }
            }else{
                $array['error'] = 'Dados incorretos';
                return $array;
            }
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
    }

    public function getDisabledDates($id){
        $array = ['error' => '', 'list' => []];
        $area = Area::find($id);

        if($area){
            $disabledDays = AreaDisabledDay::where('id_area', $id)->get();
            foreach($disabledDays as $disabledDay){
                $array['list'][] = $disabledDay['day'];
            }

            $allowedDays = explode(',', $area['days']);
            $offDays = [];
            for($q=0;$q<7;$q++){
                if(!in_array($q, $allowedDays)){
                    $offDays[] = $q;
                }
            }

            $start = time();
            $end = strtotime('+3 month');
            $current = $start;
            /*$keep = true;
            while($keep){
                if($current < $end){
                    $wd = date('w', $current);
                    if(in_array($wd, $offDays)){
                        $array['list'][] = date('Y-m-d', $current);
                    }
                    $current = strtotime('+1 day', $current);
                }else{
                    $keep = false;
                }
            }*/

            for(
                $current = $start;
                $current < $end;
                $current = strtotime('+1 day', $current)
            ){
                $wd = date('w', $current);
                    if(in_array($wd, $offDays)){
                        $array['list'][] = date('Y-m-d', $current);
                    }
            }
        }else{
            $array['error'] = 'A area informada é inválida';
            return $array;
        }

        return $array;
    }
}
