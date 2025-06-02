@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none"><i class="bi bi-house-door me-1"></i> Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.show', $news->category) }}" class="text-decoration-none">{{ $news->category->name }}</a></li>
                <li class="breadcrumb-item active text-truncate" aria-current="page">{{ Str::limit($news->title, 40) }}</li>
            </ol>
        </nav>

        <!-- News Detail Card -->
        <article class="bg-white rounded shadow-sm p-4 mb-4">
            <div class="d-flex align-items-center mb-3">
                <div class="category-badge">{{ $news->category->name }}</div>
                <div class="ms-auto">
                    <!-- Status badge for drafts (visible to admins) -->
                    @if($news->status === 'draft' && Auth::check() && Auth::user()->isAdmin())
                        <span class="badge bg-warning text-dark me-2">Draft</span>
                    @endif

                    <!-- Publication Info -->
                    <small class="text-muted">
                        <i class="bi bi-calendar-event me-1"></i>
                        @if($news->published_at)
                            {{ $news->published_at->format('d F Y, H:i') }} WIB
                        @else
                            Belum dipublikasi
                        @endif
                    </small>
                </div>
            </div>

            <h1 class="fs-2 fw-bold mb-3">{{ $news->title }}</h1>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <small class="text-muted d-flex align-items-center me-3">
                        <i class="bi bi-eye me-1"></i> {{ number_format($news->views_count) }} kali dibaca
                    </small>
                    <small class="text-muted d-flex align-items-center">
                        <i class="bi bi-newspaper me-1"></i> Sumber: {{ $news->source }}
                    </small>
                </div>
                <div class="d-flex">
                    @auth
                        <div x-data="{ isBookmarked: {{ $isBookmarked ? 'true' : 'false' }} }" class="me-2">
                            <button type="button"
                                    class="btn btn-sm rounded-pill"
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
                                <span class="d-none d-md-inline" x-text="isBookmarked ? 'Tersimpan' : 'Simpan'"></span>
                            </button>
                        </div>
                    @endauth

                    <!-- Fixed Share Button -->
                    <button class="btn btn-sm btn-outline-primary rounded-pill"
                            onclick="shareNews('{{ $news->title }}', '{{ str_replace(["\r", "\n", "'", '"'], [' ', ' ', '&#39;', '&quot;'], Str::limit(strip_tags($news->content), 100)) }}')">
                        <i class="bi bi-share"></i> <span class="d-none d-md-inline">Bagikan</span>
                    </button>
                </div>
            </div>

            <!-- Featured Image -->
            @if($news->image_url)
                <figure class="mb-4 text-center">
                    <img src="{{ $news->image_url }}"
                        class="img-fluid rounded shadow-sm"
                        alt="{{ $news->title }}">
                    <figcaption class="text-muted small mt-2">{{ $news->title }}</figcaption>
                </figure>
            @else
                <div class="mb-4 rounded bg-light p-5 text-center shadow-sm">
                    <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-2">Gambar Utama Berita</p>
                </div>
            @endif

            <!-- News Content -->
            <div class="mb-4 news-content">
                {!! $purifiedContent !!}
            </div>

            <!-- Tags -->
            <div class="py-3 border-top border-bottom mb-4">
                <div class="d-flex align-items-center">
                    <span class="text-muted me-2"><i class="bi bi-tags"></i> Tags:</span>
                    <a href="{{ route('category.show', $news->category) }}" class="badge bg-primary text-decoration-none me-1">{{ $news->category->name }}</a>
                    <span class="badge bg-secondary me-1">Berita</span>
                    <span class="badge bg-secondary me-1">{{ $news->created_at->format('Y') }}</span>
                </div>
            </div>
        </article>

        <!-- Related News -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Berita Terkait</h5>
            <div class="row g-3">
                @forelse($relatedNews as $related)
                <div class="col-md-6">
                    @include('components.related-news-card', ['news' => $related])
                </div>
                @empty
                <div class="col-12 text-center py-4">
                    <i class="bi bi-newspaper display-6 text-muted"></i>
                    <p class="mt-3 text-muted">Belum ada berita terkait kategori ini.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Comment Section -->
        <div class="bg-white rounded shadow-sm p-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">
                Komentar <span class="badge bg-primary rounded-pill ms-1">{{ $news->approvedComments->count() }}</span>
            </h5>

            <!-- Login prompt for guest -->
            @guest
                <div class="alert alert-info border-0 shadow-sm mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-chat-square-text fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Bergabunglah dalam diskusi!</h6>
                            <p class="mb-2 small">Login untuk memberikan komentar dan berinteraksi dengan pembaca lain</p>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-person-plus me-1"></i> Register
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div x-data="{
                    content: '',
                    submitting: false,
                    characterCount: 0,
                    maxLength: 1000
                }" class="bg-light p-3 rounded mb-4">
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
                            <label for="content" class="form-label fw-bold">
                                <i class="bi bi-chat-left-text me-1"></i> Tulis Komentar
                            </label>
                            <textarea
                                class="form-control border-0 shadow-sm"
                                x-model="content"
                                @input="characterCount = content.length"
                                id="content"
                                name="content"
                                rows="3"
                                required
                                maxlength="1000"
                                placeholder="Bagikan pendapat Anda..."></textarea>
                            <div class="d-flex justify-content-between mt-2 small text-muted">
                                <span x-show="characterCount > 0" x-text="characterCount + ' / ' + maxLength"></span>
                                <span x-show="characterCount >= maxLength" class="text-danger">Maksimal karakter tercapai</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <button
                                type="submit"
                                class="btn btn-primary"
                                x-bind:disabled="submitting || content.length === 0 || content.length > maxLength">
                                <span x-show="submitting">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Mengirim...
                                </span>
                                <span x-show="!submitting">
                                    <i class="bi bi-send me-1"></i> Kirim Komentar
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            @endguest

            <!-- Comments List -->
            <div class="mb-3">
                @foreach($news->approvedComments as $comment)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex mb-2">
                            <div class="flex-shrink-0 me-2">
                                <div class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px; font-weight: 600;">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">{{ $comment->user->name }}</h6>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="card-text mt-2 mb-1">{{ $comment->content }}</p>

                                @auth
                                    @if(auth()->id() === $comment->user_id)
                                    <div x-data="{ showEditForm: false, editContent: '{{ addslashes($comment->content) }}', submitting: false }" class="mt-2">
                                        <div class="d-flex justify-content-end" x-show="!showEditForm">
                                            <button
                                                class="btn btn-sm btn-outline-secondary me-2"
                                                @click="showEditForm = true">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </button>

                                            <form x-data="{ submitting: false }"
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
                                                    <span x-show="!submitting"><i class="bi bi-trash me-1"></i> Hapus</span>
                                                </button>
                                            </form>
                                        </div>

                                        <div x-show="showEditForm" class="mt-3 bg-light p-3 rounded">
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
                                                    <button type="button" class="btn btn-sm btn-secondary me-2" @click="showEditForm = false">
                                                        <i class="bi bi-x-circle me-1"></i> Batal
                                                    </button>
                                                    <button type="submit" class="btn btn-sm btn-primary" x-bind:disabled="submitting">
                                                        <span x-show="submitting">
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                            Menyimpan...
                                                        </span>
                                                        <span x-show="!submitting"><i class="bi bi-check-circle me-1"></i> Simpan</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                @if($news->approvedComments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-chat-square-text display-4 text-muted"></i>
                    <p class="text-muted mt-3">Belum ada komentar. Jadilah yang pertama memberikan komentar!</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Popular News -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Berita Populer</h5>
            <div class="list-group list-group-flush">
                @foreach($popularNews as $index => $popular)
                <a href="{{ $popular->source_url ? $popular->source_url : route('news.show', $popular) }}"
                   class="list-group-item list-group-item-action border-0 py-3 px-0"
                   {{ $popular->source_url ? 'target="_blank"' : '' }}>
                    <div class="row g-0">
                        <div class="col-2 d-flex align-items-center">
                            <span class="badge rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                  style="width: 30px; height: 30px;">{{ $index + 1 }}</span>
                        </div>
                        <div class="col-10">
                            <h6 class="mb-1 text-truncate-2">
                                {{ $popular->title }}
                                @if($popular->source_url)
                                    <i class="bi bi-box-arrow-up-right text-muted small"></i>
                                @endif
                            </h6>
                            <div class="d-flex align-items-center small">
                                <span class="text-muted me-2">
                                    <i class="bi bi-eye me-1"></i> {{ number_format($popular->views_count) }}
                                </span>
                                <span class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    @if($popular->published_at)
                                        {{ $popular->published_at->diffForHumans() }}
                                    @else
                                        Baru saja
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white rounded shadow-sm p-4 mb-4">
            <h5 class="wgt-title border-start border-3 border-primary ps-2 mb-3">Kategori</h5>
            <div class="list-group list-group-flush">
                @foreach(\App\Models\Category::withCount(['news' => function($query) {
                    $query->where('status', 'published')->whereNotNull('published_at');
                }])->orderBy('news_count', 'desc')->take(5)->get() as $category)
                <a href="{{ route('category.show', $category) }}"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-2 px-0
                          {{ $news->category_id == $category->id ? 'fw-bold text-primary' : '' }}">
                    <span>
                        <i class="bi bi-tag-fill me-2 {{ $news->category_id == $category->id ? 'text-primary' : 'text-muted' }}"></i>
                        {{ $category->name }}
                    </span>
                    <span class="badge {{ $news->category_id == $category->id ? 'bg-primary' : 'bg-secondary' }} rounded-pill">
                        {{ $category->news_count }}
                    </span>
                </a>
                @endforeach
                <div class="text-center mt-2">
                    <a href="{{ route('category.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua Kategori
                    </a>
                </div>
            </div>
        </div>

        <!-- Newsletter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-primary bg-opacity-10 rounded">
                <h5 class="card-title fw-bold"><i class="bi bi-envelope-paper me-2"></i> Berlangganan Newsletter</h5>
                <p class="card-text small">Dapatkan update berita terbaru langsung ke email Anda</p>
                <div x-data="{ email: '', submitted: false, valid: false }">
                    <div class="input-group shadow-sm">
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

<style>
/* News content styling */
.news-content {
    font-size: 1.05rem;
    line-height: 1.8;
    overflow-wrap: break-word;
}

.news-content p {
    margin-bottom: 1.2rem;
}

.news-content h1, .news-content h2, .news-content h3,
.news-content h4, .news-content h5, .news-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.news-content img {
    max-width: 100%;
    height: auto;
    margin: 1rem 0;
    border-radius: 0.25rem;
}

.news-content a {
    color: #3490dc;
    text-decoration: none;
}

.news-content a:hover {
    text-decoration: underline;
}

.news-content ul, .news-content ol {
    margin-bottom: 1.2rem;
    padding-left: 2rem;
}

.news-content blockquote {
    border-left: 4px solid #e2e8f0;
    padding-left: 1rem;
    margin-left: 0;
    margin-right: 0;
    font-style: italic;
}

/* Text truncate for lists */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Card hover effects */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
}

