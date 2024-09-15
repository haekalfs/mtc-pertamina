<?php

namespace App\Http\Controllers;

use App\Models\Social_token;
use App\Models\SocialsInsights;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SocialMediaController extends Controller
{
    public function index()
    {
        $socialMedia = Social_token::find(1);
        $socialMediaInstagram = Social_token::find(2);
        $pageId = $socialMedia->page_id;  // Your Page ID
        $accessToken = $socialMedia->token;  // Your long-lived Page Access Token

        // Define the time range (last 30 days)
        $since = strtotime('-30 days');  // 30 days ago
        $until = time();  // Current timestamp

        // Define the Facebook Graph API URL with the time range
        $url = "https://graph.facebook.com/v20.0/{$pageId}/insights/page_impressions_unique?access_token={$accessToken}&since={$since}&until={$until}";
        // Cache::forget('facebook_insights');
        // $this->getTotalVisitorsFacebook();
        // Fetch the cached data or fetch from the API if cache is empty
        $insights = Cache::remember('facebook_insights', now()->addDay(), function () use ($url) {
            return $this->fetchLimitedData($url, 20);  // Fetch API data and cache it for 1 day
        });

        $getFacebookInsights = SocialsInsights::where('social_id', 1)->first();

        return view('marketing.insight.index', ['insights' => $insights, 'getFacebookInsights' => $getFacebookInsights, 'socialMedia' => $socialMedia, 'socialMediaInstagram' => $socialMediaInstagram]);
    }

    // Function to fetch paginated data with a limit
    private function fetchLimitedData($url, $limit = 3) {
        $allData = [
            'day' => null,  // Initialize for daily data
            'week' => null, // Initialize for weekly data
            'days_28' => null  // Initialize for 28-day data
        ];

        // Fetch the initial URL and limit the pagination
        $urlsToFetch = [$url];
        $requestCount = 0;  // Track the number of requests

        while (!empty($urlsToFetch) && $requestCount < $limit) {
            $currentUrl = array_shift($urlsToFetch);
            $response = Http::get($currentUrl);
            $json = $response->json();

            // Merge the current data into the main array
            if (isset($json['data'])) {
                foreach ($json['data'] as $insight) {
                    $name = $insight['name'];
                    $period = $insight['period'];

                    // Check if the period data exists and merge
                    if (!isset($allData[$period])) {
                        $allData[$period] = $insight; // Initialize the array
                    } else {
                        // Append values to existing data
                        $allData[$period]['values'] = array_merge($allData[$period]['values'], $insight['values']);
                    }
                }
            }

            // Check pagination and only add "next" and "previous" if under the limit
            if (isset($json['paging']['next']) && $requestCount < $limit) {
                $urlsToFetch[] = $json['paging']['next'];
            }
            if (isset($json['paging']['previous']) && $requestCount < $limit) {
                $urlsToFetch[] = $json['paging']['previous'];
            }

            $requestCount++;  // Increment the request counter
        }

        return array_filter($allData);  // Remove any empty data arrays
    }

    public function getTotalPostFacebook()
    {
        $socialMedia = Social_token::find(1);
        $accessToken = $socialMedia->token;  // Your long-lived Page Access Token
        $url = "https://graph.facebook.com/115601096554270/posts?access_token={$accessToken}";

        $totalPosts = 0;
        $nextPage = $url;

        // Loop to handle pagination
        while ($nextPage) {
            // Fetch posts from the current page
            $response = Http::get($nextPage);
            $data = $response->json();

            // Count the posts in the current response
            $totalPosts += count($data['data']);

            // Check if there's a next page
            if (isset($data['paging']['next'])) {
                $nextPage = $data['paging']['next'];
            } else {
                $nextPage = null;
            }
        }

        // Save or update in the database using updateOrCreate for social_id = 1
        SocialsInsights::updateOrCreate(
            ['social_id' => 1], // Match by social_id
            ['posts_count' => $totalPosts] // Update the posts_count
        );
    }

    public function getTotalLikesFacebook()
    {
        $socialMedia = Social_token::find(1);
        $pageId = $socialMedia->page_id;  // Your Page ID
        $accessToken = $socialMedia->token;  // Your long-lived Page Access Token
        $url = "https://graph.facebook.com/{$pageId}/posts?fields=likes.summary(true)&access_token={$accessToken}";

        $totalLikes = 0;
        do {
            $response = Http::get($url);
            $data = $response->json();

            foreach ($data['data'] as $post) {
                if (isset($post['likes']['summary']['total_count'])) {
                    $totalLikes += $post['likes']['summary']['total_count'];
                }
            }

            $url = isset($data['paging']['next']) ? $data['paging']['next'] : null;

        } while ($url != null);

        SocialsInsights::updateOrCreate(
            ['social_id' => 1],
            ['likes_count' => $totalLikes]
        );

        return $totalLikes;
    }

    public function getTotalCommentsFacebook()
    {
        $socialMedia = Social_token::find(1);
        $pageId = $socialMedia->page_id;  // Your Page ID
        $accessToken = $socialMedia->token;  // Your long-lived Page Access Token
        $url = "https://graph.facebook.com/{$pageId}/posts?fields=comments.summary(true)&access_token={$accessToken}";

        $totalComments = 0;
        do {
            $response = Http::get($url);
            $data = $response->json();

            foreach ($data['data'] as $post) {
                if (isset($post['comments']['summary']['total_count'])) {
                    $totalComments += $post['comments']['summary']['total_count'];
                }
            }

            $url = isset($data['paging']['next']) ? $data['paging']['next'] : null;

        } while ($url != null);

        SocialsInsights::updateOrCreate(
            ['social_id' => 1],
            ['comments_count' => $totalComments]
        );

        return $totalComments;
    }

    public function getTotalVisitorsFacebook()
    {
        $socialMedia = Social_token::find(1);
        $pageId = $socialMedia->page_id;  // Your Page ID
        $accessToken = $socialMedia->token;  // Your long-lived Page Access Token

        // Define the time range (last 30 days)
        $since = strtotime('-30 days');  // 30 days ago
        $until = time();  // Current timestamp

        // Facebook Graph API URL for page views total metric
        $url = "https://graph.facebook.com/v20.0/{$pageId}/insights/page_views_total?access_token={$accessToken}&since={$since}&until={$until}";

        // Function to fetch paginated data for visitors
        function fetchPaginatedVisitorsData($url, $limit = 3) {
            $totalVisitors = 0;  // Total visitors counter
            $urlsToFetch = [$url];
            $requestCount = 0;

            while (!empty($urlsToFetch) && $requestCount < $limit) {
                $currentUrl = array_shift($urlsToFetch);
                $response = Http::get($currentUrl);
                $json = $response->json();

                // Process the current page's visitor data
                if (isset($json['data'])) {
                    foreach ($json['data'] as $insight) {
                        if (isset($insight['values'])) {
                            foreach ($insight['values'] as $value) {
                                $totalVisitors += $value['value'];
                            }
                        }
                    }
                }

                // Handle pagination (fetch the next page if available)
                if (isset($json['paging']['next']) && $requestCount < $limit) {
                    $urlsToFetch[] = $json['paging']['next'];
                }

                $requestCount++;  // Increment the request count
            }

            return $totalVisitors;
        }

        // Fetch paginated visitor data (with max 3 requests)
        $totalVisitors = fetchPaginatedVisitorsData($url, 20);

        // Update or create the database record for visitors count
        SocialsInsights::updateOrCreate(
            ['social_id' => 1],
            ['visitors_count' => $totalVisitors]
        );

        return $totalVisitors;
    }

    public function updateFacebookToken(Request $request, $id)
    {
        // Validate the token
        $request->validate([
            'token' => 'required|string'
        ]);

        // Fetch the account details from Facebook
        $url = 'https://graph.facebook.com/me?fields=id,name&access_token=' . $request->token;
        $response = Http::get($url);

        if ($response->ok()) {
            // Get account details
            $data = $response->json();
            $accountId = $data['id'];
            $accountName = $data['name'];

            // Update the social media token in the database
            $socialToken = Social_token::find($id);
            $socialToken->token = $request->token;
            $socialToken->account_name = $accountName;
            $socialToken->page_id = $accountId;
            $socialToken->save();

            return response()->json(['success' => true, 'message' => 'Token updated successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid token or unable to fetch data from Facebook'], 400);
        }
    }
}
