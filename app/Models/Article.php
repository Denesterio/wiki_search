<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'link',
    ];

    public function words()
    {
        return $this->belongsToMany(Word::class, 'articles_words');
    }
}
