<?php

// Base Model: kontrak dasar untuk semua model
abstract class Model
{
    abstract public function toArray(): array;

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
