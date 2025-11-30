<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlashCardSetDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'flash_card_set_id',
        'word',
        'word_description'
    ];

    public function flashCardSet()
    {
        return $this->belongsTo(FlashCardSet::class);
    }
}
