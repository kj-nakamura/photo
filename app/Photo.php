<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Photo extends Model
{
    protected $keyType = 'string';

    protected $appends = [
        'url',
        'likes_count',
        'liked_by_user',
    ];

    /** JSONに含める属性 */
    protected $visible = [
        'id',
        'owner',
        'url',
        'comments',
        'likes_count',
        'liked_by_user',
    ];

    public function comments()
    {
        return $this->hasMany('App\Comment')->orderBy('id', 'desc');
    }

    public function likes()
    {
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }

    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }

        return $this->likes->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }

    const ID_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!array_get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    // ランダムなIDをid属性に代入する
    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }

    // ランダムなIDを生成する
    private function getRandomId()
    {
        $characters = array_merge(
            range(0, 9),
            range('a', 'z'),
            range('A', 'Z'),
            ['-', '_']
        );

        $length = count($characters);

        $id = "";

        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->attributes['filename']);
    }
}
