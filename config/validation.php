<?php
/**
 * Validation Class
 * 
 * Class để validate và sanitize dữ liệu đầu vào
 */

class Validation {
    
    /**
     * Sanitize string input
     */
    public static function sanitizeString($value) {
        return filter_var(trim($value), FILTER_SANITIZE_STRING);
    }
    
    /**
     * Sanitize email
     */
    public static function sanitizeEmail($value) {
        return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Sanitize integer
     */
    public static function sanitizeInt($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Sanitize float
     */
    public static function sanitizeFloat($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    
    /**
     * Sanitize URL
     */
    public static function sanitizeURL($value) {
        return filter_var(trim($value), FILTER_SANITIZE_URL);
    }
    
    /**
     * Validate email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate URL
     */
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Validate integer
     */
    public static function validateInt($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
    
    /**
     * Validate float
     */
    public static function validateFloat($value) {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }
    
    /**
     * Validate required field
     */
    public static function required($value) {
        return !empty(trim($value));
    }
    
    /**
     * Validate min length
     */
    public static function minLength($value, $min) {
        return strlen(trim($value)) >= $min;
    }
    
    /**
     * Validate max length
     */
    public static function maxLength($value, $max) {
        return strlen(trim($value)) <= $max;
    }
    
    /**
     * Validate between length
     */
    public static function betweenLength($value, $min, $max) {
        $len = strlen(trim($value));
        return $len >= $min && $len <= $max;
    }
    
    /**
     * Validate username (alphanumeric and underscore only)
     */
    public static function validateUsername($username) {
        return preg_match('/^[a-zA-Z0-9_]+$/', $username);
    }
    
    /**
     * Validate phone number
     */
    public static function validatePhone($phone) {
        // Vietnam phone format
        return preg_match('/^[0-9]{10,11}$/', preg_replace('/[^0-9]/', '', $phone));
    }
    
    /**
     * Validate date
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    /**
     * Validate password strength
     */
    public static function validatePasswordStrength($password) {
        // At least 8 characters, contain letters and numbers
        return strlen($password) >= 8 && preg_match('/[A-Za-z]/', $password) && preg_match('/[0-9]/', $password);
    }
    
    /**
     * Sanitize và validate array
     */
    public static function sanitizeArray($array, $keys) {
        $sanitized = [];
        foreach ($keys as $key => $type) {
            if (isset($array[$key])) {
                switch ($type) {
                    case 'string':
                        $sanitized[$key] = self::sanitizeString($array[$key]);
                        break;
                    case 'int':
                        $sanitized[$key] = self::sanitizeInt($array[$key]);
                        break;
                    case 'float':
                        $sanitized[$key] = self::sanitizeFloat($array[$key]);
                        break;
                    case 'email':
                        $sanitized[$key] = self::sanitizeEmail($array[$key]);
                        break;
                    case 'url':
                        $sanitized[$key] = self::sanitizeURL($array[$key]);
                        break;
                    default:
                        $sanitized[$key] = $array[$key];
                }
            }
        }
        return $sanitized;
    }
    
    /**
     * Validate data theo rules
     */
    public static function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleArray = explode('|', $rule);
            
            foreach ($ruleArray as $singleRule) {
                $params = [];
                if (strpos($singleRule, ':') !== false) {
                    list($ruleName, $ruleParams) = explode(':', $singleRule, 2);
                    $params = explode(',', $ruleParams);
                    $singleRule = $ruleName;
                }
                
                switch ($singleRule) {
                    case 'required':
                        if (!self::required($value)) {
                            $errors[$field][] = "Trường $field không được để trống";
                        }
                        break;
                        
                    case 'email':
                        if (!empty($value) && !self::validateEmail($value)) {
                            $errors[$field][] = "Email không hợp lệ";
                        }
                        break;
                        
                    case 'int':
                        if (!empty($value) && !self::validateInt($value)) {
                            $errors[$field][] = "Trường $field phải là số nguyên";
                        }
                        break;
                        
                    case 'float':
                        if (!empty($value) && !self::validateFloat($value)) {
                            $errors[$field][] = "Trường $field phải là số thực";
                        }
                        break;
                        
                    case 'min':
                        if (!empty($value) && count($params) > 0 && !self::minLength($value, $params[0])) {
                            $errors[$field][] = "Trường $field phải có ít nhất {$params[0]} ký tự";
                        }
                        break;
                        
                    case 'max':
                        if (!empty($value) && count($params) > 0 && !self::maxLength($value, $params[0])) {
                            $errors[$field][] = "Trường $field không được vượt quá {$params[0]} ký tự";
                        }
                        break;
                        
                    case 'phone':
                        if (!empty($value) && !self::validatePhone($value)) {
                            $errors[$field][] = "Số điện thoại không hợp lệ";
                        }
                        break;
                        
                    case 'username':
                        if (!empty($value) && !self::validateUsername($value)) {
                            $errors[$field][] = "Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới";
                        }
                        break;
                        
                    case 'password':
                        if (!empty($value) && !self::validatePasswordStrength($value)) {
                            $errors[$field][] = "Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ cái và số";
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Sanitize toàn bộ input data
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            $sanitized = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $sanitized[$key] = self::sanitizeInput($value);
                } else {
                    $sanitized[$key] = htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8');
                }
            }
            return $sanitized;
        }
        return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Parse JSON input and sanitize
     */
    public static function getJsonInput() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        return $data ? self::sanitizeInput($data) : [];
    }
    
    /**
     * Get và validate POST data
     */
    public static function getPostData($rules = []) {
        $data = $_POST;
        
        if (empty($rules)) {
            return self::sanitizeInput($data);
        }
        
        return self::validate($data, $rules);
    }
    
    /**
     * Get và validate GET data
     */
    public static function getGetData($rules = []) {
        $data = $_GET;
        
        if (empty($rules)) {
            return self::sanitizeInput($data);
        }
        
        return self::validate($data, $rules);
    }
}

?>

