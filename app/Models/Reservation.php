<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'room_type',
        'room_name',
        'price_per_night',
        'check_in_date',
        'check_out_date',
        'total_nights',
        'total_amount',
        'status',
        'special_requests',
        'guest_name',
        'guest_email',
        'guest_phone',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'price_per_night' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relación con el usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Calcular número de noches automáticamente
    public function calculateTotalNights(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    // Calcular monto total automáticamente
    public function calculateTotalAmount(): float
    {
        return $this->price_per_night * $this->total_nights;
    }

    // Boot method para calcular automáticamente
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($reservation) {
            $reservation->total_nights = $reservation->calculateTotalNights();
            $reservation->total_amount = $reservation->calculateTotalAmount();
        });
    }

    // Scope para reservas activas
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    // Scope para reservas por usuario
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
