<?php

namespace App\Modules\System\Models;

use App\Modules\Admin\Traits\HasCompanyScope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalTierApproval extends Model
{
    use HasCompanyScope;
    protected $table = 'approval_tier_approvals';

    protected $fillable = ['company_id', 'tier_id', 'user_id', 'comments', 'approved_at'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function tier(): BelongsTo
    {
        return $this->belongsTo(ApprovalTier::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Modules\Admin\Models\Company::class, 'company_id', 'id');
    }
}
