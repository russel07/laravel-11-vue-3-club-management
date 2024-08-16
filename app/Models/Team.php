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
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Get the athlete associated with the team.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->team_id = self::generateUniqueTeamId();
        });
    }

    private static function generateUniqueTeamId()
    {
        do {
            // Generate a 6-digit random number, padded with leading zeros if necessary
            $teamId = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('team_id', $teamId)->exists());

        return $teamId;
    }
}
