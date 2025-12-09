<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Welcome • Angel</title>

  <!-- Tailwind CDN for quick styling (ok for dev / demo) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* small custom touches */
    .glow {
      text-shadow: 0 6px 18px rgba(99,102,241,0.18);
    }
    /* confetti pieces */
    .confetti {
      position: absolute;
      width: 10px;
      height: 14px;
      opacity: 0.95;
      transform: translate3d(0,0,0);
      border-radius: 2px;
      will-change: transform, opacity;
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-6">
  <main class="max-w-3xl w-full bg-white shadow-2xl rounded-2xl overflow-hidden">
    <!-- Header -->
    <header class="px-8 py-10 bg-gradient-to-r from-indigo-600 to-violet-500 text-white">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-extrabold tracking-tight glow">Welcome, <span class="inline-block transform -skew-x-6 bg-white/10 px-3 py-1 rounded">Angel</span></h1>
          <p class="mt-2 text-indigo-100/90">A little message from your friendly (and mischievous) team.</p>
        </div>

        <div class="text-right">
          <img src="https://via.placeholder.com/64x64.png?text=A" alt="Angel avatar" class="rounded-full border-2 border-white shadow"/>
        </div>
      </div>
    </header>

    <!-- Content -->
    <section class="px-10 py-12 text-slate-700">
      <h2 class="text-2xl font-semibold">Angel — you caught our attention ✨</h2>
      <p class="mt-4 leading-relaxed">
        We built this page to say hello and wish you something great — whether you're crushing tasks, sipping coffee, or scheming your next big idea.
      </p>

      <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-6 rounded-xl border border-slate-100 bg-white">
          <h3 class="font-medium">A quick note</h3>
          <p class="mt-2 text-sm text-slate-500">This page is part of a friendly prank — all in good fun. No harm intended; just a little surprise to brighten your day.</p>
        </div>

        <div class="p-6 rounded-xl border border-slate-100 bg-white flex flex-col justify-between">
          <div>
            <h3 class="font-medium">Want the reveal?</h3>
            <p class="mt-2 text-sm text-slate-500">Click the big button below to confirm the prank and celebrate — confetti included.</p>
          </div>

          <div class="mt-4">
            <button id="prankBtn" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-lg bg-indigo-600 text-white font-semibold shadow hover:bg-indigo-700 transition">
              🎉 Reveal the Prank
            </button>
            <button id="resetBtn" class="hidden mt-3 w-full py-2 rounded-lg border border-slate-200 bg-white text-sm text-slate-600">Reset</button>
          </div>
        </div>
      </div>

      <!-- bottom message (hidden until reveal) -->
      <div id="revealCard" class="mt-8 p-6 rounded-xl border border-rose-100 bg-rose-50 hidden">
        <p class="font-semibold text-rose-700">Angel — this is just a prank. Hope you're doing something great (or at least smiling)! 😄</p>
        <p class="mt-2 text-sm text-rose-600">If you'd like us to remove this page, say the word — no hard feelings.</p>
      </div>
    </section>

    <!-- Footer -->
    <footer class="px-8 py-6 bg-slate-50 text-sm text-slate-500">
      <div class="flex items-center justify-between">
        <div>Made with 💙 for Angel</div>
        <div><a class="underline" href="#" id="claimLink">Tell us to stop</a></div>
      </div>
    </footer>
  </main>

  <!-- container for confetti pieces -->
  <div id="confettiRoot" aria-hidden="true"></div>

  <script>
    // small confetti generator — no external libs
    const confettiRoot = document.getElementById('confettiRoot');
    confettiRoot.style.position = 'fixed';
    confettiRoot.style.left = 0;
    confettiRoot.style.top = 0;
    confettiRoot.style.width = '100%';
    confettiRoot.style.height = '100%';
    confettiRoot.style.pointerEvents = 'none';
    confettiRoot.style.overflow = 'visible';
    confettiRoot.style.zIndex = 9999;

    function random(min, max){ return Math.random() * (max - min) + min; }

    function spawnConfettiBurst(count = 40) {
      const colors = ['#f43f5e','#fb7185','#f97316','#f59e0b','#84cc16','#06b6d4','#6366f1','#a78bfa'];
      for (let i = 0; i < count; i++){
        const el = document.createElement('div');
        el.className = 'confetti';
        el.style.background = colors[Math.floor(Math.random() * colors.length)];
        const startX = window.innerWidth * Math.random();
        el.style.left = startX + 'px';
        el.style.top = '-10px';
        el.style.transform = `rotate(${random(0, 360)}deg)`;
        confettiRoot.appendChild(el);

        // random falling trajectory
        const duration = random(1800, 3400);
        const endX = startX + random(-300, 300);
        const endY = window.innerHeight + 40 + random(0, 200);
        const rotateTo = random(180, 720);

        // use the Web Animations API when available
        if (el.animate) {
          el.animate([
            { transform: `translate3d(0,0,0) rotate(${random(0,360)}deg)`, opacity: 1 },
            { transform: `translate3d(${endX - startX}px, ${endY}px, 0) rotate(${rotateTo}deg)`, opacity: 0.9 }
          ], {
            duration: duration,
            easing: 'cubic-bezier(.2,.7,.2,1)',
            iterations: 1,
            fill: 'forwards'
          }).onfinish = () => el.remove();
        } else {
          // fallback simple JS animation
          const start = performance.now();
          function tick(t){
            const p = Math.min(1, (t - start) / duration);
            const curX = startX + (endX - startX) * p;
            const curY = (endY) * p;
            el.style.transform = `translate3d(${curX - startX}px, ${curY}px, 0) rotate(${rotateTo * p}deg)`;
            el.style.opacity = 1 - p * 0.2;
            if (p < 1) requestAnimationFrame(tick);
            else el.remove();
          }
          requestAnimationFrame(tick);
        }
      }
    }

    // behavior wiring
    const prankBtn = document.getElementById('prankBtn');
    const revealCard = document.getElementById('revealCard');
    const resetBtn = document.getElementById('resetBtn');
    const claimLink = document.getElementById('claimLink');

    prankBtn.addEventListener('click', () => {
      // show reveal text, spawn confetti bursts
      revealCard.classList.remove('hidden');
      resetBtn.classList.remove('hidden');
      spawnConfettiBurst(50);
      // second, smaller burst after a short delay
      setTimeout(() => spawnConfettiBurst(30), 450);
      // friendly console message for anyone inspecting
      console.log("Prank revealed — all friendly!");
    });

    resetBtn.addEventListener('click', () => {
      // remove existing confetti nodes quickly
      document.querySelectorAll('.confetti').forEach(n => n.remove());
      revealCard.classList.add('hidden');
      resetBtn.classList.add('hidden');
    });

    claimLink.addEventListener('click', (e) => {
      e.preventDefault();
      alert("Message received — we'll take it down. (Simulated in demo.)");
      // you could call an endpoint here to log a request to remove the prank
    });

    // Optional: keyboard shortcut to reveal: press 'R'
    window.addEventListener('keydown', (ev) => {
      if (ev.key.toLowerCase() === 'r') prankBtn.click();
    });
  </script>
</body>
</html>
