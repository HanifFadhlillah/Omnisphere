<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — PMO Workspace</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-pmo-workspace.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    <style>
        /* ── Entrance animations ───────────────────────────────────── */
        @keyframes slideFromLeft {
            from { opacity: 0; transform: translateX(-40px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideFromTop {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Default: semua tersembunyi sebelum JS jalan */
        [data-anim] { opacity: 0; }

        /* Kelas yang ditambahkan JS */
        [data-anim="sidebar"].anim-run  { animation: slideFromLeft 0.55s cubic-bezier(.22,1,.36,1) both; }
        [data-anim="topbar"].anim-run   { animation: slideFromTop  0.50s cubic-bezier(.22,1,.36,1) 0.15s both; }
        [data-anim="main"].anim-run     { animation: fadeSlideUp   0.55s cubic-bezier(.22,1,.36,1) 0.25s both; }

        /* KPI card staggered — delay diset via inline style --anim-delay */
        [data-anim="kpi"].anim-run {
            animation: fadeSlideUp 0.55s cubic-bezier(.22,1,.36,1) var(--anim-delay, 0s) both;
        }
        /* Panel card staggered */
        [data-anim="panel"].anim-run {
            animation: fadeSlideUp 0.55s cubic-bezier(.22,1,.36,1) var(--anim-delay, 0.3s) both;
        }

        /* ── KPI card hover shimmer ────────────────────────────────── */
        .kpi-card-wrap {
            position: relative;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .kpi-card-wrap:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px -4px rgba(0,0,0,0.12);
        }
        .kpi-card-wrap::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.18) 50%, transparent 60%);
            transform: translateX(-100%);
            transition: transform 0.55s ease;
            pointer-events: none;
        }
        .kpi-card-wrap:hover::after {
            transform: translateX(100%);
        }

        /* ── Panel card hover ──────────────────────────────────────── */
        .panel-card {
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }
        .panel-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px -4px rgba(0,0,0,0.10);
        }
    </style>
