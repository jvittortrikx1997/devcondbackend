<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;

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
}
