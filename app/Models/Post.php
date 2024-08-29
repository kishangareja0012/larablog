<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\PostObserver;

class Post extends Model
{
    use HasFactory;

    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function like()
    {
        return $this->hasMany(Like::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class)->orderBy("id","DESC");
    }

    public function visit()
    {
        return $this->hasMany(Visitor::class);

    }
}
