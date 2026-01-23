<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.bunny.net/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #f8fafc;
            background-image:
                radial-gradient(#3b82f6 0.6px, transparent 0.6px),
                radial-gradient(#3b82f6 0.6px, transparent 0.6px);
            background-size: 40px 40px;
            background-position: 0 0, 20px 20px;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center font-['Poppins'] p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg border border-slate-200">

        <div class="text-center px-6 pt-8">
            <img src="https://yt3.googleusercontent.com/aqwnd_6PPBpG0PqWP1QMcBjJZX0GwVYQCmJ0_r0pdJPrAgiqjH3TaxhHCF9a-oHRbhk90Bpz=s900-c-k-c0x00ffffff-no-rj"
                alt="Logo" class="w-28 h-auto mx-auto mb-4">

            <h1 class="text-2xl font-semibold">
                Login
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Silakan masuk ke akun Anda
            </p>
        </div>

        <div class="px-6 mb-10">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="text-sm font-medium">
                        Email
                    </label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-3 flex items-center text-blue-600">
                            <i class="fa-regular fa-envelope"></i>
                        </span>

                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition @error('email') border-red-400 @enderror"
                            placeholder="email@example.com">
                    </div>
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium">
                        Password
                    </label>
                    <div class="relative mt-1">
                        <!-- icon kiri -->
                        <span class="absolute inset-y-0 left-3 flex items-center text-blue-600">
                            <i class="fa-solid fa-lock"></i>
                        </span>

                        <!-- input -->
                        <input type="password" id="password" name="password" required class="w-full pl-10 pr-12 py-2.5 rounded-xl border border-gray-300 
                                   focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition
                                   @error('password') border-red-400 @enderror" placeholder="••••••••">

                        <!-- toggle eye -->
                        <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-blue-600 transition">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>

                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                        Remember me
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline font-medium">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <button type="submit" id="loginBtn" class="w-full mt-2 py-3  rounded-xl bg-blue-600 text-white font-semibold 
                       hover:bg-blue-700 active:scale-95 transition 
                       flex items-center justify-center gap-2">

                    <span id="btnText">Login</span>

                    <svg id="btnLoader" class="hidden w-5 h-5 animate-spin text-white"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>

            </form>
        </div>
    </div>

    <script>
        const form = document.querySelector("form");
        const btn = document.getElementById("loginBtn");
        const btnText = document.getElementById("btnText");
        const btnLoader = document.getElementById("btnLoader");

        const passwordInput = document.getElementById("password");
        const togglePassword = document.getElementById("togglePassword");
        const toggleIcon = togglePassword.querySelector("i");

        togglePassword.addEventListener("click", () => {
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";

            toggleIcon.classList.toggle("fa-eye");
            toggleIcon.classList.toggle("fa-eye-slash");
        });

        form.addEventListener("submit", function () {
            btn.disabled = true;
            btn.classList.add("opacity-70", "cursor-not-allowed");
            btnText.textContent = "Memproses...";
            btnLoader.classList.remove("hidden");
        });
    </script>

</body>

</html>