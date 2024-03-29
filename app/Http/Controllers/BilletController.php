<?php

namespace App\Http\Controllers;

use App\Models\Billet;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BilletController extends Controller
{
    public function getAll(Request $request){
        $array = ['error' => ''];

        $user = auth()->user();

        $property = $request->input('property');
        $unit = Unit::where('id', $property)->where('id_owner', $user['id'])->count();

        if($property){
            if($unit > 0){
                $billets = Billet::where('id_unit', $property)->get();

                foreach($billets as $billetKey => $billetValue){
                    $billets[$billetKey]['fileurl'] = asset('storage/'.$billetValue['fileurl']);
                }
                $array['list'] = $billets;
            }else{
                $array['error'] = 'A unidade informada não é sua.';
            }
        }else{
            $array['error'] = 'A propriedade é obrigatória.';
        }
        return $array;
    }
}
