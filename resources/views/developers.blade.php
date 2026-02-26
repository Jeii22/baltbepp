<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Developer - Balt Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root{
            --bg: #0a192f;
            --card: rgba(255,255,255,0.08);
            --border: rgba(144,224,239,0.25);
            --blue: #00b4d8;
            --light-blue: #90e0ef;
            --text: #e6f7ff;
            --muted: #9fb3c8;
        }

        html, body {
            min-height: 100vh;
            background: var(--bg);
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            overflow-x: hidden;
        }

        /* ─── Background ─── */
        ..bg-mesh{
            position: fixed;
            inset: 0;
            z-index: 0;

            background:
                linear-gradient(rgba(10, 25, 47, 0.78), rgba(10, 25, 47, 0.88)),
                url("{{ asset('images/barko.png') }}");

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .bg-grid {
            position: fixed; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(79,142,247,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79,142,247,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black 30%, transparent 100%);
        }
        .orb {
            position: fixed; border-radius: 50%; filter: blur(90px);
            pointer-events: none; z-index: 0;
            animation: orbDrift 12s ease-in-out infinite alternate;
        }
        .orb-1 { width:500px; height:500px; background:rgba(79,142,247,0.07); top:-120px; left:-120px; }
        .orb-2 { width:400px; height:400px; background:rgba(139,77,247,0.06); bottom:-80px; right:-80px; animation-delay:-6s; }
        @keyframes orbDrift {
            from { transform:translate(0,0) scale(1); }
            to   { transform:translate(40px,30px) scale(1.1); }
        }

        /* ─── Particles ─── */
        .particles { position:fixed; inset:0; z-index:0; pointer-events:none; overflow:hidden; }
        .particle {
            position:absolute; border-radius:50%; background:var(--blue);
            opacity:0; animation:particleFly 8s ease-in-out infinite;
        }
        @keyframes particleFly {
            0%   { opacity:0; transform:translate(0,0) scale(0); }
            20%  { opacity:0.6; }
            80%  { opacity:0.2; }
            100% { opacity:0; transform:translate(var(--tx),var(--ty)) scale(1.5); }
        }

        /* ─── Page ─── */
        .page {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 60px 24px 80px;
        }

        /* ─── Label tag ─── */
        .label-tag {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(79,142,247,0.08);
            border: 1px solid var(--border); border-radius: 999px;
            padding: 6px 18px;
            font-family: 'Syne', sans-serif; font-size: 0.72rem;
            font-weight: 700; letter-spacing: 0.13em; text-transform: uppercase;
            color: var(--blue); margin-bottom: 28px;
            animation: fadeDown 0.8s ease both;
        }
        .label-dot {
            width:6px; height:6px; border-radius:50%; background:var(--blue);
            box-shadow:0 0 8px var(--blue); animation:blink 2s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.3;} }

        /* ─── Headings ─── */
        .section-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.8rem, 7vw, 5.5rem);
            font-weight: 800; line-height: 1.0; text-align: center;
            color: var(--white); letter-spacing: -0.03em;
            margin-bottom: 12px; animation: fadeDown 0.9s 0.1s ease both;
        }
        .section-title span {
            background: linear-gradient(135deg, var(--blue) 0%, #a78bfa 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .section-sub {
            font-size: 1.05rem; color: var(--muted); text-align: center;
            margin-bottom: 64px; font-style: italic;
            animation: fadeDown 0.9s 0.2s ease both;
        }

        /* ─── Profile card ─── */
        .profile-card {
            display: flex; flex-direction: row; align-items: center; gap: 60px;
            max-width: 880px; width: 100%;
            background: var(--card); border: 1px solid var(--border);
            border-radius: 28px; padding: 56px 64px;
            position: relative; overflow: hidden;
            animation: fadeUp 1s 0.3s ease both;
            transition: border-color 0.4s, box-shadow 0.4s;
        }
        .profile-card::before {
            content:''; position:absolute; inset:0;
            background:linear-gradient(135deg, rgba(79,142,247,0.04) 0%, transparent 55%);
            pointer-events:none;
        }
        .profile-card:hover {
            border-color: rgba(79,142,247,0.3);
            box-shadow: 0 0 80px rgba(79,142,247,0.1), 0 40px 80px rgba(0,0,0,0.4);
        }
        .card-glow-tl {
            position:absolute; top:0; left:0; width:200px; height:200px;
            background:radial-gradient(circle at 0 0, rgba(79,142,247,0.15) 0%, transparent 70%);
            pointer-events:none;
        }
        .card-glow-br {
            position:absolute; bottom:0; right:0; width:180px; height:180px;
            background:radial-gradient(circle at 100% 100%, rgba(139,77,247,0.10) 0%, transparent 70%);
            pointer-events:none;
        }

        /* ─── Avatar ─── */
        .avatar-wrap { position:relative; flex-shrink:0; width:200px; height:200px; }

        .avatar-halo {
            position:absolute; inset:-28px; border-radius:50%;
            background:radial-gradient(circle, rgba(79,142,247,0.22) 0%, transparent 70%);
            animation:haloPulse 3s ease-in-out infinite; z-index:0;
        }
        @keyframes haloPulse {
            0%,100%{opacity:0.6; transform:scale(1);}
            50%    {opacity:1;   transform:scale(1.06);}
        }

        .avatar-ring {
            position:absolute; inset:0; border-radius:50%;
            background:conic-gradient(var(--blue) 0deg, #a78bfa 130deg, transparent 200deg, transparent 360deg);
            animation:spinRing 6s linear infinite; z-index:1;
        }
        @keyframes spinRing { to{transform:rotate(360deg);} }

        .avatar-inner {
            position:absolute; inset:4px; border-radius:50%;
            background:var(--bg); z-index:2;
        }
        .avatar-img-wrap {
            position:absolute; inset:8px; border-radius:50%;
            overflow:hidden; z-index:3;
            border:2px solid rgba(79,142,247,0.25);
        }
        .avatar-img-wrap img { width:100%; height:100%; object-fit:cover; }
        .avatar-fallback {
            width:100%; height:100%; display:flex; align-items:center; justify-content:center;
            background:linear-gradient(135deg, #1a3a8f, #4f8ef7);
        }
        .avatar-fallback span {
            font-family:'Syne',sans-serif; font-size:2.6rem; font-weight:800; color:#fff;
        }

        /* ─── Info block ─── */
        .info-block { flex:1; min-width:0; }

        .role-badge {
            display:inline-block;
            background:rgba(79,142,247,0.1); border:1px solid rgba(79,142,247,0.3);
            color:var(--blue); font-family:'Syne',sans-serif;
            font-size:0.68rem; font-weight:700; letter-spacing:0.14em;
            text-transform:uppercase; padding:4px 14px; border-radius:999px; margin-bottom:16px;
        }

        .dev-name {
            font-family:'Syne',sans-serif;
            font-size:clamp(2rem, 4vw, 3rem); font-weight:800;
            color:var(--white); letter-spacing:-0.03em; line-height:1.1; margin-bottom:6px;
        }
        .dev-title {
            font-size:1rem; color:var(--blue); font-weight:500;
            letter-spacing:0.02em; margin-bottom:22px;
        }
        .divider {
            width:48px; height:3px;
            background:linear-gradient(90deg, var(--blue), #a78bfa);
            border-radius:99px; margin-bottom:20px;
        }
        .dev-bio {
            font-size:0.97rem; line-height:1.8; color:var(--muted);
            margin-bottom:28px; font-weight:300;
        }

        /* ─── Stats ─── */
        .stats-row { display:flex; gap:32px; margin-bottom:28px; }
        .stat { display:flex; flex-direction:column; gap:2px; }
        .stat-num {
            font-family:'Syne',sans-serif; font-size:1.5rem; font-weight:800; color:var(--white);
        }
        .stat-lbl { font-size:0.68rem; color:var(--muted); letter-spacing:0.1em; text-transform:uppercase; }

        /* ─── Skills ─── */
        .skills { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:32px; }
        .skill-chip {
            background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);
            color:#9090c0; font-size:0.74rem; font-weight:500; padding:5px 14px; border-radius:999px;
            transition:all 0.2s; cursor:default;
        }
        .skill-chip:hover { border-color:var(--blue-dim); color:var(--blue); background:rgba(79,142,247,0.06); }

        /* ─── CTA ─── */
        .cta-btn {
            display:inline-flex; align-items:center; gap:10px;
            background:linear-gradient(135deg, var(--blue) 0%, #7c6af7 100%);
            color:#fff; font-family:'Syne',sans-serif; font-size:0.87rem; font-weight:700;
            letter-spacing:0.04em; text-decoration:none;
            padding:14px 30px; border-radius:14px;
            box-shadow:0 0 30px rgba(79,142,247,0.3), 0 8px 24px rgba(0,0,0,0.3);
            transition:transform 0.2s, box-shadow 0.2s;
        }
        .cta-btn:hover {
            transform:translateY(-2px);
            box-shadow:0 0 50px rgba(79,142,247,0.5), 0 12px 32px rgba(0,0,0,0.4);
        }
        .cta-btn svg { width:18px; height:18px; fill:#fff; flex-shrink:0; }

        /* ─── Footer ─── */
        .footer {
            position:relative; z-index:1; text-align:center;
            padding:32px 24px 40px; color:#2e2e50; font-size:0.82rem;
        }
        .footer strong { color:#44446a; font-weight:500; }

        /* ─── Animations ─── */
        @keyframes fadeDown { from{opacity:0;transform:translateY(-20px);} to{opacity:1;transform:translateY(0);} }
        @keyframes fadeUp   { from{opacity:0;transform:translateY(30px);} to{opacity:1;transform:translateY(0);} }

        /* ─── Responsive ─── */
        @media (max-width:680px) {
            .profile-card { flex-direction:column; padding:40px 28px; gap:36px; text-align:center; }
            .divider      { margin:0 auto 20px; }
            .skills       { justify-content:center; }
            .stats-row    { justify-content:center; }
            .avatar-wrap  { width:160px; height:160px; }
            .avatar-fallback span { font-size:2rem; }
        }

        .team-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:28px;
            max-width:1000px;
            width:100%;
        }

        .avatar{
            width: 95px;
            height: 95px;
            margin: 0 auto 14px;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(135deg, #00b4d8, #90e0ef);
        }

        .avatar img{
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: block;
        }

        .team-card{
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            backdrop-filter: blur(16px);
            transition: 0.35s ease;
            box-shadow: 0 8px 30px rgba(0,0,0,0.35);
        }

        .team-card:hover{
            transform: translateY(-10px) scale(1.03);
            border-color: var(--blue);

            box-shadow:
                0 0 25px rgba(0,180,216,0.45),
                0 0 60px rgba(0,180,216,0.25),
                0 15px 40px rgba(0,0,0,0.45);
        }

        .team-card:hover .avatar{
            box-shadow: 0 0 18px rgba(0,180,216,0.9);
        }

        .team-card h3{
            transition: 0.3s;
        }

        .team-card:hover h3{
            color: var(--light-blue);
        }
        .role{
            font-size:0.9rem;
            color:var(--muted);
            background:var(--light-blue);
            display:inline-block;
            padding:4px 12px;
            border-radius:999px;
        }
    </style>
</head>
<body>

    <x-loading-screen message="Loading..." />

    <div class="bg-mesh"></div>
    <div class="bg-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="particles" id="particles"></div>

    <main class="page">

        <div class="label-tag">
            <span class="label-dot"></span>
            Balt Bep Team
        </div>

        <h1 class="section-title">Meet the <span>Team</span></h1>
            <p class="section-sub">The people behind BaltBep</p>

            <div class="team-grid">

                <div class="team-card">
                    <div class="avatar">
                        <img src="{{ asset('images/developers/jake.jpg') }}" alt="Jake Rodriguez">
                    </div>
                    <h3>Jake Rodriguez</h3>
                    <p class="role">Developer / Programmer</p>
                </div>

                <div class="team-card">
                    <div class="avatar">
                        <img src="{{ asset('images/developers/kyle.jpg') }}" alt="Kyle Gadiano">
                    </div>
                    <h3>Kyle Gadiano</h3>
                    <p class="role">Designer</p>
                </div>

                <div class="team-card">
                    <div class="avatar">
                        <img src="{{ asset('images/developers/melchades.jpg') }}" alt="Melchades Mansueto">
                    </div>
                    <h3>Melchades Mansueto</h3>
                    <p class="role">Designer</p>
                </div>

                <div class="team-card">
                    <div class="avatar">
                        <img src="{{ asset('images/developers/rudelyn.jpg') }}" alt="Rudelyn Illut">
                    </div>
                    <h3>Rudelyn Illut</h3>
                    <p class="role">Main Researcher</p>
                </div>

                <div class="team-card">
                    <div class="avatar">
                        <img src="{{ asset('images/developers/jonamae.jpg') }}" alt="Jona Mae Illut">
                    </div>
                    <h3>Jona Mae Illut</h3>
                    <p class="role">Assistant Researcher</p>
                </div>

            </div>

    </main>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} <strong>Balt Bep</strong>. All rights reserved.</p>
        <p style="margin-top:6px;">Built with passion</p>
    </footer>

    <script>
        const container = document.getElementById('particles');
        for (let i = 0; i < 35; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const tx = (Math.random() - 0.5) * 200 + 'px';
            const ty = -(Math.random() * 300 + 80) + 'px';
            p.style.cssText = `
                left:${Math.random()*100}%; top:${Math.random()*100}%;
                --tx:${tx}; --ty:${ty};
                animation-delay:${Math.random()*8}s;
                animation-duration:${6+Math.random()*6}s;
                width:${Math.random()<0.3?3:2}px;
                height:${Math.random()<0.3?3:2}px;
            `;
            container.appendChild(p);
        }
    </script>

</body>
</html>