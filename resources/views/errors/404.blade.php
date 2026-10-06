<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan | PMO Workspace</title>

    <link rel="icon" type="image/png" href="/images/logo-pmo-workspace.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F5F7FA;
            color: #1A202C;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-8px); }
        }
        @keyframes pulseRing {
            0%, 100% { transform: scale(1);    opacity: 0.15; }
            50%       { transform: scale(1.08); opacity: 0.08; }
        }

        .card {
            background: #ffffff;
            border: 1px solid #E8ECF0;
            border-radius: 1.25rem;
            box-shadow: 0 4px 24px -4px rgba(0,0,0,0.08);
            padding: 3rem 2.5rem;
            max-width: 440px;
            width: 100%;
            text-align: center;
            animation: fadeSlideUp 0.6s cubic-bezier(.22,1,.36,1) both;
        }

        .icon-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            margin-bottom: 1.5rem;
            animation: floatY 4s ease-in-out infinite;
        }
        .icon-ring {
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            border: 2px solid #EBF8FF;
            animation: pulseRing 3s ease-in-out infinite;
        }
        .icon-bg {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #EBF8FF;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon-bg svg { color: #33B0E6; }

        .code {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 4rem;
            font-weight: 800;
            color: #1A202C;
            line-height: 1;
            letter-spacing: -2px;
            margin-bottom: 0.5rem;
        }
        .title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.125rem;
            font-weight: 700;
            color: #1A202C;
            margin-bottom: 0.75rem;
        }
        .desc {
            font-size: 0.875rem;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.625rem 1.375rem;
            border-radius: 0.625rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-primary {
            background: #33B0E6;
            color: #fff;
        }
        .btn-primary:hover {
            background: #1D9DD4;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #F5F7FA;
            color: #4A5568;
            border: 1px solid #E8ECF0;
            margin-left: 0.5rem;
        }
        .btn-secondary:hover {
            background: #EDF2F7;
            transform: translateY(-1px);
        }

        .divider {
            margin: 1.5rem 0;
            border: none;
            border-top: 1px solid #E8ECF0;
        }
        .brand {
            font-size: 0.75rem;
            color: #A0AEC0;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <div class="icon-ring"></div>
            <div class="icon-bg">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607zM13.5 10.5h-6"/>
                </svg>
            </div>
        </div>

        <p class="code">404</p>
        <p class="title">Halaman Tidak Ditemukan</p>
        <p class="desc">
            Halaman yang Anda cari tidak ada atau telah dipindahkan.
            Periksa kembali URL atau kembali ke dashboard.
        </p>

        <div>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
            <a href="/dashboard" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
        </div>

        <hr class="divider">
        <p class="brand">PMO Workspace &middot; Perum Jasa Tirta II</p>
    </div>
</body>
</html>
