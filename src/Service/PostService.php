<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\Category;
use App\Repositories\PostRepository;
use App\Repositories\CategoryRepository;

class PostService
{
    /**
     * @var PostRepository
     */
    private PostRepository $postRepository;
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    /**
     *
     */
    public function __construct()
    {
        $this->postRepository = new PostRepository();
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getHomepagePosts(int $limit = 6): array
    {
        $postsData = $this->postRepository->getLatestPublished($limit);

        $posts = [];
        foreach ($postsData as $postData) {
            $categories = $this->categoryRepository->getByPostId($postData['id']);
            $postData['categories'] = array_map(
                fn($cat) => Category::fromArray($cat),
                $categories
            );
            $posts[] = Post::fromArray($postData);
        }

        return $posts;
    }

    /**
     * @param int $postId
     * @param int $limit
     * @return array
     */
    public function getSimilarPosts(int $postId, int $limit = 3): array
    {
        $post = $this->postRepository->findById($postId);

        if (!$post) {
            return [];
        }

        $categories = $this->categoryRepository->getByPostId($postId);
        $categoryIds = array_column($categories, 'id');

        if (empty($categoryIds)) {
            return [];
        }

        $similarPostsData = $this->postRepository->findSimilar($postId, $categoryIds, $limit);

        $similarPosts = [];
        foreach ($similarPostsData as $postData) {
            $postCategories = $this->categoryRepository->getByPostId($postData['id']);
            $postData['categories'] = array_map(
                fn($cat) => Category::fromArray($cat),
                $postCategories
            );
            $similarPosts[] = Post::fromArray($postData);
        }

        return $similarPosts;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getPopularPosts(int $limit = 5): array
    {
        $postsData = $this->postRepository->getPopular($limit);

        return array_map(
            fn($data) => Post::fromArray($data),
            $postsData
        );
    }

    /**
     * @return int
     */
    public function getTotalPublishedCount(): int
    {
        return $this->postRepository->getTotalCount();
    }

    /**
     * @param int $limit
     * @param int|null $lastId
     * @param int|null $lastViews
     * @return array
     */
    public function getPopularPostsPaginated(int $limit = 6, ?int $lastId = null, ?int $lastViews = null): array
    {
        $postsData = $this->postRepository->getPopularPaginated($limit, $lastId, $lastViews);

        $posts = [];
        foreach ($postsData as $data) {
            $posts[] = Post::fromArray($data);
        }

        $hasMore = count($posts) === $limit;
        $nextCursor = null;

        if ($hasMore && !empty($posts)) {
            $lastPost = end($posts);
            $nextCursor = [
                'last_id' => $lastPost->id,
                'last_views' => $lastPost->views
            ];
        }

        return [
            'posts' => $posts,
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor
        ];
    }

    /**
     * @param int $limit
     * @param int|null $lastId
     * @param string|null $lastCreatedAt
     * @return array
     */
    public function getLatestPostsPaginated(int $limit = 6, ?int $lastId = null, ?string $lastCreatedAt = null): array
    {
        $postsData = $this->postRepository->getLatestPublishedPaginated($limit, $lastId, $lastCreatedAt);

        $posts = [];
        foreach ($postsData as $data) {
            $posts[] = Post::fromArray($data);
        }

        $hasMore = count($posts) === $limit;
        $nextCursor = null;

        if ($hasMore && !empty($posts)) {
            $lastPost = end($posts);
            $nextCursor = [
                'last_id' => $lastPost->id,
                'last_created_at' => $lastPost->created_at
            ];
        }

        return [
            'posts' => $posts,
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor
        ];
    }

    /**
     * @param string $slug
     * @return Post|null
     */
    public function getPostBySlug(string $slug): ?Post
    {
        $postData = $this->postRepository->findBySlug($slug);

        if (!$postData) {
            return null;
        }

        $categories = $this->categoryRepository->getByPostId($postData['id']);
        $postData['categories'] = array_map(
            fn($cat) => Category::fromArray($cat),
            $categories
        );

        return Post::fromArray($postData);
    }

    /**
     * @param int $postId
     * @return bool
     */
    public function incrementPostViews(int $postId): bool
    {
        return $this->postRepository->incrementViews($postId);
    }

}