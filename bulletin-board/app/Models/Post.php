<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
      'title',
      'description',
      'status',
      'created_user_id',
      'updated_user_id',
      'deleted_user_id',
    ];
  
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
