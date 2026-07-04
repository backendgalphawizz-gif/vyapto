<?php
// app/Models/VehicleUsage.php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class VehicleUsage extends Model
{
    use HasFactory;

    protected $table = 'vehicle_usage';

    protected $fillable = [
        'vehicle_number',
        'user_id',
        'image',
        'kms'
    ];

    protected $casts = [
        'kms' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * For each vehicle + user on the given day, create pair rows:
     * (1st,2nd), then (3rd,4th), then (5th,6th), and so on.
     * Each pair is returned as one summary row.
     */
    public static function todayFirstSecondKmSummary(?Carbon $date = null): Collection
    {
        $day = ($date ?? Carbon::today())->toDateString();

        return static::query()
            ->with('user')
            ->select(['id', 'vehicle_number', 'user_id', 'kms', 'created_at'])
            ->whereDate('created_at', $day)
            ->orderBy('vehicle_number')
            ->orderBy('user_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn (self $row) => $row->vehicle_number . "\x1e" . (string) $row->user_id)
            ->sortKeys()
            ->flatMap(function (Collection $records) {
                $sorted = $records->sortBy('created_at')->values();
                $pairs = collect();

                for ($i = 0; $i < $sorted->count(); $i += 2) {
                    /** @var self|null $first */
                    $first = $sorted->get($i);
                    /** @var self|null $second */
                    $second = $sorted->get($i + 1);

                    $startKm = $first && $first->kms !== null ? (float) $first->kms : null;
                    $secondKm = $second && $second->kms !== null ? (float) $second->kms : null;
                    $difference = ($startKm !== null && $secondKm !== null)
                        ? round($secondKm - $startKm, 2)
                        : null;

                    $pairs->push((object) [
                        'vehicle_number' => $first?->vehicle_number ?? $records->first()->vehicle_number,
                        'user_id' => $first?->user_id ?? $records->first()->user_id,
                        'user_name' => $first?->user->name ?? optional($records->first()->user)->name,
                        'start_km' => $startKm,
                        'end_km' => $secondKm,
                        'difference_km' => $difference,
                        'first_entry_at' => $first?->created_at,
                        'second_entry_at' => $second?->created_at,
                    ]);
                }

                return $pairs;
            })
            ->values();
    }
}