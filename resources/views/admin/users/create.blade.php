@extends('admin.layouts.app')

@section('title', 'Nuevo usuario | Klassio')

@section('page-title')
    Crear usuario
@endsection

@section('page-actions')
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver
    </a>
@endsection

@section('content')
<div class="mx-auto max-w-2xl px-4 py-6 sm:px-6">

    <section class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
        
        <!-- Encabezado de la tarjeta -->
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-5 sm:px-8">
            <div class="flex items-center gap-3.5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Datos de la cuenta</h2>
                    <p class="text-xs font-medium text-slate-500">Completa la información para registrar un nuevo usuario en la plataforma.</p>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">

            <!-- Alertas de validación -->
            @if ($errors->any())
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50/80 p-4 text-red-800">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <div>
                        <p class="text-sm font-bold">Revisa los siguientes campos:</p>
                        <ul class="mt-1.5 list-disc space-y-0.5 pl-4 text-xs font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

                <!-- Nombre completo -->
                <div>
                    <label for="name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Nombre completo
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           placeholder="Ej. Ana García"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    @error('name')
                        <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Correo electrónico -->
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Correo electrónico
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           placeholder="correo@ejemplo.com"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    @error('email')
                        <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseñas -->
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                            Contraseña
                        </label>
                        <input type="password" id="password" name="password"
                               placeholder="Mínimo 8 caracteres"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                        @error('password')
                            <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                            Confirmar contraseña
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="Repite la contraseña"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                    </div>
                </div>

                <!-- Rol -->
                <div>
                    <label for="role" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Rol del usuario
                    </label>
                    <select id="role" name="role"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10">
                        @foreach (['student' => 'Estudiante', 'teacher' => 'Maestro', 'admin' => 'Administrador'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', 'student') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-slate-500">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <span>Los usuarios creados desde este panel se habilitan automáticamente.</span>
                    </div>
                    @error('role')
                        <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
                    <a href="{{ route('admin.users.index') }}"
                       class="inline-flex justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Crear usuario
                    </button>
                </div>

            </form>
        </div>

    </section>

</div>
@endsection