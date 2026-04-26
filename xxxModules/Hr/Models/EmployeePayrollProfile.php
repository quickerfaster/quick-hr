<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\Employee;
use App\Modules\Hr\Models\PaySchedule;

use Illuminate\Database\Eloquent\Model;


class EmployeePayrollProfile extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'employee_payroll_profiles';
    
    
    
    
    

    protected $fillable = [
        'employee_id', 'pay_schedule_id', 'bank_account', 'bank_routing', 'tax_filing_status', 'allowances', 'is_exempt_from_federal_tax', 'currency'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'bank_account' => 'encrypted',
        'bank_routing' => 'encrypted',
        'allowances' => 'integer',
        'is_exempt_from_federal_tax' => 'boolean'
    ];

    protected $attributes = [
        'allowances' => 0,
        'is_exempt_from_federal_tax' => false,
        'currency' => 'USD'
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

    public function paySchedule()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\PaySchedule::class, 'pay_schedule_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\EmployeePayrollProfileFactory::new();
    }
}