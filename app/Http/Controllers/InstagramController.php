<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstagramController extends Controller
{
    public function fetchInstagramData()
    {
        $accessToken = 'IGQWRNS21hWmRQMjRDZAXNfQlgwcWlJYUdfaFFGZA1RZANHdXSTNiRnRYanBhODBQUndvM3N5WXBBdG1RbFQxcERlc3U0MFF3WVZAZASWNQVno5WEk4eDUtamx3YlpGTTNQaVBpVlB5a0J0dWJJQ0F0Wmw4ZAFJKSHNYbjgZD';
        $instagramAccountId = 'YOUR_INSTAGRAM_ACCOUNT_ID';

        $response = Http::get("https://graph.instagram.com/{$instagramAccountId}/media", [
            'fields' => 'id,caption,media_url,permalink',
            'access_token' => $accessToken
        ]);

        $data = $response->json();

        return view('instagram', ['data' => $data]);
    }
}
