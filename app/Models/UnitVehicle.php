<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitVehicle extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $table = 'unitvehicles';
}
