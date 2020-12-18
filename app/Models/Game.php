<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $board = [['tw','na','na','dl','na','na','na','tw','na','na','na','dl','na','na','tw'],
                     ['na','dw','na','na','na','tl','na','na','na','tl','na','na','na','dw','na'],
                     ['na','na','dw','na','na','na','dl','na','dl','na','na','na','dw','na','na'],
                     ['dl','na','na','dw','na','na','na','dl','na','na','na','dw','na','na','dl'],
                     ['na','na','na','na','dw','na','na','na','na','na','dw','na','na','na','na'],
                     ['na','tl','na','na','na','tl','na','na','na','tl','na','na','na','tl','na'],
                     ['na','na','dl','na','na','na','dl','na','dl','na','na','na','dl','na','na'],
                     ['tw','na','na','dl','na','na','na','mi','na','na','na','dl','na','na','tw'],
                     ['na','na','dl','na','na','na','dl','na','dl','na','na','na','dl','na','na'],
                     ['na','tl','na','na','na','tl','na','na','na','tl','na','na','na','tl','na'],
                     ['na','na','na','na','dw','na','na','na','na','na','dw','na','na','na','na'],
                     ['dl','na','na','dw','na','na','na','dl','na','na','na','dw','na','na','dl'],
                     ['na','na','dw','na','na','na','dl','na','dl','na','na','na','dw','na','na'],
                     ['na','dw','na','na','na','tl','na','na','na','tl','na','na','na','dw','na'],
                     ['tw','na','na','dl','na','na','na','tw','na','na','na','dl','na','na','tw']];

    public function players() {
        return $this->hasMany(Player::class);
    }

    public function currentTurn() {
        return Player::find($this->current_turn);
    }
}
