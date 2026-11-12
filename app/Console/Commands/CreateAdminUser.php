<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

#[Signature('klassio:create-admin {--name= : Nombre del administrador} {--email= : Email del administrador} {--password= : Contraseña del administrador}')]
#[Description('Crea o promueve a administrador un usuario para despliegues frescos.')]
class CreateAdminUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Nombre del administrador');
        $email = $this->option('email') ?: $this->ask('Email del administrador');
        $password = $this->option('password') ?: $this->secret('Contraseña (mínimo 8 caracteres)');

        $validated = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:8', 'max:50'],
            ],
        );

        if ($validated->fails()) {
            foreach ($validated->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'admin',
                'status' => 'active',
            ],
        );

        $this->info("Administrador listo: {$user->email} (role: {$user->role}, estado: {$user->status}).");

        return self::SUCCESS;
    }
}
