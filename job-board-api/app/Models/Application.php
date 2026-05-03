<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = ['candidate_id', 'job_id', 'resume_path', 'cover_letter', 'status', 'is_paid'];

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
