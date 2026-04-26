<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\PaySchedule;
use App\Modules\Hr\Models\PayrollPayslip;
use App\Modules\Hr\Models\PayrollRunAdjustment;

use Illuminate\Database\Eloquent\Model;


class PayrollRun extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'payroll_runs';
    
    
    
    public $timestamps = true;
    

    protected $fillable = [
        'pay_schedule_id', 'period_start', 'period_end', 'status', 'current_step', 'total_gross_pay', 'total_deductions', 'total_taxes', 'total_employer_contributions', 'total_cash_required', 'total_employees', 'processed_employees', 'calculation_status', 'processed_by', 'processed_at', 'approved_by', 'approved_at', 'notes'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'current_step' => 'integer',
        'total_gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_taxes' => 'decimal:2',
        'total_employer_contributions' => 'decimal:2',
        'total_cash_required' => 'decimal:2',
        'total_employees' => 'integer',
        'processed_employees' => 'integer',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    protected $attributes = [
        'status' => 'draft',
        'current_step' => 1,
        'total_gross_pay' => 0,
        'total_deductions' => 0,
        'total_taxes' => 0,
        'total_employer_contributions' => 0,
        'total_cash_required' => 0,
        'total_employees' => 0,
        'processed_employees' => 0,
        'calculation_status' => 'pending'
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

    public function paySchedule()
    {
        return $this->belongsTo(\App\Modules\Hr\Models\PaySchedule::class, 'pay_schedule_id', 'id');
    }

    public function payslips()
    {
        return $this->hasMany(\App\Modules\Hr\Models\PayrollPayslip::class, 'payroll_run_id', 'id');
    }

    public function adjustments()
    {
        return $this->hasMany(\App\Modules\Hr\Models\PayrollRunAdjustment::class, 'payroll_run_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\PayrollRunFactory::new();
    }
}