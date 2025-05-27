<?php

namespace MVC\core\validations;

use MVC\core\Log;

class Validation
{
    use CheckInArrayValidations, DataTypeValidations, QueryValidations;
    protected static array $errors = [];
    protected static array $validated = [];
    public static function request($key, $requests): mixed
    {
        return $requests[$key] ?? '';
    }
    public static function make(array|object $requests, array $rules, array|null $attributes = []): Validation
    {
        foreach ($rules as $rule_key => $rule_value) {
            $value = self::request($rule_key, $requests);
            $real_rule_key = explode('.', $rule_key)[0];
            $attribute = self::attribute($attributes, $real_rule_key);
            foreach (self::rule($rule_value) as $rule) {
                $method = self::getMethodName($rule);
                if (!method_exists(new self, $method)) {
                    throw new Log('There is not validation called ' . $method);
                } elseif (preg_match('/^in:|^unique:|^exists:/i', $rule)) {
                    if (self::$method($rule, $value)) {
                        if (preg_match('/^in:/i', $rule)) {
                            $attribute_in = explode(':', $rule);
                            $attribute = $attribute . ' - ' . $attribute_in[1];
                        }
                        self::add_error($rule_key, $method, $attribute);
                    }
                } elseif (preg_match('/\./', $rule_key)) {
                    self::validate_sub_value($rule_key, $requests, $attribute, $rule);
                } elseif (self::$method($value)) {
                    self::add_error($rule_key, $rule, $attribute);
                } else {
                    self::$validated[$rule_key] = $value;
                }
            }
        }
        return new self;
    }
    public static function validated(): array
    {
        return static::$validated;
    }
    public static function failed(): array
    {
        return static::$errors;
    }
    protected static function getMethodName($rule): string
    {
        if (preg_match('/^in:/i', $rule)) {
            return 'in';
        } elseif (preg_match('/^unique:/i', $rule)) {
            return 'unique';
        } elseif (preg_match('/^exists:/i', $rule)) {
            return 'exists';
        } else {
            return $rule;
        }
    }
    protected static function validate_sub_value($rule_key, $requests, $attribute, $rule): void
    {
        $split_key = explode('.', $rule_key);
        if (isset($split_key[1]) && $split_key[1] == '*' && is_array($requests[$split_key[0]])) {
            $index = 0;
            foreach ($requests[$split_key[0]] as $array_value) {
                if (self::$rule($array_value)) {
                    self::add_error($split_key[0], $rule, $attribute . ' (' . $index . ') ');
                }
                $index++;
            }
        } elseif (is_array($requests[$split_key[0]]) && isset($split_key[1])) {
            $select_request = $requests[$split_key[0]];
            if (isset($select_request[$split_key[1]])) {
                if (self::$rule($select_request[$split_key[1]])) {
                    self::add_error($split_key[0], $rule, $attribute . ' (' . $split_key[1] . ') ');
                }
            }
        }
    }
    private static function add_error($key, $rule, $attribute): void
    {
        static::$errors[$key][] = $rule . ' ' . $attribute;
    }
    private static function rule(string|array $rule): array
    {
        return is_array($rule) ? $rule : explode('|', $rule);
    }
    private static function attribute($attributes, $key): string
    {
        return $attributes[$key] ?? $key;
    }
}