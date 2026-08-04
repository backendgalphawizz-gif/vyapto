<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\StorageAssets;
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
        'office_id',
        'hub_id',
        'job_type',
        'designation_id',
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

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'id');
    }

    public function profileImageUrl(): string
    {
        $fallback = asset('assets/admin/images/no-image.png');

        return StorageAssets::url($this->profile_image, $fallback) ?? $fallback;
    }
}
