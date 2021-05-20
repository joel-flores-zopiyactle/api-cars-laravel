<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuh;
use App\Models\Car;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function index(Request $request)
    {

        $hash = $request->header('Authorization', null);
        $jwtAuh = new JwtAuh();

        $checkToken = $jwtAuh->checkToken($hash);

        if($checkToken) {
            $cars = Car::all();

            return response()->json(array(
                'cars' => $cars,
                'status' => 'success'
            ), 200);
        } else {
            echo "No autenticado";
        }


    }

    public function show($id)
    {
        $car = Car::find($id);

        return response()->json(array(
            'cars' => $car,
            'status' => 'success'
        ), 200);
    }

    public function store(Request $request)
    {
        $hash = $request->header('Authorization', null);

        $jwtAuh = new JwtAuh();
        $checkToken = $jwtAuh->checkToken($hash);

        if($checkToken) {
            //$json = $request->input('json', null);
            //$request = $request->all();
            //$request->all() = json_decode($json, true);

            //  conseguir el usuario identificado
            $user = $jwtAuh->checkToken($hash, true);

            //$request->merge($request->all());

            $validated = Validator::make($request->all(),[
                'title' => 'required|min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);

            if($validated->fails()){
                return response()->json($validated, 200);
                die();
            }

            //  Guardar el coche
            $car = new Car();
            $car->user_id = $user->sub;
            $car->title = $request->title;
            $car->description = $request->description;
            $car->price = $request->price;
            $car->status = $request->status;

            $car->save();

            $data = array(
                'message' => $car,
                'status' => "success",
                'code' => 200
            );
        } else {
            //throw Devolver error
            $data = array(
                'message' =>"Login incorrecto" ,
                'status' => "success",
                'code' => 400
            );
        }

        return response()->json($data, 200);
    }

    public function update(Request $request, $id)
    {
        $hash = $request->header('Authorization', null);
        $jwtAuh = new JwtAuh();

        $checkToken = $jwtAuh->checkToken($hash);

        if($checkToken) {

            $validated = Validator::make($request->all(),[
                'title' => 'required|min:5',
                'description' => 'required',
                'price' => 'required',
                'status' => 'required'
            ]);

            if($validated->fails()){
                return response()->json($validated, 200);
                die();
            }

            //  Guardar el coche
            /* $car = new Car();
            $car->title = $request->title;
            $car->description = $request->description;
            $car->price = $request->price;
            $car->status = $request->status; */

            $car = Car::where('id', $id)->update($request->all());

            //$car->save();

            $data = array(
                'message' => $car,
                'status' => "success",
                'code' => 200
            );

            return response()->json($data, 200);

        } else {
            echo "No autenticado";
        }

    }

    public function destroy(Request $request, $id)
    {
        $hash = $request->header('Authorization', null);
        $jwtAuh = new JwtAuh();

        $checkToken = $jwtAuh->checkToken($hash);

        if($checkToken) {
            $car = Car::find($id);
            $car->delete();

            $data = array(
                'message' => "Data eliminado",
                "data" => $car,
                'status' => "success",
                'code' => 200
            );
        }else {
            $data = array(
                'message' => "Fallo al elimiar el data",
                'status' => "success",
                'code' => 400
            );
        }

        return response()->json($data, 200);
    }
}
