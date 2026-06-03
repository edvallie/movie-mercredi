<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Movie Mercredi — Admin</title>
    <style>
        *, ::before, ::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0f0f13;
            color: #e2e2e2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .card {
            background: #1a1a24;
            border: 1px solid #2e2e42;
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 380px;
        }
        .logo { font-size: 1rem; letter-spacing: 0.15em; text-transform: uppercase; color: #e5b000; font-weight: 700; margin-bottom: 0.4rem; }
        h1 { font-size: 1.3rem; font-weight: 700; color: #fff; margin-bottom: 1.75rem; }
        label { display: block; font-size: 0.82rem; color: #888; margin-bottom: 0.4rem; }
        input[type="password"] {
            width: 100%;
            background: #0f0f13;
            border: 1px solid #2e2e42;
            border-radius: 6px;
            color: #e2e2e2;
            padding: 0.65rem 0.9rem;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.15s;
        }
        input[type="password"]:focus { border-color: #e5b000; }
        .error { color: #e05555; font-size: 0.82rem; margin-top: 0.4rem; }
        button {
            margin-top: 1.25rem;
            width: 100%;
            background: #e5b000;
            color: #0f0f13;
            border: none;
            border-radius: 6px;
            padding: 0.7rem;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
        }
        button:hover { background: #f0c000; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">🎬 Movie Mercredi</div>
        <h1>Admin Login</h1>
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autofocus autocomplete="current-password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
            <button type="submit">Sign in</button>
        </form>
    </div>
</body>
</html>
