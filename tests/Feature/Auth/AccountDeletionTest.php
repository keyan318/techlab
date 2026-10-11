<?php

namespace Tests\Feature\Auth;

use App\Models\Conversation;
use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_delete_their_account_and_related_data(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $conversation = Conversation::create(['user_id' => $user->id, 'title' => 'Test chat']);

        $response = $this->actingAs($user)->delete('/account');

        $this->assertGuest();
        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('conversations', ['id' => $conversation->id]);
    }

    public function test_faculty_without_a_crew_or_course_can_delete_their_account(): void
    {
        $user = User::factory()->create(['role' => 'faculty']);

        $response = $this->actingAs($user)->delete('/account');

        $this->assertGuest();
        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_faculty_who_captains_a_crew_cannot_delete_their_account(): void
    {
        $user = User::factory()->create(['role' => 'faculty']);
        Crew::create([
            'name' => 'Test Crew',
            'code' => 'ABC123',
            'teacher_id' => $user->id,
            'planet' => 'networking',
            'course_slug' => 'networking-1',
        ]);

        $response = $this->actingAs($user)->delete('/account');

        $this->assertAuthenticated();
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertTrue(session()->has('accountDeleteError'));
    }

    public function test_faculty_with_a_course_assignment_cannot_delete_their_account(): void
    {
        $user = User::factory()->create(['role' => 'faculty']);
        CourseAssignment::create([
            'planet' => 'networking',
            'course_slug' => 'networking-1',
            'faculty_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete('/account');

        $this->assertAuthenticated();
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_their_account(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->delete('/account');

        $this->assertAuthenticated();
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_guest_cannot_delete_an_account(): void
    {
        $response = $this->delete('/account');

        $response->assertRedirect(route('login'));
    }
}
