<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\Employee;
use App\Modules\Admin\Models\JobTitle;
use App\Modules\Admin\Models\Department;
use App\Modules\Admin\Models\Location;
use App\Modules\Hr\Models\Shift;
use App\Modules\Hr\Models\EmployeeWorkPattern;
use App\Modules\Hr\Models\AttendancePolicy;

use Illuminate\Database\Eloquent\Model;


class EmployeePosition extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'employee_positions';
    
    
    
    
    

    protected $fillable = [
        'employee_id', 'job_title_id', 'department_id', 'manager_id', 'pay_type', 'hourly_rate', 'shift_id', 'employment_status', 'location_id', 'start_date', 'end_date', 'base_salary', 'salary_currency', 'pay_frequency', 'attendance_policy_id', 'cost_center', 'work_email', 'work_phone_extension', 'reports_to', 'job_description', 'is_primary'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'base_salary' => 'decimal:2',
        'is_primary' => 'boolean'
    ];

    protected $attributes = [
        'hourly_rate' => 0,
        'employment_status' => 'Active',
        'base_salary' => 0,
        'salary_currency' => 'USD'
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

    public function jobTitle()
    {
        return $this->belongsTo(\App\Modules\Admin\Models\JobTitle::class, 'job_title_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Modules\Admin\Models\Department::class, 'department_id', 'id');
    }

    public function manager()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Employee::class, 'manager_id', 'id');
    }

    public function reportsTo()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Employee::class, 'reports_to', 'id');
    }

    public function location()
    {
        return $this->belongsTo(\App\Modules\Admin\Models\Location::class, 'location_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Shift::class, 'shift_id', 'id');
    }

    public function employeeWorkPatterns()
    {
        return $this->hasMany(\App\Modules\Hr\Models\EmployeeWorkPattern::class, 'employee_id', 'id');
    }

    public function attendancePolicy()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\AttendancePolicy::class, 'attendance_policy_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\EmployeePositionFactory::new();
    }
}