<article
    class="group flex flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl">

    {{-- Cabecera de tarjeta --}}
                    <div class="bg-gradient-to-br from-emerald-600 to-teal-600 p-6 text-white">

                        <div class="flex items-start justify-between gap-4">

                            <div
                                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 text-3xl shadow-inner backdrop-blur-sm">
                                📚
                            </div>

                            @if ($class->status === 'active')
                                <span
                                    class="rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">
                                    Activa
                                </span>
                            @else
                                <span
                                    class="rounded-full bg-slate-900/20 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">
                                    Archivada
                                </span>
                            @endif

                        </div>

                        <h4 class="mt-5 break-words text-2xl font-black tracking-tight">
                            {{ $class->name }}
                        </h4>

                    </div>


                    {{-- Contenido --}}
                    <div class="flex flex-1 flex-col p-6">

                        <p class="min-h-[72px] text-sm font-medium leading-6 text-slate-600">
                            {{ $class->description ?: 'Sin descripción disponible para esta clase.' }}
                        </p>


                        {{-- Datos --}}
                        <div class="mt-6 space-y-4">

                            <div class="rounded-2xl bg-slate-50 p-4">

                                <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                                    Código de acceso
                                </p>

                                <p class="mt-2 break-all text-xl font-black tracking-widest text-emerald-700">
                                    {{ $class->code }}
                                </p>

                            </div>


                            <div class="grid grid-cols-2 gap-3">

                                <div class="rounded-2xl border border-slate-100 p-3">

                                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                                        Estado
                                    </p>

                                    <p class="mt-1 text-sm font-black text-slate-800">
                                        @if ($class->status === 'active')
                                            Activa
                                        @else
                                            Archivada
                                        @endif
                                    </p>

                                </div>


                                <div class="rounded-2xl border border-slate-100 p-3">

                                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                                        Creada
                                    </p>

                                    <p class="mt-1 text-sm font-black text-slate-800">
                                        {{ $class->created_at->format('d/m/Y') }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Acciones --}}
                        <div class="mt-6 space-y-3 border-t border-slate-100 pt-6">

                            <a href="{{ route('teacher.classes.show', $class->id) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-700">

                                Ver clase
                                <span>→</span>
                            </a>


                            <a href="{{ route('teacher.classes.grading', $class->id) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm font-black text-amber-700 transition hover:bg-amber-100">

                                ⭐
                                Calificaciones
                            </a>


                            @if ($class->status === 'active')
                                <a href="{{ route('teacher.classes.edit', $class->id) }}"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700">

                                    ✏️
                                    Editar clase
                                </a>


                                <form action="{{ route('teacher.classes.archive', $class->id) }}" method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-black text-rose-700 transition hover:bg-rose-100">

                                        📦
                                        Archivar clase
                                    </button>

                                </form>
                            @else
                                <form action="{{ route('teacher.classes.unarchive', $class->id) }}" method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-5 py-3 text-sm font-black text-cyan-700 transition hover:bg-cyan-100">

                                        ↩️
                                        Desarchivar clase
                                    </button>

                                </form>
                            @endif

                        </div>

                    </div>
</article>
