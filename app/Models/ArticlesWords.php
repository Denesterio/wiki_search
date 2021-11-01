<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticlesWords extends Model
{
    use HasFactory;

    protected $fillable = [
        'word_id',
        'article_id',
        'count',
    ];
}
