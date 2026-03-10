<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Post;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

/**
 *
 */
class CategoryService
{
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;
    /**
     * @var PostRepository
     */
    private PostRepository $postRepository;

    /**
     *
     */
    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
        $this->postRepository = new PostRepository();
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getCategoriesWithPostsPaginated(int $page = 1, int $perPage = 3): array
    {
        $categoriesData = $this->categoryRepository->getPaginatedWithPosts($page, $perPage);
        $totalCategories = $this->categoryRepository->getTotalWithPostsCount();

        $categories = [];
        foreach ($categoriesData as $categoryData) {
            $latestPostsData = $this->postRepository->getLatestByCategory(
                $categoryData['id'],
                3
            );

            $latestPosts = [];
            foreach ($latestPostsData as $postData) {
                $postCategories = $this->getCategoriesForPost($postData['id']);
                $postData['categories'] = $postCategories;
                $latestPosts[] = Post::fromArray($postData);
            }

            $category = Category::fromArray($categoryData);
            $category->posts = $latestPosts;
            $categories[] = $category;
        }

        return [
            'categories' => $categories,
            'total' => $totalCategories,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($totalCategories / $perPage)
        ];
    }

    /**
     * @param int $postId
     * @return array
     */
    private function getCategoriesForPost(int $postId): array
    {
        $categoriesData = $this->categoryRepository->getByPostId($postId);

        return array_map(
            fn($data) => Category::fromArray($data),
            $categoriesData
        );
    }

    /**
     * @param string $slug
     * @param string $sortBy
     * @param string $order
     * @param int $page
     * @param int $perPage
     * @return array|null
     */
    public function getCategoryBySlugWithPosts(string $slug, string $sortBy = 'created_at', string $order = 'DESC', int $page = 1, int $perPage = 10): ?array
    {
        $categoryData = $this->categoryRepository->findBySlug($slug);

        if (!$categoryData) {
            return null;
        }

        $id = $categoryData['id'];
        $offset = ($page - 1) * $perPage;

        $postsData = $this->categoryRepository->getWithPostsById($id, $sortBy, $order, $perPage, $offset);
        $totalPosts = $this->postRepository->getCountByCategory($id);

        $posts = [];
        foreach ($postsData as $postData) {
            $categories = $this->getCategoriesForPost($postData['id']);
            $postData['categories'] = $categories;
            $posts[] = Post::fromArray($postData);
        }

        $categoryData['posts_count'] = $totalPosts;
        $category = Category::fromArray($categoryData);

        return [
            'category' => $category,
            'posts' => $posts,
            'total' => $totalPosts,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($totalPosts / $perPage)
        ];
    }
}