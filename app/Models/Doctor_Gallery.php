<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorGallery extends Model
{
    protected $table = 'doctor_gallery';

    protected $fillable = [
        'doctor_id',
        'image_url'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_id');
    }
}