</head>
<body class="h-full bg-canvas font-sans text-ink-900 antialiased" x-data="{ mobileSidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        {{-- ===== Mobile Sidebar Backdrop ===== --}}
        <div x-show="mobileSidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileSidebarOpen = false"
             class="fixed inset-0 z-40 bg-ink-900/60 backdrop-blur-sm lg:hidden"
             style="display: none;">
        </div>

        {{-- ===== Mobile Sidebar Drawer (Layar Kecil / Zoom In) ===== --}}
        <aside x-show="mobileSidebarOpen"
               x-transition:enter="transition ease-in-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in-out duration-300 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-ink-900 shadow-2xl lg:hidden"
               style="display: none;">
            <div class="flex items-center justify-between px-5 py-5 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo-pmo-workspace.png') }}" alt="PMO Workspace"
                         class="h-8 w-8 rounded-lg bg-pmo-500 object-contain p-1">
                    <div class="leading-tight">
                        <p class="font-display text-sm font-bold text-white">PMO Workspace</p>
                        <p class="text-[11px] text-ink-300">Perum Jasa Tirta II</p>
                    </div>
                </div>
                <button @click="mobileSidebarOpen = false" class="rounded-lg p-1.5 text-ink-300 hover:bg-white/10 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="mt-3 flex-1 space-y-1 px-3 overflow-y-auto">
                @php
                    $navItems = [
                        ['label' => 'Dashboard',     'route' => 'dashboard',             'icon' => 'grid'],
                        ['label' => 'Proyek',         'route' => 'projects.index',        'icon' => 'folder'],
                        ['label' => 'Change Request',  'route' => 'change-requests.index', 'icon' => 'megaphone'],
                        ['label' => 'Laporan',        'route' => 'reports.index',         'icon' => 'chart'],
                    ];
                    if (auth()->user()?->isAdmin()) {
                        $navItems[] = ['label' => 'Pengguna', 'route' => 'users.index', 'icon' => 'users'];
                    }
                @endphp

                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['route'].'*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                              {{ $active ? 'bg-pmo-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}">
                        <x-nav-icon :name="$item['icon']" :active="$active" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 px-3 py-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-ink-300 transition hover:bg-white/5 hover:text-white">
                        <svg class="h-4.5 w-4.5" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 17l5-5-5-5M20 12H9M12 19H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== Desktop Sidebar — (Layar Laptop/Desktop) ===== --}}
        <aside data-anim="sidebar" class="hidden w-64 shrink-0 flex-col overflow-y-auto bg-ink-900 lg:flex">
            <div class="flex items-center gap-2.5 px-6 py-6">
                <img src="{{ asset('images/logo-pmo-workspace.png') }}" alt="PMO Workspace"
                     class="h-9 w-9 rounded-lg bg-pmo-500 object-contain p-1.5">
                <div class="leading-tight">
                    <p class="font-display text-sm font-bold text-white">PMO Workspace</p>
                    <p class="text-xs text-ink-300">Perum Jasa Tirta II</p>
                </div>
            </div>

            <nav class="mt-2 flex-1 space-y-0.5 px-3">
                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['route'].'*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                              {{ $active ? 'bg-pmo-500 text-white' : 'text-ink-300 hover:bg-white/5 hover:text-white' }}">
                        <x-nav-icon :name="$item['icon']" :active="$active" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 px-3 py-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-ink-300 transition hover:bg-white/5 hover:text-white">
                        <svg class="h-4.5 w-4.5" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 17l5-5-5-5M20 12H9M12 19H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== Main column ===== --}}
        <div class="flex h-full flex-1 flex-col overflow-hidden min-w-0">

            {{-- Topbar — posisi tetap, dengan tombol hamburger untuk mobile/zoom --}}
            <header data-anim="topbar" class="flex shrink-0 items-center justify-between border-b border-ink-100 bg-surface px-4 sm:px-6 py-3.5 sm:py-4 gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Hamburger Button (Mobile / Zoom-in < 1024px) -->
                    <button @click="mobileSidebarOpen = true"
                            class="rounded-lg border border-ink-100 p-2 text-ink-700 hover:bg-canvas lg:hidden shrink-0"
                            aria-label="Buka Menu Navigation">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium uppercase tracking-wide text-pmo-600">{{ $eyebrow ?? 'Program Management Office' }}</p>
                        <h1 class="truncate font-display text-lg sm:text-xl font-bold text-ink-900">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <div class="flex items-center gap-2 sm:gap-2.5 rounded-full border border-ink-100 py-1 pl-1 pr-2.5 sm:pr-3">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-pmo-100 text-xs font-semibold text-pmo-700">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <div class="hidden sm:block leading-tight">
                            <p class="text-xs font-semibold text-ink-900 truncate max-w-[120px] lg:max-w-none">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-ink-500">{{ match(auth()->user()->role ?? '') {
                                'admin_pmo' => 'Administrator PMO',
                                'staff_pmo' => 'Staff PMO',
                                default => 'Manajemen / Viewer',
                            } }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main data-anim="main" class="flex-1 overflow-y-auto px-4 sm:px-6 py-6">
                {{ $slot }}
            </main>
        </div>
    </div>
    {{-- ===== Toast Notification (Flash Message — semua ukuran layar) ===== --}}
    @if (session('status'))
        @php
            // Deteksi apakah pesan adalah warning (mengandung ⚠️) atau success biasa
            $toastMsg     = session('status');
            $isWarning    = str_contains($toastMsg, '⚠️');
            $borderColor  = $isWarning ? 'border-status-warning/40'  : 'border-status-success/30';
            $iconColor    = $isWarning ? 'text-status-warning'        : 'text-status-success';
            $bgColor      = $isWarning ? 'bg-status-warningBg'        : 'bg-white';
        @endphp
        <div id="pmo-toast"
             class="fixed bottom-5 right-5 z-[9998] flex items-start gap-3 rounded-xl border {{ $borderColor }} {{ $bgColor }} px-4 py-3.5 shadow-2xl max-w-sm"
             style="animation: toastSlideIn 0.4s cubic-bezier(.22,1,.36,1) both;">
            @if ($isWarning)
                <svg class="mt-0.5 h-4 w-4 shrink-0 {{ $iconColor }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            @else
                <svg class="mt-0.5 h-4 w-4 shrink-0 {{ $iconColor }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            @endif
            <p class="text-sm font-medium text-ink-900">{{ $toastMsg }}</p>
            <button onclick="document.getElementById('pmo-toast').remove()"
                    class="-mr-1 ml-2 mt-0.5 shrink-0 text-ink-400 hover:text-ink-700"
                    aria-label="Tutup">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <style>
            @keyframes toastSlideIn {
                from { opacity: 0; transform: translateY(16px) scale(0.97); }
                to   { opacity: 1; transform: translateY(0)  scale(1); }
            }
        </style>
        <script>
            (function () {
                var t = document.getElementById('pmo-toast');
                if (!t) return;
                setTimeout(function () {
                    t.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    t.style.opacity = '0';
                    t.style.transform = 'translateY(8px)';
                    setTimeout(function () { t.remove(); }, 400);
                }, 4000);
            })();
        </script>
    @endif
