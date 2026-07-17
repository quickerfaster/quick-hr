<?php

namespace App\Modules\System\Models;

use Illuminate\Database\Eloquent\Model;
use QuickerFaster\UILibrary\Traits\HasSettings;

class System extends Model
{
    use HasSettings;

    protected $table = 'systems';
    protected $fillable = ['id'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (static::count() > 0) {
                return false;
            }
        });
    }
}
