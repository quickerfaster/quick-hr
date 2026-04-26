<?php

namespace App\Modules\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Modules\Hr\Models\Holiday;
use App\Modules\Hr\Models\Department;
use App\Modules\Hr\Models\Location;

use Illuminate\Database\Eloquent\Model;


class HolidayCalendar extends Model 
{
    use HasFactory;
    
    

    

    protected $table = 'holiday_calendars';
    
    
    
    
    

    protected $fillable = [
        'name', 'country_code', 'region', 'is_default', 'is_active', 'description', 'applicable_to', 'year', 'holiday_count', 'last_updated'
    ];

    protected $guarded = [
        
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'holiday_count' => 'integer',
        'last_updated' => 'datetime'
    ];

    protected $attributes = [
        'is_default' => false,
        'is_active' => true,
        'applicable_to' => 'all_employees',
        'year' => 2026,
        'holiday_count' => 0
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

    public function holidays()
    {
        return $this->hasMany(\App\Modules\Hr\Models\Holiday::class, 'calendar_id', 'id');
    }

    public function departments()
    {
        return $this->belongsToMany(\App\Modules\Hr\Models\Department::class, 'department_holiday_calendar', 'holiday_calendar_id', 'department_id', 'id', 'id');
    }

    public function locations()
    {
        return $this->belongsToMany(\App\Modules\Hr\Models\Location::class, 'holiday_calendar_location', 'holiday_calendar_id', 'location_id', 'id', 'id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \App\Modules\Hr\Database\Factories\HolidayCalendarFactory::new();
    }
}