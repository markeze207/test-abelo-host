<?php
namespace App\Helpers\Validation;

trait ValidationTrait
{
    /**
     * @var Validator|null
     */
    private ?Validator $validator = null;

    /**
     * Получить экземпляр валидатора (ленивая инициализация)
     */
    protected function getValidator(): Validator
    {
        if ($this->validator === null) {
            $this->validator = new Validator();
        }
        return $this->validator;
    }

    /**
     * Валидация данных
     */
    protected function validate(array $data, array $rules): bool
    {
        return $this->getValidator()
            ->reset()
            ->addRules($rules)
            ->validate($data);
    }

    /**
     * Валидация с получением всех ошибок
     */
    protected function validateWithErrors(array $data, array $rules): array
    {
        $validator = $this->getValidator()->reset();
        $validator->addRules($rules);
        $isValid = $validator->validate($data);

        return [
            'is_valid' => $isValid,
            'errors' => $validator->getErrors(),
            'first_error' => $validator->getFirstError()
        ];
    }

    /**
     * Валидация одного поля
     */
    protected function validateField(string $field, $value, string $rules): bool
    {
        return $this->validate([$field => $value], [$field => $rules]);
    }

    /**
     * Получить последние ошибки валидации
     */
    protected function getValidationErrors(): array
    {
        return $this->validator ? $this->validator->getErrors() : [];
    }

    /**
     * Получить первую ошибку валидации
     */
    protected function getFirstValidationError(): ?string
    {
        return $this->validator ? $this->validator->getFirstError() : null;
    }
}