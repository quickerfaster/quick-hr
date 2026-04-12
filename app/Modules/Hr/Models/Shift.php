<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\ShiftSchedule;
use App\Modules\Hr\Models\Attendance;
use App\Modules\Hr\Models\Shift;

use Illuminate\Database\Eloquent\Model;


class Shift extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'shifts';
    
    
    
    
    

    protected $fillable = [
        'name', 'code', 'start_time', 'end_time', 'duration_hours', 'is_overnight', 'description', 'is_active', 'is_default', 'shift_category', 'created_from_template_id', 'last_used_date', 'usage_count'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'duration_hours' => 'decimal:2',
        'is_overnight' => 'boolean',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'created_from_template_id' => 'integer',
        'last_used_date' => 'date',
        'usage_count' => 'integer'
    ];

    protected $attributes = [
        'is_overnight' => false,
        'is_active' => true,
        'is_default' => false,
        'shift_category' => 'regular',
        'usage_count' => 0
    ];

    protected $dispatchesEvents = [
        
    ];

    /**
     * Validation rules for the model.
     */
    protected static $rules = [
        
    ];

    /**
     * Custom validation messages.
     */
    protected static $messages = [
        
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
    }

    /**
     * Validate the model instance.
     */
    public function validate()
    {
        $validator = Validator::make($this->attributesToArray(), static::$rules, static::$messages);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return true;
    }

    /**
     * Save the model to the database with validation.
     */
    public function save(array $options = [])
    {
        $this->validate();
        return parent::save($options);
    }

    public function shiftSchedules()
    {
        return $this->hasMany(\App\Modules\Hr\Models\ShiftSchedule::class, 'shift_id', 'id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(\App\Modules\Hr\Models\Attendance::class, 'shift_id', 'id');
    }

    public function templateSource()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Shift::class, 'created_from_template_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\ShiftFactory::new();
    }
}