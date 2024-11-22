<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;
    protected $fillable = ['country_id',    'description',    'pages',    'line_date',    'c',    'created_at',    'updated_at'];
}
