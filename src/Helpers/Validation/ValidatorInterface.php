<?php
namespace App\Helpers\Validation;

interface ValidatorInterface
{
    /**
     * Добавить правило валидации
     */
    public function addRule(string $field, string $rule, ?string $message = null, $parameter = null): self;

    /**
     * Добавить несколько правил
     */
    public function addRules(array $rules): self;

    /**
     * Валидировать данные
     */
    public function validate(array $data): bool;

    /**
     * Получить ошибки валидации
     */
    public function getErrors(): array;

    /**
     * Получить первую ошибку
     */
    public function getFirstError(): ?string;

    /**
     * Очистить правила и ошибки
     */
    public function reset(): self;
}