</body>

<script>
/* ============================================================
   ENTRANCE ANIMATIONS — jalankan saat DOM siap
============================================================ */
(function () {
    /* Trigger animasi semua elemen [data-anim] */
    requestAnimationFrame(function () {
        document.querySelectorAll('[data-anim]').forEach(function (el) {
            el.classList.add('anim-run');
        });
    });
})();

/* ============================================================
   NUMBER ROLL (countUp) — untuk elemen [data-countup]
   Cara pakai: <span data-countup="42">0</span>
============================================================ */
(function () {
    function easeOutCubic(t) { return 1 - Math.pow(1 - t, 3); }

    function animateCount(el) {
        var target = parseInt(el.getAttribute('data-countup'), 10);

        /* Angka 0 atau tidak valid → tampilkan langsung, tidak perlu rolling */
        if (isNaN(target) || target <= 0) {
            el.textContent = isNaN(target) ? 0 : target;
            return;
        }

        /*
         * Masalah utama: KPI card pakai CSS entrance animation dengan delay.
         * CSS animation berjalan:  delay (0.3–0.6 s)  +  durasi (0.55 s)
         * Total card baru terlihat: ~0.85 – 1.15 detik dari page load.
         *
         * Kita baca --anim-delay dari parent card melalui getComputedStyle,
         * lalu tambahkan durasi animasi (550 ms) + buffer (100 ms).
         */
        var parent      = el.closest('[data-anim]');
        var delayMs     = 0;
        if (parent) {
            var raw = getComputedStyle(parent).getPropertyValue('--anim-delay').trim();
            delayMs = Math.round(parseFloat(raw || '0') * 1000); /* "0.4s" → 400 */
        }
        var startAfter = delayMs + 650; /* delay card + 550ms animasi + 100ms buffer */

        setTimeout(function () {
            var DURATION  = 1500; /* total durasi rolling (ms) */
            var ROLL_STOP = 0.60; /* di titik ini angka mulai konvergen ke target */
            var startTime = null;

            function step(ts) {
                if (!startTime) startTime = ts;
                var progress = Math.min((ts - startTime) / DURATION, 1);
                var displayed;

                if (progress < ROLL_STOP) {
                    /* Fase acak: angka random 0 – target */
                    displayed = Math.floor(Math.random() * (target + 1));
                } else {
                    /* Fase konvergen: easing menuju angka asli */
                    var t2 = (progress - ROLL_STOP) / (1 - ROLL_STOP);
                    displayed = Math.round(easeOutCubic(t2) * target);
                }

                el.textContent = displayed;

                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = target; /* pastikan tepat di akhir */
                }
            }

            requestAnimationFrame(step);

        }, startAfter);
    }

    /* Observe semua [data-countup] */
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCount(entry.target);
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('[data-countup]').forEach(function (el) {
            io.observe(el);
        });
    } else {
        /* Fallback browser lama */
        document.querySelectorAll('[data-countup]').forEach(animateCount);
    }
})();
</script>
</html>