/* Breadcrumb styling */
.breadcrumb-item + .breadcrumb-item::before {
    content: "\F231";
    font-family: "bootstrap-icons";
    font-size: 0.7rem;
    vertical-align: middle;
}

/* Category badge */
.category-badge {
    background-color: #FF4B91;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Widget title */
.wgt-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Dark mode compatibility */
.dark .bg-white,
.dark .card,
.dark .list-group-item {
    background-color: var(--dark-card) !important;
    color: var(--text-light) !important;
    border-color: var(--dark-border) !important;
}

.dark .bg-light,
.dark .btn-outline-secondary {
    background-color: var(--dark-bg) !important;
    color: var(--text-light) !important;
    border-color: var(--dark-border) !important;
}

.dark .text-muted {
    color: var(--text-muted-dark) !important;
}

.dark .text-dark {
    color: var(--text-light) !important;
}

.dark .bg-primary.bg-opacity-10 {
    background-color: rgba(65, 105, 225, 0.2) !important;
}
</style>

<!-- Script untuk fungsi share -->
<script>
function shareNews(title, text) {
    if (navigator.share) {
        navigator.share({
            title: title,
            text: text,
            url: window.location.href
        }).catch(err => {
            console.error('Error sharing:', err);
        });
    } else {
        prompt('Copy link to share:', window.location.href);
    }
}
</script>
@endsection
