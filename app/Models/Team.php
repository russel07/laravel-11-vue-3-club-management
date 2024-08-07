<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'coach_id',
        'coach_name',
        'coach_email',
        'club_id',
        'sport_id',
    ];

    /**
     * Get the club that owns the team.
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the sport that the team plays.
     */
    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }
    

    /**
     * Get the sport that the team plays.
     */
    public function coach()
    {
        return $this->belongsTo(User::class);
    }
}
