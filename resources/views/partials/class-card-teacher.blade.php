<article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-md">

    {{-- Cabecera compacta --}}
    <div class="border-b border-slate-100 p-4">

        <div class="flex items-center justify-between gap-3">

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0-1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
            </div>

            @if ($class->status === 'active')
                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">Activa</span>
            @else
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">Archivada</span>
            @endif

        </div>

        <h4 class="mt-3 break-words text-lg font-bold tracking-tight text-slate-900">
            {{ $class->name }}
        </h4>

    </div>


    {{-- Contenido compacto --}}
    <div class="flex flex-1 flex-col p-4">

        <p class="line-clamp-2 text-sm leading-6 text-slate-600">
            {{ $class->description ?: 'Sin descripción disponible para esta clase.' }}
        </p>


        {{-- Código y fecha en una sola franja --}}
        <div class="mt-3 flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5">

            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Código de acceso
                </p>

                <p class="truncate font-mono text-lg font-bold tracking-widest text-emerald-700">
                    {{ $class->code }}
                </p>
            </div>

            <div class="shrink-0 text-right">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    Creada
                </p>

                <p class="text-sm font-bold text-slate-800">
                    {{ $class->created_at->format('d/m/Y') }}
                </p>
            </div>

        </div>


        {{-- Acciones en cuadrícula compacta --}}
        <div class="mt-3 flex flex-1 flex-col justify-end gap-2 border-t border-slate-100 pt-3">

            <a href="{{ route('teacher.classes.show', $class->id) }}"
               class="col-span-2 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">
                Ver clase
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>

            <div class="col-span-2 grid grid-cols-2 gap-2">

                @if ($class->status === 'active')
                    <a href="{{ route('teacher.game-sessions.create', ['class_id' => $class->id]) }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-3 py-2.5 text-sm font-bold text-white transition hover:bg-violet-700">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-3.409-.042.75.75 0 0 1-.707-.632 48.264 48.264 0 0 1-.034-2.897.75.75 0 0 1 .515-.707 48.334 48.334 0 0 1 2.887-.085v0c.355 0 .676-.186.959-.401.29-.221.634-.349 1.003-.349 1.036 0 1.875-1.007 1.875-2.25S12.535 1.5 11.5 1.5s-1.875 1.007-1.875 2.25c0 .369.128.713.349 1.003.215.283.401.604.401.959v0c0 .355-.186.676-.401.959-.221.29-.349.634-.349 1.003 0 1.036-1.007 1.875-2.25 1.875s-1.875-1.007-1.875-2.25c0-.369.128-.713.349-1.003.215-.283.401-.604.401-.959v0a.64.64 0 0 0-.657-.643 48.39 48.39 0 0 0-3.409.042.75.75 0 0 0-.707.632 48.264 48.264 0 0 0-.034 2.897.75.75 0 0 0 .515.707c.438.154.902.226 1.397.226v0c.466 0 .872-.054 1.21-.14v5.628c0 1.499 1.193 2.755 2.691 2.755h.002c.325 0 .591.266.591.591v4.875c0 .414.336.75.75.75h.006a.75.75 0 0 0 .75-.75v-4.875c0-.325.266-.591.591-.591h.002c1.498 0 2.691-1.256 2.691-2.755V9.75c.338.086.744.14 1.21.14v0c.495 0 .959-.072 1.397-.226a.75.75 0 0 0 .515-.707 48.264 48.264 0 0 0-.034-2.897.75.75 0 0 0-.707-.632 48.39 48.39 0 0 0-3.409-.042.64.64 0 0 0-.657.643Z" />
                        </svg>
                        Crear sala
                    </a>
                @endif


                <a href="{{ route('teacher.classes.grading', $class->id) }}"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-sm font-bold text-amber-700 transition hover:bg-amber-100">
                    <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                    </svg>
                    Calificaciones
                </a>


                @if ($class->status === 'active')
                    <a href="{{ route('teacher.classes.edit', $class->id) }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        Editar
                    </a>


                    <form action="{{ route('teacher.classes.archive', $class->id) }}" method="POST" class="contents">
                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-sm font-bold text-rose-700 transition hover:bg-rose-100">
                            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M21 7.5H3m4.5 0V5.25A2.25 2.25 0 0 1 9.75 3h4.5a2.25 2.25 0 0 1 2.25 2.25V7.5" />
                            </svg>
                            Archivar
                        </button>
                    </form>
                @else
                    <form action="{{ route('teacher.classes.unarchive', $class->id) }}" method="POST" class="contents">
                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-violet-200 bg-violet-50 px-3 py-2.5 text-sm font-bold text-violet-700 transition hover:bg-violet-100">
                            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 0 13.803 3.7l3.181-3.182m0-4.991v4.99" />
                            </svg>
                            Desarchivar clase
                        </button>
                    </form>
                @endif

            </div>

        </div>

    </div>

</article>
