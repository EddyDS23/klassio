<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    public function test_only_admins_can_list_users(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $this->get(route('admin.users.index'))->assertRedirect('/login');

        $this->actingAs($student)->get(route('admin.users.index'))
            ->assertRedirect(route('student.dashboard'));

        $this->actingAs($teacher)->get(route('admin.users.index'))
            ->assertRedirect(route('teacher.dashboard'));

        $this->actingAs($this->admin())->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewHas('users');
    }

    public function test_admin_can_list_users_with_roles(): void
    {
        $this->actingAs($this->admin());

        $student = User::factory()->create(['name' => 'Lina Torres', 'email' => 'lina@example.com', 'role' => 'student']);
        $teacher = User::factory()->create(['name' => 'Marcos Ruiz', 'email' => 'marcos@example.com', 'role' => 'teacher']);

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Lina Torres')
            ->assertSee('Marcos Ruiz')
            ->assertSee($student->email)
            ->assertSee($teacher->email);
    }

    public function test_admin_can_search_users_by_name_and_email(): void
    {
        $this->actingAs($this->admin());

        User::factory()->create(['name' => 'Ana García', 'email' => 'ana@example.com', 'role' => 'student']);
        User::factory()->create(['name' => 'Pedro López', 'email' => 'pedro@example.com', 'role' => 'student']);

        $this->get(route('admin.users.index', ['search' => 'Ana']))
            ->assertOk()
            ->assertSee('Ana García')
            ->assertDontSee('Pedro López');

        $this->get(route('admin.users.index', ['search' => 'pedro@example.com']))
            ->assertOk()
            ->assertSee('Pedro López')
            ->assertDontSee('Ana García');
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $this->actingAs($this->admin());

        User::factory()->create(['name' => 'Profesor Uno', 'role' => 'teacher']);
        User::factory()->create(['name' => 'Profesor Dos', 'role' => 'teacher']);
        User::factory()->create(['name' => 'Alumno Uno', 'role' => 'student']);

        $this->get(route('admin.users.index', ['role' => 'teacher']))
            ->assertOk()
            ->assertSee('Profesor Uno')
            ->assertSee('Profesor Dos')
            ->assertDontSee('Alumno Uno');
    }

    public function test_admin_can_filter_users_by_status(): void
    {
        $this->actingAs($this->admin());

        User::factory()->create(['name' => 'Usuario Activo', 'status' => 'active']);
        User::factory()->create(['name' => 'Usuario Suspendido', 'status' => 'suspended']);
        User::factory()->create(['name' => 'Usuario Inactivo', 'status' => 'inactive']);

        $this->get(route('admin.users.index', ['status' => 'suspended']))
            ->assertOk()
            ->assertSee('Usuario Suspendido')
            ->assertDontSee('Usuario Activo')
            ->assertDontSee('Usuario Inactivo');
    }

    public function test_user_list_is_paginated(): void
    {
        $this->actingAs($this->admin());

        User::factory()->count(25)->create(['role' => 'student', 'status' => 'active']);

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewHas('users', fn ($users) => $users->total() === 26);
    }

    public function test_admin_can_view_user_detail(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['name' => 'Rosa Méndez', 'role' => 'teacher', 'status' => 'active']);

        $this->get(route('admin.users.show', $teacher->id))
            ->assertOk()
            ->assertSee('Rosa Méndez')
            ->assertViewHas('statistics');
    }

    public function test_non_existent_user_returns_404(): void
    {
        $this->actingAs($this->admin())->get(route('admin.users.show', 9999))->assertNotFound();
    }

    public function test_admin_can_open_create_user_page(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.users.create'))
            ->assertOk();
    }

    public function test_admin_can_create_student_user(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.users.store'), [
            'name' => 'Nuevo Estudiante',
            'email' => 'nuevo@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'student',
        ])->assertRedirect(route('admin.users.show', User::latest('id')->first()->id));

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@example.com',
            'role' => 'student',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_admin_user(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.users.store'), [
            'name' => 'Nuevo Administrador',
            'email' => 'nuevo.admin@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'admin',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo.admin@example.com',
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_store_validates_unique_email_and_password_confirmation(): void
    {
        $this->actingAs($this->admin());

        User::factory()->create(['email' => 'duplicado@example.com', 'role' => 'student']);

        $this->post(route('admin.users.store'), [
            'name' => 'Duplicado',
            'email' => 'duplicado@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'distinta123',
            'role' => 'student',
        ])->assertSessionHasErrors(['email', 'password']);

        $this->post(route('admin.users.store'), [
            'name' => 'Rol inválido',
            'email' => 'rol.invalido@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'supervisor',
        ])->assertSessionHasErrors('role');
    }

    public function test_public_registration_cannot_create_admin_users(): void
    {
        $this->post(route('register'), [
            'name' => 'Usuario Público',
            'email' => 'publico@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'publico@example.com']);
    }

    public function test_admin_cannot_change_own_status(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.users.update-status', $admin->id), ['status' => 'suspended'])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'status' => 'active',
        ]);
    }

    public function test_cannot_deactivate_the_only_active_admin(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.users.update-status', $admin->id), ['status' => 'inactive'])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_change_status_of_other_users(): void
    {
        $admin = $this->admin();

        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $this->actingAs($admin)
            ->patch(route('admin.users.update-status', $student->id), ['status' => 'suspended'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'status' => 'suspended',
        ]);
    }

    public function test_admin_can_reactivate_a_suspended_student(): void
    {
        $admin = $this->admin();

        $student = User::factory()->create(['role' => 'student', 'status' => 'suspended']);

        $this->actingAs($admin)
            ->patch(route('admin.users.update-status', $student->id), ['status' => 'active'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'status' => 'active',
        ]);
    }

    public function test_status_update_validates_allowed_statuses(): void
    {
        $admin = $this->admin();

        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $this->actingAs($admin)
            ->patch(route('admin.users.update-status', $student->id), ['status' => 'banned'])
            ->assertSessionHasErrors('status');
    }
}