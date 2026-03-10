<?php

namespace App\Repositories;

use App\Core\QueryBuilder;
use App\Repositories\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    private QueryBuilder $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder('posts');
    }

    public function getLatestPublished(int $limit = 6): array
    {
        return $this->queryBuilder
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function findById(int $id): ?array
    {
        return $this->queryBuilder
            ->where('id', '=', $id)
            ->first();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->queryBuilder
            ->where('slug', '=', $slug)
            ->first();
    }

    public function findWithCategories(int $id): ?array
    {
        $post = $this->findById($id);

        if ($post) {
            $categories = (new QueryBuilder('categories c'))
                ->select(['c.*'])
                ->join('post_category pc', 'c.id', '=', 'pc.category_id')
                ->where('pc.post_id', '=', $id)
                ->orderBy('c.name')
                ->get();

            $post['categories'] = $categories;
        }

        return $post;
    }

    public function getLatestByCategory(int $categoryId, int $limit = 3): array
    {
        return (new QueryBuilder('posts p'))
            ->select(['p.*'])
            ->join('post_category pc', 'p.id', '=', 'pc.post_id')
            ->where('pc.category_id', '=', $categoryId)
            ->orderByDesc('p.created_at')
            ->limit($limit)
            ->get();
    }

    public function getByCategory(int $categoryId, string $sortBy = 'created_at', string $order = 'DESC', int $limit = 10, int $offset = 0): array
    {
        $allowedSort = ['views', 'created_at', 'title'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        return (new QueryBuilder('posts p'))
            ->select(['p.*'])
            ->join('post_category pc', 'p.id', '=', 'pc.post_id')
            ->where('pc.category_id', '=', $categoryId)
            ->orderBy("p.$sortBy", $order)
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function findSimilar(int $postId, array $categoryIds, int $limit = 3): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        return (new QueryBuilder('posts p'))
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
        $query = addslashes($query);

        return (new QueryBuilder('posts p'))
            ->select(['p.*', "MATCH(p.title, p.content) AGAINST('$query' IN NATURAL LANGUAGE MODE) as relevance"])
            ->whereRaw("MATCH(p.title, p.content) AGAINST('$query' IN NATURAL LANGUAGE MODE)")
            ->orderByDesc('relevance')
            ->limit(20)
            ->get();
    }

    public function getPopular(int $limit = 5): array
    {
        return $this->queryBuilder
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    public function incrementViews(int $id): bool
    {
        return $this->queryBuilder
                ->where('id', '=', $id)
                ->increment('views') > 0;
    }

    public function getTotalCount(): int
    {
        return $this->queryBuilder->count();
    }

    public function getCountByCategory(int $categoryId): int
    {
        return (new QueryBuilder('post_category'))
            ->where('category_id', '=', $categoryId)
            ->count();
    }

    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        return $this->queryBuilder
            ->select(['*'])
            ->orderByDesc('created_at')
            ->limit($perPage)
            ->offset($offset)
            ->get();
    }

    public function getPopularPaginated(int $limit = 6, int $offset = 0): array
    {
        return $this->queryBuilder
            ->orderByDesc('views')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function getLatestPublishedPaginated(int $limit = 6, int $offset = 0): array
    {
        return $this->queryBuilder
            ->orderByDesc('created_at')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }
}