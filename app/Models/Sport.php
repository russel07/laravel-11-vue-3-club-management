<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The clubs that belong to the sport.
     */
    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_sport');
    }


    /**
     * Get the teams for the sport.
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
