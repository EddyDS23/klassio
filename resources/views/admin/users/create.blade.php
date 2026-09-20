@extends('admin.layouts.app')

@section('title', 'Nuevo usuario | Klassio')

@section('page-title')
    Crear usuario
@endsection

@section('page-actions')
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-black text-slate-600 shadow-sm transition hover:bg-slate-50">
        <span>←</span>
        Volver
    </a>
@endsection

@section('content')

    <section class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-8 shadow-lg shadow-slate-200/60">

        @if ($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm">
                <span class="text-xl">⚠️</span>
                <div>
                    <p class="font-bold">Revisa los siguientes campos:</p>
                    <ul class="mt-1 list-disc pl-4 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">

            @csrf

            <div>
                <label for="name" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Nombre completo
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       placeholder="Ej. Ana García"
                       class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                @error('name')
                    <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Correo electrónico
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       placeholder="correo@ejemplo.com"
                       class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                @error('email')
                    <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">

                <div>
                    <label for="password" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                        Contraseña
                    </label>
                    <input type="password" id="password" name="password"
                           placeholder="Mínimo 8 caracteres"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    @error('password')
                        <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                        Confirmar contraseña
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           placeholder="Repite la contraseña"
                           class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                </div>

            </div>

            <div>
                <label for="role" class="mb-1 block text-xs font-black uppercase tracking-widest text-slate-500">
                    Rol
                </label>
                <select id="role" name="role"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-medium outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                    @foreach (['student' => 'Estudiante', 'teacher' => 'Maestro', 'admin' => 'Administrador'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', 'student') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">
                    Los usuarios creados aquí se habilitan automáticamente.
                </p>
                @error('role')
                    <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="rounded-2xl bg-slate-900 px-6 py-3 text-sm font-black text-white shadow-lg shadow-slate-900/20 transition hover:bg-slate-700">
                    Crear usuario
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-black text-slate-600 transition hover:bg-slate-50">
                    Cancelar
                </a>
            </div>

        </form>

    </section>

@endsection