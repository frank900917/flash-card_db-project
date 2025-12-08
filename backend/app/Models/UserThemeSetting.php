<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserThemeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme_id',
        'flash_card_set_id',
    ];

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }
}
