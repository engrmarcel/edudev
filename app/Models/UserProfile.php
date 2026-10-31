<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'account_type', 'phone', 'class_level', 'exam_goals', 'organisation_name', 'state',
    ];

    protected function casts(): array
    {
        return [
            'account_type' => AccountType::class,
            'exam_goals' => 'array',
            'onboarded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
