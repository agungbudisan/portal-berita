<div class="card h-100 border-0 shadow-sm">
    <div class="row g-0 h-100">
        <div class="col-4">
            <div class="h-100 position-relative" style="min-height: 80px;">
                <div class="news-card-img h-100" style="background-image: url('{{ $news->image_url ? $news->image_url : asset('images/placeholder.jpg') }}');">
                    @if(!$news->image)
                        <span>Gambar</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card-body p-2">
                <h6 class="card-title mb-1" style="font-size: 0.95rem;">
                    @if($news->source_url)
                        <a href="{{ $news->source_url }}" target="_blank" class="text-decoration-none text-dark stretched-link">
                            {{ Str::limit($news->title, 50) }} <i class="bi bi-box-arrow-up-right text-muted small"></i>
                        </a>
                    @else
                        <a href="{{ route('news.show', $news) }}" class="text-decoration-none text-dark stretched-link">
                            {{ Str::limit($news->title, 50) }}
                        </a>
                    @endif
                </h6>
                <div class="d-flex align-items-center">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i> {{ $news->published_at->diffForHumans() }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.news-card-img {
    background-color: #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    background-size: cover;
    background-position: center;
}
</style>
