<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotData extends Model
{
    protected $fillable=[
        'user_id',
        'notifbot_code',
        'bale_notifbot_id',
        'telegram_notifbot_id',
    ];
}
