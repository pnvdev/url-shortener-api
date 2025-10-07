<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UrlModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_url_record()
    {
        // Arrange & Act
        $url = Url::create([
            'original_url' => 'https://www.example.com/test',
            'short_code' => 'abc123',
        ]);

        // Assert
        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals('https://www.example.com/test', $url->original_url);
        $this->assertEquals('abc123', $url->short_code);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        // Arrange
        $url = new Url();

        // Act
        $fillable = $url->getFillable();

        // Assert
        $this->assertContains('original_url', $fillable);
        $this->assertContains('short_code', $fillable);
    }

    /** @test */
    public function it_can_be_retrieved_by_short_code()
    {
        // Arrange
        Url::create([
            'original_url' => 'https://www.laravel.com',
            'short_code' => 'xyz789',
        ]);

        // Act
        $url = Url::where('short_code', 'xyz789')->first();

        // Assert
        $this->assertNotNull($url);
        $this->assertEquals('https://www.laravel.com', $url->original_url);
    }

    /** @test */
    public function it_has_timestamps()
    {
        // Arrange & Act
        $url = Url::create([
            'original_url' => 'https://www.example.com',
            'short_code' => 'test01',
        ]);

        // Assert
        $this->assertNotNull($url->created_at);
        $this->assertNotNull($url->updated_at);
    }

    /** @test */
    public function short_codes_should_be_unique()
    {
        // Arrange
        Url::create([
            'original_url' => 'https://www.example.com/first',
            'short_code' => 'unique',
        ]);

        // Act & Assert
        $this->expectException(\Illuminate\Database\QueryException::class);

        Url::create([
            'original_url' => 'https://www.example.com/second',
            'short_code' => 'unique',
        ]);
    }

    /** @test */
    public function it_can_store_long_urls()
    {
        // Arrange
        $longUrl = 'https://www.example.com/' . str_repeat('long-path/', 50);

        // Act
        $url = Url::create([
            'original_url' => $longUrl,
            'short_code' => 'long01',
        ]);

        // Assert
        $this->assertEquals($longUrl, $url->original_url);
        $this->assertDatabaseHas('urls', [
            'short_code' => 'long01',
        ]);
    }
}
