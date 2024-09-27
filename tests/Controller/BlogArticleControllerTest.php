<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogArticleControllerTest extends WebTestCase
{ private static $client;
    private string $apiUrl;

    public static function setUpBeforeClass(): void
    {
        self::$client = static::createClient();
    }

    protected function setUp(): void
    {
        $this->apiUrl = 'http://127.0.0.1:8000/api/blog-articles';
    }

    public function testCreateBlogArticle()
    {
        $data = [
            'title' => 'Sample Blog Article',
            'content' => 'This is a sample blog article.',
            'keywords' => ['sample', 'blog', 'article'],
            'authorId' => 1,
        ];

        self::$client->request('POST', $this->apiUrl, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testGetBlogArticle()
    {
        $articleId = 1; // Ensure this article exists

        self::$client->request('GET', $this->apiUrl . '/' . $articleId);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['id' => $articleId]);
    }

    public function testPatchBlogArticle()
    {
        $articleId = 1; // Ensure this article exists

        $data = [
            'title' => 'Updated Blog Article Title',
        ];

        self::$client->request('PATCH', $this->apiUrl . '/' . $articleId, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(204); // No content on success
    }

    public function testPublishBlogArticle()
    {
        $articleId = 1; // Ensure this article exists

        $data = [
            'status' => 'published',
        ];

        self::$client->request('PATCH', $this->apiUrl . '/blog-article-publish/' . $articleId, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(204); // No content on success
    }

    public function testDeleteBlogArticle()
    {
        $articleId = 1; // Ensure this article exists

        self::$client->request('DELETE', $this->apiUrl . '/' . $articleId, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(204); // No content on success
    }
}
