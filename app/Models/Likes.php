<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    protected $table = 'likes';
    protected $fillable = [
        'reactId',
        'senderId',
        'model_id',
        'model_type'
    ];

}