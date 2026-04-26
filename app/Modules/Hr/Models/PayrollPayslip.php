<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\PayrollRun;
use App\Modules\Hr\Models\Employee;
use App\Modules\Hr\Models\PayslipItem;

use Illuminate\Database\Eloquent\Model;


class PayrollPayslip extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'payroll_payslips';
    
    
    
    public $timestamps = true;
    

    protected $fillable = [
        'payslip_number', 'payroll_run_id', 'employee_id', 'base_salary', 'gross_pay', 'total_deductions', 'total_taxes', 'total_benefit_deductions', 'net_pay', 'payment_status', 'paid_at', 'payment_reference', 'bank_account_snapshot', 'notes'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_taxes' => 'decimal:2',
        'total_benefit_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'paid_at' => 'datetime',
        'bank_account_snapshot' => 'array',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    protected $attributes = [
        'total_deductions' => 0,
        'total_taxes' => 0,
        'total_benefit_deductions' => 0,
        'payment_status' => 'pending'
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

    public function payrollRun()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\PayrollRun::class, 'payroll_run_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\Employee::class, 'employee_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(\App\Modules\Hr\Models\PayslipItem::class, 'payslip_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\PayrollPayslipFactory::new();
    }
}