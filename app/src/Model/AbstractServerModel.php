<?php

namespace App\Model;

abstract class AbstractServerModel
{
    abstract public static function fromArray(array $data): static;
}