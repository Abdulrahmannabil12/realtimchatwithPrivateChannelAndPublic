<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'body',
        'sender_id',
        'receiver_id',
        'group_id',
        'message_type',
        'created_at',
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    function sender(){
        return $this->belongsTo(User::class, 'sender_id');
    }

    function receiver(){
        return $this->belongsTo(User::class, 'receiver_id');
    }


    public function group(){

        return $this->belongsToMany(Group::class, 'group_id', 'id');

    }
    public function groups()
    {
        //return $this->belongsToMany(RelatedModel, pivot_table_name, foreign_key_of_current_model_in_pivot_table, foreign_key_of_other_model_in_pivot_table);
        return $this->belongsToMany(
            Group::class,
            'message_group',
            'message_id',
            'group_id');

    }
}
