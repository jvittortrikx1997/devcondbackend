<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitPeople;
use App\Models\UnitPet;
use App\Models\UnitVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function getInfo($id){
        $array = ['error' => ''];
        $user = auth()->user();

        $unit = Unit::find($id);
        if($unit){
            $people = UnitPeople::where('id_unit', $id)->get();
            $vehicles = UnitVehicle::where('id_unit', $id)->get();
            $pets = UnitPet::where('id_unit', $id)->get();

            foreach($people as $peopleKey => $peopleValue){
                $people[$peopleKey]['birthdate'] = date('d/m/Y', strtotime($peopleValue['birthdate']));
            }
            $array['people'] = $people;
            $array['vehicles'] = $vehicles;
            $array['pets'] = $pets;
        }else{
            $array['error'] = 'Propriedade inexistente';
        }
        return $array;
    }

    public function addPerson($id, Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'birthdate' => 'required|date'
        ]);

        if(!$validator->fails()){
            $name = $request->input('name');
            $birthdate = $request->input('birthdate');

            $person = New UnitPeople();
            $person->id_unit = $id;
            $person->name = $name;
            $person->birthdate = $birthdate;
            $person->save();
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
    }

    public function addVehicle($id, Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'color' => 'required',
            'plate' => 'required'
        ]);

        if(!$validator->fails()){
            $title = $request->input('title');
            $color = $request->input('color');
            $plate = $request->input('plate');

            $vehicle = New UnitVehicle();
            $vehicle->id_unit = $$id;
            $vehicle->title = $title;
            $vehicle->color = $color;
            $vehicle->plate = $plate;
            $vehicle->save();
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
    }

    public function addPet($id, Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'race' => 'required'
        ]);

        if(!$validator->fails()){
            $name = $request->input('name');
            $race = $request->input('race');

            $pet = New UnitPet();
            $pet->id_unit = $id;
            $pet->name = $name;
            $pet->race = $race;
            $pet->save();
        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
    }

    public function removePerson($id, Request $request){
        $array = ['error' => ''];

        $idItem = $request->input('id');

        if($idItem ){
            UnitPeople::where('ID', $idItem)
            ->where('id_unit', $id)
            ->delete();
        }else{
            $array['error'] = 'A pessoa informada não existe.';
        }
        return $array;
    }

    public function removeVeiculo($id, Request $request){
        $array = ['error' => ''];

        $idItem = $request->input('id');

        if($idItem ){
            UnitVehicle::where('ID', $idItem)
            ->where('id_unit', $id)
            ->delete();
        }else{
            $array['error'] = 'A pessoa informada não existe.';
        }
        return $array;
    }

    public function removePet($id, Request $request){
        $array = ['error' => ''];

        $idItem = $request->input('id');

        if($idItem ){
            UnitPet::where('ID', $idItem)
            ->where('id_unit', $id)
            ->delete();
        }else{
            $array['error'] = 'A pessoa informada não existe.';
        }
        return $array;
    }
}
