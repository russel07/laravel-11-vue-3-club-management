<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location',
        'manager_name',
        'manager_email',
    ];


    /**
     * The sports that belong to the club.
     */
    public function sports()
    {
        return $this->belongsToMany(Sport::class, 'club_sport');
    }
}
