<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Preference;
use App\Models\Source;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_articles()
    {
        Article::factory(12)->create();

        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/articles', [
                'page' => 1,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);
    }

    public function test_article_detail()
    {
        Article::factory(1)->create();

        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/articles/' . Article::first()->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);
    }

    public function test_user_preferred_articles()
    {
        $source = Source::create(['name' => 'User Preferred Source']);

        Article::factory(12)->create([
            'source_id' => $source->id
        ]);

        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        Preference::create([
            'user_id' => $user->id,
            'type' => 'source',
            'value' => $source->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/preferred-articles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);
    }
}
