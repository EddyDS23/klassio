<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_displayed(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_register_page_can_be_displayed(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_student_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_teacher_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'role' => 'teacher',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('teacher.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'status' => 'inactive',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_student_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        $response->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'role' => 'student',
        ]);
    }

    public function test_teacher_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Teacher',
            'email' => 'teacher@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'teacher',
        ]);

        $response->assertRedirect(route('teacher.dashboard'));

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Test Teacher',
            'email' => 'teacher@test.com',
            'role' => 'teacher',
        ]);
    }

    public function test_registration_requires_valid_data(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
            'role' => 'invalid-role',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'password',
            'role',
        ]);

        $this->assertGuest();
    }

    public function test_email_must_be_unique_when_registering(): void
    {
        User::factory()->create([
            'email' => 'existing@test.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Another User',
            'email' => 'existing@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');

        $this->assertGuest();
    }
}