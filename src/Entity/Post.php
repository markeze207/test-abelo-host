<?php
namespace App\Entity;

class Post
{
    public function __construct(
        public readonly ?int    $id,
        public readonly string  $title,
        public readonly ?string $description,
        public readonly string  $content,
        public readonly ?string $image,
        public readonly int     $views,
        public readonly array   $categories,
        public readonly ?string $published_at,
        public readonly string  $created_at,
        public readonly ?string $updated_at = null,
        public readonly ?string $slug = null,
    )
    {
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return "/post/{$this->slug}";
    }

    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, '/public/')) {
            return $this->image;
        }

        return ltrim($this->image, '/');
    }

    /**
     * @param int $length
     * @return string
     */
    public function getExcerpt(int $length = 200): string
    {
        $text = $this->description ?? strip_tags($this->content);

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . '...';
    }

    /**
     * @param string $format
     * @return string
     */
    public function getFormattedDate(string $format = 'd.m.Y'): string
    {
        $date = $this->published_at ?? $this->created_at;
        return date($format, strtotime($date));
    }

    /**
     * @return Category|null
     */
    public function getMainCategory(): ?Category
    {
        return $this->categories[0] ?? null;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            title: $data['title'] ?? '',
            description: $data['description'] ?? null,
            slug: $data['slug'] ?? null,
            content: $data['content'] ?? '',
            image: $data['image'] ?? null,
            views: $data['views'] ?? 0,
            categories: $data['categories'] ?? [],
            published_at: $data['published_at'] ?? null,
            created_at: $data['created_at'] ?? date('Y-m-d H:i:s'),
            updated_at: $data['updated_at'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->image,
            'views' => $this->views,
            'slug' => $this->slug,
            'categories' => array_map(fn($cat) => $cat->toArray(), $this->categories),
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}