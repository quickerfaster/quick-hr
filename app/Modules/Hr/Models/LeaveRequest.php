<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\Employee;
use App\Modules\Hr\Models\LeaveType;
use App\Modules\Hr\Models\Attendance;

use Illuminate\Database\Eloquent\Model;


class LeaveRequest extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'leave_requests';
    
    
    
    
    

    protected $fillable = [
        'employee_id', 'leave_type_id', 'start_date', 'end_date', 'reason', 'status', 'approved_by', 'approved_at', 'denial_reason', 'attendance_synced', 'attendance_records_count', 'last_sync_at', 'is_retroactive', 'reported_after_absence', 'workdays_count', 'overlap_with_holiday'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'attendance_synced' => 'boolean',
        'attendance_records_count' => 'integer',
        'last_sync_at' => 'datetime',
        'is_retroactive' => 'boolean',
        'reported_after_absence' => 'boolean',
        'workdays_count' => 'integer',
        'overlap_with_holiday' => 'boolean'
    ];

    protected $attributes = [
        'status' => 'Pending',
        'attendance_synced' => false,
        'attendance_records_count' => 0,
        'is_retroactive' => false,
        'reported_after_absence' => false,
        'overlap_with_holiday' => false
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

    public function leaveType()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\LeaveType::class, 'leave_type_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Employee::class, 'approved_by', 'id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(\App\Modules\Hr\Models\Attendance::class, 'leave_request_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\LeaveRequestFactory::new();
    }
}