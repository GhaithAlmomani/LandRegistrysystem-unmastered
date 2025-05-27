<?php

namespace MVC\core\validations;

trait CheckInArrayValidations
{
    protected static function in(mixed $rule, mixed $value): bool
    {
        $values = isset(explode(':', $rule)[1]) ? explode(',', explode(':', $rule)[1]) : [];
        return !in_array($value, $values);
    }
}