<div class="news-card h-100">
    <div class="news-card-img" style="background-image: url('{{ $news->image ? asset('storage/'.$news->image) : asset('images/placeholder.jpg') }}');">
        @if(!$news->image)
            <span>Gambar Berita</span>
        @endif
    </div>
    <div class="news-card-body p-3">
        <div class="category-badge">{{ $news->category->name }}</div>
        <h5><a href="{{ route('news.show', $news) }}" class="text-decoration-none text-dark">{{ $news->title }}</a></h5>
        <p class="text-muted small">{{ Str::limit(strip_tags($news->content), 100) }}</p>
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $news->published_at->diffForHumans() }}</small>
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
