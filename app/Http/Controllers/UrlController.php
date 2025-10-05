<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Url;

/**
 * @OA\Tag(
 *     name="URL Shortener",
 *     description="API Endpoints for URL shortening service"
 * )
 */
class UrlController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/short-urls",
     *     summary="Create a shortened URL",
     *     description="Takes a long URL and returns a shortened version",
     *     operationId="shortenUrl",
     *     tags={"URL Shortener"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="URL to be shortened",
     *         @OA\JsonContent(
     *             required={"url"},
     *             @OA\Property(
     *                 property="url",
     *                 type="string",
     *                 format="url",
     *                 description="The original URL to be shortened",
     *                 example="https://www.example.com/very/long/url/path"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created shortened URL",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="original_url",
     *                 type="string",
     *                 format="url",
     *                 description="The original URL that was shortened",
     *                 example="https://www.example.com/very/long/url/path"
     *             ),
     *             @OA\Property(
     *                 property="short_url",
     *                 type="string",
     *                 format="url",
     *                 description="The shortened URL (use this to redirect)",
     *                 example="http://localhost:8000/s/abc123"
     *             ),
     *             @OA\Property(
     *                 property="short_code",
     *                 type="string",
     *                 description="The short code (6 characters)",
     *                 example="abc123"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="date-time",
     *                 description="Creation timestamp",
     *                 example="2025-10-05T12:34:56.000000Z"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Invalid or missing URL",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="url",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="The url field is required."
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - Database or server issue",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Server Error"
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:2048'
        ]);

        $shortCode = substr(md5(uniqid()), 0, 6);

        $url = Url::create([
            'original_url' => $request->url,
            'short_code' => $shortCode,
        ]);

        return response()->json([
            'original_url' => $url->original_url,
            'short_url' => url("/s/{$shortCode}"),
            'short_code' => $shortCode,
            'created_at' => $url->created_at
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/s/{shortCode}",
     *     summary="Redirect to original URL",
     *     description="Redirects to the original URL using the short code. This is the actual URL shortener redirect endpoint.",
     *     operationId="redirectToOriginalUrl",
     *     tags={"URL Shortener"},
     *     @OA\Parameter(
     *         name="shortCode",
     *         in="path",
     *         required=true,
     *         description="The short code for the URL (6 alphanumeric characters)",
     *         @OA\Schema(
     *             type="string",
     *             pattern="^[a-zA-Z0-9]{6}$",
     *             example="abc123"
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Successfully redirecting to the original URL",
     *         @OA\Header(
     *             header="Location",
     *             description="The original URL to redirect to",
     *             @OA\Schema(
     *                 type="string",
     *                 format="url",
     *                 example="https://www.example.com/very/long/url/path"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Short URL not found - Invalid or expired short code",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="URL not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - Database or server issue",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Server Error"
     *             )
     *         )
     *     )
     * )
     */
    public function redirect($shortCode)
    {
        $url = Url::where('short_code', $shortCode)->firstOrFail();

        // Increment click counter if you want to track usage (optional improvement)
        // $url->increment('clicks');

        return redirect()->away($url->original_url);
    }

    /**
     * @OA\Get(
     *     path="/api/short-urls/{shortCode}",
     *     summary="Get URL details by short code",
     *     description="Retrieves detailed information about a shortened URL without redirecting",
     *     operationId="getUrlDetails",
     *     tags={"URL Shortener"},
     *     @OA\Parameter(
     *         name="shortCode",
     *         in="path",
     *         required=true,
     *         description="The short code for the URL",
     *         @OA\Schema(
     *             type="string",
     *             pattern="^[a-zA-Z0-9]{6}$",
     *             example="abc123"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved URL details",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="original_url",
     *                 type="string",
     *                 format="url",
     *                 description="The original URL",
     *                 example="https://www.example.com/very/long/url/path"
     *             ),
     *             @OA\Property(
     *                 property="short_code",
     *                 type="string",
     *                 description="The short code",
     *                 example="abc123"
     *             ),
     *             @OA\Property(
     *                 property="short_url",
     *                 type="string",
     *                 format="url",
     *                 description="The complete shortened URL",
     *                 example="http://localhost:8000/s/abc123"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="date-time",
     *                 description="When the short URL was created",
     *                 example="2025-10-05T12:34:56.000000Z"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Short URL not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="URL not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error - Database or server issue",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Server Error"
     *             )
     *         )
     *     )
     * )
     */
    public function show($shortCode)
    {
        $url = Url::where('short_code', $shortCode)->first();

        if (!$url) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        return response()->json([
            'original_url' => $url->original_url,
            'short_code' => $url->short_code,
            'short_url' => url("/s/{$url->short_code}"),
            'created_at' => $url->created_at
        ]);
    }
}
