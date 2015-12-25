<?php

namespace Flocc;

use Illuminate\Database\Eloquent\Model;

class PlaceCategory extends Model
{
    Schema::create('place_categories', function (Blueprint $table) {
        $table->increments('id');
        $table->string('category');
    });
}
