<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $table = 'user_tokens';
    protected $fillable = ['user_id', 'token'];
    public $timestamps = true;

    public function user() {
        return $this->belongsTo(User::class);
    }
}