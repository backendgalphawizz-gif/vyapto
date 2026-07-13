<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
        'profile_image',
        'role_id',
        'department_id',
        'job_type',
        'password',
        'email_verified_at',
        'status_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(): bool
    {
        // Primary admin account (production dump had role_id NULL)
        if ((int) $this->id === 1 || strcasecmp((string) $this->email, 'admin@gmail.com') === 0) {
            return true;
        }

        $roleId = (int) ($this->attributes['role_id'] ?? 0);

        if (in_array($roleId, [1, 2], true)) {
            return true;
        }

        $roleName = strtolower(trim((string) ($this->role?->name ?? '')));
        if ($roleName !== '' && (str_contains($roleName, 'admin') || $roleName === 'hr')) {
            return true;
        }

        try {
            return $this->hasAnyRole(['Admin', 'HR Admin']);
        } catch (Throwable $e) {
            return false;
        }
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'id');
    }

    public function profileImageUrl(): string
    {
        $fallback = asset('assets/admin/images/no-image.png');

        if (empty($this->profile_image)) {
            return $fallback;
        }

        if (str_starts_with($this->profile_image, 'http')) {
            return $this->profile_image;
        }

        $path = ltrim($this->profile_image, '/');

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        if (str_starts_with($path, 'storage/')) {
            $storageRelative = substr($path, strlen('storage/'));
            if (file_exists(storage_path('app/public/'.$storageRelative))) {
                return asset($path);
            }
        }

        return $fallback;
    }
}
