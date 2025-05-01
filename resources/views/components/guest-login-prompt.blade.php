@guest
<div class="alert alert-info" x-data="{ show: true }" x-show="show">
    <h5>Dapatkan lebih banyak fitur!</h5>
    <p>Login untuk menyimpan berita favorit dan memberikan komentar</p>
    <div class="d-grid gap-2">
        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
        <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
    </div>
    <button @click="show = false" class="btn-close position-absolute top-0 end-0 m-2" aria-label="Close"></button>
</div>
@endguest
