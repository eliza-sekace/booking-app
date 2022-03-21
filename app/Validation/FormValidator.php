<?php

namespace App\Validation;

use App\Exceptions\FormValidationException;
use Cassandra\Exception\ValidationException;

class FormValidator
{
    private $data;
    private array $errors = [];
    private array $rules;

    public function __construct($data, array $rules = [])
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function passes()
    {
        foreach ($this->rules as $key => $rules)
        {
            foreach ($rules as $rule) {
                //"required"
                //"min:3"
                if(strpos($rule,":")>0){
                    [$name, $attribute] = explode(":", $rule);
                    $ruleName = 'validate' . ucfirst($name); //validateRequired validateMin
                    //check if method exists
                    $this->{$ruleName}($key, $attribute);
                } else  $this->{'validate' . ucfirst($rule)}($key);
            }
        }
        if (count($this->errors) > 0) {
            throw new FormValidationException();
        }
    }

    private function validateRequired(string $key): void
    {
        if (empty(trim($this->data[$key]))) {
            $this->errors[$key][] = "{$key} field is required.";
        }
    }

    private function validateMin(string $key, int $attribute)
    {
        if (strlen($this->data[$key]) < $attribute){
            $this->errors[$key][] = "{$key} must be at least {$attribute} characters.";
        }
    }


    public function getErrors(): array
    {
        return $this->errors;
    }

}