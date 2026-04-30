<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'employer_id',
        'category_id',
        'title',
        'description',
        'responsibilities',
        'requirements',
        'skills',
        'work_type',
        'location',
        'status',
        'deadline'
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
