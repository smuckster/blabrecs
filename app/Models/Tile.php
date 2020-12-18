<?php

namespace App\Models;

class Tile
{
    public $letter;
    public $value;

    public function construct($letter, $value) {
        $this->letter = $letter;
        $this->value = $value;
    }
}
