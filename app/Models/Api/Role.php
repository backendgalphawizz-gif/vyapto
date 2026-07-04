<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';   // database table name

    protected $fillable = [
        'id',
        'name',
    ];

    // Relationship with User (optional)
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}