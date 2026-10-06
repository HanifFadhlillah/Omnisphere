<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logo-pmo-workspace.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* ── Water ripple intro ─────────────────────────────────── */
            #water-intro {
                position: fixed;
                inset: 0;
                z-index: 9999;
                background: linear-gradient(140deg, #0b1e35 0%, #112d4e 55%, #091526 100%);
                overflow: hidden;
                display: none;
            }
            #water-canvas {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
            }
            /* Logo + teks muncul saat tetes air menyentuh */
            #water-label {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                text-align: center;
                opacity: 0;
                transition: opacity 0.5s ease 0.1s;
                pointer-events: none;
            }
            #water-intro.impact-done #water-label { opacity: 1; }
            #water-label p:first-child {
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 1.15rem;
                font-weight: 700;
                color: rgba(255,255,255,0.92);
                letter-spacing: 0.04em;
            }
            #water-label p:last-child {
                font-size: 0.8rem;
                color: rgba(139,195,235,0.75);
                margin-top: 0.3rem;
            }
            /* Fade out overlay */
            #water-intro.fade-out {
                animation: waterFadeOut 0.65s cubic-bezier(.4,0,.2,1) forwards;
            }
            @keyframes waterFadeOut {
                to { opacity: 0; pointer-events: none; }
            }

            /* ── Page content wrapper ───────────────────────────────── */
            #page-content {
                opacity: 0;
                transition: opacity 0.45s ease;
            }
            #page-content.visible { opacity: 1; }

            /* ── Inter-page navigation transition ──────────────────── */
            #page-content.page-exit {
                opacity: 0;
                transform: translateY(-8px);
                transition: opacity 0.25s ease, transform 0.25s ease;
            }
        </style>
    </head>
    <body class="font-sans antialiased">

        {{-- ===== Water ripple intro overlay ===== --}}
        <div id="water-intro" aria-hidden="true">
            <canvas id="water-canvas"></canvas>
            <div id="water-label">
                <p>PMO Workspace</p>
                <p>Memuat dashboard Anda&hellip;</p>
            </div>
        </div>

        {{-- ===== Main content ===== --}}
        <div id="page-content">
            <div class="min-h-screen bg-gray-100">
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
        /* ============================================================
           1. WATER RIPPLE INTRO — hanya tampil setelah redirect login
        ============================================================ */
        (function () {
            var overlay = document.getElementById('water-intro');
            var content = document.getElementById('page-content');

            if (!sessionStorage.getItem('pmo_from_login')) {
                /* Navigasi biasa: langsung tampilkan konten */
                overlay.style.display = 'none';
                content.classList.add('visible');
                return;
            }
            sessionStorage.removeItem('pmo_from_login');

            /* Tampilkan overlay */
            overlay.style.display = 'block';

            var canvas = document.getElementById('water-canvas');
            var ctx    = canvas.getContext('2d');

            canvas.width  = window.innerWidth;
            canvas.height = window.innerHeight;

            var cx = canvas.width  / 2;
            var cy = canvas.height / 2;
            var maxR = Math.sqrt(cx * cx + cy * cy) * 1.15;

            /* Timeline:
               0 – 380 ms  : tetes air jatuh ke tengah
               380 ms       : impact – burst + ripple mulai
               380 – 1980ms : cincin melebar ke seluruh layar
               1980 – 2640ms: fade-out overlay, konten muncul             */
            var IMPACT_TIME    = 380;
            var RIPPLE_DUR     = 1600;
            var TOTAL          = IMPACT_TIME + RIPPLE_DUR;

            /* Definisi cincin ripple (delay dari impact) */
            var rings = [
                { d: 0,   rgb: '139,210,245', w: 3.5 },
                { d: 130, rgb: '100,180,230', w: 2.8 },
                { d: 270, rgb: '70,150,210',  w: 2.2 },
                { d: 430, rgb: '50,120,190',  w: 1.8 },
                { d: 620, rgb: '35,95,165',   w: 1.4 },
                { d: 850, rgb: '22,72,140',   w: 1.1 },
            ];

            function easeOut(t) { return 1 - Math.pow(1 - t, 2.8); }

            var startTime  = null;
            var impactFired = false;

            function frame(ts) {
                if (!startTime) startTime = ts;
                var e = ts - startTime;

                ctx.clearRect(0, 0, canvas.width, canvas.height);

                /* -- Tetes air jatuh -- */
                if (e < IMPACT_TIME) {
                    var t   = e / IMPACT_TIME;
                    var dy  = -40 + easeOut(t) * (cy + 40);

                    /* Jejak cahaya */
                    var trail = ctx.createRadialGradient(cx, dy - 15, 0, cx, dy, 25);
                    trail.addColorStop(0, 'rgba(180,230,255,0.35)');
                    trail.addColorStop(1, 'rgba(180,230,255,0)');
                    ctx.beginPath();
                    ctx.arc(cx, dy - 8, 25, 0, Math.PI * 2);
                    ctx.fillStyle = trail;
                    ctx.fill();

                    /* Tetes */
                    ctx.beginPath();
                    ctx.arc(cx, dy, 5.5, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(200,235,255,0.95)';
                    ctx.fill();
                }

                /* -- Ripple setelah impact -- */
                if (e >= IMPACT_TIME) {
                    if (!impactFired) {
                        impactFired = true;
                        overlay.classList.add('impact-done');
                    }

                    var re = e - IMPACT_TIME;

                    /* Burst kilat di titik tengah */
                    if (re < 320) {
                        var bt   = re / 320;
                        var bR   = easeOut(bt) * 110;
                        var bAlpha = (1 - bt) * 0.6;
                        var burst = ctx.createRadialGradient(cx, cy, 0, cx, cy, bR);
                        burst.addColorStop(0,   'rgba(220,245,255,' + bAlpha + ')');
                        burst.addColorStop(0.45,'rgba(139,210,245,' + (bAlpha * 0.55) + ')');
                        burst.addColorStop(1,   'rgba(139,210,245,0)');
                        ctx.beginPath();
                        ctx.arc(cx, cy, bR, 0, Math.PI * 2);
                        ctx.fillStyle = burst;
                        ctx.fill();
                    }

                    /* Cincin melebar */
                    for (var i = 0; i < rings.length; i++) {
                        var ring = rings[i];
                        var rd   = re - ring.d;
                        if (rd <= 0) continue;
                        var rt    = Math.min(rd / RIPPLE_DUR, 1);
                        var rRad  = easeOut(rt) * maxR;
                        var alpha = Math.max(0, (1 - rt) * 0.72);

                        /* Cincin utama */
                        ctx.beginPath();
                        ctx.arc(cx, cy, rRad, 0, Math.PI * 2);
                        ctx.strokeStyle = 'rgba(' + ring.rgb + ',' + alpha + ')';
                        ctx.lineWidth   = ring.w * (1 - rt * 0.55);
                        ctx.stroke();

                        /* Cincin dalam (lebih tipis, efek kedalaman) */
                        if (rRad > 40) {
                            ctx.beginPath();
                            ctx.arc(cx, cy, rRad * 0.86, 0, Math.PI * 2);
                            ctx.strokeStyle = 'rgba(' + ring.rgb + ',' + (alpha * 0.28) + ')';
                            ctx.lineWidth   = ring.w * 0.45;
                            ctx.stroke();
                        }
                    }
                }

                if (e < TOTAL) {
                    requestAnimationFrame(frame);
                } else {
                    /* Fade out overlay lalu tampilkan konten */
                    overlay.classList.add('fade-out');
                    overlay.addEventListener('animationend', function () {
                        overlay.style.display = 'none';
                    }, { once: true });
                    content.classList.add('visible');
                }
            }

            requestAnimationFrame(frame);
        })();

        /* ============================================================
           2. TRANSISI ANTAR-HALAMAN DASHBOARD — klik link → fade out/in
        ============================================================ */
        (function () {
            var content = document.getElementById('page-content');

            document.addEventListener('click', function (e) {
                var link = e.target.closest('a[href]');
                if (!link) return;

                var href = link.getAttribute('href');
                if (!href || href === '#' || href.startsWith('javascript') || href.startsWith('mailto')) return;
                if (link.target === '_blank' || link.hasAttribute('download')) return;

                /* Hanya tangani link dalam domain yang sama */
                try {
                    var url = new URL(href, window.location.origin);
                    if (url.origin !== window.location.origin) return;
                    /* Jangan intercept jika URL sama persis (reload) */
                    if (url.pathname === window.location.pathname && url.search === window.location.search) return;
                } catch (_) { return; }

                e.preventDefault();

                /* Fade out */
                content.style.transition = 'opacity 0.22s ease, transform 0.22s ease';
                content.style.opacity    = '0';
                content.style.transform  = 'translateY(-7px)';

                setTimeout(function () {
                    window.location.href = href;
                }, 230);
            });
        })();
        </script>
    </body>
</html>
