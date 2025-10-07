<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UrlShortenerApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_short_url_via_api()
    {
        // Arrange
        $payload = [
            'url' => 'https://www.example.com/very/long/url/path',
        ];

        // Act
        $response = $this->postJson('/api/short-urls', $payload);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'original_url',
                'short_url',
                'short_code',
                'created_at',
            ])
            ->assertJson([
                'original_url' => 'https://www.example.com/very/long/url/path',
            ]);

        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://www.example.com/very/long/url/path',
        ]);
    }

    /** @test */
    public function it_validates_url_is_required()
    {
        // Arrange
        $payload = [];

        // Act
        $response = $this->postJson('/api/short-urls', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['url']);
    }

    /** @test */
    public function it_validates_url_format()
    {
        // Arrange
        $payload = [
            'url' => 'not-a-valid-url',
        ];

        // Act
        $response = $this->postJson('/api/short-urls', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['url']);
    }

    /** @test */
    public function it_validates_url_max_length()
    {
        // Arrange
        $longUrl = 'https://example.com/' . str_repeat('a', 2500);
        $payload = [
            'url' => $longUrl,
        ];

        // Act
        $response = $this->postJson('/api/short-urls', $payload);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['url']);
    }

    /** @test */
    public function it_accepts_valid_http_urls()
    {
        // Arrange
        $payload = [
            'url' => 'http://www.example.com/path',
        ];

        // Act
        $response = $this->postJson('/api/short-urls', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('urls', [
            'original_url' => 'http://www.example.com/path',
        ]);
    }

    /** @test */
    public function it_accepts_valid_https_urls()
    {
        // Arrange
        $payload = [
            'url' => 'https://www.example.com/path',
        ];

        // Act
        $response = $this->postJson('/api/short-urls', $payload);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://www.example.com/path',
        ]);
    }

    /** @test */
    public function it_generates_unique_short_codes_for_same_url()
    {
        // Arrange
        $url = 'https://www.example.com/same-url';

        // Act
        $response1 = $this->postJson('/api/short-urls', ['url' => $url]);
        $response2 = $this->postJson('/api/short-urls', ['url' => $url]);

        // Assert
        $shortCode1 = $response1->json('short_code');
        $shortCode2 = $response2->json('short_code');

        $this->assertNotEquals($shortCode1, $shortCode2);
        $this->assertDatabaseCount('urls', 2);
    }

    /** @test */
    public function it_returns_short_code_with_exactly_6_characters()
    {
        // Arrange
        $payload = [
            'url' => 'https://www.example.com/test',
        ];

        // Act
        $response = $this->postJson('/api/short-urls', $payload);

        // Assert
        $response->assertStatus(201);
        $shortCode = $response->json('short_code');
        $this->assertEquals(6, strlen($shortCode));
    }

    /** @test */
    public function short_url_contains_short_code()
    {
        // Arrange
        $payload = [
            'url' => 'https://www.example.com/test',
        ];

        // Act
        $response = $this->postJson('/api/short-urls', $payload);

        // Assert
        $response->assertStatus(201);
        $shortCode = $response->json('short_code');
        $shortUrl = $response->json('short_url');
        $this->assertStringContainsString($shortCode, $shortUrl);
        $this->assertStringContainsString('/s/', $shortUrl);
    }
}
