<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageFeature extends Model
{
    use HasFactory;

    protected $table = 'package_features';

    public function up()
    {
        $this->increment('order');
    }

    public function down()
    {
        $this->decrement('order');
    }
}
