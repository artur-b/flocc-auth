<?php

namespace Flocc;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    Schema::create('countries', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
    });
}
}
