<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    const PARENT_ID_COLUMN = 'parentId';
    const TITLE_COLUMN = 'title';
    const SLUG_COLUMN = 'slug';
    const CONTENT_COLUMN = 'content';

    public function post()
    {
        return $this->belongsToMany(Post::class, 'post_category', 'categoryId', 'postId')
            ->withPivot('position');
    }
}
