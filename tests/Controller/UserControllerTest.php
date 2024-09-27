<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testUserCreation(): void
    {
        $client = static::createClient();

        // Prepare the data to send in JSON format
        $data = [
            'email' => 'test6@example.com',
            'fullName' => 'Test User',
            'password' => 'password123',
        ];

        // Convert the data to JSON
        $jsonData = json_encode($data);

        // Send the POST request with the correct headers
        $client->request(
            'POST',
            '/api/user-create',
            [], // parameters array
            [], // files array
            ['CONTENT_TYPE' => 'application/ld+json'], // headers
            $jsonData // JSON data
        );

        // Assert the response status and content
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'email' => 'test6@example.com'
        ]);
    }
}
