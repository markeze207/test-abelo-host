<?php

namespace App\Repositories;

use App\Core\QueryBuilder;
use App\Factory\QueryBuilderFactory;
use App\Repositories\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(
        private readonly QueryBuilderFactory $factory
    ) {}

    private function table(string $table): QueryBuilder
    {
        return $this->factory->create($table);
    }

    public function getLatestPublished(int $limit = 6): array
    {
        return $this->table('posts')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function findById(int $id): ?array
    {
        return $this->table('posts')
            ->where('id', '=', $id)
            ->first();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->table('posts')
            ->where('slug', '=', $slug)
            ->first();
    }

    public function findWithCategories(int $id): ?array
    {
        $post = $this->findById($id);

        if ($post) {
            $post['categories'] = $this->table('categories c')
                ->select(['c.*'])
                ->join('post_category pc', 'c.id', '=', 'pc.category_id')
                ->where('pc.post_id', '=', $id)
                ->orderBy('c.name')
                ->get();
        }

        return $post;
    }

    public function getLatestByCategory(int $categoryId, int $limit = 3): array
    {
        return $this->table('posts p')
            ->select(['p.*'])
            ->join('post_category pc', 'p.id', '=', 'pc.post_id')
            ->where('pc.category_id', '=', $categoryId)
            ->orderByDesc('p.created_at')
            ->limit($limit)
            ->get();
    }

    public function getByCategory(
        int $categoryId,
        string $sortBy = 'created_at',
        string $order = 'DESC',
        int $limit = 10,
        ?int $lastId = null,
        $lastValue = null
    ): array {
        $allowedSort = ['views', 'created_at', 'title'];
        $sortBy      = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
        $order       = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $query = $this->table('posts p')
            ->select(['p.*'])
            ->join('post_category pc', 'p.id', '=', 'pc.post_id')
            ->where('pc.category_id', '=', $categoryId);

        if ($lastId !== null && $lastValue !== null) {
            $operator = $order === 'DESC' ? '<' : '>';
            $query->whereRaw("(p.$sortBy $operator ? OR (p.$sortBy = ? AND p.id < ?))", [
                $lastValue,
                $lastValue,
                $lastId,
            ]);
        }

        return $query
            ->orderBy("p.$sortBy", $order)
            ->orderByDesc('p.id')
            ->limit($limit)
            ->get();
    }

    public function findSimilar(int $postId, array $categoryIds, int $limit = 3): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        return $this->table('posts p')
            ->select(['p.*', 'COUNT(pc.category_id) as matches'])
            ->join('post_category pc', 'p.id', '=', 'pc.post_id')
            ->whereIn('pc.category_id', $categoryIds)
            ->where('p.id', '!=', $postId)
            ->groupBy('p.id')
            ->orderByDesc('matches')
            ->orderByDesc('p.views')
            ->limit($limit)
            ->get();
    }

    public function search(string $query): array
    {
        return $this->table('posts p')
            ->select(['p.*', 'MATCH(p.title, p.content) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance'])
            ->whereRaw('MATCH(p.title, p.content) AGAINST(? IN NATURAL LANGUAGE MODE)', [$query])
            ->orderByDesc('relevance')
            ->limit(20)
            ->get();
    }

    public function getPopular(int $limit = 5): array
    {
        return $this->table('posts')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    public function incrementViews(int $id): bool
    {
        return $this->table('posts')
                ->where('id', '=', $id)
                ->increment('views') > 0;
    }

    public function getTotalCount(): int
    {
        return $this->table('posts')->count();
    }

    public function getCountByCategory(int $categoryId): int
    {
        return $this->table('post_category')
            ->where('category_id', '=', $categoryId)
            ->count();
    }

    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        return $this->table('posts')
            ->select(['*'])
            ->orderByDesc('created_at')
            ->forPage($page, $perPage)
            ->get();
    }

    public function getPopularPaginated(int $limit = 6, ?int $lastId = null, ?int $lastViews = null): array
    {
        $query = $this->table('posts')
            ->orderByDesc('views')
            ->orderByDesc('id')
            ->limit($limit);

        if ($lastId !== null && $lastViews !== null) {
            $query->whereRaw('(views < ? OR (views = ? AND id < ?))', [
                $lastViews,
                $lastViews,
                $lastId,
            ]);
        }

        return $query->get();
    }

    public function getLatestPublishedPaginated(int $limit = 6, ?int $lastId = null, ?string $lastCreatedAt = null): array
    {
        $query = $this->table('posts')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($limit);

        if ($lastId !== null && $lastCreatedAt !== null) {
            $query->whereRaw('(created_at < ? OR (created_at = ? AND id < ?))', [
                $lastCreatedAt,
                $lastCreatedAt,
                $lastId,
            ]);
        }

        return $query->get();
    }

    public function getPaginatedByCategory(
        int $categoryId,
        int $page = 1,
        int $perPage = 6,
        string $sortBy = 'created_at',
        string $order = 'DESC'
    ): array {
        return $this->table('posts p')
            ->select(['p.*'])
            ->join('post_category pc', 'p.id', '=', 'pc.post_id')
            ->where('pc.category_id', '=', $categoryId)
            ->orderBy($sortBy, $order)
            ->forPage($page, $perPage)
            ->get();
    }
}