<div class="card shadow-sm h-100 mb-4">
    <div class="card-body d-flex flex-column justify-content-between">
        <div class="d-flex align-items-center mb-3">
            <div class="me-3">
                <i class="bx bx-{{ $card['icon'] ?? 'user' }} text-primary" style="font-size:2rem;"></i>
            </div>
            <div>
                <h5 class="card-title mb-1">{{ $card['title'] ?? '' }}</h5>
                <p class="card-text text-muted mb-0">{{ $card['description'] ?? '' }}</p>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route($card['route']) }}" class="btn btn-outline-primary w-100">Acceder</a>
        </div>
    </div>
</div> 