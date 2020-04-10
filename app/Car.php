<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
 protected $table ='cars';

 //relacion entre entidades se van a listar todos los datos de quien creo el coche

 public function user (){

    return $this->belongsTo('App\User','user_id');
 }
}
