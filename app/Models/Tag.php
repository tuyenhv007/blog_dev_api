<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    const TITLE_COLUMN = 'title';
    const SLUG_COLUMN = 'slug';
    const META_TITLE_COLUMN = 'metaTitle';
    const CONTENT_COLUMN = 'content';

    public function post()
    {
        return $this->belongsToMany(Post::class,'post_tag', 'tagId', 'postId')
            ->withPivot('position');
    }
}
