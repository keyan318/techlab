<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechLab — Learn. Build. Level Up.</title>
    <meta name="description" content="TechLab is a gamified EdTech platform in the Codexia universe. Learn programming, networking, and cybersecurity with Astro, your AI guide.">

    <!-- Fonts: Space Grotesk for headings, Inter for body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js for hero transitions -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        :root {
            /* Colors */
            --white: #FFFFFF;
            --off-white: #F7F9FC;
            --blue: #2563EB; /* Primary accent blue */
            --heading: #111827; /* Near-black */
            --body: #4B5563; /* Slate gray */
            --muted: #6B7280; /* For subtle text */
            --border: #E5E7EB; /* Light gray for dividers */

            /* Fonts */
            --font-heading: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        /* Base styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            color: var(--body);
            background-color: var(--white);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            color: var(--heading);
            font-weight: 700;
            line-height: 1.2;
        }

        h1 { font-size: 2.5rem; }
        h2 { font-size: 2rem; }
        h3 { font-size: 1.5rem; }

        p { margin-bottom: 1rem; }

        a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        a:hover {
            color: var(--blue);
        }

        /* Button styles */
        .btn-primary {
            background-color: var(--blue);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: background-color 0.2s ease, transform 0.1s ease;
            border: none;
            cursor: pointer;
            display: inline-block;
        }

        .btn-primary:hover {
            background-color: #1d4ed8; /* Blue-700 */
            transform: translateY(-2px);
        }

        .btn-primary:focus {
            outline: 2px solid var(--blue);
            outline-offset: 2px;
        }

        .btn-outline {
            border: 2px solid var(--blue);
            color: var(--blue);
            background-color: transparent;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: background-color 0.2s ease, color 0.2s ease, transform 0.1s ease;
        }

        .btn-outline:hover {
            background-color: var(--blue);
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline:focus {
            outline: 2px solid var(--blue);
            outline-offset: 2px;
        }

        /* Section padding */
        .section-padding {
            padding-top: 4rem;
            padding-bottom: 4rem;
        }

        @media (min-width: 1024px) {
            .section-padding {
                padding-top: 6rem;
                padding-bottom: 6rem;
            }
        }

        /* Container */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Header */
        header {
            background-color: var(--white);
            border-bottom: 1px solid var(--border);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--heading);
        }

        .logo-dot {
            width: 0.5rem;
            height: 0.5rem;
            background-color: var(--blue);
            border-radius: 50%;
        }

        /* Hero Section */
        .hero {
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            max-width: 40rem;
        }

        .hero-title {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.125rem;
            color: var(--body);
            margin-bottom: 2rem;
            max-width: 32rem;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* Features Section */
        .features {
            display: grid;
            gap: 2.5rem;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .feature {
            text-align: center;
            padding: 2rem;
            background-color: var(--off-white);
            border-radius: 0.75rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .feature:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 3rem;
            height: 3rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--blue);
            color: white;
            border-radius: 0.5rem;
        }

        .feature-title {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .feature-description {
            color: var(--muted);
            font-size: 0.95rem;
        }

        /* Stats Section */
        .stats {
            background-color: var(--off-white);
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 2rem;
            padding: 2.5rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--blue);
            line-height: 1;
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.95rem;
            color: var(--body);
            font-weight: 500;
        }

        /* CTA Section */
        .cta {
            text-align: center;
            padding: 4rem 2rem;
            background-color: var(--blue);
            color: white;
        }

        .cta-title {
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .cta-description {
            font-size: 1.125rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            max-width: 32rem;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        footer {
            background-color: var(--heading);
            color: var(--off-white);
            padding: 3rem 0;
        }

        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--white);
        }

        .footer-links {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .footer-link {
            color: var(--off-white);
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }

        .footer-link:hover {
            color: var(--blue);
        }

        .footer-bottom {
            margin-top: 2rem;
            font-size: 0.875rem;
            opacity: 0.7;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (min-width: 640px) {
            .hero {
                display: grid;
                grid-template-columns: 1fr 1fr;
                align-items: center;
                gap: 3rem;
            }

            .hero-content {
                text-align: left;
            }

            .hero-actions {
                justify-content: flex-start;
            }
        }

        @media (min-width: 1024px) {
            .footer-content {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .footer-links {
                justify-content: flex-start;
            }
        }

        /* Focus states for accessibility */
        :focus-visible {
            outline: 2px solid var(--blue);
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <div class="logo">
                    <div class="logo-dot"></div>
                    <span>TechLab</span>
                </div>
                <nav>
                    <ul class="flex space-x-6">
                        <li><a href="#" class="hover:text-blue-600">Features</a></li>
                        <li><a href="#" class="hover:text-blue-600">Pricing</a></li>
                        <li><a href="#" class="hover:text-blue-600">About</a></li>
                        <li><a href="#" class="btn-outline ml-4">Get Started</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero section-padding" x-data="{ open: false }" x-init="setTimeout(() => { $dispatch('hero-animate'); }, 100)" x-on:hero-animate.window="open = true">
        <div class="container">
            <div class="hero-content" x-show="open" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4">
                <h1 class="hero-title">Learn by Doing in the Codexia Universe</h1>
                <p class="hero-subtitle">
                    Master programming, networking, and cybersecurity through hands-on, gamified learning adventures with your AI guide Astro.
                </p>
                <div class="hero-actions">
                    <a href="#" class="btn-primary">Start Learning Free</a>
                    <a href="#" class="btn-outline">Watch Tour</a>
                </div>
            </div>

            <!-- Optional: Simple illustration or decoration -->
            <div class="hidden md:block" x-show="open" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4">
                <div class="w-64 h-64 mx-auto mt-8" style="background: radial-gradient(circle at 30% 30%, #2563EB22, transparent); border-radius: 50%;"></div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features section-padding">
        <div class="container">
            <h2 class="text-center mb-10">How TechLab Works</h2>
            <div class="features-grid">
                <div class="feature">
                    <div class="feature-icon">🚀</div>
                    <h3 class="feature-title">Learn by Doing</h3>
                    <p class="feature-description">
                        Every concept is taught through interactive challenges and projects—no passive lectures.
                    </p>
                </div>
                <div class="feature">
                    <div class="feature-icon">👽</div>
                    <h3 class="feature-title">Guided by Astro</h3>
                    <p class="feature-description">
                        Your AI companion provides hints, feedback, and encouragement as you learn.
                    </p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🏆</div>
                    <h3 class="feature-title">Earn Progress & Rewards</h3>
                    <p class="feature-description">
                        Complete missions, level up your skills, and unlock achievements in the Codexia universe.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats section-padding">
        <div class="container">
            <h2 class="text-center mb-12">Join Thousands of Learners</h2>
            <div class="stats-grid">
                <div class="stat">
                    <span class="stat-number">50K+</span>
                    <span class="stat-label">Active Learners</span>
                </div>
                <div class="stat">
                    <span class="stat-number">120+</span>
                    <span class="stat-label">Courses & Missions</span>
                </div>
                <div class="stat">
                    <span class="stat-number">92%</span>
                    <span class="stat-label">Course Completion Rate</span>
                </div>
                <div class="stat">
                    <span class="stat-number">4.8/5</span>
                    <span class="stat-label">Average Rating</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="cta section-padding">
        <div class="container">
            <h2 class="cta-title">Ready to Start Your Mission?</h2>
            <p class="cta-description">
                Join the TechLab academy today and begin your journey from zero to hero in the Codexia universe. No credit card required to start.
            </p>
            <a href="#" class="btn-primary">Begin Your Adventure →</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div>
                    <div class="footer-logo">
                        <div class="logo-dot"></div>
                        <span>TechLab</span>
                    </div>
                    <p class="mt-2 text-sm">
                        Gamified learning in the Codexia universe. Learn programming, networking, and cybersecurity with hands-on missions and AI guidance.
                    </p>
                </div>
                <div class="footer-links">
                    <a href="#" class="footer-link">Features</a>
                    <a href="#" class="footer-link">Pricing</a>
                    <a href="#" class="footer-link">Blog</a>
                    <a href="#" class="footer-link">Contact</a>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; <span id="year"></span> TechLab. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Set current year in footer
        document.getElementById('year').textContent = new Date().getFullYear();

        // Optional: Add intersection observer for subtle section reveals (only if prefers-reduced-motion allows)
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            // Observe sections for reveal (excluding hero which has its own animation)
            document.querySelectorAll('section:not(.hero)').forEach(section => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                section.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
                observer.observe(section);
            });

            // Add animation class when in view
            const style = document.createElement('style');
            style.textContent = `
                .animate-fade-in {
                    opacity: 1 !important;
                    transform: translateY(0) !important;
                }
            `;
            document.head.appendChild(style);
        }
    </script>
</body>
</html>