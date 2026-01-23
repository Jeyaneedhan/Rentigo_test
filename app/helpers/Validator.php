/**
 * Validator Class
 * THE GATEKEEPER: This class makes sure that user input is safe and correct 
 * before we actually do anything with it.
 */
class Validator
{
    // A list of things that failed (e.g., "Email is missing")
    private $errors = [];

    /**
     * Rule: The field cannot be empty.
     */
    public function required($field, $value, $message = null)
    {
        if (empty($value)) {
            $this->errors[$field] = $message ?? ucfirst($field) . ' is required';
            return false;
        }
        return true;
    }

    // Make sure a field has at least a certain number of characters
    public function minLength($field, $value, $min, $message = null)
    {
        if (strlen($value) < $min) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must be at least {$min} characters";
            return false;
        }
        return true;
    }

    // Make sure a field doesn't exceed a maximum length
    public function maxLength($field, $value, $max, $message = null)
    {
        if (strlen($value) > $max) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must not exceed {$max} characters";
            return false;
        }
        return true;
    }

    // Check if a value is in a list of allowed values
    // Useful for dropdowns and radio buttons
    public function inArray($field, $value, $validValues, $message = null)
    {
        if (!in_array($value, $validValues)) {
            $this->errors[$field] = $message ?? 'Please select a valid ' . str_replace('_', ' ', $field);
            return false;
        }
        return true;
    }

    // Validate email format using PHP's built-in filter
    public function email($field, $value, $message = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? 'Please enter a valid email address';
            return false;
        }
        return true;
    }

    /**
     * Rule: Check if two inputs are identical.
     * Use this for "Confirm Password" or "Confirm Email" fields.
     */
    public function matches($field, $value, $matchValue, $matchField, $message = null)
    {
        if ($value !== $matchValue) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must match {$matchField}";
            return false;
        }
        return true;
    }

    // Custom validation - pass in any condition you want to check
    public function custom($field, $condition, $message)
    {
        if (!$condition) {
            $this->errors[$field] = $message;
            return false;
        }
        return true;
    }

    // Get all the errors that were found
    public function getErrors()
    {
        return $this->errors;
    }

    // Quick check to see if there are any errors
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    // Clear all errors - useful when reusing the validator
    public function clearErrors()
    {
        $this->errors = [];
    }
}
