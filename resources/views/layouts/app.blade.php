<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin') }}</title>

    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 font-[Inter]">

    <div class="flex min-h-screen">

        <aside class="w-64 bg-white border-r hidden md:flex flex-col">
            <div class="h-16 flex items-center px-6 text-xl font-bold text-blue-600">
                TailAdmin
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-blue-50 text-blue-600 font-medium">
                    Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    Analytics
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    Marketing
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    CRM
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">

            <header class="h-16 bg-white border-b flex items-center justify-between px-6">
                <input type="text" placeholder="Search..."
                    class="w-72 px-4 py-2 rounded-lg bg-slate-100 focus:outline-none">

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-sm font-medium">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="text-xs text-slate-500">Administrator</div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}"
                        class="w-10 h-10 rounded-full">
                </div>
            </header>

            <main class="p-6 space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="text-sm text-slate-500">Avg. Client Rating</div>
                        <div class="text-3xl font-bold mt-2">7.8/10</div>
                        <div class="text-sm text-green-500 mt-1">+20%</div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="text-sm text-slate-500">Instagram Followers</div>
                        <div class="text-3xl font-bold mt-2">5,934</div>
                        <div class="text-sm text-red-500 mt-1">-3.5%</div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="text-sm text-slate-500">Total Revenue</div>
                        <div class="text-3xl font-bold mt-2">$9,758</div>
                        <div class="text-sm text-green-500 mt-1">+15%</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm h-80">
                    <div class="font-semibold mb-4">Impression & Traffic</div>
                    <div class="h-full flex items-center justify-center text-slate-400">
                        Chart Area
                    </div>
                </div>

                @yield('content')

            </main>
        </div>
    </div>

</body>

</html>