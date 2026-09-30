<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_redirects_to_dashboard(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
    }

    public function test_kenanga_pages_render_successfully(): void
    {
        $paths = [
            '/dashboard',
            '/analytics',
            '/settings',
            '/components/cards',
            '/components/tables',
            '/components/forms',
            '/components/buttons',
            '/components/feedback',
            '/components/navigation',
            '/login',
            '/demo/404',
        ];

        foreach ($paths as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('Kenanga', false);
        }
    }

    public function test_dashboard_marks_current_navigation_item(): void
    {
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('aria-current="page"', false);
    }
}
