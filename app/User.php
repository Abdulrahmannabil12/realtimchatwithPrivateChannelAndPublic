<?php

namespace App;

use App\Models\Conversation;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'photo', 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    public function scopeSelection($query)
    {
        return $query->select('name', 'mobile', 'photo', 'active', 'created_at', 'updated_at');
    }

    function senderChat()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    function receiverChat()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function conversation()
    {
        return $this->belongsToMany(Conversation::class,'conversation_user','user_id','conversation_id');
    }
    public function groups()
    {
        return $this->belongsToMany(
            Group::class,
            'user_group',
            'user_id',
            'group_id');

    }

    public function receiver()
    {
        //return $this->belongsToMany(RelatedModel, pivot_table_name, foreign_key_of_current_model_in_pivot_table, foreign_key_of_other_model_in_pivot_table);
        return $this->hasMany(
            Message::class,
            'receiver_id');

    }

    public function sender()
    {
        //return $this->belongsToMany(RelatedModel, pivot_table_name, foreign_key_of_current_model_in_pivot_table, foreign_key_of_other_model_in_pivot_table);
        return $this->hasMany(
            Message::class,
            'sender_id');

    }

}
