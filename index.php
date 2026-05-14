<?php
// Fast Track Academy - Portal Tempahan
// Futuristic Homepage

$quotes = [
    ["text" => "Pendidikan adalah pasport ke masa depan.", "author" => "Malcolm X"],
    ["text" => "Kecemerlangan bukan satu tindakan, tetapi satu tabiat.", "author" => "Aristotle"],
    ["text" => "Cara untuk bermula adalah berhenti bercakap dan mula melakukan.", "author" => "Walt Disney"],
    ["text" => "Masa depan bergantung kepada apa yang kita lakukan hari ini.", "author" => "Mahatma Gandhi"],
];

// Get random quote for initial display (JS will handle rotation)
$currentQuote = $quotes[array_rand($quotes)];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fast Track Academy | Portal Tempahan</title>
    <meta name="description" content="Selamat datang ke portal tempahan Fast Track Academy - Strive for Excellence">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        :root {
            --ft-blue: #004a80;
            --ft-blue-glow: rgba(0, 74, 128, 0.5);
            --ft-orange: #f58220;
            --ft-orange-glow: rgba(245, 130, 32, 0.5);
            --bg-dark: #0a0e1a;
            --bg-card: rgba(15, 23, 42, 0.7);
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border-color: rgba(59, 130, 246, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Grid Background */
        .grid-background {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: 0.5;
            pointer-events: none;
        }

        /* Glowing Orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            transition: transform 0.3s ease-out;
        }

        .orb-blue {
            width: 600px;
            height: 600px;
            background: rgba(0, 74, 128, 0.2);
            top: 10%;
            left: 10%;
        }

        .orb-orange {
            width: 500px;
            height: 500px;
            background: rgba(245, 130, 32, 0.15);
            bottom: 10%;
            right: 10%;
            animation: float 10s ease-in-out infinite;
        }

        .orb-accent {
            width: 300px;
            height: 300px;
            background: rgba(59, 130, 246, 0.1);
            top: 50%;
            left: 50%;
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            25% { transform: translateY(-20px) translateX(10px); }
            50% { transform: translateY(-10px) translateX(-10px); }
            75% { transform: translateY(-30px) translateX(5px); }
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.05); }
        }

        @keyframes border-glow {
            0%, 100% { border-color: rgba(59, 130, 246, 0.3); }
            50% { border-color: rgba(59, 130, 246, 0.6); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes arrowMove {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(5px); }
        }

        /* Particles */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(96, 165, 250, 0.4);
            border-radius: 50%;
            animation: particleFloat 4s infinite;
        }

        @keyframes particleFloat {
            0% {
                opacity: 0;
                transform: translateY(0);
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: translateY(-200px);
            }
        }

        /* Container */
        .container {
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Glass Effect */
        .glass {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
        }

        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(59, 130, 246, 0.15);
        }

        /* Neon Effects */
        .neon-border-blue {
            box-shadow: 0 0 10px rgba(0, 74, 128, 0.3), 
                        0 0 20px rgba(0, 74, 128, 0.2), 
                        0 0 30px rgba(0, 74, 128, 0.1),
                        inset 0 0 10px rgba(0, 74, 128, 0.05);
        }

        .text-glow-orange {
            text-shadow: 0 0 20px rgba(245, 130, 32, 0.8), 0 0 40px rgba(245, 130, 32, 0.4);
        }

        /* Header / Logo */
        .header {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
            animation: fadeInUp 0.8s ease-out;
        }

        .logo-wrapper {
            position: relative;
        }

        .logo-wrapper::before {
            content: '';
            position: absolute;
            inset: -1rem;
            background: linear-gradient(to right, rgba(0, 74, 128, 0.2), transparent, rgba(245, 130, 32, 0.2));
            border-radius: 1.5rem;
            filter: blur(20px);
            opacity: 0;
            transition: opacity 0.5s;
        }

        .logo-wrapper:hover::before {
            opacity: 1;
        }

        .logo {
            width: 280px;
            position: relative;
            z-index: 1;
        }

        @media (min-width: 768px) {
            .logo { width: 320px; }
        }

        /* Hero Section */
        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
            margin-bottom: 3rem;
            flex: 1;
            justify-content: center;
        }

        @media (min-width: 1024px) {
            .hero {
                flex-direction: row;
                gap: 4rem;
            }
        }

        /* Tagline Box */
        .tagline-box {
            position: relative;
            animation: slideInLeft 0.8s ease-out 0.5s both;
        }

        .tagline-box::before {
            content: '';
            position: absolute;
            inset: -2rem;
            background: linear-gradient(to bottom right, rgba(0, 74, 128, 0.3), transparent);
            border-radius: 1.5rem;
            filter: blur(30px);
        }

        .tagline-inner {
            position: relative;
            border-radius: 1.5rem;
            padding: 2rem;
        }

        @media (min-width: 768px) {
            .tagline-inner { padding: 3rem; }
        }

        .tagline-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .tagline-badge span {
            color: var(--ft-orange);
            font-size: 0.875rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .tagline-title {
            font-size: 2.5rem;
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: -0.02em;
        }

        @media (min-width: 768px) {
            .tagline-title { font-size: 3rem; }
        }

        @media (min-width: 1024px) {
            .tagline-title { font-size: 3.75rem; }
        }

        .tagline-title .line-1 { color: #d1d5db; }
        .tagline-title .line-2 { color: #9ca3af; }
        .tagline-title .line-orange { color: var(--ft-orange); }

        .tagline-footer {
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tagline-footer span {
            color: rgba(96, 165, 250, 0.8);
            font-size: 0.875rem;
        }

        /* Quote Box */
        .quote-box {
            width: 100%;
            max-width: 28rem;
            animation: slideInRight 0.8s ease-out 0.7s both;
        }

        .quote-wrapper {
            position: relative;
        }

        .quote-wrapper::before {
            content: '';
            position: absolute;
            inset: -1rem;
            background: linear-gradient(to bottom right, rgba(245, 130, 32, 0.2), rgba(0, 74, 128, 0.2));
            border-radius: 1.5rem;
            filter: blur(20px);
            animation: pulse-glow 4s ease-in-out infinite;
        }

        .quote-inner {
            position: relative;
            border-radius: 1.5rem;
            padding: 2rem;
            border: 2px solid rgba(245, 130, 32, 0.3);
            transition: border-color 0.5s;
        }

        .quote-inner:hover {
            border-color: rgba(245, 130, 32, 0.5);
        }

        .quote-dots {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.25rem;
        }

        .quote-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .quote-dot:nth-child(1) { background: var(--ft-orange); }
        .quote-dot:nth-child(2) { background: #3b82f6; animation-delay: 0.2s; }
        .quote-dot:nth-child(3) { background: #22c55e; animation-delay: 0.4s; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .quote-content {
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .quote-text {
            font-size: 1.125rem;
            font-style: italic;
            color: #e5e7eb;
            line-height: 1.7;
            margin-bottom: 1rem;
            opacity: 1;
            transition: opacity 0.3s;
        }

        @media (min-width: 768px) {
            .quote-text { font-size: 1.25rem; }
        }

        .quote-author {
            color: var(--ft-orange);
            font-weight: 700;
            font-size: 0.875rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .quote-divider {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quote-bar {
            height: 4px;
            border-radius: 9999px;
        }

        .quote-bar-1 { width: 2rem; background: var(--ft-orange); }
        .quote-bar-2 { width: 1rem; background: #3b82f6; }
        .quote-bar-3 { width: 0.5rem; background: #6b7280; }

        /* System Label */
        .system-label {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
            animation: fadeInUp 0.6s ease-out 0.9s both;
        }

        .system-badge {
            border-radius: 9999px;
            padding: 0.75rem 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: border-glow 3s ease-in-out infinite;
        }

        .system-badge span {
            color: #9ca3af;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        /* Portal Cards */
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            max-width: 56rem;
            margin: 0 auto;
            width: 100%;
            margin-bottom: 4rem;
        }

        @media (min-width: 768px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .card-link {
            text-decoration: none;
            display: block;
        }

        .card {
            position: relative;
            height: 100%;
            animation: fadeInUp 0.8s ease-out 1.1s both;
        }

        .card:nth-child(2) {
            animation-delay: 1.2s;
        }

        .card::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 1.5rem;
            filter: blur(15px);
            opacity: 0.2;
            transition: opacity 0.5s;
        }

        .card-blue::before {
            background: linear-gradient(to right, var(--ft-blue), #2563eb);
        }

        .card-orange::before {
            background: linear-gradient(to right, var(--ft-orange), #f97316);
        }

        .card:hover::before {
            opacity: 0.4;
        }

        .card-inner {
            position: relative;
            border-radius: 1.5rem;
            padding: 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.5s;
        }

        @media (min-width: 768px) {
            .card-inner { padding: 2.5rem; }
        }

        .card:hover .card-inner {
            transform: translateY(-8px);
        }

        .card-blue:hover .card-inner {
            box-shadow: 0 0 30px rgba(0, 74, 128, 0.5), 0 0 60px rgba(0, 74, 128, 0.3);
        }

        .card-orange:hover .card-inner {
            box-shadow: 0 0 30px rgba(245, 130, 32, 0.5), 0 0 60px rgba(245, 130, 32, 0.3);
        }

        .card-icon-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .card-icon-wrapper::before {
            content: '';
            position: absolute;
            inset: -1rem;
            border-radius: 1rem;
            filter: blur(20px);
            transition: background 0.5s;
        }

        .card-blue .card-icon-wrapper::before { background: rgba(0, 74, 128, 0.3); }
        .card-orange .card-icon-wrapper::before { background: rgba(245, 130, 32, 0.3); }

        .card:hover .card-blue .card-icon-wrapper::before { background: rgba(0, 74, 128, 0.5); }
        .card:hover .card-orange .card-icon-wrapper::before { background: rgba(245, 130, 32, 0.5); }

        .card-icon {
            position: relative;
            width: 5rem;
            height: 5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s;
        }

        .card:hover .card-icon {
            transform: scale(1.1) rotate(-5deg);
        }

        .card-blue .card-icon {
            background: linear-gradient(to bottom right, var(--ft-blue), #2563eb);
            box-shadow: 0 10px 25px rgba(0, 74, 128, 0.3);
        }

        .card-orange .card-icon {
            background: linear-gradient(to bottom right, var(--ft-orange), #f97316);
            box-shadow: 0 10px 25px rgba(245, 130, 32, 0.3);
        }

        .card-icon svg {
            width: 2.5rem;
            height: 2.5rem;
            color: white;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.75rem;
            transition: text-shadow 0.3s;
        }

        .card-blue:hover .card-title {
            text-shadow: 0 0 20px rgba(0, 74, 128, 0.8), 0 0 40px rgba(0, 74, 128, 0.4);
        }

        .card-orange:hover .card-title {
            text-shadow: 0 0 20px rgba(245, 130, 32, 0.8), 0 0 40px rgba(245, 130, 32, 0.4);
        }

        .card-desc {
            color: #9ca3af;
            line-height: 1.7;
        }

        .card-cta {
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.3s;
        }

        .card-blue .card-cta { color: var(--ft-blue); }
        .card-orange .card-cta { color: var(--ft-orange); }

        .card-blue:hover .card-cta { color: #60a5fa; }
        .card-orange:hover .card-cta { color: #fb923c; }

        .card-cta .arrow {
            animation: arrowMove 1.5s infinite;
        }

        /* Footer */
        footer {
            margin-top: auto;
            animation: fadeIn 0.8s ease-out 1.3s both;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .social-link {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            transition: all 0.3s;
        }

        .social-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.2) translateY(-3px);
        }

        .social-link svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .footer-text {
            text-align: center;
        }

        .footer-text p {
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .footer-text p:first-child {
            color: #6b7280;
        }

        .footer-text p:last-child {
            color: #4b5563;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Grid Background -->
    <div class="grid-background"></div>

    <!-- Glowing Orbs -->
    <div class="orb orb-blue" id="orb-blue"></div>
    <div class="orb orb-orange"></div>
    <div class="orb orb-accent"></div>

    <!-- Particles -->
    <div class="particles" id="particles"></div>

    <!-- Main Container -->
    <div class="container">
        <!-- Header / Logo -->
        <header class="header">
            <div class="logo-wrapper">
                <img 
                    src="Picture1.png" 
                    alt="Fast Track Academy Logo" 
                    class="logo"
                >
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <!-- Tagline Box -->
            <div class="tagline-box">
                <div class="tagline-inner glass neon-border-blue">
                    <div class="tagline-badge">
                        <i data-lucide="sparkles" style="width: 20px; height: 20px; color: var(--ft-orange);"></i>
                        <span>Portal Pintar</span>
                    </div>
                    <h1 class="tagline-title">
                        <span class="line-1">DI SINI,</span><br>
                        <span class="line-2">SETIAP SESI</span><br>
                        <span class="line-orange text-glow-orange">BERMULA</span><br>
                        <span class="line-orange text-glow-orange">DENGAN</span><br>
                        <span class="line-orange text-glow-orange">TERANCANG</span>
                    </h1>
                    <div class="tagline-footer">
                        <i data-lucide="zap" style="width: 16px; height: 16px; color: #60a5fa;"></i>
                        <span>Powered by Technology</span>
                    </div>
                </div>
            </div>

            <!-- Quote Box -->
            <div class="quote-box">
                <div class="quote-wrapper">
                    <div class="quote-inner glass-card">
                        <div class="quote-dots">
                            <span class="quote-dot"></span>
                            <span class="quote-dot"></span>
                            <span class="quote-dot"></span>
                        </div>
                        <div class="quote-content">
                            <p class="quote-text" id="quote-text">"<?php echo htmlspecialchars($currentQuote['text']); ?>"</p>
                            <span class="quote-author" id="quote-author">- <?php echo htmlspecialchars($currentQuote['author']); ?></span>
                        </div>
                        <div class="quote-divider">
                            <span class="quote-bar quote-bar-1"></span>
                            <span class="quote-bar quote-bar-2"></span>
                            <span class="quote-bar quote-bar-3"></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- System Label -->
        <div class="system-label">
            <div class="system-badge glass">
                <span>
                    <span class="status-dot"></span>
                    Selamat Datang ke Portal Tempahan Fast Track Academy
                </span>
            </div>
        </div>

        <!-- Portal Cards -->
        <section class="cards-grid">
            <!-- Tempahan Bilik Card -->
            <a href="room_booking" class="card-link">
                <div class="card card-blue">
                    <div class="card-inner glass-card">
                        <div class="card-icon-wrapper">
                            <div class="card-icon">
                                <i data-lucide="door-open"></i>
                            </div>
                        </div>
                        <h2 class="card-title">Tempahan Bilik</h2>
                        <p class="card-desc">Tempah bilik darjah anda mengikut keperluan anda dengan lebih mudah.</p>
                        <div class="card-cta">
                            <span>Teruskan</span>
                            <span class="arrow">→</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Tempahan Peranti Card -->
            <a href="tempahan_projector.php" class="card-link">
                <div class="card card-orange">
                    <div class="card-inner glass-card">
                        <div class="card-icon-wrapper">
                            <div class="card-icon" style="position: relative;">
                            <i data-lucide="projector"></i>
                            <i data-lucide="tablet" style="position:absolute; bottom:4px; left:4px; width:20px; height:20px; opacity:0.85; color:white;"></i>
                            <i data-lucide="tablet-smartphone" style="position:absolute; top:4px; right:4px; width:20px; height:20px; opacity:0.85; color:white;"></i>
                        </div>
                        </div>
                        <h2 class="card-title">Tempahan Peranti</h2>
                        <p class="card-desc">Tempah projector, tablet, atau iPad untuk sesi pembelajaran anda.</p>
                        <div class="card-cta">
                            <span>Teruskan</span>
                            <span class="arrow">→</span>
                        </div>
                    </div>
                </div>
            </a>
        </section>

        <!-- Footer -->
        <footer>
            <div class="social-links">
                <a href="https://fasttrackacademy.edu.my/" target="_blank" rel="noopener noreferrer" class="social-link glass">
                    <i data-lucide="globe"></i>
                </a>
                <a href="https://www.instagram.com/fasttrackacademymy" target="_blank" rel="noopener noreferrer" class="social-link glass">
                    <i class="fa-brands fa-instagram" style="font-size: 18px;"></i>
                </a>
                <a href="https://www.tiktok.com/@fasttrackacademymy" target="_blank" rel="noopener noreferrer" class="social-link glass">
                    <i data-lucide="music-2"></i>
                </a>
            </div>
            <div class="footer-text">
                <p>Strive For Excellence • Fast • Energetic • Futuristic</p>
                <p>Copyright © 2026 Team Intern</p>
            </div>
        </footer>
    </div>

    <script>
        // Initialize Lucide Icons - run after DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        // Quotes rotation
        const quotes = <?php echo json_encode($quotes); ?>;
        let currentIndex = 0;

        function rotateQuote() {
            currentIndex = (currentIndex + 1) % quotes.length;
            const quoteText = document.getElementById('quote-text');
            const quoteAuthor = document.getElementById('quote-author');
            
            // Fade out
            quoteText.style.opacity = '0';
            quoteAuthor.style.opacity = '0';
            
            setTimeout(() => {
                quoteText.textContent = '"' + quotes[currentIndex].text + '"';
                quoteAuthor.textContent = '- ' + quotes[currentIndex].author;
                
                // Fade in
                quoteText.style.opacity = '1';
                quoteAuthor.style.opacity = '1';
            }, 300);
        }

        setInterval(rotateQuote, 4000);

        // Mouse tracking for blue orb
        const orbBlue = document.getElementById('orb-blue');
        document.addEventListener('mousemove', (e) => {
            const x = e.clientX * 0.02;
            const y = e.clientY * 0.02;
            orbBlue.style.transform = `translate(${x}px, ${y}px)`;
        });

        // Create particles
        const particlesContainer = document.getElementById('particles');
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 4 + 's';
            particle.style.animationDuration = (Math.random() * 3 + 2) + 's';
            particlesContainer.appendChild(particle);
        }
    </script>
</body>
</html>