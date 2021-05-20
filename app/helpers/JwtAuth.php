<?php

namespace APP\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Firebase\JWT\JWT;

class JwtAuh {
    public $key;

    public function __construct()
    {
        $this->key = 'esta-es-mi-clave-api-123456789012345';
    }

    public function singup($email, $password, $getToken = null)
    {
        $user = User::where(
            array(
                'email' => $email,
                "password" => $password
            )
        )->first();

        $singup = false;
        if(is_object($user)) {
            $singup = true;
        }

        if($singup) {
            //  Generar el token y devolverlo
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'username' => $user->username,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));

            if(is_null($getToken)) {
                return $jwt;
            } else {
                return $decoded;
            }

        } else {
            //  Devolver un error
            return array(
                'status' => 'error',
                'message' => 'Login ha fallado !!'
            );
        }
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;

        try {
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
        } catch (\UnexpectedValueException $e) {
           $auth = false;
        } catch(\DomainException $e) {
            $auth = false;
        }

        if(isset($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if($getIdentity) {
            return $decoded;
        }

        return $auth;
    }
}
