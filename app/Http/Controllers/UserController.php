<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Http\Requests;


use Illuminate\Support\Facades\DB; // funcionalidad de base de datos
use App\User; // modelo de ususario 

class UserController extends Controller
{
    //registro de usuarios

    public function register(Request $request){

     //recoger variables por post

         $json = $request->input('json',null);
         $params = json_decode($json);

         $email=(!is_null($json) && isset($params->email)) ? $params->email : null;
         $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
         $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
         $role = 'ROLE_USER';
         $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

   if(!is_null($email)&&!is_null($password)&&!is_null($name)){
    
    //creamos el usuario
    $user= new User();

    $user->email = $email;
    $user->password = $password;
    $user->name = $name;
    $user->surname = $surname;
    $user->role = $role;

    //contraseña cifrada

    $pwd =hash('sha256',$password);

    $user->password = $pwd;

     //compuebo si el usuario esta en la base de datos
    $isset_user = User::where('email', '=' ,$email )->first();

    if(!$isset_user){

        //guardar usuario

        $user->save();

        $data = array(
            'status'=>'success',
            'code'=>200,
            'message'=>'Usuario registrado correctamente'
         
             );
    }else{
            //no guardar usuario por que ya existe
            $data = array(
                'status'=>'error',
                'code'=>400,
                'message'=>'Usuario duplicado, no puede registrarse'
             
                 );

    }

   }else{

    $data = array(
   'status'=>'error',
   'code'=>400,
   'message'=>'Usuario no creado'

    );
   }

     return response()->json($data,200);

    }

    //login

    public function login(Request $request){

        $jwtAuth = new JwtAuth();

        //recibir post

        $json = $request->input('json',null);
        $params =json_decode($json);

        $email= (!is_null($json)&& isset($params->email)) ? $params->email : null;

        $password = (!is_null($json)&& isset($params->password)) ? $params->password : null;

        $getToken = (!is_null($json)&& isset($params->gettoken)) ? $params->gettoken : null;

       //Cifrar la password

       $pwd = hash('sha256',$password);

       if(!is_null($email) && !is_null($password)&&($getToken == null || $getToken == 'false')){
             //logueamos al usuario
                 $signup = $jwtAuth->signup($email,$pwd);// si le paso true decodifica el objeto

                 
       }elseif($getToken != null){
    //var_dump($getToken);die();
        $signup = $jwtAuth->signup($email, $pwd, $getToken);

       
       }else{

        $signup = array(
            'status'=> 'error',
            'message' =>'Envia tus datos por post'
           );
       }
       return response()->json($signup, 200);
    }
}
