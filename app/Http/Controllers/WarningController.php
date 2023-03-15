<?php

namespace App\Http\Controllers;

use App\Models\Warning;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WarningController extends Controller
{
    public function getMyWarnings(Request $request){
        $array = ['error' => ''];

        $property = $request->input('property');
        $user = auth()->user();

        if($property){
            $unit = Unit::where('id', $property)->where('id_owner', $user['id'])->count();
            if($unit > 0){
                $warnings = Warning::where('id_unit', $property)
                ->orderBy('datecreated', 'DESC')
                ->orderBy('id', 'DESC')
                ->get();

                foreach($warnings as $warningKey => $warningValue){
                    $warnings[$warningKey]['datecreated'] = date('d/m/Y', strtotime($warningValue['datecreated']));
                    $photoList = [];
                    $photos = explode(',', $warningValue['photos']);
                    foreach($photos as $photo){
                        if(!empty($photo)){
                            $photoList[] = asset('storage/'.$photo);
                        }
                    }
                    $warnings[$warningKey]['photos'] = $photoList;
                }
                $array['list'] = $warnings;
            }else{
                $array['error'] = 'A unidade informada não é sua';
            }
        }else{
            $array['error'] = 'Você precisa informar a propriedade';
        }
        return $array;
    }

    public function addWarningFile(Request $request){
        $array = ['error' => ''];
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'photo' => 'required|file|mimes:jpg,png'
        ]);

        if(!$validator->fails()){
            $file = $request->file('photo')->store('public');

            $array['photo'] = asset(Storage::url($file));
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function setWarning(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'property' => 'required'
        ]);

        if(!$validator->fails()){
            $title = $request->input('title');
            $property = $request->input('property');
            $list = $request->input('list');

            $newWarning = new Warning();
            $newWarning->id_unit = $property;
            $newWarning->title = $title;
            $newWarning->status = 'IN_REVIEW';
            $newWarning->datecreated = date('Y-m-d');

            if($list && is_array($list)){
                $photos = [];
                foreach($list as $listItem){
                    $url = explode('/', $listItem);
                    $photos[] = end($url);
                }
                $newWarning->photos = implode(',', $photos);
            }else{
                $newWarning->photos = '';
            }
            $newWarning->save();
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
    }
}
