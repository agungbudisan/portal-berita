<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5 class="logo">Winni<span>News</span></h5>
                <p>Portal berita terkini dan terpercaya</p>
            </div>
            <div class="col-md-4">
                <h5>Kategori</h5>
                <ul class="list-unstyled">
                    @foreach(\App\Models\Category::take(5)->get() as $category)
                        <li><a href="{{ route('category.show', $category) }}" class="text-decoration-none">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Hubungi Kami</h5>
                <address>
                    PT Winnicode Garuda Teknologi<br>
                    Jl. Asia Afrika No.158, Bandung<br>
                    Email: info@winnicode.com<br>
                    Telp: 6285159932501
                </address>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center">
                <small>&copy; {{ date('Y') }} WinniNews. All rights reserved.</small>
            </div>
        </div>
    </div>
</footer>
