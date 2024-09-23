<?php

namespace App\Jobs;

use App\Models\Social_token;
use App\Models\SocialsInsights;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FetchFacebookInsights implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Call your insight methods here
        $this->getTotalPostFacebook();
        $this->getTotalLikesFacebook();
        $this->getTotalCommentsFacebook();
        $this->getTotalVisitorsFacebook();
    }

    public function getTotalPostFacebook()
    {
        $socialMedia = Social_token::find(1);
        $accessToken = $socialMedia->token;
        $url = "https://graph.facebook.com/115601096554270/posts?access_token={$accessToken}";

        $totalPosts = 0;
        $nextPage = $url;

        while ($nextPage) {
            $response = Http::get($nextPage);
            $data = $response->json();
            $totalPosts += count($data['data']);

            $nextPage = isset($data['paging']['next']) ? $data['paging']['next'] : null;
        }

        SocialsInsights::updateOrCreate(
            ['social_id' => 1],
            ['posts_count' => $totalPosts]
        );
    }

    public function getTotalLikesFacebook()
    {
        $socialMedia = Social_token::find(1);
        $pageId = $socialMedia->page_id;
        $accessToken = $socialMedia->token;
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
    }

    public function getTotalCommentsFacebook()
    {
        $socialMedia = Social_token::find(1);
        $pageId = $socialMedia->page_id;
        $accessToken = $socialMedia->token;
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
    }

    public function getTotalVisitorsFacebook()
    {
        $socialMedia = Social_token::find(1);
        $pageId = $socialMedia->page_id;
        $accessToken = $socialMedia->token;
        $since = strtotime('-30 days');
        $until = time();
        $url = "https://graph.facebook.com/v20.0/{$pageId}/insights/page_views_total?access_token={$accessToken}&since={$since}&until={$until}";

        $totalVisitors = $this->fetchPaginatedVisitorsData($url, 20);

        SocialsInsights::updateOrCreate(
            ['social_id' => 1],
            ['visitors_count' => $totalVisitors]
        );
    }

    private function fetchPaginatedVisitorsData($url, $limit = 3)
    {
        $totalVisitors = 0;
        $urlsToFetch = [$url];
        $requestCount = 0;

        while (!empty($urlsToFetch) && $requestCount < $limit) {
            $currentUrl = array_shift($urlsToFetch);
            $response = Http::get($currentUrl);
            $json = $response->json();

            if (isset($json['data'])) {
                foreach ($json['data'] as $insight) {
                    if (isset($insight['values'])) {
                        foreach ($insight['values'] as $value) {
                            $totalVisitors += $value['value'];
                        }
                    }
                }
            }

            if (isset($json['paging']['next']) && $requestCount < $limit) {
                $urlsToFetch[] = $json['paging']['next'];
            }

            $requestCount++;
        }

        return $totalVisitors;
    }
}
