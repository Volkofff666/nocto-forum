<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'bio',
        'avatar_url',
        'cover_url',
        'bg_url',
        'telegram_id',
        'role',
        'banned_at',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at'         => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    public function getAvatarAttribute(): string
    {
        if ($this->avatar_url) {
            return $this->avatar_url;
        }

        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }

        return $initials ?: '?';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return in_array($this->role, ['moderator', 'admin']);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    // Users who follow THIS user
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id');
    }

    // Users THIS user follows
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id');
    }
}
