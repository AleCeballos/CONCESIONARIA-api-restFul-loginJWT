<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Helpers\JwtAuth;
use App\Car;
class CarController extends Controller
{
    //------------------------INDEX--------------------------//

    public function index (){
   
      $cars = Car::all()->load('user');
      return response()->json(array(

           'cars'=>$cars,
           'status'=>'success'

      ),200);



   //Esto era para comprobar que redirigia al index parametros quitados Request $request
        //     $hash = $request->header('Authorization',null);
    //    $jwtAuth = new JwtAuth();
    //    $checkToken = $jwtAuth->checkToken($hash);
         
    //    if($checkToken){
    //     echo "Index de car controller AUTENTICADO"; die();
    //    }else{

    //     echo "NO AUTENTICADO -> Index de car controller";die();
    //    }
      
    }
//------------------------SHOW--------------------------//
public function show($id){
    $car = Car::find($id)->load('user');
    return response()->json(array('car'=>$car,'status'=>'success'),200);


}




//------------------------STORE--------------------------//
       public function store(Request $request){
        $hash = $request->header('Authorization',null);
       
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);
          
        if($checkToken){
        //agarrar los datos por post
        $json =$request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json,true);

        
        //conseguir el usuario identificado
        $user= $jwtAuth->checkToken($hash, true);
        
        //Validacion 
     
            $validate = \Validator::make($params_array,[

                'title'=>'required',
                'description'=>'required',
                'price'=>'required',
                'status'=>'required'
        
                ]); 

                if($validate->fails()){

                    return response()->json($validate->errors(),400);
                }
        
    
        
        //guardar el coche
        $car = new Car();
        $car->user_id = $user->sub;
        $car->title = $params->title;
        $car->description = $params->description;
        $car->price = $params->price ;
        $car->status = $params->status;

        $car->save();

       
        $data = array(

            'car'=>$car,
            'status'=>'success',
            'code'=>200,
        );


        }else{
 
         //devolver error
         $data = array(

            'message'=>'Login incorrecto',
            'status'=>'error',
            'code'=>300,
        );
        }

        return response()->json($data, 200);

    }


  

}
