<?php

namespace App\Repositories\Interfaces;

interface PostRepositoryInterface
{
    /**
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array;

    /**
     * @param string $slug
     * @return array|null
     */
    public function findBySlug(string $slug): ?array;

    /**
     * @param int $id
     * @return array|null
     */
    public function findWithCategories(int $id): ?array;

    /**
     * @param int $limit
     * @return array
     */
    public function getLatestPublished(int $limit = 6): array;

    /**
     * @param int $categoryId
     * @param int $limit
     * @return array
     */
    public function getLatestByCategory(int $categoryId, int $limit = 3): array;

    /**
     * @param int $categoryId
     * @param string $sortBy
     * @param string $order
     * @param int $limit
     * @param int|null $lastId
     * @param mixed|null $lastValue
     * @return array
     */
    public function getByCategory(int $categoryId, string $sortBy = 'created_at', string $order = 'DESC', int $limit = 10, ?int $lastId = null, $lastValue = null): array;

    /**
     * @param int $postId
     * @param array $categoryIds
     * @param int $limit
     * @return array
     */
    public function findSimilar(int $postId, array $categoryIds, int $limit = 3): array;

    /**
     * @param int $limit
     * @return array
     */
    public function getPopular(int $limit = 5): array;

    /**
     * @param int $id
     * @return bool
     */
    public function incrementViews(int $id): bool;

    /**
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * @param int $categoryId
     * @return int
     */
    public function getCountByCategory(int $categoryId): int;

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @deprecated Используйте getPaginatedWithCursor для лучшей производительности
     */
    public function getPaginated(int $page = 1, int $perPage = 10): array;

    /**
     * @param int $limit
     * @param int|null $lastId
     * @param int|null $lastViews
     * @return array
     */
    public function getPopularPaginated(int $limit = 6, ?int $lastId = null, ?int $lastViews = null): array;

    /**
     * @param int $limit
     * @param int|null $lastId
     * @param string|null $lastCreatedAt
     * @return array
     */
    public function getLatestPublishedPaginated(int $limit = 6, ?int $lastId = null, ?string $lastCreatedAt = null): array;
}