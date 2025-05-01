@extends('layouts.app')

@section('title', 'Kategori: ' . $category->name)

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-md-8">
        <!-- Category Title -->
        <div class="mb-4">
            <h3>Kategori: {{ $category->name }}</h3>
            <p class="text-muted">Temukan berita terbaru seputar {{ strtolower($category->name) }}</p>
        </div>

        <!-- News Grid -->
        <div class="row">
            @foreach($news as $item)
            <div class="col-md-6 mb-4">
                @include('components.news-card', ['news' => $item])
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $news->links() }}
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Search -->
        <div class="mb-4">
            <h5 class="wgt-title">Pencarian</h5>
            <form action="{{ route('news.search') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="Cari berita..." value="{{ request('query') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>
        </div>

        <!-- Popular News in Category -->
        <div class="mb-4">
            <h5 class="wgt-title">Berita Populer di Kategori Ini</h5>
            <div class="list-group">
                @foreach($popularInCategory as $news)
                <a href="{{ route('news.show', $news) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $news->title }}</h6>
                    </div>
                    <small class="text-muted">{{ $news->published_at->diffForHumans() }}</small>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Other Categories -->
        <div class="mb-4">
            <h5 class="wgt-title">Kategori Lainnya</h5>
            <div class="list-group">
                @foreach($otherCategories as $otherCategory)
                <a href="{{ route('category.show', $otherCategory) }}" class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $otherCategory->name }}
                    <span class="badge bg-primary rounded-pill">{{ $otherCategory->news_count }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
