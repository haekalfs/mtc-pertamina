<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class InstagramController extends Controller
{
    private $accessToken;
    private $instagramAccountId;

    public function __construct()
    {
        // Replace these with your actual values
        $this->accessToken = env('INSTAGRAM_ACCESS_TOKEN');
        $this->instagramAccountId = env('INSTAGRAM_ACCOUNT_ID');
    }

    public function getInstagramInsights()
    {
        $client = new Client();

        // Get user insights
        $userInsightsEndpoint = 'https://graph.instagram.com/' . $this->instagramAccountId . '/insights';
        $userInsightParams = [
            'metric' => 'follower_count,impressions,profile_views,reach',
            'period' => 'day',
            'access_token' => $this->accessToken,
        ];

        $userResponse = $client->request('GET', $userInsightsEndpoint, ['query' => $userInsightParams]);
        $userInsights = json_decode($userResponse->getBody(), true);

        $followers = $userInsights['data'][0]['values'][0]['value'] ?? 0;
        $visitors = $userInsights['data'][1]['values'][0]['value'] ?? 0;

        // Get media insights
        $mediaEndpoint = 'https://graph.instagram.com/me/media';
        $mediaParams = [
            'fields' => 'id,caption',
            'access_token' => $this->accessToken,
        ];

        $mediaResponse = $client->request('GET', $mediaEndpoint, ['query' => $mediaParams]);
        $mediaData = json_decode($mediaResponse->getBody(), true);

        $totalLikes = 0;

        foreach ($mediaData['data'] as $media) {
            $mediaInsightsEndpoint = 'https://graph.instagram.com/' . $media['id'] . '/insights';
            $mediaInsightParams = [
                'metric' => 'engagement,impressions,reach,saved,video_views',
                'access_token' => $this->accessToken,
            ];

            $mediaInsightResponse = $client->request('GET', $mediaInsightsEndpoint, ['query' => $mediaInsightParams]);
            $mediaInsights = json_decode($mediaInsightResponse->getBody(), true);

            foreach ($mediaInsights['data'] as $insight) {
                if ($insight['name'] === 'engagement') {
                    $totalLikes += $insight['values'][0]['value'];
                }
            }
        }

        return response()->json([
            'followers' => $followers,
            'visitors' => $visitors,
            'total_likes' => $totalLikes,
        ]);
    }
}
