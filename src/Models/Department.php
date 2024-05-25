<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Models;

use Atendwa\SuStarterKit\Observers\DepartmentObserver;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(DepartmentObserver::class)]
class Department extends Model implements ShouldHandleEventsAfterCommit
{
    use SoftDeletes;

    protected $guarded = [];

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_department_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_department_id');
    }

    protected function isSubDepartment(): Attribute
    {
        $key = 'parent_department_id';

        return Attribute::make(
            get: static fn ($value) => $value,
            set: static fn (array $attributes) => filled($attributes[$key]),
        );
    }
}
