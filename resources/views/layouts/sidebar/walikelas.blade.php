<aside id="sidebar"
    class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-white border-r transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out flex flex-col">

    <div class="h-16 flex items-center px-6 text-xl font-semibold text-blue-600 border-b">
        <i class="fa-solid fa-layer-group mr-2"></i> PERIZINAN SISWA
    </div>

    <nav class="flex-1 px-4 py-4 space-y-1">

        <a href="/home" class="flex items-center gap-3 mb-5 px-4 py-2 rounded-lg {{ isActive('dashboard') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-chart-line"></i></span>
            Dashboard
        </a>

        <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 px-4">
            Master
        </div>

        {{-- Siswa --}}
        <a href="/master/students"
            class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('master/students*') }}">
            <span class="w-5 text-center">
                <i class="fa-solid fa-user-graduate"></i>
            </span>
            Siswa
        </a>

        <div class="pt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 px-4">
            Menu
        </div>
        {{-- Pelanggaran --}}
        <a href="/violations" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('violations') }}">
            <span class="w-5 text-center">
                <i class="fa-solid fa-ban"></i>
            </span>
            Pelanggaran
        </a>

        {{-- Permohonan Izin --}}
        <a href="/permissions" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('permissions') }}">
            <span class="w-5 text-center">
                <i class="fa-solid fa-clipboard-list"></i>
            </span>
            Permohonan Izin
        </a>

    </nav>
</aside>