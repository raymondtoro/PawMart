<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>Login</title>
</head>
<body>
    @auth
    <div class="container">
        <div class="image-section"></div>

        <div class="form-section">
            <h2>Welcome back, {{ auth()->user()->name }}!</h2>

            <!-- Logout Form -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn">Logout</button>
            </form>
        </div>
    </div>

    @else
        <div class="container">
            <div class="image-section"></div>

            <div class="form-section">
                <h2>Login</h2>

                <!-- Toggle Buttons -->
                <div class="toggle-buttons" id="toggleContainer">
                    <button type="button" id="userBtn" class="active">User</button>
                    <button type="button" id="adminBtn">Admin</button>
                </div>

                <!-- Error Messages -->
                @if (session('error'))
                    <div class="error-messages">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="error-messages">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- User Login Form -->
                <form id="userForm" class="active" action="{{ url('/login') }}" method="POST">
                    @csrf
                    <input name="email" type="email" placeholder="Email" class="form-control" required>
                    <input name="password" type="password" placeholder="Password" class="form-control" required>
                    <button type="submit" class="btn">Login</button>
                </form>

                <!-- Admin Login Form -->
                <form id="adminForm" action="{{ url('/admin/login') }}" method="POST">
                    @csrf
                    <input name="email" type="email" placeholder="Admin Email" class="form-control" required>
                    <input name="password" type="password" placeholder="Admin Password" class="form-control" required>
                    <button type="submit" class="btn">Admin Login</button>
                </form>

               <!-- Footer text -->
<p class="footer-text" id="footerText">
    Belum punya akun? <a href="{{ route('register') }}">Register</a>
</p>

            </div>
        </div>
    @endauth

    <script>
    const toggleContainer = document.getElementById('toggleContainer');
    const userBtn = document.getElementById('userBtn');
    const adminBtn = document.getElementById('adminBtn');
    const userForm = document.getElementById('userForm');
    const adminForm = document.getElementById('adminForm');
    const footerText = document.getElementById('footerText');

    if (userBtn && adminBtn) {
        userBtn.addEventListener('click', () => {
            userBtn.classList.add('active');
            adminBtn.classList.remove('active');
            userForm.classList.add('active');
            adminForm.classList.remove('active');
            toggleContainer.classList.remove('admin-active');
            footerText.classList.remove('hidden'); // show footer
        });

        adminBtn.addEventListener('click', () => {
            adminBtn.classList.add('active');
            userBtn.classList.remove('active');
            adminForm.classList.add('active');
            userForm.classList.remove('active');
            toggleContainer.classList.add('admin-active');
            footerText.classList.add('hidden'); // hide + collapse space
        });
    }
</script>


</body>
</html>
