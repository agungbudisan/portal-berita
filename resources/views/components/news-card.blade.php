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
                @if($news->isBookmarkedByUser(auth()->user()))
                    <form action="{{ route('bookmark.destroy', $news) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bookmark-btn" title="Hapus dari bookmark">
                            <i class="bi bi-bookmark-fill"></i>
                        </button>
                    </form>
                @else
                    <form action="{{ route('bookmark.store', $news) }}" method="POST">
                        @csrf
                        <button type="submit" class="bookmark-btn" title="Simpan ke bookmark">
                            <i class="bi bi-bookmark"></i>
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</div>
