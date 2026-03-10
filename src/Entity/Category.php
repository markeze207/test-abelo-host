<?php
namespace App\Entity;

class Category
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $slug,
        public readonly ?string $description,
        public readonly ?int $posts_count,
        public readonly string $created_at,
        public readonly ?string $updated_at,
        public array $posts,
    ) {

    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return "/category/{$this->slug}";
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? null,
            description: $data['description'] ?? null,
            posts_count: $data['posts_count'] ?? null,
            created_at: $data['created_at'] ?? date('Y-m-d H:i:s'),
            updated_at: $data['updated_at'] ?? null,
            posts: $data['posts'] ?? [],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'posts_count' => $this->posts_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}