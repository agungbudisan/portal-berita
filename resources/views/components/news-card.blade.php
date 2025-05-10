<div class="news-card h-100">
    <div class="news-card-img" style="background-image: url('{{ $news->image ? (Str::startsWith($news->image, 'http') ? $news->image : asset('storage/'.$news->image)) : asset('images/placeholder.jpg') }}');">
        @if(!$news->image)
            <span><i class="bi bi-image me-2"></i>Gambar Berita</span>
        @endif

        <div class="position-absolute bottom-0 start-0 w-100 p-3">
            <div class="category-badge">{{ $news->category->name }}</div>
        </div>
    </div>
    <div class="news-card-body">
        <h5 class="mb-2 news-title">
            @if($news->source_url)
                <a href="{{ $news->source_url }}" target="_blank" class="text-decoration-none text-dark">
                    {{ $news->title }} <i class="bi bi-box-arrow-up-right text-muted small"></i>
                </a>
            @else
                <a href="{{ route('news.show', $news) }}" class="text-decoration-none text-dark">
                    {{ $news->title }}
                </a>
            @endif
        </h5>
        <p class="text-muted small mb-3">{{ Str::limit(strip_tags($news->content), 100) }}</p>
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-clock me-1 small"></i>
                <small class="text-muted">{{ $news->published_at->diffForHumans() }}</small>
                @if($news->source)
                    <small class="text-muted ms-2">
                        <span class="mx-1">•</span>
                        <i class="bi bi-newspaper me-1 small"></i>
                        {{ $news->source }}
                    </small>
                @endif
            </div>
            @auth
                <div x-data="{ isBookmarked: {{ $news->isBookmarkedByUser(auth()->user()) ? 'true' : 'false' }} }">
                    <button type="button"
                            class="bookmark-btn"
                            @click="
                                $dispatch('bookmark-toggle');
                                isBookmarked = !isBookmarked;
                                fetch('{{ $news->isBookmarkedByUser(auth()->user())
                                    ? route('bookmark.destroy', $news)
                                    : route('bookmark.store', $news) }}', {
                                    method: isBookmarked ? 'POST' : 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                });
                            "
                            x-bind:title="isBookmarked ? 'Hapus dari bookmark' : 'Simpan ke bookmark'">
                        <i class="bi" x-bind:class="isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark'"></i>
                    </button>
                </div>
            @endauth
        </div>
    </div>
</div>
