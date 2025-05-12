@foreach($news as $item)
<div class="col news-article">
    @include('components.news-card', ['news' => $item])
</div>
@endforeach
