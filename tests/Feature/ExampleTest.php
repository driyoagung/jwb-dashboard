<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_dashboard(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
    }

    public function test_kenanga_pages_render_successfully(): void
    {
        $paths = [
            '/components/cards',
            '/components/tables',
            '/components/forms',
            '/components/buttons',
            '/components/feedback',
            '/components/navigation',
            '/components/filters',
            '/components/table-states',
            '/components/charts',
            '/components/data-patterns',
            '/examples/records',
            '/examples/records/new',
            '/examples/records/detail',
            '/examples/records/edit',
            '/login',
            '/demo/404',
        ];

        foreach ($paths as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('Griyo POS', false);
        }
    }

    public function test_dashboard_marks_current_navigation_item(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/dashboard')
            ->assertOk()
            ->assertSee('aria-current="page"', false);
    }

    public function test_pos_shell_shows_only_pos_navigation_and_fixed_appearance(): void
    {
        $response = $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/dashboard');

        $response->assertOk()
            ->assertSee('Kasir')
            ->assertSee('Master data')
            ->assertSee('Riwayat stok')
            ->assertDontSee('Contoh halaman')
            ->assertDontSee('Komponen')
            ->assertDontSee('id="customizer"', false)
            ->assertDontSee('themeToggle', false)
            ->assertSee('"theme":"light"', false)
            ->assertSee('"accent":"indigo"', false)
            ->assertSee('"radius":"0.3"', false);
    }

    public function test_blank_layout_renders_testing_page_without_admin_site_binding(): void
    {
        $this->get('/testing')
            ->assertOk()
            ->assertSee('Component Testing')
            ->assertSee('card-title', false);
    }

    public function test_frontend_showcase_exposes_reusable_components(): void
    {
        $this->get('/components/feedback')
            ->assertOk()
            ->assertSee('<dialog id="modal-info"', false);

        $this->get('/components/charts')
            ->assertOk()
            ->assertSee('data-chart', false);

        $this->get('/components/filters')
            ->assertOk()
            ->assertSee('data-combobox', false)
            ->assertSee('data-date-range', false)
            ->assertSee('data-file-preview', false);
    }

    public function test_data_patterns_render_and_preview_confirmation_redirects(): void
    {
        $this->get('/components/data-patterns')
            ->assertOk()
            ->assertSee('data-enhanced-select', false)
            ->assertSee('role="tabpanel"', false)
            ->assertSee('Panduan orientasi untuk anggota tim baru.')
            ->assertSee('Filter aktif:')
            ->assertSee('method="POST"', false);

        $this->get('/components/filters')
            ->assertOk()
            ->assertSee('data-date-picker', false)
            ->assertSee('name="from"', false)
            ->assertSee('name="to"', false);

        $this->post(route('showcase.data-patterns.preview'))
            ->assertRedirect(route('showcase.data-patterns'))
            ->assertSessionHas('status');
    }
}
