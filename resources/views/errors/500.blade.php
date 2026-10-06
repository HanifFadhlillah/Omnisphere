<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Terjadi Kesalahan | PMO Workspace</title>

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
        @keyframes spinSlow {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
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
        }
        .icon-ring {
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            border: 2px solid #FBF1DD;
            animation: pulseRing 3s ease-in-out infinite;
        }
        .icon-bg {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #FBF1DD;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon-bg svg { color: #C98A1B; }
        .spin-svg { animation: spinSlow 3s linear infinite; }

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
                <svg class="spin-svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>
                </svg>
            </div>
        </div>

        <p class="code">500</p>
        <p class="title">Terjadi Kesalahan Server</p>
        <p class="desc">
            Maaf, terjadi kesalahan pada server kami. Tim teknis telah diberitahu.
            Silakan coba lagi beberapa saat atau hubungi Administrator PMO.
        </p>

        <div>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                Coba Lagi
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
