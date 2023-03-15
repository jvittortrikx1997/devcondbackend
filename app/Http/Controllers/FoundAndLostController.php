<?php

namespace App\Http\Controllers;

use App\Models\FoundAndLost;
use Illuminate\Http\Request;

class FoundAndLostController extends Controller
{
    public function getAll(){
        $array = ['error' => ''];
        $user = auth()->user();

        $lost = FoundAndLost::where('status', 'LOST')
        ->orderBy('datecreated', 'DESC')
        ->orderBy('id', 'DESC')
        ->get();

        $recovered = FoundAndLost::where('status', 'RECOVERED')
        ->orderBy('datecreated', 'DESC')
        ->orderBy('id', 'DESC')
        ->get();

        foreach($lost as $lostKey => $lostValue){
            $lost[$lostKey]['datecreated'] = date('d/m/Y', strtotime($lostValue['datecreated']));
            $lost[$lostKey]['photo'] = asset('storage/'.$lostValue['photo']);
        }

        foreach($recovered as $recoveredKey => $recoveredValue){
            $recovered[$recoveredKey]['datecreated'] = date('d/m/Y', strtotime($recoveredValue['datecreated']));
            $recovered[$recoveredKey]['photo'] = asset('storage/'.$recoveredValue['photo']);
        }

        $array['lost'] = $lost;
        $array['recovered'] = $recovered;


        return $array;
    }
}
