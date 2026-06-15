<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    protected $table = 'site_contents';

    protected $fillable = [
        'key',
        'value'
    ];
}
