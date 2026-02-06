<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Siswa</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .bg-gradient-main {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .btn-grad {
            background-image: linear-gradient(to right, #4f46e5 0%, #3b82f6 51%, #4f46e5 100%);
            transition: 0.5s;
            background-size: 200% auto;
        }

        .btn-grad:hover {
            background-position: right center;
            transform: translateY(-1px);
        }

        .card-anim {
            transition: all 0.3s cubic-bezier(.25, .8, .25, 1);
        }

        .card-anim:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">

    <div class="bg-gradient-main h-52 w-full absolute top-0 left-0 z-0"></div>

    <div class="relative z-10 w-full max-w-5xl mx-auto px-4 pt-12 pb-12">
        <div class="glass-effect rounded-2xl shadow-2xl p-8 mb-8 border border-white/20">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full mb-4 shadow-inner">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Tracking Siswa</h1>
                <p class="text-gray-500 mt-2">Monitoring riwayat perizinan santri secara real-time</p>
            </div>

            <div class="relative flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-indigo-400"></i>
                    </div>
                    <input id="nis" type="text" placeholder="Masukkan NIS siswa..."
                        class="block w-full pl-11 pr-3 py-4 border-2 border-gray-100 rounded-xl leading-5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all">
                </div>
                <button id="searchBtn"
                    class="btn-grad text-white font-bold py-4 px-10 rounded-xl shadow-lg flex items-center justify-center gap-2 uppercase text-sm tracking-wider">
                    <i class="fas fa-magnifying-glass"></i> Cari Data
                </button>
            </div>
        </div>

        <div id="result" class="space-y-5">
        </div>
    </div>

    <script>
        const searchBtn = document.getElementById('searchBtn');
        const nisInput = document.getElementById('nis');
        const resultDiv = document.getElementById('result');

        nisInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') searchBtn.click(); });

        searchBtn.addEventListener('click', async () => {
            const nis = nisInput.value.trim();
            if (!nis) {
                showError('Silakan masukkan NIS siswa terlebih dahulu');
                return;
            }

            resultDiv.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                    <p class="mt-4 text-gray-500 font-medium animate-pulse">Menghubungkan ke database...</p>
                </div>
            `;

            try {
                const response = await axios.get(`/student/tracking`, { params: { nis } });

                if (!response.data.success || response.data.data.length === 0) {
                    resultDiv.innerHTML = `
                        <div class="bg-white rounded-2xl p-12 text-center shadow-md border border-gray-100">
                            <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-folder-open text-gray-300 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Data Tidak Ditemukan</h3>
                            <p class="text-gray-500">Riwayat untuk NIS <b>${nis}</b> belum tersedia.</p>
                        </div>
                    `;
                    return;
                }

                renderResults(response.data.data);
            } catch (error) {
                showError('Gagal mengambil data. Pastikan koneksi internet aktif.');
            }
        });

        function renderResults(history) {
            resultDiv.innerHTML = history.map(item => {

                return `
                    <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100 card-anim">
                        <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-indigo-200 shadow-lg">
                                    <i class="fas fa-user text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-800">${item.nama}</h2>
                                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                                        <span class="text-sm text-indigo-600 font-semibold uppercase tracking-tight">
                                            <i class="fas fa-id-card mr-1"></i> ${item.nis || '-'}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            <i class="fas fa-graduation-cap mr-1"></i> ${item.kelas}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            <div class="space-y-2">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">Waktu Keluar</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                                        <i class="fas fa-sign-out-alt text-orange-500 text-sm"></i>
                                    </div>
                                    <span class="font-bold text-gray-700">${item.checkout_at || '--:--'}</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">Waktu Kembali</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                                        <i class="fas fa-sign-in-alt text-emerald-500 text-sm"></i>
                                    </div>
                                    <span class="font-bold text-gray-700">${item.checkin_at || '<span class="text-gray-300 font-normal italic">On Going</span>'}</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">Durasi Izin</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                        <i class="fas fa-calendar-day text-blue-500 text-sm"></i>
                                    </div>
                                    <span class="font-bold text-gray-700">${item.duration || '1'} Hari</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">Jenis & Alasan</p>
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-file-signature text-purple-500 text-sm"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="font-bold text-gray-700 leading-tight truncate">${item.type || '-'}</p>
                                        <p class="text-xs text-gray-400 italic mt-0.5 truncate">${item.reason || 'Tanpa alasan'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function showError(msg) {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border-l-4 border-red-500 p-5 rounded-xl flex items-center shadow-sm">
                    <i class="fas fa-circle-exclamation text-red-500 text-xl mr-4"></i>
                    <p class="text-red-800 font-medium">${msg}</p>
                </div>
            `;
        }
    </script>
</body>

</html>