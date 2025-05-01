@extends('layouts.user-dashboard')

@section('header', 'Bookmark Saya')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">Daftar Bookmark</h5>

        @if($bookmarks->isEmpty())
            <div class="alert alert-info">
                <p class="mb-0">Anda belum menyimpan bookmark. Jelajahi berita dan simpan yang Anda sukai!</p>
            </div>
        @else
            <div class="list-group mb-4">
                @foreach($bookmarks as $bookmark)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5>
                                <a href="{{ route('news.show', $bookmark->news) }}" class="text-decoration-none text-dark">
                                    {{ $bookmark->news->title }}
                                </a>
                            </h5>
                            <p class="text-muted mb-1">{{ Str::limit(strip_tags($bookmark->news->content), 100) }}</p>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge bg-primary me-2">{{ $bookmark->news->category->name }}</span>
                                <small class="text-muted">Disimpan: {{ $bookmark->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('news.show', $bookmark->news) }}" class="btn btn-sm btn-outline-primary me-2">
                                Baca
                            </a>
                            <form action="{{ route('bookmark.destroy', $bookmark->news) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus bookmark">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center">
                {{ $bookmarks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
