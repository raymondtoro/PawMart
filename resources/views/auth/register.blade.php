<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Load your login.css (you can rename if you want) --}}
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <title>Register</title>
</head>
<body>

    <div class="container">
        <!-- Image Section -->
        <div class="image-section"></div>

        <!-- Form Section -->
        <div class="form-section fade-in">
            <h2>Register</h2>

            <!-- Register Form -->
            <form action="{{ route('register') }}" method="POST" class="active">
    @csrf

    {{-- Show success message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Show error message --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Show validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <input name="name" type="text" placeholder="Username" class="form-control" value="{{ old('name') }}" required>
    <input name="email" type="email" placeholder="Email" class="form-control" value="{{ old('email') }}" required>
    <input name="phone" type="text" placeholder="No. Handphone" class="form-control" value="{{ old('phone') }}" required>
    <input name="password" type="password" placeholder="Password" class="form-control" required>
    <input name="password_confirmation" type="password" placeholder="Confirm Password" class="form-control" required>
    <button type="submit" class="btn">Daftar</button>
</form>


            <p class="footer-text">
                Sudah punya akun? <a href="{{ route('login') }}">Login</a>
            </p>
        </div>
    </div>

    <script>
        // Fade animation on load
        window.addEventListener('load', () => {
            document.querySelector('.form-section').classList.add('active');
            document.querySelector('.image-section').classList.add('active');
            document.querySelector('form').classList.add('active'); // 👈 add this line
        });

    </script>
</body>
</html>
