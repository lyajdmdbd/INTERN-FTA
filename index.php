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

        html {
            overflow-x: hidden;
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

        /* Container - Reduced padding */
        .container {
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0rem 1rem 2rem; /* Push header up */
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

        /* COMPACT ADVANCED HEADER */
        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem; /* Reduced gap */
            margin-bottom: 1rem; /* Reduced margin */
            animation: fadeInUp 1s ease-out;
        }

        .logo-section {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem; /* Reduced gap */
        }

        .logo-wrapper {
            position: relative;
            transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .logo-wrapper::before {
            content: '';
            position: absolute;
            inset: -1.5rem; /* Smaller glow */
            background: conic-gradient(from 0deg, rgba(0, 74, 128, 0.3), rgba(245, 130, 32, 0.3), rgba(59, 130, 246, 0.3), rgba(0, 74, 128, 0.3));
            border-radius: 1.5rem;
            filter: blur(25px);
            opacity: 0;
            animation: logoGlowRotate 8s linear infinite;
            z-index: -1;
        }

        .logo-wrapper:hover::before {
            opacity: 1;
            animation-play-state: paused;
        }

        @keyframes logoGlowRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .logo-glow-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: min(320px, 80vw); /* Responsive */
            height: min(320px, 80vw);
            border: 2px solid transparent;
            border-image: linear-gradient(45deg, var(--ft-blue), var(--ft-orange), #3b82f6) 1;
            border-radius: 50%;
            opacity: 0.6;
            animation: ringPulse 3s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes ringPulse {
            0%, 100% { 
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.6;
            }
            50% { 
                transform: translate(-50%, -50%) scale(1.1);
                opacity: 0.3;
            }
        }

        .logo {
            width: min(240px, 60vw); /* Responsive */
            position: relative;
            z-index: 2;
            filter: drop-shadow(0 0 30px rgba(0, 74, 128, 0.5));
            transition: all 0.4s ease;
        }

        .logo:hover {
            transform: scale(1.05) rotate(2deg);
            filter: drop-shadow(0 0 50px rgba(245, 130, 32, 0.8));
        }

        @media (min-width: 768px) {
            .logo { width: 280px; }
            .logo-glow-ring { width: 380px; height: 380px; }
        }

        /* Compact Header Rotator */
        .header-rotator {
            position: relative;
            max-width: 700px; /* Slightly smaller */
            text-align: center;
            animation: slideInUp 1s ease-out 0.3s both;
        }

        .rotator-container {
            position: relative;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(25px);
            border-radius: 1.5rem; /* Smaller radius */
            padding: 1.75rem 1.5rem; /* Reduced padding */
            border: 2px solid transparent;
            background-clip: padding-box;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .rotator-container { padding: 2.25rem 2.5rem; }
        }

        .rotator-container::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(245, 130, 32, 0.1) 50%, transparent 70%);
            border-radius: 1.5rem;
            animation: shimmer 4s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0; }
        }

        .rotator-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem; /* Reduced gap */
            background: linear-gradient(135deg, rgba(0, 74, 128, 0.2), rgba(245, 130, 32, 0.2));
            padding: 0.5rem 1.25rem; /* Smaller padding */
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem; /* Reduced margin */
            font-size: 0.8rem; /* Smaller font */
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--ft-orange);
            position: relative;
            z-index: 2;
            animation: badgeFloat 6s ease-in-out infinite;
        }

        @keyframes badgeFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        .status-indicator {
            width: 8px; /* Smaller */
            height: 8px;
            background: linear-gradient(45deg, #22c55e, #4ade80);
            border-radius: 50%;
            animation: statusPulse 2s infinite;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.6);
        }

        @keyframes statusPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.2); }
        }

        #rotating-text {
            font-size: clamp(1.2rem, 3.5vw, 1.9rem); /* Smaller text */
            font-weight: 800;
            line-height: 1.2;
            background: linear-gradient(135deg, #e2e8f0 0%, #d1d5db 50%, var(--ft-orange) 100%);
            background-size: 200% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            z-index: 2;
            margin-bottom: 0.75rem; /* Reduced margin */
            letter-spacing: -0.02em;
            animation: textGradientShift 4s ease-in-out infinite;
        }

        @keyframes textGradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .rotator-progress {
            position: absolute;
            bottom: 0.75rem; /* Adjusted position */
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 2px; /* Thinner */
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            z-index: 2;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--ft-blue), var(--ft-orange));
            border-radius: 2px;
            width: 0%;
            animation: progressFill 3.5s linear infinite;
        }

        @keyframes progressFill {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        /* Portal Cards - Reduced margin */
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            max-width: 56rem;
            margin: 0 auto 2rem; /* Reduced bottom margin */
            width: 100%;
        }

        @media (min-width: 768px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Rest of styles remain exactly the same... */
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

        /* --- PERANTI ICON GRID --- */
        .card-icon-peranti {
            flex-direction: column !important;
            gap: 4px;
            padding: 8px;
        }

        .peranti-icon-top {
            width: 1.6rem !important;
            height: 1.6rem !important;
            color: white;
        }

        .peranti-icon-row {
            display: flex;
            gap: 6px;
            align-items: center;
            justify-content: center;
        }

        .peranti-icon-sm {
            width: 1.3rem !important;
            height: 1.3rem !important;
            color: white;
            opacity: 0.92;
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
        <!-- COMPACT ADVANCED HEADER -->
        <header class="header">
            <div class="logo-section">
                <div class="logo-wrapper">
                    <div class="logo-glow-ring"></div>
                    <img src="Picture1.png" alt="Fast Track Academy Logo" class="logo">
                </div>
            </div>
            
            <div class="header-rotator glass">
                <div class="rotator-container">
                    <div class="rotator-badge">
                        <div class="status-indicator"></div>
                        <span>Portal Tempahan Aktif</span>
                    </div>
                    <h2 id="rotating-text">
                        Selamat Datang ke Portal Tempahan Fast Track Academy
                    </h2>
                    <div class="rotator-progress">
                        <div class="progress-fill"></div>
                    </div>
                </div>
            </div>
        </header>

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
            <a href="Projector" class="card-link">
                <div class="card card-orange">
                    <div class="card-inner glass-card">
                        <div class="card-icon-wrapper">
                            <div class="card-icon card-icon-peranti">
                                <i data-lucide="projector" class="peranti-icon-top"></i>
                                <div class="peranti-icon-row">
                                    <i data-lucide="tablet" class="peranti-icon-sm"></i>
                                    <i data-lucide="tablet-smartphone" class="peranti-icon-sm"></i>
                                </div>
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

        const rotatingTexts = [
            "Selamat Datang ke Portal Tempahan Fast Track Academy",
            "Di Sini, Setiap Sesi Bermula Dengan Terancang",
            "Tempahan Mudah • Pantas • Terjamin",
            "Strive for Excellence in Every Booking",
            "Portal Tempahan Masa Depan",
            "Fast Track Your Learning Journey"
        ];
        
        let textIndex = 0;
        const rotateEl = document.getElementById("rotating-text");
        const progressFill = document.querySelector('.progress-fill');
        
        function rotateHeaderText() {
            textIndex = (textIndex + 1) % rotatingTexts.length;
            
            // Fade out
            rotateEl.style.opacity = '0';
            rotateEl.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                rotateEl.textContent = rotatingTexts[textIndex];
                rotateEl.style.opacity = '1';
                rotateEl.style.transform = 'scale(1)';
                
                // Reset progress bar
                progressFill.style.width = '0%';
            }, 400);
        }
        
        setInterval(rotateHeaderText, 3500);

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