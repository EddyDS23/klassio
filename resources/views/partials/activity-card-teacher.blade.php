                    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60 transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                        <!-- Cabecera de tarjeta -->
                        <div class="border-b border-slate-100 bg-slate-50 px-6 py-5">

                            <div class="flex items-start justify-between gap-4">

                                <div class="min-w-0">

                                    <p class="mb-2 text-xs font-black uppercase tracking-widest text-emerald-600">
                                        Actividad educativa
                                    </p>

                                    <h3 class="break-words text-xl font-black text-slate-900">
                                        {{ $activity->title }}
                                    </h3>

                                </div>

                                @if ($activity->status === 'draft')

                                    <span class="shrink-0 rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700">
                                        Borrador
                                    </span>

                                @else

                                    <span class="shrink-0 rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">
                                        {{ $activity->status }}
                                    </span>

                                @endif

                            </div>

                        </div>


                        <!-- Información -->
                        <div class="space-y-4 px-6 py-6">

                            <div class="grid gap-3 sm:grid-cols-2">

                                <div class="rounded-2xl bg-emerald-50 p-4">
                                    <p class="text-xs font-bold text-emerald-700">
                                        Tipo
                                    </p>

                                    <p class="mt-1 text-sm font-black capitalize text-slate-900">
                                        @include('partials.game-type-label', ['type' => $activity->type])
                                    </p>
                                </div>


                                <div class="rounded-2xl bg-cyan-50 p-4">
                                    <p class="text-xs font-bold text-cyan-700">
                                        Modo
                                    </p>

                                    <p class="mt-1 text-sm font-black capitalize text-slate-900">
                                        {{ $activity->mode }}
                                    </p>
                                </div>


                                <div class="rounded-2xl bg-teal-50 p-4">
                                    <p class="text-xs font-bold text-teal-700">
                                        Puntuación máxima
                                    </p>

                                    <p class="mt-1 text-sm font-black text-slate-900">
                                        {{ $activity->max_score }} puntos
                                    </p>
                                </div>


                                <div class="rounded-2xl bg-slate-100 p-4">
                                    <p class="text-xs font-bold text-slate-500">
                                        Tiempo límite
                                    </p>

                                    <p class="mt-1 text-sm font-black text-slate-900">
                                        {{ $activity->time_limit }} segundos
                                    </p>
                                </div>

                            </div>


                            @if ($activity->due_at)

                                <div class="flex items-start gap-3 rounded-2xl border border-orange-100 bg-orange-50 p-4">

                                    <span class="text-xl">📅</span>

                                    <div>
                                        <p class="text-xs font-bold text-orange-700">
                                            Fecha límite
                                        </p>

                                        <p class="mt-1 text-sm font-black text-slate-800">
                                            {{ $activity->due_at }}
                                        </p>
                                    </div>

                                </div>

                            @endif


                            <!-- Acción principal -->
                            <a
                                href="{{ route('teacher.activities.show', $activity->id) }}"
                                class="flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-black text-white transition hover:bg-slate-800"
                            >
                                Ver actividad
                                <span>→</span>
                            </a>


                            <!-- Acciones de borrador -->
                            @if ($activity->status === 'draft')

                                <div class="border-t border-slate-100 pt-4">

                                    <p class="mb-3 text-xs font-black uppercase tracking-widest text-slate-400">
                                        Opciones de configuración
                                    </p>

                                    <div class="grid gap-3 sm:grid-cols-2">

                                        <a
                                            href="{{ route('teacher.activities.edit', $activity->id) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-700 transition hover:bg-emerald-100"
                                        >
                                            ✏️ Editar actividad
                                        </a>


                                        {{-- Configuración del crucigrama --}}
                                        @if ($activity->type === 'crossword')

                                            @if ($activity->crossword && $activity->crossword->words()->exists())

                                                <a
                                                    href="{{ route('teacher.crossword.edit', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm font-black text-cyan-700 transition hover:bg-cyan-100"
                                                >
                                                    🧩 Editar crucigrama
                                                </a>

                                            @else

                                                <a
                                                    href="{{ route('teacher.crossword.configure', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm font-black text-cyan-700 transition hover:bg-cyan-100"
                                                >
                                                    🧩 Configurar crucigrama
                                                </a>

                                            @endif

                                        @endif


                                        {{-- Configuración del Kahoot --}}
                                        @if ($activity->type === 'kahoot')

                                            @if ($activity->kahoot && $activity->kahoot->questions()->exists())

                                                <a
                                                    href="{{ route('teacher.kahoot.edit', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm font-black text-violet-700 transition hover:bg-violet-100"
                                                >
                                                    🏆 Editar Kahoot
                                                </a>

                                            @else

                                                <a
                                                    href="{{ route('teacher.kahoot.configure', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm font-black text-violet-700 transition hover:bg-violet-100"
                                                >
                                                    🏆 Configurar Kahoot
                                                </a>

                                            @endif

                                        @endif


                                        {{-- Configuración de unir palabras --}}
                                        @if ($activity->type === 'matching')

                                            @if ($activity->matching && $activity->matching->items()->exists())

                                                <a
                                                    href="{{ route('teacher.matching.edit', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-black text-orange-700 transition hover:bg-orange-100"
                                                >
                                                    🔗 Editar unir palabras
                                                </a>

                                            @else

                                                <a
                                                    href="{{ route('teacher.matching.configure', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-black text-orange-700 transition hover:bg-orange-100"
                                                >
                                                    🔗 Configurar unir palabras
                                                </a>

                                            @endif

                                        @endif

                                        {{-- Configuración de la ruleta --}}
                                        @if ($activity->type === 'roulette')

                                            @if ($activity->roulette && $activity->roulette->items()->exists())

                                                <a
                                                    href="{{ route('teacher.roulette.edit', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-fuchsia-200 bg-fuchsia-50 px-4 py-3 text-sm font-black text-fuchsia-700 transition hover:bg-fuchsia-100"
                                                >
                                                    🎡 Editar ruleta
                                                </a>

                                            @else

                                                <a
                                                    href="{{ route('teacher.roulette.configure', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-fuchsia-200 bg-fuchsia-50 px-4 py-3 text-sm font-black text-fuchsia-700 transition hover:bg-fuchsia-100"
                                                >
                                                    🎡 Configurar ruleta
                                                </a>

                                            @endif

                                        @endif

                                    </div>

                                </div>

                            @endif

                        </div>
                    </article>
