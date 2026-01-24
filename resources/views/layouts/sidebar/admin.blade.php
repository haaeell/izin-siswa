<aside id="sidebar"
    class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-white border-r transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out flex flex-col">

    <div class="h-16 flex items-center px-6 text-xl font-semibold text-blue-600 border-b">
        <i class="fa-solid fa-layer-group mr-2"></i> PERIZINAN SISWA
    </div>

    <nav class="flex-1 px-4 py-4 space-y-1">

        {{-- Dashboard --}}
        <a href="/home" class="flex items-center gap-3 mb-5 px-4 py-2 rounded-lg {{ isActive('dashboard') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-chart-line"></i></span>
            Dashboard
        </a>

        <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 px-4">
            Menu Master
        </div>

        <a href="/master/students"
            class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('master/students*') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-user-graduate"></i></span>
            Siswa
        </a>

        <a href="/master/classes"
            class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('master/classes*') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-chalkboard"></i></span>
            Kelas
        </a>

        <a href="/master/teachers"
            class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('master/teachers*') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-user-tie"></i></span>
            Guru
        </a>

        <div class="pt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 px-4">
            Pelanggaran
        </div>

        <a href="/violations" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('violations') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-ban"></i></span>
            Data Pelanggaran
        </a>

        <div class="pt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 px-4">
            Menu Perizinan
        </div>

        <a href="/permissions" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('permissions') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-user-graduate"></i></span>
            Permohonan Izin
        </a>

        <a href="/checkin" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('checkin') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-qrcode"></i></span>
            Scan QR Siswa
        </a>

        <div class="pt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 px-4">
            Laporan
        </div>

        <a href="/reports" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('reports') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-chart-line"></i></span>
            Laporan
        </a>

    </nav>
</aside>