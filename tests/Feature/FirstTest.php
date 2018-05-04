<?php

namespace Tests\Feature;

use App\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FirstTest extends TestCase
{
    //use DatabaseMigrations;
    /**
     * A basic test example.
     * @group basic-tests
     * @return void
     */
    public function testExample()
    {
        //creating post
        $post = Post::create([
            'title' => 'Test post',
            'content' => 'Test post content'
        ]);

        //action
        $resp = $this->get("/about/{$post->id}");
        //assert
        $resp->assertSee($post->title);
        $resp->assertSee($post->content);
        $resp->assertSee($post->created_at->toFormattedDateString());
        $resp->assertStatus(200);
    }

    /**
     * @group post-not-found
     *
     */
    public function testShow404WhenPostIsNotFound()
    {
        //action
        $resp = $this->get('/about/Invalid_id');

        //assert
        $resp->assertStatus(404);
        $resp->assertSee('The post you are looking for does not exist');

    }

    /**
     * @group posts
     *
     */
    public function testShowAllPosts()
    {
        $post1 = factory(Post::class)->create();
        $post2 = factory(Post::class)->create();

        $resp = $this->get('/posts');

        $resp->assertStatus(200);
        $resp->assertSee($post1->title);
        $resp->assertSee($post2->title);

        $resp->assertSee($post1->content);
        $resp->assertSee($post2->content);

    }

    /**
     * @group create_posts
     *
     */
    public function testCreatePost()
    {
        $resp = $this->Post('/create-post', [

            'title' => 'first title',
            'content' => 'testing testing content'

        ]);

        $this->assertDatabaseHas('posts',[

            'title' => 'first title',
            'content' => 'testing testing content'

        ]);

        $post = Post::where('title', '=', 'first title')->first();

        $this->assertEquals('first title', $post->title);
        $this->assertEquals('testing testing content', $post->content);
    }

    /**
     * @group validation
     *
     */
    public function testValidationRules()
    {
        $resp = $this->post('/create-post', [

            'title' => null,
            'content' => 'coooooooooooontent'

        ]);
        $resp->assertSessionHasErrors('title');
        //$resp->assertSessionHasErrors('content');

    }

}
