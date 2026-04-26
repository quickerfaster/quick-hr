<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


use Illuminate\Database\Eloquent\Model;


class ActivityLog extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'activity_logs';
    
    
    
    public $timestamps = false;
    

    protected $fillable = [
        'causer_type', 'causer_id', 'subject_type', 'subject_id', 'log_name', 'action', 'description', 'old_values', 'new_values', 'properties', 'created_at', 'updated_at'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'causer_id' => 'integer',
        'subject_id' => 'integer',
        'old_values' => 'array',
        'new_values' => 'array',
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        
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

    public function causer()
    {
        return $this->morphTo('causer', 'causer_type', 'causer_id', 'id');
    }

    public function subject()
    {
        return $this->morphTo('subject', 'subject_type', 'subject_id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Admin\Database\Factories\ActivityLogFactory::new();
    }
}