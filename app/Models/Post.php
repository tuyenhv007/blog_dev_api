<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;

class Post extends Model
{
    const USER_ID_COLUMN = 'userId';
    const PARENT_ID_COLUMN = 'parentId';
    const TITLE_COLUMN = 'title';
    const SLUG_COLUMN = 'slug';
    const DESCRIPTION_COLUMN = 'description';
    const CONTENT_COLUMN = 'content';
    const PUBLISHED_COLUMN = 'published';
    const PUBLISHED_AT_COLUMN = 'publishedAt';

    public function user()
    {
        return $this->belongsTo(User::class, self::USER_ID_COLUMN);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class, Comment::POST_ID_COLUMN);
    }

    public function category()
    {
        return $this->belongsToMany(Category::class, 'post_category', 'postId', 'categoryId')
            ->withPivot('position');
    }
    public function tag()
    {
        return $this->belongsToMany(Tag::class, 'post_tag','postId', 'tagId')
            ->withPivot('position');
    }
}
