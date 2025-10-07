<?php

namespace Database\Factories;

use App\Models\Url;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UrlFactory extends Factory
{
    protected $model = Url::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'original_url' => $this->faker->url(),
            'short_code' => substr(md5(uniqid()), 0, 6),
        ];
    }

    /**
     * Indicate that the URL should have a specific short code.
     *
     * @param string $shortCode
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withShortCode(string $shortCode)
    {
        return $this->state(function (array $attributes) use ($shortCode) {
            return [
                'short_code' => $shortCode,
            ];
        });
    }

    /**
     * Indicate that the URL should have a specific original URL.
     *
     * @param string $originalUrl
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withOriginalUrl(string $originalUrl)
    {
        return $this->state(function (array $attributes) use ($originalUrl) {
            return [
                'original_url' => $originalUrl,
            ];
        });
    }
}
