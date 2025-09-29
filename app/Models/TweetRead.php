<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TweetRead extends Model
{
    /** @use HasFactory<\Database\Factories\TweetReadFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'tweet_id', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tweet()
    {
        return $this->belongsTo(Tweet::class);
    }

}
