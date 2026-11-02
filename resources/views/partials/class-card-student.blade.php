<article
    class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">


    <!-- CABECERA DE TARJETA -->

    <div class="relative overflow-hidden bg-indigo-600 px-6 py-6 text-white">


        <div class="relative z-10">


            <span
                class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-indigo-100">

                Clase inscrita

            </span>


            <h3 class="mt-4 text-xl font-extrabold leading-tight">

                {{ $class->name }}

            </h3>

        </div>


        <div class="absolute -right-6 -top-10 h-32 w-32 rounded-full border border-white/15">
        </div>


        <div class="absolute -bottom-12 -left-8 h-32 w-32 rounded-full border border-white/10">
        </div>

    </div>



    <!-- CUERPO -->

    <div class="flex flex-1 flex-col p-6">


        @if ($class->description)
            <p class="text-sm leading-6 text-slate-500">

                {{ $class->description }}

            </p>
        @else
            <p class="text-sm italic leading-6 text-slate-400">

                Sin descripción disponible.

            </p>
        @endif



        <!-- ESTADO -->

        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">


            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">

                Estado

            </span>


            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">

                {{ match ($class->status) { 'active' => 'Activa', 'archived' => 'Archivada', default => ucfirst($class->status) } }}

            </span>

        </div>



        <!-- BOTÓN -->

        <a href="{{ route('student.class.show', $class->id) }}"
            class="mt-6 inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-4 text-base font-black text-white shadow-md transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 active:scale-95">

            ¡Entrar! 🚪

            <span class="ml-2 text-lg">

                →

            </span>

        </a>

    </div>

</article>
