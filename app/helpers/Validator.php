<?php

class Validator
{
    private $errors = [];

    public function required($field, $value, $message = null)
    {
        if (empty($value)) {
            $this->errors[$field] = $message ?? ucfirst($field) . ' is required';
            return false;
        }
        return true;
    }

    public function minLength($field, $value, $min, $message = null)
    {
        if (strlen($value) < $min) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must be at least {$min} characters";
            return false;
        }
        return true;
    }

    public function maxLength($field, $value, $max, $message = null)
    {
        if (strlen($value) > $max) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must not exceed {$max} characters";
            return false;
        }
        return true;
    }

    public function inArray($field, $value, $validValues, $message = null)
    {
        if (!in_array($value, $validValues)) {
            $this->errors[$field] = $message ?? 'Please select a valid ' . str_replace('_', ' ', $field);
            return false;
        }
        return true;
    }

    public function email($field, $value, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? 'Please enter a valid email address';
            return false;
        }
        return true;
    }

    public function matches($field, $value, $matchValue, $matchField, $message = null)
    {
        if ($value !== $matchValue) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must match {$matchField}";
            return false;
        }
        return true;
    }

    public function custom($field, $condition, $message)
    {
        if (!$condition) {
            $this->errors[$field] = $message;
            return false;
        }
        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function clearErrors()
    {
        $this->errors = [];
    }
}
