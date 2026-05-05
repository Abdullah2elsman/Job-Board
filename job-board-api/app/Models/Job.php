<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'category_id',
        'title',
        'description',
        'responsibilities',
        'requirements',
        'salary',
        'skills',
        'work_type',
        'location',
        'experience_level',
        'status',
        'deadline'
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function user()
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

    public function skills()
    {
        return $this->hasMany(JobSkill::class);
    }

    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }
        return $query;
    }

    public function scopeLocation($query, $location)
    {
        if ($location) {
            $query->where('location', 'like', "%{$location}%");
        }
        return $query;
    }

    public function scopeCategory($query, $categoryId)
    {
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        return $query;
    }

    public function scopeWorkType($query, $workType)
    {
        if ($workType) {
            $query->where('work_type', $workType);
        }
        return $query;
    }

    public function scopeSalaryRange($query, $min, $max)
    {
        if ($min) {
            $query->where('salary', '>=', $min);
        }
        if ($max) {
            $query->where('salary', '<=', $max);
        }
        return $query;
    }

    public function scopeExperienceLevel($query, $level)
    {
        if ($level) {
            $query->where('experience_level', $level);
        }
        return $query;
    }

    public function scopeSort($query, $sortBy)
    {
        if ($sortBy === 'salary_desc') {
            return $query->orderByDesc('salary');
        } elseif ($sortBy === 'salary_asc') {
            return $query->orderBy('salary');
        } elseif ($sortBy === 'oldest') {
            return $query->oldest();
        }

        return $query->latest();
    }
}
