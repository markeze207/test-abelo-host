<?php

namespace App\Builders;

use App\Builders\Interfaces\BuilderInterface;

class CategoryBuilder implements BuilderInterface
{
    /**
     * @var array
     */
    private array $data = [
        'name' => '',
        'description' => null,
        'slug' => null,
        'created_at' => null,
        'updated_at' => null
    ];

    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @param string $name
     * @return $this
     */
    public function withName(string $name): self
    {
        $name = trim($name);

        if (empty($name)) {
            $this->errors['name'] = 'Название категории обязательно';
        } elseif (strlen($name) < 3) {
            $this->errors['name'] = 'Название должно быть не менее 3 символов';
        } elseif (strlen($name) > 255) {
            $this->errors['name'] = 'Название должно быть не более 255 символов';
        }

        $this->data['name'] = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        return $this;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function withDescription(?string $description): self
    {
        if ($description !== null) {
            $description = trim($description);
            $this->data['description'] = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        }

        return $this;
    }

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
            'name' => '',
            'description' => null,
            'slug' => null,
            'created_at' => null,
            'updated_at' => null
        ];
        $this->errors = [];

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

        if (empty($this->data['name'])) {
            throw new \Exception('Название категории обязательно');
        }

        if (empty($this->data['slug']) && !empty($this->data['name'])) {
            $this->data['slug'] = $this->generateSlug();
        }

        $result = $this->data;
        $this->reset();

        return $result;
    }

    /**
     * @return string
     */
    private function generateSlug(): string
    {
        $slug = $this->transliterate($this->data['name']);
        return $this->slugify($slug);
    }

    /**
     * @param string $text
     * @return string
     */
    private function slugify(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }

    /**
     * @param string $text
     * @return string
     */
    private function transliterate(string $text): string
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch',
            'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];

        return strtr($text, $converter);
    }
}