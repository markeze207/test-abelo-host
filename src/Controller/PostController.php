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

        $similarPosts = $this->postService->getSimilarPosts($post->id, 3);

        $this->render('post/view.tpl', [
            'post' => $post,
            'similar_posts' => $similarPosts,
            'page_title' => $post->title
        ]);
    }

    public function loadMorePopular(): void
    {
        $page = (int)($this->getParam('page', 1));
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $posts = $this->postService->getPopularPostsPaginated($limit, $offset);

        $this->json([
            'success' => true,
            'posts' => array_map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->getExcerpt(100),
                    'image' => $post->getImageUrl(),
                    'url' => $post->getUrl(),
                    'views' => $post->views,
                    'created_at' => $post->getFormattedDate('d.m.Y')
                ];
            }, $posts),
            'has_more' => count($posts) === $limit
        ]);
    }

    public function loadMoreLatest(): void
    {
        $page = (int)($this->getParam('page', 1));
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $posts = $this->postService->getLatestPostsPaginated($limit, $offset);

        $this->json([
            'success' => true,
            'posts' => array_map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->getExcerpt(100),
                    'image' => $post->getImageUrl(),
                    'url' => $post->getUrl(),
                    'views' => $post->views,
                    'created_at' => $post->getFormattedDate('d.m.Y')
                ];
            }, $posts),
            'has_more' => count($posts) === $limit
        ]);
    }
}