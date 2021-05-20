<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Helpers\JwtAuh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //Recoger post
        $json = $request->all();

        //$request = json_decode($json);

        //return !is_null($request->all());

        $email = (!is_null($json) && isset($request->email)) ? $request->email : null;
        $name = (!is_null($json) && isset($request->name)) ? $request->name : null;
        $username = (!is_null($json) && isset($request->username)) ? $request->username : null;
        $role = "ROLE_USER";
        $password = (!is_null($json) && isset($request->password)) ? $request->password : null;

        if(!is_null($email) && !is_null($password) && !is_null($name)) {
            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->username = $username;
            $user->role = $role;

            $pwd = hash('sha256', $password);
            $user->password = $pwd;

            // Comprobar usuario duplicado
            $isset_user = User::where('email', $email)->get();

            if(count($isset_user) == 0) {
                // Guardar el usuario
                $user->save();

                $data = array(
                    "status" =>"success",
                    "code" => 200,
                    "message" => "Usuario registrado correctamente"
                );
            } else {
                // No Guardarlo porque ya existe

                $data = array(
                    "status" =>"error",
                    "code" => 400,
                    "message" => "Usuario duplicado no se puede registrar"
                );
            }

        } else {
            $data = array(
                "status" =>"error",
                "code" => 400,
                "message" => "Usuario no encontrado"
            );
        }

        return response()->json($data, 200);

    }

    public function login(Request $request)
    {
        $jwtAuh = new JwtAuh();

        // Recibir post
        $json = $request->all();

        //$params = json_decode($json);

        $email = (!is_null($json) && isset($request->email)) ? $request->email : null;
        $password = (!is_null($json) && isset($request->password)) ? $request->password : null;
        $getToken = (!is_null($json) && isset($request->getToken)) ? $request->getToken : null;

        // Cifrar la password
        $pwd = hash('sha256', $password);

        if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){
            $singup = $jwtAuh->singup($email, $pwd);
        } else if ($getToken != null) {
            $singup = $jwtAuh->singup($email, $pwd, $getToken);


        } else {
            $singup = array(
                'status' => "error",
                'message' => "Envia tus datos por post"
            );
        }

        return response()->json($singup, 200);

    }
}



