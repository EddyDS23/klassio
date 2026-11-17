<article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-orange-200 hover:shadow-md">

    {{-- Cabecera compacta: ícono + nombre --}}
    <div class="border-b border-slate-100 p-4">

        <div class="flex items-center gap-3">

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-700 ring-1 ring-orange-100">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
            </div>

            <h4 class="min-w-0 break-words text-lg font-bold leading-snug tracking-tight text-slate-900">
                {{ $class->name }}
            </h4>

        </div>

    </div>

    {{-- Contenido compacto --}}
    <div class="flex flex-1 flex-col p-4">

        <p class="line-clamp-2 text-sm leading-6 text-slate-600">
            {{ $class->description ?: 'Sin descripción disponible para esta clase.' }}
        </p>

        {{-- Acción --}}
        <div class="mt-3 flex flex-1 flex-col justify-end border-t border-slate-100 pt-3">

            <a href="{{ route('student.class.show', $class->id) }}"
               class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-orange-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-orange-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-700 focus-visible:ring-offset-2">
                Entrar
                <svg class="h-4 w-4 transition group-hover:translate-x-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>

        </div>

    </div>

</article>
