<?php

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_admin_user_with_role_and_status(): void
    {
        $this->artisan('klassio:create-admin', [
            '--name' => 'Administrador',
            '--email' => 'admin@example.com',
            '--password' => 'ClaveSegura123',
        ])->assertSuccessful();

        $user = User::where('email', 'admin@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('admin', $user->role);
        $this->assertSame('active', $user->status);
        $this->assertTrue(Hash::check('ClaveSegura123', $user->password));
    }

    public function test_promotes_existing_user_instead_of_duplicating(): void
    {
        User::factory()->create([
            'email' => 'estudiante@example.com',
            'role' => 'student',
            'status' => 'active',
        ]);

        $this->artisan('klassio:create-admin', [
            '--name' => 'Promovido',
            '--email' => 'estudiante@example.com',
            '--password' => 'ClaveSegura123',
        ])->assertSuccessful();

        $this->assertSame(1, User::where('email', 'estudiante@example.com')->count());

        $user = User::where('email', 'estudiante@example.com')->first();
        $this->assertSame('admin', $user->role);
    }

    public function test_rejects_invalid_email_and_short_password(): void
    {
        $this->artisan('klassio:create-admin', [
            '--name' => 'Administrador',
            '--email' => 'no-es-un-email',
            '--password' => '123',
        ])->assertFailed();

        $this->assertSame(0, User::where('email', 'no-es-un-email')->count());
    }
}
