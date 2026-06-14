<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    protected $table = 'specializations';

    protected $fillable = [
        'name'
    ];

    public function doctors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(DoctorProfile::class, 'doctor_specializations', 'specialization_id', 'doctor_id');
    }
}
