<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    Schema::create('places', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->integer('country_id');
        $table->integer('region_id'); /* possible n:m relation, f.e. Tatry, Karpaty */
        $table->integer('category_id');
    });
    
}
