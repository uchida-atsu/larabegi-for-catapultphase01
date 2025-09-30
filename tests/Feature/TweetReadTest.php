<?php

use App\Models\User;
use App\Models\Tweet;
use App\Models\TweetRead;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->userA = User::factory()->create();
    $this->userB = User::factory()->create();
});

it('allows nonauth tweet to unreading', function () {
    // Aが投稿
    $tweet = Tweet::factory()->create(['user_id' => $this->userA->id]);

    // Bでログイン
    $this->actingAs($this->userB);

    // 未読を取得
    $response = $this->get('/tweets');
    $response->assertSee('unreadCount');
    $this->assertDatabaseCount('tweet_reads', 0); // まだ既読はない

    $unreadCount = Tweet::whereDoesntHave('reads', function ($query) {
        $query->where('user_id', $this->userB->id);
    })->count();

    expect($unreadCount)->toBe(1); // Aの投稿が未読としてカウントされる
});

it('allows displaying s tweet to reading', function () {
    $tweet = Tweet::factory()->create(['user_id' => $this->userA->id]);

    $this->actingAs($this->userB);

    // 詳細画面を開く（既読登録されるはず）
    $this->get("/tweets/{$tweet->id}");

    // DBに TweetRead が入っている
    $this->assertDatabaseHas('tweet_reads', [
        'user_id' => $this->userB->id,
        'tweet_id' => $tweet->id,
    ]);

    $unreadCount = Tweet::whereDoesntHave('reads', function ($query) {
        $query->where('user_id', $this->userB->id);
    })->count();

    expect($unreadCount)->toBe(0); // 既読になったので未読数は0
});

it('not contain auth tweet into unreading', function () {
    $this->actingAs($this->userA);

    $tweet = Tweet::factory()->create(['user_id' => $this->userA->id]);

    $unreadCount = Tweet::where('user_id', '!=', $this->userA->id) // ★自分の投稿を除外
        ->whereDoesntHave('reads', function ($query) {
            $query->where('user_id', $this->userA->id);
        })
        ->count();

    expect($unreadCount)->toBe(0);
});

it('display the batch on indexpage', function () {
    $tweet = Tweet::factory()->create(['user_id' => $this->userA->id]);

    $this->actingAs($this->userB);

    $response = $this->get('/dashboard'); // navigation.blade.php が含まれる画面
    $response->assertSee((string) 1); // バッジに「1」が表示される
});

it('not allow the batch to display', function () {
    $this->actingAs($this->userB);

    $response = $this->get('/dashboard');
    $response->assertDontSee('<span'); // バッジ用のHTMLが出ない想定
});

