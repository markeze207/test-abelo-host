<?php

namespace App\Controller;

use App\Service\PostService;

class PostController extends BaseController
{
    private PostService $postService;

    public function __construct()
    {
        parent::__construct();
        $this->postService = new PostService();
    }

    public function view(string $slug): void
    {
        if (!$this->validate(['slug' => $slug], [
            'slug' => 'required|regex:/^[a-z0-9-]+$/|max:100'
        ])) {
            $this->notFound();
            return;
        }

        $post = $this->postService->getPostBySlug($slug);

        if (!$post) {
            $this->notFound();
            return;
        }

        $this->postService->incrementPostViews($post->id);

//        if (!$this->session->has('post_viewed_' . $post->id)) {
//             $this->postService->incrementPostViews($post->id);
//             $this->session->set('post_viewed_' . $post->id, true);
//        }

        $similarPosts = $this->postService->getSimilarPosts($post->id, 3);

        $this->render('post/view.tpl', [
            'post' => $post,
            'similar_posts' => $similarPosts,
            'page_title' => $post->title
        ]);
    }

    public function loadMorePopular(): void
    {
        $lastId = $this->getParam('last_id') ? (int)$this->getParam('last_id') : null;
        $lastViews = $this->getParam('last_views') ? (int)$this->getParam('last_views') : null;
        $limit = 6;

        if ($lastId === null || $lastViews === null) {
            $postsData = $this->postService->getPopularPostsPaginated($limit, null, null);
        } else {
            $postsData = $this->postService->getPopularPostsPaginated($limit, $lastId, $lastViews);
        }

        $posts = $postsData['posts'] ?? [];
        $nextCursor = $postsData['next_cursor'] ?? null;

        $this->json([
            'success' => true,
            'posts' => array_map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->getExcerpt(100),
                    'image' => $post->getImageUrl(),
                    'url' => $post->getUrl(),
                    'views' => $post->views,
                    'created_at' => $post->getFormattedDate('d.m.Y')
                ];
            }, $posts),
            'has_more' => count($posts) === $limit,
            'next_cursor' => $nextCursor
        ]);
    }

    public function loadMoreLatest(): void
    {
        $lastId = $this->getParam('last_id') ? (int)$this->getParam('last_id') : null;
        $lastCreatedAt = $this->getParam('last_created_at');
        $limit = 6;

        if ($lastId === null || $lastCreatedAt === null) {
            $postsData = $this->postService->getLatestPostsPaginated($limit, null, null);
        } else {
            $postsData = $this->postService->getLatestPostsPaginated($limit, $lastId, $lastCreatedAt);
        }

        $posts = $postsData['posts'] ?? [];
        $nextCursor = $postsData['next_cursor'] ?? null;

        $this->json([
            'success' => true,
            'posts' => array_map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->getExcerpt(100),
                    'image' => $post->getImageUrl(),
                    'url' => $post->getUrl(),
                    'views' => $post->views,
                    'created_at' => $post->getFormattedDate('d.m.Y')
                ];
            }, $posts),
            'has_more' => count($posts) === $limit,
            'next_cursor' => $nextCursor
        ]);
    }
}