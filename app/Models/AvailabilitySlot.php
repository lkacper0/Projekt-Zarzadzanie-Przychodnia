<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilitySlot extends Model
{
    protected $table = 'availability_slots';

    protected $fillable = [
        'doctor_id',
        'start_time',
        'end_time',
        'is_booked'
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_booked' => 'boolean',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_id');
    }

    public function appointment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Appointment::class, 'slot_id');
    }
}
