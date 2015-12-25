<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    Schema::create('regions', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
    });
}
