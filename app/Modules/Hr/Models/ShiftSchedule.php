<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\Employee;
use App\Modules\Hr\Models\Shift;
use App\Modules\Hr\Models\Attendance;

use Illuminate\Database\Eloquent\Model;


class ShiftSchedule extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'shift_schedules';
    
    
    
    
    

    protected $fillable = [
        'employee_id', 'shift_id', 'schedule_date', 'status', 'start_time_override', 'end_time_override', 'actual_start_time', 'actual_end_time', 'notes', 'approved_by', 'approved_at', 'is_cover_required', 'cover_employee_id', 'attendance_id', 'schedule_type', 'is_published', 'published_at', 'created_from_template', 'last_modified_by', 'last_modified_at'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'approved_at' => 'datetime',
        'is_cover_required' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'created_from_template' => 'boolean',
        'last_modified_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'scheduled',
        'is_cover_required' => false,
        'schedule_type' => 'regular',
        'is_published' => true,
        'created_from_template' => false
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

    public function employee()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Employee::class, 'employee_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Shift::class, 'shift_id', 'id');
    }

    public function coverEmployee()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Employee::class, 'cover_employee_id', 'id');
    }

    public function attendance()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Attendance::class, 'attendance_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\ShiftScheduleFactory::new();
    }
}