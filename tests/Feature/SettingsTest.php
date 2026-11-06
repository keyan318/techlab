<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_and_faculty_see_settings(): void
    {
        foreach (['student', 'faculty'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->get('/settings')
                ->assertOk()
                ->assertSee('Settings')
                ->assertSee('Change password')
                ->assertSee(route($role === 'faculty' ? 'faculty.avatar' : 'student.avatar'), false)
                ->assertSee(route('account.destroy'), false);
        }
    }

    public function test_account_menu_links_to_settings_not_the_avatar_builder(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)->get('/settings')
            ->assertSee('href="'.route('settings').'"', false);
    }

    public function test_guests_are_kept_out(): void
    {
        $this->get('/settings')->assertRedirect(route('login'));
    }

    public function test_admin_sees_settings_with_the_captain_builder_and_no_delete(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/settings')
            ->assertOk()
            ->assertSee('Change password')
            ->assertSee(route('admin.avatar'), false)
            ->assertSee('Admins')
            ->assertDontSee(route('account.destroy'), false);

        $this->actingAs($admin)->get('/admin/avatar')->assertOk();
        $look = ['animal' => 'lion', 'coat' => 'snow', 'suit' => 'admiral', 'suit_color' => 'midnight', 'patch' => 'star', 'helmet' => '0', 'visor' => 'gold'];
        $this->actingAs($admin)->post('/admin/avatar', $look)->assertRedirect(route('admin.dashboard'));
        $this->assertSame(['lion', 'snow'], [$admin->refresh()->avatar['animal'], $admin->avatar['coat']]);
    }

    public function test_only_the_first_admin_can_add_and_remove_admins(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $other = User::factory()->create(['role' => 'admin']);

        $this->actingAs($owner)->get('/settings')->assertSee('Add an admin');
        $this->actingAs($other)->get('/settings')->assertDontSee('Add an admin')->assertSee('Only the first admin');

        $new = ['first_name' => 'Ana', 'last_name' => 'Cruz', 'email' => 'ana@techlab.local',
            'password' => 'a-long-password', 'password_confirmation' => 'a-long-password'];

        $this->actingAs($other)->post('/admin/admins', $new)->assertForbidden();
        $this->actingAs($owner)->from('/settings')->post('/admin/admins', ['password' => 'short', 'password_confirmation' => 'short'] + $new)
            ->assertSessionHasErrorsIn('admin', 'password');
        $this->actingAs($owner)->from('/settings')->post('/admin/admins', $new)
            ->assertRedirect('/settings')->assertSessionHas('settingsStatus');

        $ana = User::where('email', 'ana@techlab.local')->first();
        $this->assertSame(['admin', 'Ana Cruz'], [$ana->role, $ana->name]);
        $this->assertTrue(Hash::check('a-long-password', $ana->password));

        $this->actingAs($other)->delete("/admin/admins/{$ana->id}")->assertForbidden();
        $this->actingAs($owner)->delete("/admin/admins/{$owner->id}")->assertNotFound();
        $this->actingAs($owner)->from('/settings')->delete("/admin/admins/{$ana->id}")->assertRedirect('/settings');
        $this->assertModelMissing($ana);
    }

    public function test_student_can_change_name_program_and_year(): void
    {
        $user = User::factory()->create(['role' => 'student', 'name' => 'Old Name']);

        $this->actingAs($user)->get('/settings')->assertSee('Pick your program and year level');

        $this->actingAs($user)->from('/settings')->put('/settings/profile', ['first_name' => '  New ', 'last_name' => ' Name ', 'program' => 'BLIS', 'year_level' => '3'])
            ->assertRedirect('/settings')
            ->assertSessionHas('settingsStatus');

        $user->refresh();
        $this->assertSame(['New Name', 'BLIS', 3], [$user->name, $user->program, $user->year_level]);
    }

    public function test_older_accounts_get_their_name_split_for_them(): void
    {
        $user = User::factory()->create(['role' => 'student', 'name' => 'Keyan Jaoric Rima']);

        $this->actingAs($user)->get('/settings')
            ->assertSee('value="Keyan Jaoric"', false)
            ->assertSee('value="Rima"', false);
    }

    public function test_student_must_pick_a_real_program_and_year(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->from('/settings')->put('/settings/profile', ['first_name' => 'Nova', 'last_name' => 'Reyes', 'program' => 'NURSING', 'year_level' => 7])
            ->assertSessionHasErrors(['program', 'year_level']);
        $this->actingAs($user)->from('/settings')->put('/settings/profile', ['first_name' => 'Nova', 'last_name' => 'Reyes'])
            ->assertSessionHasErrors(['program', 'year_level']);
    }

    public function test_faculty_only_change_their_name(): void
    {
        $user = User::factory()->create(['role' => 'faculty', 'name' => 'Old Name']);

        $this->actingAs($user)->get('/settings')->assertDontSee('Year level');
        $this->actingAs($user)->from('/settings')->put('/settings/profile', ['first_name' => 'Captain', 'last_name' => 'Nova'])
            ->assertSessionHasNoErrors();

        $this->assertSame('Captain Nova', $user->fresh()->name);
        $this->assertNull($user->fresh()->program);
    }

    public function test_password_change_needs_the_current_password(): void
    {
        $user = User::factory()->create(['role' => 'student', 'password' => 'old-password']);

        $this->actingAs($user)->from('/settings')->put('/settings/password', [
            'current_password' => 'wrong-password',
            'password' => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ])->assertRedirect('/settings')->assertSessionHasErrorsIn('password', 'current_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_password_can_be_changed(): void
    {
        $user = User::factory()->create(['role' => 'faculty', 'password' => 'old-password']);

        $this->actingAs($user)->from('/settings')->put('/settings/password', [
            'current_password' => 'old-password',
            'password' => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ])->assertRedirect('/settings')->assertSessionHas('settingsStatus');

        $this->assertTrue(Hash::check('brand-new-pass', $user->fresh()->password));
    }
}
