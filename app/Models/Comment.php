<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    const POST_ID_COLUMN = 'postId';
    const PARENT_ID_COLUMN = 'parentId';
    const TITLE_COLUMN = 'title';
    const CONTENT_COLUMN = 'content';
    const PUBLISHED_COLUMN = 'published';
    const PUBLISHED_AT_COLUMN = 'publishedAt';


    public function post()
    {
        return $this->belongsTo(Post::class, self::POST_ID_COLUMN);
    }
}
