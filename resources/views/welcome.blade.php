<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Angel</title>

    <style>
        /* ===== SKY BACKGROUND ===== */
        body {
            margin: 0;
            height: 100vh;
            font-family: "Poppins", sans-serif;
            background: linear-gradient(to bottom, #7ecfff, #d0ecff);
            background-attachment: fixed;
            overflow: hidden;
        }

        /* Soft cloud shapes */
        .cloud {
            position: absolute;
            background: white;
            opacity: 0.85;
            border-radius: 50%;
            filter: blur(12px);
            animation: float 18s infinite linear;
        }

        @keyframes float {
            from { transform: translateX(-200px); }
            to   { transform: translateX(120vw); }
        }

        /* Cloud sizes */
        .cloud.small  { width: 120px; height: 70px; top: 15%; animation-duration: 22s; }
        .cloud.medium { width: 200px; height: 110px; top: 35%; animation-duration: 32s; }
        .cloud.large  { width: 300px; height: 150px; top: 60%; animation-duration: 40s; }

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

    <!-- Floating Clouds -->
    <div class="cloud small"></div>
    <div class="cloud medium"></div>
    <div class="cloud large"></div>

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
