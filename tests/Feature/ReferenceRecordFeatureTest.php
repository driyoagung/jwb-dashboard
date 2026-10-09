<?php

namespace Tests\Feature;

use App\Models\ReferenceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceRecordFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_must_log_in_before_using_reference_records(): void
    {
        $this->get(route('reference.records.index'))->assertRedirect(route('login'));
    }

    public function test_login_and_logout_use_a_real_session(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_owner_can_create_update_and_delete_a_record(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('reference.records.create'))->assertOk();

        $this->actingAs($user)->post(route('reference.records.store'), [
            'title' => 'Panduan tim',
            'category' => 'guide',
            'status' => 'draft',
            'summary' => 'Ringkasan awal.',
            'user_id' => 999,
        ])->assertRedirect();

        $record = ReferenceRecord::query()->sole();
        $this->assertSame($user->id, $record->user_id);

        $this->actingAs($user)->get(route('reference.records.show', $record))
            ->assertOk()->assertSee('Panduan tim');

        $this->actingAs($user)->get(route('reference.records.edit', $record))->assertOk();

        $this->actingAs($user)->put(route('reference.records.update', $record), [
            'title' => 'Panduan baru',
            'category' => 'guide',
            'status' => 'active',
            'summary' => 'Sudah diperbarui.',
        ])->assertRedirect(route('reference.records.show', $record));

        $this->assertDatabaseHas('reference_records', ['id' => $record->id, 'title' => 'Panduan baru']);

        $this->actingAs($user)->delete(route('reference.records.destroy', $record))
            ->assertRedirect(route('reference.records.index'));

        $this->assertDatabaseMissing('reference_records', ['id' => $record->id]);
    }

    public function test_validation_rejects_invalid_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('reference.records.create'))->assertOk();

        $this->actingAs($user)->post(route('reference.records.store'), [
            'title' => '',
            'category' => 'unknown',
            'status' => 'draft',
        ])->assertSessionHasErrors(['title', 'category']);

        $this->assertDatabaseCount('reference_records', 0);
    }

    public function test_other_user_cannot_read_or_change_record(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $record = $owner->referenceRecords()->create([
            'title' => 'Pribadi', 'category' => 'note', 'status' => 'draft',
        ]);

        $this->actingAs($other)->get(route('reference.records.show', $record))->assertForbidden();
        $this->actingAs($other)->get(route('reference.records.edit', $record))->assertForbidden();
        $this->actingAs($other)->put(route('reference.records.update', $record), [
            'title' => 'Diubah', 'category' => 'note', 'status' => 'active',
        ])->assertForbidden();
        $this->actingAs($other)->delete(route('reference.records.destroy', $record))->assertForbidden();
        $this->actingAs($other)->get(route('reference.records.index'))
            ->assertOk()->assertDontSee('Pribadi');
    }

    public function test_filter_and_pagination_run_against_owner_records(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        for ($index = 1; $index <= 20; $index++) {
            $user->referenceRecords()->create([
                'title' => 'Panduan '.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'category' => 'guide',
                'status' => $index === 20 ? 'active' : 'draft',
            ]);
        }

        $other->referenceRecords()->create([
            'title' => 'Panduan rahasia', 'category' => 'guide', 'status' => 'active',
        ]);

        $this->actingAs($user)->get(route('reference.records.index'))
            ->assertOk()->assertSee('Panduan 20')->assertDontSee('Panduan 01')
            ->assertSee('aria-label="Paginasi"', false);

        $this->actingAs($user)->get(route('reference.records.index', ['page' => 2]))
            ->assertOk()->assertSee('Panduan 01')->assertDontSee('Panduan 20');

        $this->actingAs($user)->get(route('reference.records.index', ['status' => 'active']))
            ->assertOk()->assertSee('Panduan 20')->assertDontSee('Panduan rahasia');

        $this->actingAs($user)->get(route('reference.records.index', ['q' => 'Panduan 01']))
            ->assertOk()->assertSee('Panduan 01')->assertDontSee('Panduan 20');
    }
}
