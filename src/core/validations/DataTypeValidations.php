<?php

namespace MVC\core\validations;

trait DataTypeValidations
{
    protected static function required(mixed $value): bool
    {
        return (empty($value) || (isset($value['tmp_name']) && empty($value['tmp_name'])));
    }
    protected static function string(mixed $value): bool
    {
        return !is_string($value) || is_numeric(($value));
    }
    protected static function integer(mixed $value): bool
    {
        return !filter_var((int) $value, FILTER_VALIDATE_INT) || !is_numeric(($value));
    }
    protected static function numeric(mixed $value): bool
    {
        return !preg_match('/^[0-9]+$/',$value);
    }
    protected static function json(mixed $value): bool
    {
        return !(json_validate($value));
    }
    protected static function array(mixed $value): bool
    {
        return !is_array($value);
    }
    protected static function email(mixed $value): bool
    {
        return !filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}