<?php

namespace Modules\Forms\Entities;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $table = 'module_forms_fields';

    protected $fillable = [
        'form_id',
        'name',
        'type',
        'label',
        'description',
        'placeholder',
        'default_value',
        'rules',
        'options',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function up()
    {
        $this->increment('order');
    }

    public function down()
    {
        $this->decrement('order');
    }
}

