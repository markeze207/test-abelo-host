<?php

namespace App\Builders;

use App\Builders\Interfaces\BuilderInterface;

class PostBuilder implements BuilderInterface
{
    /**
     * @var array
     */
    private array $data = [
        'title' => '',
        'description' => '',
        'content' => '',
        'image' => null,
        'views' => 0,
        'category_ids' => [],
        'meta_title' => '',
        'meta_description' => '',
        'published_at' => null,
        'created_at' => null,
        'updated_at' => null
    ];

    /**
     * @var array
     */
    private array $errors = [];
    /**
     * @var array
     */
    private array $uploadedFiles = [];

    /**
     * @return $this
     */
    public function withTimestamps(): self
    {
        $now = date('Y-m-d H:i:s');
        $this->data['created_at'] = $now;
        $this->data['updated_at'] = $now;
        return $this;
    }

    /**
     * @return $this
     */
    public function reset(): self
    {
        $this->data = [
            'title' => '',
            'description' => '',
            'content' => '',
            'image' => null,
            'views' => 0,
            'category_ids' => [],
            'meta_title' => '',
            'meta_description' => '',
            'published_at' => null,
            'created_at' => null,
            'updated_at' => null
        ];
        $this->errors = [];
        $this->uploadedFiles = [];

        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function build(): array
    {
        if (!empty($this->errors)) {
            throw new \Exception('Ошибки валидации: ' . json_encode($this->errors, JSON_UNESCAPED_UNICODE));
        }

        if (empty($this->data['title'])) {
            throw new \Exception('Заголовок статьи обязателен');
        }

        if (empty($this->data['content'])) {
            throw new \Exception('Текст статьи обязателен');
        }

        if (!empty($this->uploadedFiles)) {
            $this->processUploadedFiles();
        }

        $result = $this->data;
        $this->reset();

        return $result;
    }

    /**
     * @return void
     */
    private function processUploadedFiles(): void
    {
        $uploadDir = __DIR__ . '/../../public/assets/uploads/posts/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($this->uploadedFiles as $field => $file) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $this->data[$field] = 'assets/uploads/posts/' . $filename;
            }
        }
    }
}