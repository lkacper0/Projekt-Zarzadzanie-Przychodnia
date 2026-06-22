<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'slot_id',
        'patient_id',
        'service_id',
        'status',
        'medical_note'
    ];

    public function slot(): BelongsTo
    {
        return $this->belongsTo(AvailabilitySlot::class, 'slot_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'appointment_id');
    }
}
