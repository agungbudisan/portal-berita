@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-md-8">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.show', $news->category) }}">{{ $news->category->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $news->title }}</li>
            </ol>
        </nav>

        <!-- News Detail -->
        <article>
            <div class="category-badge">{{ $news->category->name }}</div>
            <h2 class="mb-3">{{ $news->title }}</h2>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <small class="text-muted">Dipublikasikan: {{ $news->published_at->format('d F Y, H:i') }} WIB</small>
                    <br>
                    <small class="text-muted">Sumber: {{ $news->source }}</small>
                </div>
                <div>
                    @auth
                        <div x-data="{ isBookmarked: {{ $isBookmarked ? 'true' : 'false' }} }">
                            <button type="button"
                                    class="btn btn-sm"
                                    x-bind:class="isBookmarked ? 'btn-primary' : 'btn-outline-primary'"
                                    @click="
                                        isBookmarked = !isBookmarked;
                                        fetch('{{ $isBookmarked ? route('bookmark.destroy', $news) : route('bookmark.store', $news) }}', {
                                            method: isBookmarked ? 'POST' : 'DELETE',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            }
                                        });
                                    ">
                                <i class="bi" x-bind:class="isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark'"></i>
                                <span x-text="isBookmarked ? 'Tersimpan' : 'Simpan'"></span>
                            </button>
                        </div>
                    @endauth

                    <button class="btn btn-sm btn-outline-primary" x-data @click="
                        navigator.share ?
                            navigator.share({
                                title: '{{ $news->title }}',
                                text: '{{ Str::limit(strip_tags($news->content), 100) }}',
                                url: window.location.href
                            }) :
                            prompt('Copy link to share:', window.location.href)
                    ">
                        <i class="bi bi-share"></i> Bagikan
                    </button>
                </div>
            </div>

            <!-- Featured Image -->
            @if($news->image)
            <div class="mb-4">
                <img src="{{ asset('storage/'.$news->image) }}" class="img-fluid rounded" alt="{{ $news->title }}">
            </div>
            @else
            <div class="mb-4" style="height: 400px; background-color: #dee2e6; display: flex; align-items: center; justify-content: center;">
                <span>Gambar Utama Berita</span>
            </div>
            @endif

            <!-- News Content -->
            <div class="mb-4">
                {!! nl2br(e($news->content)) !!}
            </div>

            <!-- Tags -->
            <div class="mb-4">
                <span class="badge bg-secondary me-1">{{ $news->category->name }}</span>
                <span class="badge bg-secondary me-1">Berita</span>
                <span class="badge bg-secondary me-1">{{ $news->created_at->format('Y') }}</span>
            </div>
        </article>

        <!-- Related News -->
        <div class="mb-4">
            <h5 class="wgt-title">Berita Terkait</h5>
            <div class="row">
                @foreach($relatedNews as $related)
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="row g-0">
                            <div class="col-md-4" style="background-color: #dee2e6; display: flex; align-items: center; justify-content: center; {{ $related->image ? 'background-image: url('.asset('storage/'.$related->image).'); background-size: cover; background-position: center;' : '' }}">
                                @if(!$related->image)<span>Gambar</span>@endif
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h6 class="card-title"><a href="{{ route('news.show', $related) }}" class="text-decoration-none text-dark">{{ $related->title }}</a></h6>
                                    <small class="text-muted">{{ $related->published_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Comment Section -->
        <div class="comment-section">
            <h5 class="wgt-title">Komentar ({{ $news->approvedComments->count() }})</h5>

            <!-- Login prompt for guest -->
            @guest
                <div class="alert alert-info mb-3">
                    <p class="mb-2">Login untuk memberikan komentar</p>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary">Register</a>
                </div>
            @else
                <div x-data="{
                    content: '',
                    submitting: false,
                    characterCount: 0,
                    maxLength: 1000
                }">
                    <form @submit.prevent="
                        submitting = true;
                        fetch('{{ route('comment.store', $news) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ content: content })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                content = '';
                                window.location.reload();
                            }
                        })
                        .finally(() => {
                            submitting = false;
                        })
                    ">
                        <div class="mb-3">
                            <label for="content" class="form-label">Tulis Komentar</label>
                            <textarea
                                class="form-control"
                                x-model="content"
                                @input="characterCount = content.length"
                                id="content"
                                name="content"
                                rows="3"
                                required
                                maxlength="1000"></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <span x-show="characterCount > 0" x-text="characterCount + ' / ' + maxLength"></span>
                                <span x-show="characterCount >= maxLength" class="text-danger">Maksimal karakter tercapai</span>
                            </div>
                        </div>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            x-bind:disabled="submitting || content.length === 0 || content.length > maxLength">
                            <span x-show="submitting">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Mengirim...
                            </span>
                            <span x-show="!submitting">Kirim Komentar</span>
                        </button>
                    </form>
                </div>
            @endguest

            <!-- Comments List -->
            <div class="mb-3 mt-4">
                @foreach($news->approvedComments as $comment)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">{{ $comment->user->name }}</h6>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="card-text">{{ $comment->content }}</p>
                        @auth
                            @if(auth()->id() === $comment->user_id)
                            <div x-data="{ showEditForm: false, editContent: '{{ addslashes($comment->content) }}', submitting: false }" class="d-flex justify-content-end">
                                <button
                                    class="btn btn-sm btn-outline-secondary me-2"
                                    @click="showEditForm = true"
                                    x-show="!showEditForm">
                                    Edit
                                </button>

                                <div x-show="showEditForm" class="w-100 mt-2">
                                    <form @submit.prevent="
                                        submitting = true;
                                        fetch('{{ route('comment.update', $comment) }}', {
                                            method: 'PUT',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ content: editContent })
                                        })
                                        .then(response => {
                                            if (response.ok) {
                                                window.location.reload();
                                            }
                                        })
                                        .finally(() => {
                                            submitting = false;
                                        })
                                    ">
                                        <div class="mb-2">
                                            <textarea class="form-control" x-model="editContent" rows="3" required></textarea>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-sm btn-secondary me-2" @click="showEditForm = false">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-primary" x-bind:disabled="submitting">
                                                <span x-show="submitting">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    Menyimpan...
                                                </span>
                                                <span x-show="!submitting">Simpan</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <form x-data="{ submitting: false }"
                                      x-show="!showEditForm"
                                      action="{{ route('comment.destroy', $comment) }}"
                                      method="POST"
                                      @submit.prevent="
                                        submitting = true;
                                        fetch('{{ route('comment.destroy', $comment) }}', {
                                            method: 'DELETE',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            }
                                        })
                                        .then(() => window.location.reload())
                                      ">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" x-bind:disabled="submitting">
                                        <span x-show="submitting">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </span>
                                        <span x-show="!submitting">Hapus</span>
                                    </button>
                                </form>
                            </div>
                            @endif
                        @endauth
                    </div>
                </div>
                @endforeach

                @if($news->approvedComments->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted">Belum ada komentar. Jadilah yang pertama memberikan komentar!</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Popular News -->
        <div class="mb-4">
            <h5 class="wgt-title">Berita Populer</h5>
            <div class="list-group">
                @foreach($popularNews as $popular)
                <a href="{{ route('news.show', $popular) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ $popular->title }}</h6>
                    </div>
                    <small class="text-muted">{{ $popular->published_at->diffForHumans() }}</small>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Categories -->
        <div class="mb-4">
            <h5 class="wgt-title">Kategori</h5>
            <div class="list-group">
                @foreach(\App\Models\Category::withCount('news')->orderBy('news_count', 'desc')->take(5)->get() as $category)
                <a href="{{ route('category.show', $category) }}" class="list-group-item d-flex justify-content-between align-items-center {{ $news->category_id == $category->id ? 'active' : '' }}">
                    {{ $category->name }}
                    <span class="badge {{ $news->category_id == $category->id ? 'bg-light text-dark' : 'bg-primary' }} rounded-pill">{{ $category->news_count }}</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Newsletter -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Berlangganan Newsletter</h5>
                <p class="card-text">Dapatkan update berita terbaru langsung ke email Anda</p>
                <div x-data="{ email: '', submitted: false, valid: false }">
                    <div class="input-group">
                        <input
                            type="email"
                            class="form-control"
                            placeholder="Email Anda"
                            x-model="email"
                            @input="valid = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/.test(email)">
                        <button
                            class="btn btn-primary"
                            type="button"
                            x-bind:disabled="!valid || submitted"
                            @click="
                                submitted = true;
                                setTimeout(() => {
                                    alert('Terima kasih telah berlangganan newsletter kami!');
                                    email = '';
                                    submitted = false;
                                }, 1000);
                            ">
                            <span x-show="submitted">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                            <span x-show="!submitted">Subscribe</span>
                        </button>
                    </div>
                    <small x-show="email && !valid" class="text-danger">
                        Email tidak valid
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
