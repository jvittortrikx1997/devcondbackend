<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doc;

class DocController extends Controller
{
    public function getAll(){
        $array = ['error' => ''];

        $user = auth()->user();

        $docs = Doc::all();

        $array['list'] = $docs;
        foreach($docs as $docKey => $docValue ){
            $docs[$docKey]['fileurl'] = asset('storage/'.$docValue['fileurl']);
        }

        return $array;
    }
}
