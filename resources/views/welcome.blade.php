<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Angel</title>

    <style>
        /* ===== OCEAN BACKGROUND ===== */
        body {
            margin: 0;
            height: 100vh;
            font-family: "Poppins", sans-serif;
            background: linear-gradient(to bottom, #87CEEB 0%, #4A90E2 50%, #1E3A8A 100%);
            background-attachment: fixed;
            overflow: hidden;
            position: relative;
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
            background: white;
            padding: 35px;
            margin: 120px auto;
            border-radius: 18px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.12);
            text-align: center;
            position: relative;
            z-index: 10;
            animation: fadeIn 1.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .title {
            font-size: 2.3rem;
            font-weight: 700;
            color: #1a4d7a;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 1rem;
            color: #557a95;
        }

        .prank-btn {
            margin-top: 25px;
            background: #1a73e8;
            color: white;
            border: none;
            padding: 12px 28px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.25s;
        }

        .prank-btn:hover {
            background: #125bb3;
        }

        /* Reveal box */
        .reveal {
            margin-top: 25px;
            padding: 20px;
            background: #fff5f5;
            color: #d9534f;
            font-weight: 600;
            border-radius: 12px;
            display: none;
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

        <button class="prank-btn" onclick="revealPrank()">Click Me</button>

        <div id="revealBox" class="reveal">
            Angel… this is just a prank 😆  
            <br>But seriously, hope you're doing something great!
        </div>
    </div>

    <script>
        function revealPrank() {
            document.getElementById("revealBox").style.display = "block";
        }
    </script>

</body>
</html>
