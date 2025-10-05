<?php

namespace App\Services;

use App\Models\Url;

class UrlService
{
    public function createShortUrl(string $originalUrl): string
    {
        $shortCode = substr(md5($originalUrl . time()), 0, 6);

        Url::create([
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
        ]);

        return url("/s/{$shortCode}");
    }
}
