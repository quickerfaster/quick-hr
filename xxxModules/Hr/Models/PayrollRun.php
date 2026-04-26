<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\PaySchedule;
use App\Modules\Hr\Models\PayrollPayslip;

use Illuminate\Database\Eloquent\Model;


class PayrollRun extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'payroll_runs';
    
    
    
    
    

    protected $fillable = [
        'pay_schedule_id', 'period_start', 'period_end', 'status', 'processed_by', 'processed_at', 'notes'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'processed_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'Draft'
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

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\PayrollRunFactory::new();
    }
}