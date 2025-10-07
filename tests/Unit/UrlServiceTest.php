<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UrlService;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UrlServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UrlService $urlService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->urlService = new UrlService();
    }

    /** @test */
    public function it_can_create_a_short_url()
    {
        // Arrange
        $originalUrl = 'https://www.example.com/very/long/url/path';

        // Act
        $shortUrl = $this->urlService->createShortUrl($originalUrl);

        // Assert
        $this->assertNotNull($shortUrl);
        $this->assertStringContainsString('/s/', $shortUrl);
        $this->assertDatabaseHas('urls', [
            'original_url' => $originalUrl,
        ]);
    }

    /** @test */
    public function it_generates_a_unique_short_code()
    {
        // Arrange
        $url1 = 'https://www.example.com/first-url';
        $url2 = 'https://www.example.com/second-url';

        // Act
        $shortUrl1 = $this->urlService->createShortUrl($url1);
        $shortUrl2 = $this->urlService->createShortUrl($url2);

        // Assert
        $this->assertNotEquals($shortUrl1, $shortUrl2);
    }

    /** @test */
    public function short_code_is_exactly_6_characters()
    {
        // Arrange
        $originalUrl = 'https://www.example.com/test';

        // Act
        $shortUrl = $this->urlService->createShortUrl($originalUrl);

        // Assert
        $urlRecord = Url::where('original_url', $originalUrl)->first();
        $this->assertNotNull($urlRecord);
        $this->assertEquals(6, strlen($urlRecord->short_code));
    }

    /** @test */
    public function it_stores_url_in_database()
    {
        // Arrange
        $originalUrl = 'https://www.laravel.com/docs';

        // Act
        $this->urlService->createShortUrl($originalUrl);

        // Assert
        $this->assertDatabaseCount('urls', 1);
        $urlRecord = Url::first();
        $this->assertEquals($originalUrl, $urlRecord->original_url);
        $this->assertNotNull($urlRecord->short_code);
    }

    /** @test */
    public function it_returns_full_short_url_with_domain()
    {
        // Arrange
        $originalUrl = 'https://www.example.com/test';

        // Act
        $shortUrl = $this->urlService->createShortUrl($originalUrl);

        // Assert
        $this->assertStringStartsWith('http', $shortUrl);
        $this->assertMatchesRegularExpression('/\/s\/[a-zA-Z0-9]{6}$/', $shortUrl);
    }
}
