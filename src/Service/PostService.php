<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Post;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;

class PostService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getHomepagePosts(int $limit = 6): array
    {
        $postsData = $this->postRepository->getLatestPublished($limit);

        $posts = [];
        foreach ($postsData as $postData) {
            $postData['categories'] = $this->getCategoriesForPost($postData['id']);
            $posts[]                = Post::fromArray($postData);
        }

        return $posts;
    }

    public function getSimilarPosts(int $postId, int $limit = 3): array
    {
        $post = $this->postRepository->findById($postId);

        if (!$post) {
            return [];
        }

        $categoryIds = array_column(
            $this->categoryRepository->getByPostId($postId),
            'id'
        );

        if (empty($categoryIds)) {
            return [];
        }

        $similarPostsData = $this->postRepository->findSimilar($postId, $categoryIds, $limit);

        $similarPosts = [];
        foreach ($similarPostsData as $postData) {
            $postData['categories'] = $this->getCategoriesForPost($postData['id']);
            $similarPosts[]         = Post::fromArray($postData);
        }

        return $similarPosts;
    }

    public function getPopularPosts(int $limit = 5): array
    {
        return array_map(
            fn($data) => Post::fromArray($data),
            $this->postRepository->getPopular($limit)
        );
    }

    public function getTotalPublishedCount(): int
    {
        return $this->postRepository->getTotalCount();
    }

    public function getPopularPostsPaginated(int $limit = 6, ?int $lastId = null, ?int $lastViews = null): array
    {
        $posts = array_map(
            fn($data) => Post::fromArray($data),
            $this->postRepository->getPopularPaginated($limit, $lastId, $lastViews)
        );

        $hasMore    = count($posts) === $limit;
        $nextCursor = null;

        if ($hasMore && !empty($posts)) {
            $lastPost   = end($posts);
            $nextCursor = [
                'last_id'    => $lastPost->id,
                'last_views' => $lastPost->views,
            ];
        }

        return ['posts' => $posts, 'has_more' => $hasMore, 'next_cursor' => $nextCursor];
    }

    public function getLatestPostsPaginated(int $limit = 6, ?int $lastId = null, ?string $lastCreatedAt = null): array
    {
        $posts = array_map(
            fn($data) => Post::fromArray($data),
            $this->postRepository->getLatestPublishedPaginated($limit, $lastId, $lastCreatedAt)
        );

        $hasMore    = count($posts) === $limit;
        $nextCursor = null;

        if ($hasMore && !empty($posts)) {
            $lastPost   = end($posts);
            $nextCursor = [
                'last_id'         => $lastPost->id,
                'last_created_at' => $lastPost->created_at,
            ];
        }

        return ['posts' => $posts, 'has_more' => $hasMore, 'next_cursor' => $nextCursor];
    }

    public function getPostBySlug(string $slug): ?Post
    {
        $postData = $this->postRepository->findBySlug($slug);

        if (!$postData) {
            return null;
        }

        $postData['categories'] = $this->getCategoriesForPost($postData['id']);

        return Post::fromArray($postData);
    }

    public function incrementPostViews(int $postId): bool
    {
        return $this->postRepository->incrementViews($postId);
    }


    private function getCategoriesForPost(int $postId): array
    {
        return array_map(
            fn($cat) => Category::fromArray($cat),
            $this->categoryRepository->getByPostId($postId)
        );
    }
}