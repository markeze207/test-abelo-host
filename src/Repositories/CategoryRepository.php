<?php

namespace App\Repositories;

use App\Core\QueryBuilder;
use App\Factory\QueryBuilderFactory;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    private const TABLE = 'categories';
    private const TABLE_ALIASED = 'categories c';
    private QueryBuilderFactory $factory;

    public function __construct(QueryBuilderFactory $factory)
    {
        $this->factory = $factory;
    }

    private function table(string $table): QueryBuilder
    {
        return $this->factory->create($table);
    }

    public function getByPostId(int $postId): array
    {
        return $this->table(self::TABLE_ALIASED)
            ->select(['c.*'])
            ->join('post_category pc', 'c.id', '=', 'pc.category_id')
            ->where('pc.post_id', '=', $postId)
            ->orderBy('c.name')
            ->get();
    }

    public function getPopular(int $limit = 5): array
    {
        return $this->table(self::TABLE_ALIASED)
            ->select(['c.*', 'COUNT(p.id) as posts_count'])
            ->leftJoin('post_category pc', 'c.id', '=', 'pc.category_id')
            ->leftJoin('posts p', 'pc.post_id', '=', 'p.id')
            ->groupBy('c.id')
            ->orderByDesc('posts_count')
            ->limit($limit)
            ->get();
    }

    public function getAll(): array
    {
        return $this->table(self::TABLE)
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?array
    {
        return $this->table(self::TABLE)
            ->where('id', '=', $id)
            ->first();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->table(self::TABLE)
            ->where('slug', '=', $slug)
            ->first();
    }

    public function getWithPosts(): array
    {
        return $this->table(self::TABLE_ALIASED)
            ->select(['c.*', 'COUNT(p.id) as posts_count'])
            ->leftJoin('post_category pc', 'c.id', '=', 'pc.category_id')
            ->leftJoin('posts p', 'pc.post_id', '=', 'p.id')
            ->groupBy('c.id')
            ->having('posts_count > 0')
            ->orderBy('c.name')
            ->get();
    }

    public function getPaginatedWithPosts(int $limit = 10, ?int $lastId = null, ?string $lastName = null): array
    {
        $query = $this->table(self::TABLE_ALIASED)
            ->select(['c.*', 'COUNT(p.id) as posts_count'])
            ->leftJoin('post_category pc', 'c.id', '=', 'pc.category_id')
            ->leftJoin('posts p', 'pc.post_id', '=', 'p.id')
            ->groupBy('c.id')
            ->having('posts_count > 0')
            ->orderBy('c.name')
            ->orderBy('c.id')
            ->limit($limit);

        if ($lastId !== null && $lastName !== null) {
            $query->whereRaw('(c.name > ? OR (c.name = ? AND c.id > ?))', [
                $lastName,
                $lastName,
                $lastId,
            ]);
        }

        return $query->get();
    }

    public function getTotalWithPostsCount(): int
    {
        $result = $this->table(self::TABLE_ALIASED)
            ->select(['COUNT(DISTINCT c.id) as total'])
            ->join('post_category pc', 'c.id', '=', 'pc.category_id')
            ->join('posts p', 'pc.post_id', '=', 'p.id')
            ->first();

        return $result ? (int) $result['total'] : 0;
    }

    public function getWithPostsById(
        int $id,
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
            ->where('pc.category_id', '=', $id)
            ->orderBy("p.$sortBy", $order)
            ->orderBy('p.id', $order)
            ->limit($limit);

        if ($lastId !== null && $lastValue !== null) {
            $comparison = $order === 'ASC' ? '>' : '<';
            $query->whereRaw("(p.$sortBy $comparison ? OR (p.$sortBy = ? AND p.id $comparison ?))", [
                $lastValue,
                $lastValue,
                $lastId,
            ]);
        }

        return $query->get();
    }

    public function getTotalCount(): int
    {
        return $this->table(self::TABLE)->count();
    }

    public function getPostCount(int $categoryId): int
    {
        return $this->table('post_category')
            ->where('category_id', '=', $categoryId)
            ->count();
    }

    public function getWithLatestPosts(int $postsLimit = 3): array
    {
        $categories = $this->getWithPosts();

        foreach ($categories as &$category) {
            $latestPosts = $this->table('posts p')
                ->select(['p.*'])
                ->join('post_category pc', 'p.id', '=', 'pc.post_id')
                ->where('pc.category_id', '=', $category['id'])
                ->orderByDesc('p.created_at')
                ->limit($postsLimit)
                ->get();

            $category['latest_posts'] = $latestPosts;
            $category['posts']        = $latestPosts;
        }

        return $categories;
    }

    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        return $this->table(self::TABLE)
            ->select(['*'])
            ->orderBy('name')
            ->forPage($page, $perPage)
            ->get();
    }
}