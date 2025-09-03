<?php

namespace Modules\Forms\Entities;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $table = 'module_forms';

    protected $fillable = [
        'name',
        'title',
        'description',
        'slug',
        'price',
        'allowed_gateways',
        'required_packages',
        'notification_email',
        'max_submissions',
        'max_submissions_per_user',
        'guest',
        'can_view_submission',
        'can_respond',
        'active',
    ];

    protected $casts = [
        'allowed_gateways' => 'array',
        'required_packages' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'guest' => false,
        'can_view_submission' => false,
        'can_respond' => false,
    ];

    public function fields()
    {
        return $this->hasMany(FormField::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function isPaid() : bool
    {
        return $this->price > 0;
    }

    public function fieldNames()
    {
        return $this->fields->pluck('name')->toArray();
    }

    public function fieldRules(): array
    {
        return $this->fields->pluck('rules', 'name')->toArray() ?? [];
    }

    public function url()
    {
        return route('forms.view', $this->slug);
    }
}
