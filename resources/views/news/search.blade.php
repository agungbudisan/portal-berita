@extends('layouts.app')

@section('title', 'Hasil Pencarian: ' . $query)

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-md-8">
        <!-- Search Results -->
        <div class="mb-4">
            <h3>Hasil Pencarian: "{{ $query }}"</h3>
            <p class="text-muted">Ditemukan {{ $news->total() }} berita</p>
        </div>

        @if($news->count() > 0)
            <!-- News List -->
            <div class="row">
                @foreach($news as $item)
                <div class="col-md-6 mb-4">
                    @include('components.news-card', ['news' => $item])
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $news->appends(['query' => $query])->links() }}
            </div>
        @else
            <div class="alert alert-info">
                <p class="mb-0">Tidak ada hasil yang ditemukan untuk kata kunci "{{ $query }}". Silakan coba dengan kata kunci lainnya.</p>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Search -->
        <div class="mb-4">
            <h5 class="wgt-title">Pencarian</h5>
            <form action="{{ route('news.search') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="Cari berita..." value="{{ $query }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>
        </div>

        <!-- Categories -->
        <div class="mb-4">
            <h5 class="wgt-title">Kategori</h5>
            <div class="list-group">
                @foreach(\App\Models\Category::withCount('news')->orderBy('news_count', 'desc')->take(5)->get() as $category)
                <a href="{{ route('category.show', $category) }}" class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $category->name }}
                    <span class="badge bg-primary rounded-pill">{{ $category->news_count }}</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Login Prompt for Guest -->
        @include('components.guest-login-prompt')
    </div>
</div>
@endsection
