<!-- app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <style>
        /* Base Styles */
        body {
            font-family: system-ui, -apple-system, sans-serif;
            line-height: 1.5;
            margin: 0;
        }

        .nav {
            background: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 80rem;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            height: 4rem;
            align-items: center;
        }

        .brand {
            font-size: 1.25rem;
            font-weight: bold;
            text-decoration: none;
            color: #111827;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-link {
            text-decoration: none;
            color: #4B5563;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: #111827;
        }

        .container {
            max-width: 80rem;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="nav-container">
            <div class="nav-content">
                <!-- Logo/Brand -->
                <div>
                    <a href="/" class="brand">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="nav-links">
                    @auth
                        <!-- Notification Component -->
                        @include('components.notification')

                        <!-- User Dropdown -->
                        <div class="relative">
                            <span>{{ Auth::user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="nav-link" style="border: none; background: none; cursor: pointer;">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">Login</a>
                        <a href="{{ route('register') }}" class="nav-link">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
