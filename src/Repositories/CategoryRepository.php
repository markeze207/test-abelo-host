<?php
namespace App\Repositories;

use App\Core\QueryBuilder;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var QueryBuilder
     */
    private QueryBuilder $queryBuilder;

    /**
     *
     */
    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder('categories');
    }

    /**
     * @param int $postId
     * @return array
     */
    public function getByPostId(int $postId): array
    {
        return (new QueryBuilder('categories c'))
            ->select(['c.*'])
            ->join('post_category pc', 'c.id', '=', 'pc.category_id')
            ->where('pc.post_id', '=', $postId)
            ->orderBy('c.name')
            ->get();
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getPopular(int $limit = 5): array
    {
        return (new QueryBuilder('categories c'))
            ->select(['c.*', 'COUNT(p.id) as posts_count'])
            ->leftJoin('post_category pc', 'c.id', '=', 'pc.category_id')
            ->leftJoin('posts p', 'pc.post_id', '=', 'p.id')
            ->groupBy('c.id')
            ->orderByDesc('posts_count')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->queryBuilder
            ->orderBy('name')
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
     * @return array
     */
    public function getWithPosts(): array
    {
        return (new QueryBuilder('categories c'))
            ->select(['c.*', 'COUNT(p.id) as posts_count'])
            ->leftJoin('post_category pc', 'c.id', '=', 'pc.category_id')
            ->leftJoin('posts p', 'pc.post_id', '=', 'p.id')
            ->groupBy('c.id')
            ->having('posts_count > 0')
            ->orderBy('c.name')
            ->get();
    }

    /**
     * @param int $limit
     * @param int|null $lastId
     * @param string|null $lastName
     * @return array
     */
    public function getPaginatedWithPosts(int $limit = 10, ?int $lastId = null, ?string $lastName = null): array
    {
        $query = (new QueryBuilder('categories c'))
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
                $lastId
            ]);
        }

        return $query->get();
    }


    /**
     * @return int
     */
    public function getTotalWithPostsCount(): int
    {
        $result = (new QueryBuilder('categories c'))
            ->select(['COUNT(DISTINCT c.id) as total'])
            ->join('post_category pc', 'c.id', '=', 'pc.category_id')
            ->join('posts p', 'pc.post_id', '=', 'p.id')
            ->first();

        return $result ? (int)$result['total'] : 0;
    }

    /**
     * @param int $id
     * @param string $sortBy
     * @param string $order
     * @param int $limit
     * @param int|null $lastId
     * @param mixed|null $lastValue
     * @return array
     */
    public function getWithPostsById(int $id, string $sortBy = 'created_at', string $order = 'DESC', int $limit = 10, ?int $lastId = null, $lastValue = null): array
    {
        $allowedSort = ['views', 'created_at', 'title'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $query = (new QueryBuilder('posts p'))
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
                $lastId
            ]);
        }

        return $query->get();
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
    public function getPostCount(int $categoryId): int
    {
        return (new QueryBuilder('post_category'))
            ->where('category_id', '=', $categoryId)
            ->count();
    }

    /**
     * @param int $postsLimit
     * @return array
     */
    public function getWithLatestPosts(int $postsLimit = 3): array
    {
        $categories = $this->getWithPosts();

        foreach ($categories as &$category) {
            $latestPosts = (new QueryBuilder('posts p'))
                ->select(['p.*'])
                ->join('post_category pc', 'p.id', '=', 'pc.post_id')
                ->where('pc.category_id', '=', $category['id'])
                ->orderByDesc('p.created_at')
                ->limit($postsLimit)
                ->get();

            $category['latest_posts'] = $latestPosts;
            $category['posts'] = $latestPosts;
        }

        return $categories;
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPaginated(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        return $this->queryBuilder
            ->select(['*'])
            ->orderBy('name')
            ->limit($perPage)
            ->offset($offset)
            ->get();
    }
}