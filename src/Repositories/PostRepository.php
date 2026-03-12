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

    /**
     * @param int $limit
     * @return array
     */
    public function getLatestPublished(int $limit = 6): array
    {
        return $this->queryBuilder
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        return $this->queryBuilder
            ->where('id', '=', $id)
            ->first();
    }

    /**
     * @param string $slug
     * @return array|null
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->queryBuilder
            ->where('slug', '=', $slug)
            ->first();
    }

    /**
     * @param int $id
     * @return array|null
     */
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

    /**
     * @param int $categoryId
     * @param int $limit
     * @return array
     */
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

    /**
     * @param int $categoryId
     * @param string $sortBy
     * @param string $order
     * @param int $limit
     * @param int|null $lastId
     * @param mixed|null $lastValue
     * @return array
     */
    public function getByCategory(
        int $categoryId,
        string $sortBy = 'created_at',
        string $order = 'DESC',
        int $limit = 10,
        ?int $lastId = null,
        $lastValue = null
    ): array {
        $allowedSort = ['views', 'created_at', 'title'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $query = (new QueryBuilder('posts p'))
            ->select(['p.*'])
            ->join('post_category pc', 'p.id', '=', 'pc.post_id')
            ->where('pc.category_id', '=', $categoryId);

        if ($lastId !== null && $lastValue !== null) {
            $operator = $order === 'DESC' ? '<' : '>';
            $query->whereRaw("(p.$sortBy $operator ? OR (p.$sortBy = ? AND p.id < ?))", [
                $lastValue,
                $lastValue,
                $lastId
            ]);
        }

        return $query
            ->orderBy("p.$sortBy", $order)
            ->orderByDesc('p.id')
            ->limit($limit)
            ->get();
    }

    /**
     * @param int $postId
     * @param array $categoryIds
     * @param int $limit
     * @return array
     */
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

    /**
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        $safeQuery = addslashes($query);

        return (new QueryBuilder('posts p'))
            ->select(['p.*', "MATCH(p.title, p.content) AGAINST('$safeQuery' IN NATURAL LANGUAGE MODE) as relevance"])
            ->whereRaw("MATCH(p.title, p.content) AGAINST('$safeQuery' IN NATURAL LANGUAGE MODE)")
            ->orderByDesc('relevance')
            ->limit(20)
            ->get();
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getPopular(int $limit = 5): array
    {
        return $this->queryBuilder
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function incrementViews(int $id): bool
    {
        return $this->queryBuilder
                ->where('id', '=', $id)
                ->increment('views') > 0;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->queryBuilder->count();
    }

    /**
     * @param int $categoryId
     * @return int
     */
    public function getCountByCategory(int $categoryId): int
    {
        return (new QueryBuilder('post_category'))
            ->where('category_id', '=', $categoryId)
            ->count();
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @deprecated Используйте getPaginatedWithCursor для лучшей производительности
     */
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

    /**
     * @param int $limit
     * @param int|null $lastId
     * @param int|null $lastViews
     * @return array
     */
    public function getPopularPaginated(int $limit = 6, ?int $lastId = null, ?int $lastViews = null): array
    {
        $query = (new QueryBuilder('posts'))
            ->orderByDesc('views')
            ->orderByDesc('id')
            ->limit($limit);

        if ($lastId !== null && $lastViews !== null) {
            $query->whereRaw('(views < ? OR (views = ? AND id < ?))', [
                $lastViews,
                $lastViews,
                $lastId
            ]);
        }

        return $query->get();
    }

    /**
     * @param int $limit
     * @param int|null $lastId
     * @param string|null $lastCreatedAt
     * @return array
     */
    public function getLatestPublishedPaginated(int $limit = 6, ?int $lastId = null, ?string $lastCreatedAt = null): array
    {
        $query = (new QueryBuilder('posts'))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($limit);

        if ($lastId !== null && $lastCreatedAt !== null) {
            $query->whereRaw('(created_at < ? OR (created_at = ? AND id < ?))', [
                $lastCreatedAt,
                $lastCreatedAt,
                $lastId
            ]);
        }

        return $query->get();
    }

    /**
     * @param int $categoryId
     * @param int $page
     * @param int $perPage
     * @param string $sortBy
     * @param string $order
     * @return array
     */
    public function getPaginatedByCategory(
        int $categoryId,
        int $page = 1,
        int $perPage = 6,
        string $sortBy = 'created_at',
        string $order = 'DESC'
    ): array {
        $offset = ($page - 1) * $perPage;

        return (new QueryBuilder('posts p'))
            ->select(['p.*'])
            ->join('post_category pc', 'p.id', '=', 'pc.post_id')
            ->where('pc.category_id', '=', $categoryId)
            ->orderBy($sortBy, $order)
            ->limit($perPage)
            ->offset($offset)
            ->get();
    }

}