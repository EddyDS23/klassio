@extends('student.layouts.app')

@section('title', 'Compañeros')

@section('page-title', 'Compañeros')

@section('page-actions')
    <a href="{{ route('student.class.show', $class->id) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver a la clase
    </a>
@endsection

@section('content')

    <div class="mx-auto max-w-7xl">

        {{-- Encabezado --}}
        <section class="rounded-2xl bg-orange-600 p-4 text-white shadow-xl shadow-orange-200 sm:p-8">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-white ring-1 ring-white/20">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        ¡Mi salón!
                    </span>

                    <h2 class="mt-2 break-words text-xl font-bold tracking-tight sm:mt-3 sm:text-3xl">
                        Compañeros
                    </h2>

                    <p class="mt-1 text-sm text-orange-100 sm:mt-2 sm:text-base">
                        Mira quién juega contigo en {{ $class->name }}.
                    </p>

                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20 md:flex">
                    <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Lista de compañeros --}}
        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div class="flex flex-wrap items-center justify-between gap-3">

                <div class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-700 ring-1 ring-orange-100">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-900 sm:text-lg">
                            Mis compañeros
                        </h3>

                        <p class="text-sm text-slate-500">
                            Todos los estudiantes de la clase.
                        </p>
                    </div>
                </div>

                @if ($students->isNotEmpty())
                    <span class="inline-flex rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                        {{ $students->total() }} en total
                    </span>
                @endif

            </div>

            @if ($students->isEmpty())

                {{-- Estado vacío --}}
                <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 text-orange-400">
                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a12 12 0 0 1-1.5 3.062m3.75-3.062a12 12 0 0 0-2.812 6.875m-4.5-1.5a12 12 0 0 1-1.5 5.5M3 9.75a2.25 2.25 0 0 1 2.25-2.25h1.5a2.25 2.25 0 0 1 2.25 2.25v2.25a2.25 2.25 0 0 0 2.25 2.25h1.5a2.25 2.25 0 0 0 2.25-2.25V9.75a2.25 2.25 0 0 1 2.25-2.25h1.5a2.25 2.25 0 0 1 2.25 2.25c0 6.55-3.448 10.5-9.75 10.5S3 16.3 3 9.75Z" />
                        </svg>
                    </div>

                    <h4 class="mt-4 text-lg font-bold text-slate-800">
                        Todavía no hay compañeros
                    </h4>

                    <p class="mt-2 text-sm text-slate-500">
                        Cuando se unan estudiantes a la clase, aparecerán aquí.
                    </p>

                </div>

            @else

                {{-- Tarjetas --}}
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">

                    @foreach ($students as $enrollment)
                        <article class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-1 hover:border-orange-200 hover:bg-white hover:shadow-md">

                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-orange-600 text-lg font-black text-white shadow-sm transition group-hover:bg-orange-700">
                                {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                            </div>

                            <div class="min-w-0">
                                <h4 class="truncate font-bold text-slate-800">
                                    {{ $enrollment->student->name }}
                                </h4>

                                <p class="mt-0.5 truncate text-sm text-slate-500">
                                    {{ $enrollment->student->email }}
                                </p>
                            </div>

                        </article>
                    @endforeach

                </div>

                @if ($students->hasPages())
                    <div class="mt-6 border-t border-slate-200 pt-4">
                        {{ $students->links('vendor.pagination.student') }}
                    </div>
                @endif

            @endif

        </section>

    </div>

@endsection
