<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Admin\Models\Location;
use App\Modules\Admin\Models\Company;

use Illuminate\Database\Eloquent\Model;


class Company extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'companies';
    
    
    
    
    

    protected $fillable = [
        'name', 'parent_company_id', 'location_id', 'subdomain', 'status', 'billing_email', 'billing_address_line_1', 'billing_address_line_2', 'billing_city', 'billing_state_province', 'billing_postal_code', 'billing_country_code', 'timezone', 'currency_code', 'level', 'is_placeholder'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'is_placeholder' => 'boolean'
    ];

    protected $attributes = [
        'status' => 'pending',
        'level' => 'division',
        'is_placeholder' => true
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

    public function location()
    {
        return $this->belongsTo(\App\Modules\Admin\Models\Location::class, 'location_id', 'id');
    }

    public function parentCompany()
    {
        return $this->belongsTo(\App\Modules\Admin\Models\Company::class, 'parent_company_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Admin\Database\Factories\CompanyFactory::new();
    }
}