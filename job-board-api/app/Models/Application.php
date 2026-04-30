<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['candidate_id', 'job_id', 'resume_path', 'status', 'is_paid'];

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
