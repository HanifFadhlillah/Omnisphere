<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Masuk' }} — PMO Workspace</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-pmo-workspace.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Login page entrance animations ─────────────────────────── */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeSlideRight {
            from { opacity: 0; transform: translateX(-32px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes pulseRing {
            0%   { transform: scale(1);   opacity: 0.3; }
            50%  { transform: scale(1.12); opacity: 0.15; }
            100% { transform: scale(1);   opacity: 0.3; }
        }
        @keyframes floatGlow {
            0%, 100% { transform: translateY(0px);   opacity: 0.2; }
            50%       { transform: translateY(-14px); opacity: 0.35; }
        }

        /* Brand panel elements */
        .brand-logo    { animation: fadeSlideRight 0.7s cubic-bezier(.22,1,.36,1) 0.1s both; }
        .brand-heading { animation: fadeSlideUp 0.8s cubic-bezier(.22,1,.36,1) 0.3s both; }
        .brand-caption { animation: fadeSlideUp 0.8s cubic-bezier(.22,1,.36,1) 0.5s both; }
        .brand-footer  { animation: fadeIn      0.8s ease 0.8s both; }

        /* Form panel staggered */
        .form-heading  { animation: fadeSlideUp 0.7s cubic-bezier(.22,1,.36,1) 0.2s both; }
        .form-subtitle { animation: fadeSlideUp 0.7s cubic-bezier(.22,1,.36,1) 0.35s both; }
        .form-field-1  { animation: fadeSlideUp 0.6s cubic-bezier(.22,1,.36,1) 0.45s both; }
        .form-field-2  { animation: fadeSlideUp 0.6s cubic-bezier(.22,1,.36,1) 0.55s both; }
        .form-remember { animation: fadeSlideUp 0.6s cubic-bezier(.22,1,.36,1) 0.62s both; }
        .form-submit   { animation: fadeSlideUp 0.6s cubic-bezier(.22,1,.36,1) 0.70s both; }
        .form-register { animation: fadeSlideUp 0.6s cubic-bezier(.22,1,.36,1) 0.78s both; }

        /* Pulse rings in brand panel */
        .ring-pulse-1  { animation: pulseRing 4s ease-in-out infinite 0s; }
        .ring-pulse-2  { animation: pulseRing 4s ease-in-out infinite 0.5s; }
        .ring-pulse-3  { animation: pulseRing 4s ease-in-out infinite 1s; }

        /* Floating blobs */
        .blob-float-1  { animation: floatGlow 7s ease-in-out infinite 0s; }
        .blob-float-2  { animation: floatGlow 9s ease-in-out infinite 2s; }

        /* ── Page transition overlay ──────────────────────────────────── */
        #login-transition-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            pointer-events: none;
            opacity: 0;
        }
        #login-transition-overlay.is-active {
            pointer-events: all;
            animation: transitionReveal 0.85s cubic-bezier(.22,1,.36,1) forwards;
        }
        @keyframes transitionReveal {
            0%   { opacity: 0; clip-path: circle(0% at 50% 50%); }
            100% { opacity: 1; clip-path: circle(150% at 50% 50%); }
        }

        /* Particle canvas */
        #brand-canvas {
            position: absolute;
            top: 210px;      /* mulai tepat di bawah blok teks heading+caption */
            left: 41px;    /* sedikit menjorok ke kanan dari tepi panel        */
            right: 0px;
            bottom: 60px;  /* beri ruang untuk baris footer di bawah           */
            pointer-events: none;
            opacity: 0.55;
        }
    </style>
