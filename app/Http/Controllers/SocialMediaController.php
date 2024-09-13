<?php

namespace App\Http\Controllers;

use App\Models\SocialsInsights;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SocialMediaController extends Controller
{
    public function index()
    {
        $pageId = '115601096554270';  // Your Page ID
        $accessToken = 'EAAOluXLpDksBOZCUyLjdG42fmDZBXZA7sSIJpSwp6rfOmWMGqd63mvZAVZA5SPs8GR3nWSb8XmC1kzaZASEDpnVOf1KADx0QWhm6xiIyLMVBTTc2rcaZCdT8zpAPcqmGxsILhAL6mSB06JIblGESVetSUk0KLXmHW9RUYl0OVVVx5BfCfsw0gNgDEvinEkmKYgZD';  // Your long-lived Page Access Token

        // Define the time range (last 30 days)
        $since = strtotime('-30 days');  // 30 days ago
        $until = time();  // Current timestamp

        // Define the Facebook Graph API URL with the time range
        $url = "https://graph.facebook.com/v20.0/{$pageId}/insights/page_impressions_unique?access_token={$accessToken}&since={$since}&until={$until}";

        // Function to fetch paginated data with a limit
        function fetchLimitedData($url, $limit = 3) {
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

        // Fetch limited paginated data (with max 3 requests)
        $insights = fetchLimitedData($url, 3);  // Fetch up to 3 pages of data

        $getFacebookInsights = SocialsInsights::where('social_id', 1)->first();

        return view('marketing.insight.index', ['insights' => $insights, 'getFacebookInsights' => $getFacebookInsights]);
    }


    public function getTotalPostFacebook()
    {
        $accessToken = 'EAAOluXLpDksBOZCUyLjdG42fmDZBXZA7sSIJpSwp6rfOmWMGqd63mvZAVZA5SPs8GR3nWSb8XmC1kzaZASEDpnVOf1KADx0QWhm6xiIyLMVBTTc2rcaZCdT8zpAPcqmGxsILhAL6mSB06JIblGESVetSUk0KLXmHW9RUYl0OVVVx5BfCfsw0gNgDEvinEkmKYgZD';
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
        $url = "https://graph.facebook.com/115601096554270/posts?fields=likes.summary(true)&access_token=EAAOluXLpDksBOZCUyLjdG42fmDZBXZA7sSIJpSwp6rfOmWMGqd63mvZAVZA5SPs8GR3nWSb8XmC1kzaZASEDpnVOf1KADx0QWhm6xiIyLMVBTTc2rcaZCdT8zpAPcqmGxsILhAL6mSB06JIblGESVetSUk0KLXmHW9RUYl0OVVVx5BfCfsw0gNgDEvinEkmKYgZD";

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
        $url = "https://graph.facebook.com/115601096554270/posts?fields=comments.summary(true)&access_token=EAAOluXLpDksBOZCUyLjdG42fmDZBXZA7sSIJpSwp6rfOmWMGqd63mvZAVZA5SPs8GR3nWSb8XmC1kzaZASEDpnVOf1KADx0QWhm6xiIyLMVBTTc2rcaZCdT8zpAPcqmGxsILhAL6mSB06JIblGESVetSUk0KLXmHW9RUYl0OVVVx5BfCfsw0gNgDEvinEkmKYgZD";

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
        $pageId = '115601096554270';  // Your Page ID
        $accessToken = 'EAAOluXLpDksBOZCUyLjdG42fmDZBXZA7sSIJpSwp6rfOmWMGqd63mvZAVZA5SPs8GR3nWSb8XmC1kzaZASEDpnVOf1KADx0QWhm6xiIyLMVBTTc2rcaZCdT8zpAPcqmGxsILhAL6mSB06JIblGESVetSUk0KLXmHW9RUYl0OVVVx5BfCfsw0gNgDEvinEkmKYgZD';  // Your long-lived Page Access Token

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
        $totalVisitors = fetchPaginatedVisitorsData($url, 3);

        // Update or create the database record for visitors count
        SocialsInsights::updateOrCreate(
            ['social_id' => 1],
            ['visitors_count' => $totalVisitors]
        );

        return $totalVisitors;
    }
}
