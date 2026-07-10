<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Water Refill Station</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0f172a;
            --bg-secondary: #1e1b4b;
            --accent-cyan: #06b6d4;
            --accent-violet: #8b5cf6;
            --accent-emerald: #10b981;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 50%, #0c1222 100%); }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(6, 182, 212, 0.1); }
            50% { box-shadow: 0 0 40px rgba(6, 182, 212, 0.2); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes orbMove1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -40px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(10px, -10px) scale(1.05); }
        }
        @keyframes orbMove2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(-40px, 20px) scale(0.9); }
            50% { transform: translate(30px, -30px) scale(1.1); }
            75% { transform: translate(-10px, 40px) scale(1); }
        }
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeIn 0.8s ease-out, pulseGlow 3s ease-in-out infinite;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
        }
        .orb-1 {
            width: 300px; height: 300px;
            background: var(--accent-cyan);
            top: -100px; right: -50px;
            animation: orbMove1 12s ease-in-out infinite;
        }
        .orb-2 {
            width: 250px; height: 250px;
            background: var(--accent-violet);
            bottom: -80px; left: -60px;
            animation: orbMove2 15s ease-in-out infinite;
        }
        .orb-3 {
            width: 200px; height: 200px;
            background: var(--accent-emerald);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation: orbMove1 18s ease-in-out infinite reverse;
        }
        .input-field {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }
        .input-field:focus {
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.15);
            outline: none;
        }
        .input-field::placeholder { color: rgba(255, 255, 255, 0.3); }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-cyan), #2563eb);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(6, 182, 212, 0.3);
        }
        .btn-primary:active { transform: translateY(0); }
        .demo-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Animated Background Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="login-card rounded-3xl p-8 w-full max-w-md relative z-10" style="animation-delay: 0.1s;">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4" style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(139, 92, 246, 0.2)); border: 1px solid rgba(6, 182, 212, 0.3);">
                <span class="text-3xl" style="animation: float 3s ease-in-out infinite;">💧</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-1">Water Refill</h1>
            <p class="text-white/40 text-sm">Management System</p>
        </div>

        @if($errors->any())
            <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #fca5a5;">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #6ee7b7;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-5">
                <label class="block text-white/60 text-sm font-medium mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="input-field w-full px-4 py-3 rounded-xl text-sm"
                    placeholder="admin@waterrefill.com">
            </div>
            <div class="mb-6">
                <label class="block text-white/60 text-sm font-medium mb-2">Password</label>
                <input type="password" name="password" required
                    class="input-field w-full px-4 py-3 rounded-xl text-sm"
                    placeholder="••••••••">
            </div>
            <button type="submit" class="btn-primary w-full text-white font-semibold py-3 px-4 rounded-xl text-sm">
                Sign In
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-white/30 text-sm">
                Don't have an account? <a href="/register" class="text-cyan-400 hover:text-cyan-300 transition-colors font-medium">Create one</a>
            </p>
        </div>

        <div class="demo-card mt-6 p-4 rounded-xl">
            <p class="text-white/40 text-xs font-semibold mb-2 uppercase tracking-wider">Demo Accounts</p>
            <div class="space-y-1">
                <p class="text-white/50 text-xs">Admin: <span class="text-cyan-400/80">admin@waterrefill.com</span></p>
                <p class="text-white/50 text-xs">Staff: <span class="text-cyan-400/80">staff@waterrefill.com</span></p>
                <p class="text-white/50 text-xs">Password: <span class="text-cyan-400/80">password</span></p>
            </div>
        </div>
    </div>
</body>
</html>
