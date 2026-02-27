<aside id="sidebar"
    class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-white border-r transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out flex flex-col">

    <div class="h-16 flex items-center px-6 text-sm font-semibold text-blue-600 border-b">
        <img class="w-10"
            src="https://yt3.googleusercontent.com/aqwnd_6PPBpG0PqWP1QMcBjJZX0GwVYQCmJ0_r0pdJPrAgiqjH3TaxhHCF9a-oHRbhk90Bpz=s900-c-k-c0x00ffffff-no-rj"
            alt=""> PERIZINAN SISWA
    </div>

    <nav class="flex-1 px-4 py-4 space-y-1">

        {{-- Dashboard --}}
        <a href="/home" class="flex items-center gap-3 mb-5 px-4 py-2 rounded-lg {{ isActive('dashboard') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-chart-line"></i></span>
            Dashboard
        </a>



        <div class="pt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 px-4">
            Pelanggaran
        </div>

        <a href="{{ url('/violations?handling_type=pengasuhan') }}"
            class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('violations*') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-ban"></i></span>
            Data Pelanggaran
        </a>


        <div class="pt-5 mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400 px-4">
            Menu Perizinan
        </div>

        <a href="/permissions"
            class="flex items-center justify-between px-4 py-2 rounded-lg {{ isActive('permissions') }}">

            <div class="flex items-center gap-3">
                <span class="w-5 text-center">
                    <i class="fa-solid fa-user-graduate"></i>
                </span>
                <span>Permohonan Izin</span>
            </div>
        </a>

        <a href="/checkout" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('checkout') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-arrow-right"></i></span>
            Check-Out
        </a>

        <a href="/checkin" class="flex items-center gap-3 px-4 py-2 rounded-lg {{ isActive('checkin') }}">
            <span class="w-5 text-center"><i class="fa-solid fa-arrow-left"></i></span>
            Check-In
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