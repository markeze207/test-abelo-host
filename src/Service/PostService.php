<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\Category;
use App\Repositories\PostRepository;
use App\Repositories\CategoryRepository;

/**
 *
 */
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
     * @param int $offset
     * @return array
     */
    public function getPopularPostsPaginated(int $limit = 6, int $offset = 0): array
    {
        $postsData = $this->postRepository->getPopularPaginated($limit, $offset);

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
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getLatestPostsPaginated(int $limit = 6, int $offset = 0): array
    {
        $postsData = $this->postRepository->getLatestPublishedPaginated($limit, $offset);

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
}