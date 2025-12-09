<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Welcome Angel</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        /* ===== OCEAN BACKGROUND ===== */
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
            background: linear-gradient(to bottom, #87CEEB 0%, #4A90E2 50%, #1E3A8A 100%);
            background-attachment: fixed;
            overflow-x: hidden;
            position: relative;
            padding: 20px;
        }

        /* Animated ocean waves */
        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200%;
            height: 150px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120'%3E%3Cpath d='M0,40 Q300,80 600,40 T1200,40 L1200,120 L0,120 Z' fill='%234FC3F7' fill-opacity='0.4'/%3E%3C/svg%3E");
            animation: wave 12s linear infinite;
        }

        .wave:nth-child(2) {
            bottom: 10px;
            animation: wave 18s linear infinite;
            opacity: 0.5;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120'%3E%3Cpath d='M0,60 Q300,20 600,60 T1200,60 L1200,120 L0,120 Z' fill='%2381D4FA' fill-opacity='0.3'/%3E%3C/svg%3E");
        }

        .wave:nth-child(3) {
            bottom: 20px;
            animation: wave 24s linear infinite;
            opacity: 0.3;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120'%3E%3Cpath d='M0,50 Q300,90 600,50 T1200,50 L1200,120 L0,120 Z' fill='%23B3E5FC' fill-opacity='0.2'/%3E%3C/svg%3E");
        }

        @keyframes wave {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Ferry boat decoration */
        .ferry {
            position: absolute;
            width: 100px;
            height: 60px;
            background: white;
            border-radius: 0 0 50% 50%;
            bottom: 160px;
            left: -150px;
            animation: sail 30s linear infinite;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .ferry::before {
            content: '';
            position: absolute;
            width: 30px;
            height: 40px;
            background: #1E3A8A;
            top: -40px;
            left: 35px;
            border-radius: 5px 5px 0 0;
        }

        @keyframes sail {
            0% { left: -150px; }
            100% { left: calc(100vw + 150px); }
        }

        /* ===== MAIN CARD ===== */
        .card {
            max-width: 520px;
            width: 100%;
            background: white;
            padding: 25px 20px;
            margin: 80px auto 20px;
            border-radius: 18px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.12);
            text-align: center;
            position: relative;
            z-index: 10;
            animation: fadeIn 1.4s ease;
        }

        @media (min-width: 768px) {
            .card {
                padding: 35px;
                margin: 120px auto;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1a4d7a;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        @media (min-width: 768px) {
            .title {
                font-size: 2.3rem;
            }
        }

        .subtitle {
            font-size: 0.95rem;
            color: #557a95;
            line-height: 1.5;
        }

        @media (min-width: 768px) {
            .subtitle {
                font-size: 1rem;
            }
        }

        .prank-btn {
            margin-top: 25px;
            background: #1a73e8;
            color: white;
            border: none;
            padding: 14px 32px;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.25s;
            width: 100%;
            max-width: 200px;
            touch-action: manipulation;
        }

        .prank-btn:hover, .prank-btn:active {
            background: #125bb3;
            transform: scale(0.98);
        }

        /* Reveal box with photo */
        .reveal {
            margin-top: 25px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            border-radius: 12px;
            display: none;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reveal-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 15px auto;
            border: 5px solid white;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            object-fit: cover;
        }

        @media (min-width: 768px) {
            .reveal-photo {
                width: 200px;
                height: 200px;
            }
        }

        .reveal-text {
            font-size: 1.1rem;
            margin-top: 15px;
            line-height: 1.6;
        }

        .facebook-tag {
            background: #4267B2;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 10px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: 0.3s;
        }

        .facebook-tag:hover {
            background: #365899;
            transform: scale(1.05);
        }

        .live-badge {
            background: #ff4458;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
            animation: pulse 2s infinite;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .photo-frame {
            position: relative;
            display: inline-block;
        }

        .photo-glow {
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #764ba2, #667eea);
            opacity: 0.4;
            animation: rotate 3s linear infinite;
            z-index: -1;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>

    <!-- Animated Ocean Waves -->
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
    
    <!-- Sailing Ferry Boat -->
    <div class="ferry"></div>

    <div class="card">
        <div class="title">Welcome, Angel ☁️</div>
        <div class="subtitle">
            A short message for you — hope you're having a great day.
        </div>

        <button class="prank-btn" onclick="revealPrank()">Tap Me</button>

        <div id="revealBox" class="reveal">
            <div class="live-badge">
                <span class="live-dot"></span>
                LIVE
            </div>
            <div class="photo-frame">
                <div class="photo-glow"></div>
                <img src="{{ asset('images/angel.png') }}" alt="Angel's Photo" class="reveal-photo" id="angelPhoto">
            </div>
            <a href="https://www.facebook.com/angel.guadamor" target="_blank" class="facebook-tag">
                📘 View on Facebook
            </a>
            <div class="reveal-text">
                😆 Got you, Angel!<br>
                This is your official "caught being awesome" photo!<br>
                <small style="opacity: 0.9; font-size: 0.85rem; margin-top: 10px; display: block;">
                    Hope this made you smile! 😊
                </small>
            </div>
        </div>
    </div>

    <script>
        function revealPrank() {
            const revealBox = document.getElementById("revealBox");
            const button = document.querySelector(".prank-btn");
            
            revealBox.style.display = "block";
            button.style.display = "none";
            
            // Add confetti effect (optional)
            setTimeout(() => {
                document.getElementById("angelPhoto").style.transform = "scale(1.05)";
            }, 200);
        }
    </script>

</body>
</html>
