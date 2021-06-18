<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $guarded = [];


    public function users()
    {
        return $this->belongsToMany(User::class,'conversation_user','conversation_id','user_id');
    }
}
