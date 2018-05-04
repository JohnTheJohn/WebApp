<?php

namespace Tests\Browser;

use App\Post;
use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    /**
     * @group login
     *
     * @return void
     */
    public function testLogin()
    {

        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'secret')
                    ->press('Login')
                    ->assertPathIs('/home');
        });
    }

    /**
     * @group seePosts
     *
     * @return void
     */
    public function testSeeingPosts()
    {

        $post = factory(Post::class)->create();

        $this->browse(function (Browser $browser) use($post) {
            $browser->visit('/posts')
                    ->clickLink('View')
                    ->assertPathIs("/post/{$post->id}")
                    ->assertSee($post->title)
                    ->assertSee($post->content);
        });
    }

    /**
     * @group createPost
     *
     * @return void
     */
    public function testCreatingPost()
    {

        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($user)
                ->visit('/create-post')
                ->type('title', 'New title')
                ->type('content','New post content')
                ->press('save')
                ->assertPathIs('/posts')
                ->assertSee('New title')
                ->assertSee('New post content');
        });
    }
    /**
     * @group testMiddleware
     *
     * @return void
     */
    public function testMiddleware()
    {

        $this->browse(function (Browser $browser) {
            $browser->visit('/create-post')
                    ->assertPathIs('/login');

        });
    }
}
