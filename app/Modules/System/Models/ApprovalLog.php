<?php

namespace App\Modules\System\Models;

use App\Modules\Admin\Traits\HasCompanyScope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalLog extends Model
{
    use HasCompanyScope;
    protected $table = 'approval_logs';

    protected $fillable = [
        'company_id', 'approval_request_id', 'user_id', 'action', 'tier_id',
        'comments', 'old_status', 'new_status',
    ];

    public function approvalRequest(): BelongsTo
    {
        return $this->belongsTo(ApprovalRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(ApprovalTier::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Modules\Admin\Models\Company::class, 'company_id', 'id');
    }
}
