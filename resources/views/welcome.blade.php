<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ChatBot') }} — Intelligent Chatbot for Your Website</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #f97316;
            --primary-dark: #ea580c;
            --bg: #fafaf9;
            --surface: #ffffff;
            --text-main: #1c1917;
            --text-secondary: #57534e;
            --text-muted: #a8a29e;
            --border: #e7e5e4;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }

        .nav-brand {
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 12px;
        }

        .nav-btn {
            padding: 8px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            display: inline-block;
        }

        .nav-btn-outline {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .nav-btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .nav-btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.3);
        }

        .nav-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.4);
        }

        /* Hero */
        .hero {
            padding: 140px 24px 80px;
            text-align: center;
            background: radial-gradient(ellipse at 50% 0%, rgba(249, 115, 22, 0.08) 0%, transparent 60%);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            background: rgba(249, 115, 22, 0.08);
            color: var(--primary-dark);
            margin-bottom: 24px;
        }

        .hero h1 {
            font-size: clamp(36px, 6vw, 64px);
            font-weight: 800;
            line-height: 1.1;
            max-width: 800px;
            margin: 0 auto 20px;
            letter-spacing: -0.02em;
        }

        .hero h1 span {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 18px;
            color: var(--text-secondary);
            max-width: 560px;
            margin: 0 auto 36px;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-block;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 8px 24px rgba(249, 115, 22, 0.35);
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(249, 115, 22, 0.45);
        }

        .btn-hero-outline {
            background: var(--surface);
            color: var(--text-main);
            border: 2px solid var(--border);
        }

        .btn-hero-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Features */
        .section {
            padding: 80px 24px;
        }

        .section-inner {
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .section-subtitle {
            text-align: center;
            font-size: 16px;
            color: var(--text-secondary);
            max-width: 540px;
            margin: 0 auto 48px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            transition: all 0.25s;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
            border-color: rgba(249, 115, 22, 0.3);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(249, 115, 22, 0.05));
            color: var(--primary);
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .feature-card p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* How it works */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 32px;
            counter-reset: step;
        }

        .step {
            text-align: center;
            counter-increment: step;
        }

        .step-number {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            margin: 0 auto 16px;
            box-shadow: 0 6px 18px rgba(249, 115, 22, 0.3);
        }

        .step-number::before {
            content: counter(step);
        }

        .step h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .step p {
            font-size: 14px;
            color: var(--text-secondary);
        }

        /* CTA */
        .cta-section {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 80px 24px;
            text-align: center;
            color: #fff;
        }

        .cta-section h2 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 14px;
        }

        .cta-section p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 32px;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-cta {
            padding: 14px 40px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            background: #fff;
            color: var(--primary-dark);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            display: inline-block;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.2);
        }

        /* Footer */
        .footer {
            padding: 32px 24px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
        }

        @media (max-width: 600px) {
            .hero h1 {
                font-size: 32px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    {{-- Navbar --}}
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="/" class="nav-brand"><i class="bi bi-chat-dots-fill"></i>
                {{ config('app.name', 'ChatBot') }}</a>
            <div class="nav-links">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-btn nav-btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-btn nav-btn-primary">Get Started</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="hero">
        <div class="hero-badge"><i class="bi bi-lightning-charge-fill"></i> AI-Powered Live Chat</div>
        <h1>Add <span>Intelligent Chat</span> to Your Website in Minutes</h1>
        <p>Engage visitors, answer questions instantly, and convert leads 24/7 with our embeddable chatbot widget. No
            coding required.</p>
        <div class="hero-actions">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">Start Free &rarr;</a>
            @endif
            <a href="#features" class="btn-hero btn-hero-outline">See Features</a>
        </div>
    </section>

    {{-- Features --}}
    <section class="section" id="features">
        <div class="section-inner">
            <h2 class="section-title">Everything You Need</h2>
            <p class="section-subtitle">A complete chatbot platform designed for businesses of all sizes.</p>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-code-slash"></i></div>
                    <h3>Easy Embed</h3>
                    <p>Copy &amp; paste one snippet into your website. The widget loads asynchronously and won't slow
                        your page down.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-palette"></i></div>
                    <h3>Custom Branding</h3>
                    <p>Match your brand with custom widget colors, welcome messages, and logo. Each website gets its own
                        unique look.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-chat-left-text"></i></div>
                    <h3>Real-Time Chat</h3>
                    <p>See visitor messages instantly with WebSocket-powered live updates. Respond in real-time from
                        your dashboard.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-shield-lock"></i></div>
                    <h3>Multi-Tenant Isolation</h3>
                    <p>Each client's data is fully isolated. No cross-access, no leaks. Your visitors' conversations are
                        100% private.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-globe"></i></div>
                    <h3>Multiple Websites</h3>
                    <p>Manage chatbots for multiple domains from a single dashboard. Track conversations across all your
                        sites.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <h3>Analytics Dashboard</h3>
                    <p>Monitor conversations, response times, and visitor engagement. Get insights to improve your
                        customer support.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="section" style="background: rgba(249,115,22,0.03);" id="how-it-works">
        <div class="section-inner">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Get your chatbot live in three simple steps.</p>
            <div class="steps-grid">
                <div class="step">
                    <div class="step-number"></div>
                    <h3>Register &amp; Add Your Website</h3>
                    <p>Create an account, add your website domain, and configure your chatbot preferences.</p>
                </div>
                <div class="step">
                    <div class="step-number"></div>
                    <h3>Copy the Embed Code</h3>
                    <p>Grab the auto-generated embed snippet from your dashboard and paste it into your site.</p>
                </div>
                <div class="step">
                    <div class="step-number"></div>
                    <h3>Start Chatting</h3>
                    <p>Your widget is live! See visitor messages in real-time and respond directly from the panel.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta-section">
        <h2>Ready to Engage Your Visitors?</h2>
        <p>Join hundreds of businesses using our chatbot platform to boost customer satisfaction and conversions.</p>
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="btn-cta">Create Your Free Account</a>
        @endif
    </section>

    {{-- Footer --}}
    <footer class="footer">
        &copy; {{ date('Y') }} {{ config('app.name', 'ChatBot') }}. All rights reserved.
    </footer>
<script>
  window.ChatbotWidgetConfig = {
    apiUrl: 'http://127.0.0.1:8000/api/chat/send',
    apiKey: 'miraai_f62271f98f3f929357d573fda6959bf2fbdd0c59',
    serverUrl: 'http://127.0.0.1:8000',
    logoUrl: 'http://127.0.0.1:8000/images/chatbot-logo.png'
  };
</script>
<script src="http://127.0.0.1:8000/widget/widget-loader.js"></script>
</body>

</html>
