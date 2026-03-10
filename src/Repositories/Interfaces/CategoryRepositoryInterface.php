<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    /**
     * @return array
     */
    public function getAll(): array;

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
     * @return array
     */
    public function getWithPosts(): array;

    /**
     * @param int $id
     * @param string $sortBy
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getWithPostsById(int $id, string $sortBy = 'created_at', string $order = 'DESC', int $limit = 10, int $offset = 0): array;

    /**
     * @param int $categoryId
     * @return int
     */
    public function getPostCount(int $categoryId): int;

    /**
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * @param int $limit
     * @return array
     */
    public function getPopular(int $limit = 5): array;

    /**
     * @param int $postId
     * @return array
     */
    public function getByPostId(int $postId): array;

    /**
     * @param int $postsLimit
     * @return array
     */
    public function getWithLatestPosts(int $postsLimit = 3): array;

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPaginated(int $page = 1, int $perPage = 10): array;
}