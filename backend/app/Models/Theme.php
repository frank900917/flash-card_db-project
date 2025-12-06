<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bg_color',
        'desc',
        'unlock_level',
    ];

    public function userThemeSetting()
    {
        return $this->hasMany(UserThemeSetting::class);
    }
}
