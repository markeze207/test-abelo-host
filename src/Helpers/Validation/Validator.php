<?php
namespace App\Helpers\Validation;

class Validator implements ValidatorInterface
{
    private array $rules = [];
    private array $errors = [];

    /**
     * Доступные правила валидации
     */
    private const AVAILABLE_RULES = [
        'required', 'email', 'min', 'max', 'length', 'numeric',
        'integer', 'alpha', 'alpha_num', 'url', 'ip', 'date',
        'in', 'not_in', 'regex', 'phone', 'json', 'bool', 'confirmed'
    ];

    /**
     * Сообщения об ошибках по умолчанию
     */
    private const DEFAULT_MESSAGES = [
        'required' => 'Поле {field} обязательно для заполнения',
        'email' => 'Поле {field} должно быть корректным email адресом',
        'min' => 'Поле {field} должно содержать минимум {parameter} символов',
        'max' => 'Поле {field} должно содержать максимум {parameter} символов',
        'length' => 'Поле {field} должно содержать ровно {parameter} символов',
        'numeric' => 'Поле {field} должно быть числом',
        'integer' => 'Поле {field} должно быть целым числом',
        'alpha' => 'Поле {field} должно содержать только буквы',
        'alpha_num' => 'Поле {field} должно содержать только буквы и цифры',
        'url' => 'Поле {field} должно быть корректным URL',
        'ip' => 'Поле {field} должно быть корректным IP адресом',
        'date' => 'Поле {field} должно быть корректной датой',
        'in' => 'Поле {field} должно содержать одно из допустимых значений',
        'not_in' => 'Поле {field} содержит недопустимое значение',
        'regex' => 'Поле {field} имеет неверный формат',
        'phone' => 'Поле {field} должно быть корректным номером телефона',
        'json' => 'Поле {field} должно быть корректной JSON строкой',
        'bool' => 'Поле {field} должно быть булевым значением',
        'confirmed' => 'Пароли не совпадают'
    ];

    public function addRule(string $field, string $rule, ?string $message = null, $parameter = null): self
    {
        // Парсим правило, если оно содержит параметр (например: min:3)
        if (strpos($rule, ':') !== false) {
            [$rule, $parameter] = explode(':', $rule, 2);

            // Преобразуем параметры для in/not_in
            if (in_array($rule, ['in', 'not_in'])) {
                $parameter = explode(',', $parameter);
            }
        }

        if (!in_array($rule, self::AVAILABLE_RULES)) {
            throw new \InvalidArgumentException("Правило валидации '{$rule}' не поддерживается");
        }

        $this->rules[] = [
            'field' => $field,
            'rule' => $rule,
            'message' => $message,
            'parameter' => $parameter
        ];

        return $this;
    }

    public function addRules(array $rules): self
    {
        foreach ($rules as $field => $fieldRules) {
            if (is_string($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
            }

            foreach ($fieldRules as $rule) {
                $this->addRule($field, $rule);
            }
        }

        return $this;
    }

    public function validate(array $data): bool
    {
        $this->errors = [];

        foreach ($this->rules as $rule) {
            $field = $rule['field'];
            $value = $data[$field] ?? null;
            $ruleName = $rule['rule'];
            $parameter = $rule['parameter'];

            // Пропускаем валидацию если поле не обязательное и пустое
            if ($ruleName !== 'required' && $this->isEmpty($value)) {
                continue;
            }

            $method = 'validate' . str_replace('_', '', ucwords($ruleName, '_'));
            $isValid = $this->$method($value, $parameter, $data);

            if (!$isValid) {
                $this->addError($field, $ruleName, $rule['message'], $parameter);
            }
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }

    public function reset(): self
    {
        $this->rules = [];
        $this->errors = [];

        return $this;
    }

    /**
     * Проверить, пустое ли значение
     */
    private function isEmpty($value): bool
    {
        return $value === null || $value === '' || $value === [];
    }

    /**
     * Добавить ошибку
     */
    private function addError(string $field, string $rule, ?string $customMessage, $parameter = null): void
    {
        $message = $customMessage ?? self::DEFAULT_MESSAGES[$rule] ?? 'Ошибка валидации поля {field}';

        $message = str_replace('{field}', $field, $message);
        $message = str_replace('{parameter}', $this->formatParameter($parameter), $message);

        $this->errors[$field] = $message;
    }

    /**
     * Форматировать параметр для вывода в сообщении
     */
    private function formatParameter($parameter): string
    {
        if (is_array($parameter)) {
            return implode(', ', $parameter);
        }
        return (string)$parameter;
    }

    /**
     * Обязательное поле
     */
    private function validateRequired($value): bool
    {
        return !$this->isEmpty($value);
    }

    /**
     * Email
     */
    private function validateEmail($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Минимальная длина
     */
    private function validateMin($value, int $length): bool
    {
        return strlen((string)$value) >= $length;
    }

    /**
     * Максимальная длина
     */
    private function validateMax($value, int $length): bool
    {
        return strlen((string)$value) <= $length;
    }

    /**
     * Точная длина
     */
    private function validateLength($value, int $length): bool
    {
        return strlen((string)$value) === $length;
    }

    /**
     * Число
     */
    private function validateNumeric($value): bool
    {
        return is_numeric($value);
    }

    /**
     * Целое число
     */
    private function validateInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Только буквы
     */
    private function validateAlpha($value): bool
    {
        return ctype_alpha(str_replace(' ', '', $value));
    }

    /**
     * Буквы и цифры
     */
    private function validateAlphaNum($value): bool
    {
        return ctype_alnum(str_replace(' ', '', $value));
    }

    /**
     * URL
     */
    private function validateUrl($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * IP адрес
     */
    private function validateIp($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Дата
     */
    private function validateDate($value, string $format = 'Y-m-d'): bool
    {
        $date = \DateTime::createFromFormat($format, $value);
        return $date && $date->format($format) === $value;
    }

    /**
     * Вхождение в список
     */
    private function validateIn($value, array $allowedValues): bool
    {
        return in_array($value, $allowedValues, true);
    }

    /**
     * Не вхождение в список
     */
    private function validateNotIn($value, array $disallowedValues): bool
    {
        return !in_array($value, $disallowedValues, true);
    }

    /**
     * Регулярное выражение
     */
    private function validateRegex($value, string $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }

    /**
     * Телефон
     */
    private function validatePhone($value): bool
    {
        $phone = preg_replace('/[^0-9+]/', '', $value);
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }

    /**
     * JSON
     */
    private function validateJson($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Булево значение
     */
    private function validateBool($value): bool
    {
        return is_bool($value) || in_array(strtolower($value), ['true', 'false', '1', '0', 'on', 'off'], true);
    }

    /**
     * Подтверждение (например, пароль)
     */
    private function validateConfirmed($value, $parameter, array $data): bool
    {
        $field = $parameter ?? 'password';
        $confirmationField = $field . '_confirmation';

        return isset($data[$confirmationField]) && $value === $data[$confirmationField];
    }
}