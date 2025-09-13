<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kirim Ulasan | PAW MART</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/user/rating.css') }}">
    <style>
        /* Floating back button */
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: #af44d2;
            font-weight: bold;
            font-size: 1rem;
            padding: 10px 15px;
            border: 2px solid #9839b8;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            z-index: 1000;
            transition: background 0.3s, color 0.3s, transform 0.2s;
        }
        .back-btn:hover {
            background: #8b33a8;
            color: #fff;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Floating back navigation -->
    <a href="{{ route('about') }}" class="back-btn">&#8592; Kembali</a>

    <div class="review-card">
        <h2>Kirimkan Ulasan Anda</h2>
        <form method="POST" action="{{ route('user.rating.store') }}">
            @csrf

            <!-- Product selection (required) -->
            <div class="form-group">
                <label for="produk_id">Pilih Produk</label>
                <select name="produk_id" id="produk_id" class="styled-select" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('produk_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->nama_produk }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Star rating -->
            <div class="form-group">
                <label>Rating Bintang</label>
                <div class="stars rating-group">
                    @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star{{ $i }}" name="bintang" value="{{ $i }}" {{ old('bintang') == $i ? 'checked' : '' }} required>
                        <label for="star{{ $i }}" title="{{ $i }} bintang">&#9733;</label>
                    @endfor
                </div>
            </div>

            <!-- Review text -->
            <div class="form-group">
                <label for="ulasan">Ulasan</label>
                <textarea id="ulasan" name="ulasan" placeholder="Tulis ulasan anda di sini..." required>{{ old('ulasan') }}</textarea>
            </div>

            <button type="submit" class="submit-btn">Kirim</button>
        </form>
    </div>
</div>
</body>
</html>
