<?php
$appName = isset($setting) && !empty($setting->appname) ? $setting->appname : 'Candy CBT+';
$schoolName = isset($setting) && !empty($setting->nama_sekolah) ? $setting->nama_sekolah : (isset($exambro['school_name']) ? $exambro['school_name'] : 'Candy CBT+');
$logoPath = isset($setting) && !empty($setting->logo) ? base_url('assets/img/' . $setting->logo) : null;
$requiredAgent = isset($exambro) && !empty($exambro['user_agent']) ? $exambro['user_agent'] : 'CandyCBTBro';
$currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak | <?= esc($appName) ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.85);
            --card-border: rgba(15, 23, 42, 0.06);
            --primary-glow: rgba(99, 102, 241, 0.08);
            --accent-color: #e11d48;
            --accent-glow: rgba(225, 29, 72, 0.12);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-highlight: #be123c;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(circle at 50% 0%, var(--primary-glow) 0%, transparent 50%),
                radial-gradient(circle at 50% 100%, rgba(225, 29, 72, 0.04) 0%, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(15, 23, 42, 0.015) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(15, 23, 42, 0.015) 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
            z-index: 0;
        }

        .container {
            width: 100%;
            max-width: 500px;
            z-index: 10;
            position: relative;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.03),
                0 10px 15px -3px rgba(0, 0, 0, 0.05),
                0 20px 25px -5px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            animation: cardAppear 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardAppear {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .school-info {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(15, 23, 42, 0.05);
            padding: 6px 14px;
            border-radius: 50px;
            margin-bottom: 28px;
        }

        .school-logo {
            width: 22px;
            height: 22px;
            object-fit: contain;
        }

        .school-name {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #475569;
        }

        .icon-wrapper {
            position: relative;
            width: 90px;
            height: 90px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-glow {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulseGlow 2.5s infinite ease-in-out;
        }

        .svg-icon {
            width: 72px;
            height: 72px;
            position: relative;
            z-index: 2;
            filter: drop-shadow(0 4px 12px rgba(225, 29, 72, 0.2));
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.15); opacity: 0.9; }
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #ff6b8b 0%, var(--accent-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .description {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .notice-box {
            background: #fff5f5;
            border: 1px solid #fee2e2;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: left;
            position: relative;
            overflow: hidden;
        }

        .notice-box::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--accent-color);
        }

        .notice-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-highlight);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .notice-text {
            font-size: 0.85rem;
            line-height: 1.5;
            color: #3f3f46;
        }

        .details-wrapper {
            margin-bottom: 28px;
            text-align: left;
        }

        details {
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: rgba(15, 23, 42, 0.01);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        details[open] {
            background: #ffffff;
            border-color: rgba(15, 23, 42, 0.12);
        }

        summary {
            padding: 10px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
            outline: none;
        }

        summary:hover {
            color: var(--text-main);
        }

        .details-content {
            padding: 14px;
            border-top: 1px solid rgba(15, 23, 42, 0.06);
            font-family: monospace;
            font-size: 0.75rem;
            line-height: 1.5;
            color: #334155;
            word-break: break-all;
            background: #f8fafc;
        }

        .details-item {
            margin-bottom: 6px;
        }
        .details-item:last-child {
            margin-bottom: 0;
        }

        .details-label {
            font-weight: 700;
            color: #1e293b;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            border: none;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);
            filter: brightness(1.1);
        }

        .btn-secondary {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .footer {
            margin-top: 32px;
            font-size: 0.75rem;
            color: #64748b;
        }

        @media (max-width: 480px) {
            .card {
                padding: 32px 20px;
            }
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <?php if ($logoPath): ?>
                <div class="school-info">
                    <img src="<?= $logoPath ?>" alt="Logo Sekolah" class="school-logo">
                    <span class="school-name"><?= esc($schoolName) ?></span>
                </div>
            <?php endif; ?>

            <div class="icon-wrapper">
                <div class="icon-glow"></div>
                <!-- Shield with Padlock SVG -->
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="shieldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ff6b8b" />
                            <stop offset="100%" stop-color="#e11d48" />
                        </linearGradient>
                    </defs>
                    <path d="M12 22C12 22 20 18 20 12V5L12 2L4 5V12C4 18 12 22 12 22Z" stroke="url(#shieldGrad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="rgba(225, 29, 72, 0.03)" />
                    <rect x="9.5" y="12.5" width="5" height="4" rx="0.75" fill="url(#shieldGrad)" />
                    <path d="M10.5 12.5V11C10.5 10.1716 11.1716 9.5 12 9.5C12.8284 9.5 13.5 10.1716 13.5 11V12.5" stroke="url(#shieldGrad)" stroke-width="1.25" stroke-linecap="round" />
                </svg>
            </div>

            <h1>Akses Ditolak</h1>
            
            <p class="description">
                Browser yang Anda gunakan saat ini tidak diizinkan untuk mengakses sistem ujian ini.
            </p>

            <div class="notice-box">
                <div class="notice-title">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    Aplikasi Resmi Diperlukan
                </div>
                <div class="notice-text">
                    Silakan gunakan aplikasi resmi <strong>Candy CBT+ Exam Browser</strong> untuk melanjutkan.
                </div>
            </div>

            <div class="details-wrapper">
                <details>
                    <summary>Informasi Teknis</summary>
                    <div class="details-content">
                        <div class="details-item">
                            <span class="details-label">User Agent Anda:</span><br>
                            <?= esc($currentUserAgent) ?>
                        </div>
                        <div class="details-item" style="margin-top: 8px;">
                            <span class="details-label">User Agent Diperlukan:</span><br>
                            *<?= esc($requiredAgent) ?>*
                        </div>
                    </div>
                </details>
            </div>

            <div class="btn-group">
                <button onclick="window.location.reload();" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
                    </svg>
                    Muat Ulang Halaman
                </button>
                <a href="<?= base_url('auth/login') ?>" class="btn btn-secondary">
                    Kembali ke Beranda
                </a>
            </div>

            <div class="footer">
                &copy; <?= date('Y') ?> <?= esc($schoolName) ?> | <?= esc($appName) ?>
            </div>
        </div>
    </div>
</body>

</html>