<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['employer_id', 'application_id', 'amount', 'transaction_id', 'status'];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
