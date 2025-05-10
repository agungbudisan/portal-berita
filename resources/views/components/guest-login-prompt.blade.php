@guest
<div class="sidebar-widget" x-data="{ show: true }" x-show="show">
    <div class="position-relative">
        <h5 class="mb-3">Dapatkan Pengalaman Lengkap!</h5>
        <div class="mb-3">
            <div class="d-flex align-items-center mb-2">
                <div class="me-3">
                    <i class="bi bi-bookmark-star fs-4 text-primary"></i>
                </div>
                <div>
                    <strong>Simpan Berita Favorit</strong>
                    <p class="small text-muted mb-0">Bookmark berita yang ingin Anda baca nanti</p>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-chat-dots fs-4 text-primary"></i>
                </div>
                <div>
                    <strong>Berikan Komentar</strong>
                    <p class="small text-muted mb-0">Berikan pendapat Anda pada berita yang menarik</p>
                </div>
            </div>
        </div>
        <div class="d-grid gap-2">
            <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-primary">
                <i class="bi bi-person-plus me-1"></i> Register
            </a>
        </div>
        <button @click="show = false" class="btn-close position-absolute top-0 end-0 m-2" aria-label="Close"></button>
    </div>
</div>
@endguest
