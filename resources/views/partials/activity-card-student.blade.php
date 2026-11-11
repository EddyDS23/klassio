<div class="col-md-6 col-lg-4">
    <div class="card h-100 border-0 shadow-sm rounded-4 p-3 style="background: #ffffff; border: 1px solid #e9edf7 !important;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge rounded-pill text-bg-light text-primary fw-bold">
                {{ match($activity->type) {
                    'kahoot' => '⚡ Kahoot',
                    'crossword' => '🧩 Crucigrama',
                    'word_search' => '🔍 Sopa de letras',
                    'matching' => '🔗 Unir',
                    'roulette' => '🎡 Ruleta',
                    default => $activity->type
                } }}
            </span>
            <small class="fw-bold text-muted">⭐ {{ $activity->max_score }} pts</small>
        </div>

        <h3 class="h5 fw-bold text-dark mb-2" style="font-family: 'Fredoka', cursive;">{{ $activity->title }}</h3>

        @if($activity->description)
            <p class="text-muted small mb-3 text-truncate">{{ $activity->description }}</p>
        @endif

        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
            <span class="small text-muted fw-semibold">
                <i class="fa-regular fa-clock me-1"></i>
                {{ $activity->time_limit ? gmdate('i:s', $activity->time_limit).' min' : 'Sin tiempo' }}
            </span>
            <a href="{{ route('student.activities.show', $activity->id) }}" class="btn btn-sm btn-primary fw-bold rounded-3 px-3">
                ¡Jugar! 🎮
            </a>
        </div>
    </div>
</div>