<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'flash_card_set_id',
        'title',
        'correct_count',
        'correct_rate'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
