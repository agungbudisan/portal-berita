<footer class="footer" x-bind:class="{ 'dark': darkMode }">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="logo mb-3">Winni<span>News</span></h5>
                <p class="mb-3">Portal berita terkini dan terpercaya dengan informasi aktual dari berbagai kategori</p>
                <div class="social-links">
                    <a href="#" class="me-2 text-decoration-none">
                        <i class="bi bi-facebook fs-5"></i>
                    </a>
                    <a href="#" class="me-2 text-decoration-none">
                        <i class="bi bi-twitter-x fs-5"></i>
                    </a>
                    <a href="#" class="me-2 text-decoration-none">
                        <i class="bi bi-instagram fs-5"></i>
                    </a>
                    <a href="#" class="text-decoration-none">
                        <i class="bi bi-youtube fs-5"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3">Kategori</h5>
                <ul class="list-unstyled">
                    @foreach(\App\Models\Category::take(5)->get() as $category)
                        <li>
                            <a href="{{ route('category.show', $category) }}" class="text-decoration-none">
                                <i class="bi bi-chevron-right me-1 small"></i> {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="mb-3">Tautan</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Beranda</a></li>
                    <li><a href="#" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Tentang Kami</a></li>
                    <li><a href="#" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Kebijakan Privasi</a></li>
                    <li><a href="#" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Syarat & Ketentuan</a></li>
                    <li><a href="#" class="text-decoration-none"><i class="bi bi-chevron-right me-1 small"></i> Kontak</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3">Hubungi Kami</h5>
                <address>
                    <div class="mb-2">
                        <i class="bi bi-geo-alt-fill me-2"></i> PT Winnicode Garuda Teknologi<br>
                        <span class="ms-4">Jl. Asia Afrika No.158, Bandung</span>
                    </div>
                    <div class="mb-2">
                        <i class="bi bi-envelope-fill me-2"></i> info@winnicode.com
                    </div>
                    <div>
                        <i class="bi bi-telephone-fill me-2"></i> 6285159932501
                    </div>
                </address>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <small>&copy; {{ date('Y') }} WinniNews. All rights reserved.</small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small>Dikembangkan oleh <a href="#" class="text-decoration-none">Winnicode</a></small>
            </div>
        </div>
    </div>
</footer>
