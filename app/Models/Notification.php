<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    const USER_ID_COLUMN = 'userId';
    const TITLE_COLUMN = 'title';
    const CONTENT_COLUMN = 'content';
    const STATUS_COLUMN = 'status';

    public function user()
    {
        return $this->belongsTo(User::class, self::USER_ID_COLUMN);
    }
}
