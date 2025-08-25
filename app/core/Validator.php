<?php
namespace App\Core;

//Input validation with reusable rules
class Validator {
    private array $errors = [];
    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    //Validate required fields
    public function required(array $fields): self {
        foreach ($fields as $field) {
            if (empty($this->data[$field] ?? null)) {
                $this->addError($field, "$field is required");
            }
        }
        return $this;
    }

    //Validate email format
    public function email(string $field): self {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "$field must be a valid email address");
        }
        return $this;
    }

    //Validate minimum length
    public function minLength(string $field, int $min): self {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->addError($field, "$field must be at least $min characters");
        }
        return $this;
    }

    //Validate maximum length
    public function maxLength(string $field, int $max): self {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->addError($field, "$field must not exceed $max characters");
        }
        return $this;
    }

    //Validate numeric value
    public function numeric(string $field): self {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->addError($field, "$field must be a number");
        }
        return $this;
    }

    //Validate value is within range
    public function range(string $field, float $min, float $max): self {
        if (isset($this->data[$field])) {
            $value = $this->data[$field];
            if ($value < $min || $value > $max) {
                $this->addError($field, "$field must be between $min and $max");
            }
        }
        return $this;
    }

    //Add custom error
    public function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }

    //Check if validation passes
    public function passes(): bool {
        return empty($this->errors);
    }

    //Get validation errors
    public function getErrors(): array {
        return $this->errors;
    }

    // Get first error message
    public function getFirstError(): ?string {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }
}