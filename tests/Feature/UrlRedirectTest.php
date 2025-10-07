<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UrlRedirectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_redirects_to_original_url_with_valid_short_code()
    {
        // Arrange
        $url = Url::create([
            'original_url' => 'https://www.example.com/original',
            'short_code' => 'abc123',
        ]);

        // Act
        $response = $this->get('/s/abc123');

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect('https://www.example.com/original');
    }

    /** @test */
    public function it_returns_404_for_invalid_short_code()
    {
        // Act
        $response = $this->get('/s/invalid');

        // Assert
        $response->assertStatus(404);
    }

    /** @test */
    public function it_redirects_to_http_urls()
    {
        // Arrange
        $url = Url::create([
            'original_url' => 'http://www.example.com/http-url',
            'short_code' => 'http01',
        ]);

        // Act
        $response = $this->get('/s/http01');

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect('http://www.example.com/http-url');
    }

    /** @test */
    public function it_redirects_to_https_urls()
    {
        // Arrange
        $url = Url::create([
            'original_url' => 'https://www.secure.com/path',
            'short_code' => 'https1',
        ]);

        // Act
        $response = $this->get('/s/https1');

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect('https://www.secure.com/path');
    }

    /** @test */
    public function it_redirects_to_urls_with_query_parameters()
    {
        // Arrange
        $url = Url::create([
            'original_url' => 'https://www.example.com/page?param1=value1&param2=value2',
            'short_code' => 'query1',
        ]);

        // Act
        $response = $this->get('/s/query1');

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect('https://www.example.com/page?param1=value1&param2=value2');
    }

    /** @test */
    public function it_redirects_to_urls_with_fragments()
    {
        // Arrange
        $url = Url::create([
            'original_url' => 'https://www.example.com/page#section',
            'short_code' => 'frag01',
        ]);

        // Act
        $response = $this->get('/s/frag01');

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect('https://www.example.com/page#section');
    }

    /** @test */
    public function it_handles_multiple_redirects_independently()
    {
        // Arrange
        Url::create([
            'original_url' => 'https://www.first.com',
            'short_code' => 'first1',
        ]);

        Url::create([
            'original_url' => 'https://www.second.com',
            'short_code' => 'secnd1',
        ]);

        // Act
        $response1 = $this->get('/s/first1');
        $response2 = $this->get('/s/secnd1');

        // Assert
        $response1->assertRedirect('https://www.first.com');
        $response2->assertRedirect('https://www.second.com');
    }

    /** @test */
    public function short_code_is_case_sensitive()
    {
        // Skip en Windows/MySQL porque no es case-sensitive por defecto
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('MySQL en Windows no es case-sensitive por defecto.');
        }

        // Arrange
        Url::create([
            'original_url' => 'https://www.example.com',
            'short_code' => 'AbC123',
        ]);

        // Act
        $validResponse = $this->get('/s/AbC123');
        $invalidResponse = $this->get('/s/abc123');

        // Assert
        $validResponse->assertStatus(302);
        $invalidResponse->assertStatus(404);
    }

    /** @test */
    public function it_redirects_using_away_method_for_external_urls()
    {
        // Arrange
        $url = Url::create([
            'original_url' => 'https://www.external-site.com',
            'short_code' => 'extern',
        ]);

        // Act
        $response = $this->get('/s/extern');

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect('https://www.external-site.com');
    }
}
