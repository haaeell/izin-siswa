<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin') }}</title>

    <!-- Font -->
    <link href="https://fonts.bunny.net/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Datatable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        table.dataTable thead th {
            background-color: #f8fafc;
        }

        /* Desktop collapse */
        .sidebar-collapsed {
            width: 0;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-slate-100">

    <div class="flex min-h-screen">

        <!-- Overlay (Mobile) -->
        <div id="overlay" class="fixed inset-0 bg-black/40 z-30 opacity-0 pointer-events-none
                transition-opacity duration-300 md:hidden">
        </div>

        @php
            function isActive($pattern)
            {
                return request()->is($pattern)
                    ? 'bg-blue-50 text-blue-600 font-medium'
                    : 'text-slate-600 hover:bg-slate-100';
            }
        @endphp
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-40
        w-64 bg-white border-r
        transform -translate-x-full md:translate-x-0
        transition-all duration-300 ease-in-out
        flex flex-col">

            <div class="h-16 flex items-center px-6 text-xl font-semibold text-blue-600 border-b">
                <i class="fa-solid fa-layer-group mr-2"></i> PERIZINAN SISWA
            </div>

            <nav class="flex-1 px-4 py-4 space-y-1">

                {{-- Dashboard --}}
                <a href="/dashboard"
                    class="flex items-center gap-3 mb-5 px-4 py-2 rounded-lg {{ isActive('dashboard') }}">
                    <span class="w-5 text-center">
                        <i class="fa-solid fa-chart-line"></i>
                    </span>
                    Dashboard
                </a>

                <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 px-4">
                    Menu Master
                </div>

                {{-- Siswa --}}
                <a href="/master/students"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('master/students*') }}">
                    <span class="w-5 text-center">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>
                    Siswa
                </a>

                {{-- Kelas --}}
                <a href="/master/classes"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('master/classes*') }}">
                    <span class="w-5 text-center">
                        <i class="fa-solid fa-chalkboard"></i>
                    </span>
                    Kelas
                </a>

                {{-- Guru --}}
                <a href="/master/teachers"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('master/teachers*') }}">
                    <span class="w-5 text-center">
                        <i class="fa-solid fa-user-tie"></i>
                    </span>
                    Guru
                </a>

                {{-- Wali Kelas --}}
                <a href="/master/homeroom-teachers"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('master/homeroom-teachers*') }}">
                    <span class="w-5 text-center">
                        <i class="fa-solid fa-users-gear"></i>
                    </span>
                    Wali Kelas
                </a>

                {{-- Tahun Akademik --}}
                <a href="/master/academic-years"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('master/academic-years*') }}">
                    <span class="w-5 text-center">
                        <i class="fa-solid fa-calendar-days"></i>
                    </span>
                    Tahun Akademik
                </a>

            </nav>
        </aside>


        <!-- Content -->
        <div class="flex-1 flex flex-col">

            <!-- Header -->
            <header class="h-16 bg-white border-b flex items-center justify-between px-4 md:px-6">

                <div class="flex items-center gap-3">
                    <button id="menuBtn" class="p-2 rounded-lg hover:bg-slate-100">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <input type="text" placeholder="Search..."
                        class="hidden sm:block w-56 md:w-72 px-4 py-2 rounded-lg bg-slate-100 focus:outline-none">
                </div>

                <div class="relative">
                    <button id="userMenuBtn" class="flex items-center gap-3 focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-medium">{{ Auth::user()->name ?? 'User' }}</div>
                            <div class="text-xs text-slate-500">Administrator</div>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}"
                            class="w-9 h-9 rounded-full border">
                    </button>

                    <div id="userMenu" class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-slate-200
                               opacity-0 scale-95 pointer-events-none
                               transition-all duration-200 origin-top-right z-50">

                        <a href="#"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-100 rounded-t-lg">
                            <i class="fa-solid fa-user"></i>
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-b-lg">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

            </header>

            <!-- Main -->
            <main class="p-4 md:p-6 space-y-6">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
    <script>
        $(function () {

            const userMenuBtn = $('#userMenuBtn')
            const userMenu = $('#userMenu')

            userMenuBtn.on('click', function (e) {
                e.stopPropagation()
                userMenu.toggleClass('opacity-0 scale-95 pointer-events-none')
            })

            $(document).on('click', function () {
                if (!userMenu.hasClass('opacity-0')) {
                    userMenu.addClass('opacity-0 scale-95 pointer-events-none')
                }
            })


            const sidebar = $('#sidebar')
            const overlay = $('#overlay')
            let isOpen = true

            function openSidebar() {
                sidebar.removeClass('sidebar-collapsed -translate-x-full').addClass('w-64')
                overlay.removeClass('pointer-events-none').addClass('opacity-100')
                isOpen = true
            }

            function closeSidebar() {
                if (window.innerWidth < 768) {
                    sidebar.addClass('-translate-x-full')
                    overlay.addClass('pointer-events-none').removeClass('opacity-100')
                } else {
                    sidebar.addClass('sidebar-collapsed').removeClass('w-64')
                }
                isOpen = false
            }

            $('#menuBtn').on('click', function () {
                isOpen ? closeSidebar() : openSidebar()
            })

            $('#overlay').on('click', closeSidebar)

            $('#sidebar a').on('click', function () {
                if (window.innerWidth < 768) closeSidebar()
            })

            $('.datatable').DataTable({
                responsive: true,
                pageLength: 10
            })

        })
    </script>



    @if ($errors->any())
        <script script>
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += "{{ $error }}\n";
            @endforeach

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: errorMessages,
            });
        </script>
    @endif

    @if (session('success') || session('error'))
        <script>
            $(document).ready(function () {
                var successMessage = "{{ session('success') }}";
                var errorMessage = "{{ session('error') }}";

                if (successMessage) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: successMessage,
                    });
                }

                if (errorMessage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                    });
                }
            });
        </script>
    @endif

</body>

</html>