</head>
<body class="h-full bg-canvas font-sans text-ink-900 antialiased">

    {{-- ===== Page transition overlay ===== --}}
    <div id="login-transition-overlay" aria-hidden="true">
        <div class="absolute inset-0 bg-gradient-to-br from-pmo-700 via-ink-900 to-ink-900"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center gap-5">
            <svg class="h-12 w-12 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            </svg>
            <p class="font-display text-lg font-semibold text-white tracking-wide">Memuat dashboard…</p>
            <div class="flex gap-1.5">
                <span class="h-2 w-2 rounded-full bg-white/70" style="animation:dotBounce 1.2s ease-in-out infinite 0s"></span>
                <span class="h-2 w-2 rounded-full bg-white/70" style="animation:dotBounce 1.2s ease-in-out infinite 0.2s"></span>
                <span class="h-2 w-2 rounded-full bg-white/70" style="animation:dotBounce 1.2s ease-in-out infinite 0.4s"></span>
            </div>
        </div>
        <style>
            @keyframes dotBounce {
                0%,80%,100% { transform:translateY(0); opacity:.6; }
                40%          { transform:translateY(-6px); opacity:1; }
            }
        </style>
    </div>

    <div class="relative flex min-h-screen w-full overflow-x-hidden overflow-y-auto">

        {{-- ===== Panel brand (kiri, tersembunyi di layar kecil) ===== --}}
        <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-ink-900 p-10 pr-16 lg:flex">
            {{-- Particle canvas --}}
            <canvas id="brand-canvas" aria-hidden="true"></canvas>

            <div class="absolute inset-0 bg-gradient-to-br from-pmo-700/40 via-ink-900 to-ink-900"></div>
            <div class="blob-float-1 absolute -right-16 -top-24 h-72 w-72 rounded-full bg-pmo-500/20 blur-3xl"></div>
            <div class="blob-float-2 absolute -bottom-32 left-10 h-72 w-72 rounded-full bg-pmo-500/10 blur-3xl"></div>
            {{-- Motif riak air, senada dengan logo Perum Jasa Tirta II --}}
            <div class="pointer-events-none absolute bottom-16 left-12 hidden xl:block">
                <div class="ring-pulse-1 h-40 w-40 rounded-full border border-pmo-300/20"></div>
                <div class="ring-pulse-2 absolute inset-3 rounded-full border border-pmo-300/15"></div>
                <div class="ring-pulse-3 absolute inset-6 rounded-full border border-pmo-300/10"></div>
            </div>

            {{-- Lockup logo: PMO Workspace + Perum Jasa Tirta II --}}
            <div class="brand-logo relative flex items-center gap-4">
                <img src="{{ asset('images/logo-pmo-workspace.png') }}" alt="PMO Workspace" class="h-11 w-auto">
                <span class="h-9 w-px bg-white/15"></span>
                <div class="flex items-center rounded-lg bg-white px-2.5 py-1.5 shadow-sm">
                    <img src="{{ asset('images/logo-pjt2.png') }}" alt="Perum Jasa Tirta II" class="h-8 w-auto">
                </div>
            </div>

            <div class="relative max-w-md">
                <p class="brand-heading font-display text-3xl font-bold leading-snug text-white">
                    Manage Every Digital Transformation Project from One Workspace
                </p>
                <p class="brand-caption mt-4 text-sm leading-relaxed text-ink-300">
                    PMO Workspace centralizes project monitoring, milestone tracking, change
                    requests, documentation, and executive dashboards into a single
                    platform&mdash;helping PMO teams deliver projects with greater visibility,
                    control, and efficiency.
                </p>
            </div>

            <p class="brand-footer relative text-xs text-ink-300">&copy; {{ date('Y') }} Program Management Office &middot; Perum Jasa Tirta II</p>
        </div>


        {{-- ===== Panel form (kanan) ===== --}}
        <div class="flex w-full flex-col justify-center px-6 py-12 lg:w-1/2 lg:pl-20 lg:pr-16">
            <div class="mx-auto w-full max-w-sm">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <span class="flex h-11 items-center rounded-lg bg-ink-900 px-2.5">
                        <img src="{{ asset('images/logo-pmo-workspace.png') }}" alt="PMO Workspace" class="h-7 w-auto">
                    </span>
                    <img src="{{ asset('images/logo-pjt2.png') }}" alt="Perum Jasa Tirta II" class="h-9 w-auto">
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>

    <script>
    /* ============================================================
       1. PARTICLE CANVAS — brand panel kiri
    ============================================================ */
    (function () {
        var canvas = document.getElementById('brand-canvas');
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        var particles = [];
        var PARTICLE_COUNT = 55;

        function resize() {
            canvas.width  = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        }

        function randomBetween(a, b) { return a + Math.random() * (b - a); }

        function createParticle() {
            return {
                x  : randomBetween(0, canvas.width),
                y  : randomBetween(0, canvas.height),
                r  : randomBetween(1, 10),
                vx : randomBetween(-0.25, 0.25),
                vy : randomBetween(-0.5, -0.15),
                alpha: randomBetween(0.15, 0.55),
                life : randomBetween(0.003, 0.007)
            };
        }

        function initParticles() {
            particles = [];
            for (var i = 0; i < PARTICLE_COUNT; i++) particles.push(createParticle());
        }

        function drawParticle(p) {
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(139,195,235,' + p.alpha + ')';
            ctx.fill();
        }

        function drawLines() {
            for (var i = 0; i < particles.length; i++) {
                for (var j = i + 1; j < particles.length; j++) {
                    var dx = particles[i].x - particles[j].x;
                    var dy = particles[i].y - particles[j].y;
                    var dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 90) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = 'rgba(139,195,235,' + (0.12 * (1 - dist / 90)) + ')';
                        ctx.lineWidth = 0.6;
                        ctx.stroke();
                    }
                }
            }
        }

        function tick() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            drawLines();
            for (var i = 0; i < particles.length; i++) {
                var p = particles[i];
                p.x += p.vx;
                p.y += p.vy;
                p.alpha -= p.life;
                if (p.alpha <= 0 || p.y < -10) {
                    particles[i] = createParticle();
                    particles[i].y = canvas.height + 5;
                } else {
                    drawParticle(p);
                }
            }
            requestAnimationFrame(tick);
        }

        resize();
        initParticles();
        tick();
        window.addEventListener('resize', function () { resize(); initParticles(); });
    })();

    /* ============================================================
       2. TOGGLE PASSWORD VISIBILITY — dipakai di halaman login/register
    ============================================================ */
    function togglePassword(inputId, iconOpenId, iconClosedId) {
        var input      = document.getElementById(inputId);
        var iconOpen   = document.getElementById(iconOpenId);
        var iconClosed = document.getElementById(iconClosedId);
        if (!input) return;

        if (input.type === 'password') {
            // Password menjadi terlihat → tampilkan mata terbuka, sembunyikan mata-coret
            input.type     = 'text';
            if (iconOpen)   iconOpen.classList.remove('hidden');
            if (iconClosed) iconClosed.classList.add('hidden');
        } else {
            // Password kembali tersembunyi → tampilkan mata-coret, sembunyikan mata terbuka
            input.type     = 'password';
            if (iconOpen)   iconOpen.classList.add('hidden');
            if (iconClosed) iconClosed.classList.remove('hidden');
        }
    }

    /* ============================================================
       3. PAGE TRANSITION — saat form login disubmit
    ============================================================ */
    (function () {
        var form    = document.querySelector('form[action*="login"]');
        var overlay = document.getElementById('login-transition-overlay');
        if (!form || !overlay) return;

        form.addEventListener('submit', function (e) {
            /* Jika ada error validasi HTML5 jangan tampilkan overlay */
            if (!form.checkValidity()) return;

            var email = form.querySelector('[name="email"]');
            var pass  = form.querySelector('[name="password"]');
            /* Validasi sederhana: keduanya harus terisi */
            if (!email || !email.value.trim() || !pass || !pass.value.trim()) return;

            overlay.classList.add('is-active');
            /* Beri tanda ke dashboard bahwa ini redirect dari login */
            sessionStorage.setItem('pmo_from_login', '1');
            /* Biarkan form submit normal setelah animasi */
        });
    })();
    </script>
</body>
</html>
