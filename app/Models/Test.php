<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'test_date', 'test_results'];

    protected $casts = [
        'test_results' => 'array', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
