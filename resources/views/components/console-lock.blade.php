<script>
(function(){
    // Prevent right-click context menu
    document.addEventListener('contextmenu', function(e){ e.preventDefault(); });

    // Block common dev tools & source view shortcuts
    document.addEventListener('keydown', function(e){
        const key = e.key.toLowerCase();
        if (
            key === 'f12' ||
            (e.ctrlKey && e.shiftKey && ['i','j','c'].includes(key)) || // DevTools / console
            (e.ctrlKey && key === 'u') // View source
        ) {
            e.preventDefault();
            warnUser();
        }
    });

    let warned = false;
    function warnUser(){
        if(warned) return;
        warned = true;
        // Lightweight visual warning without blocking UI permanently
        const box = document.createElement('div');
        box.textContent = 'Developer tools are restricted.';
        box.style.cssText = 'position:fixed;bottom:20px;right:20px;background:#1e3a8a;color:#fff;padding:12px 16px;font-size:12px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.15);z-index:99999;font-family:system-ui;';
        document.body.appendChild(box);
        setTimeout(()=> box.remove(), 2500);
    }

    // DevTools open detection using size heuristics
    function detectDevTools(){
        const threshold = 160; // panel size heuristic
        if (
            (window.outerWidth - window.innerWidth) > threshold ||
            (window.outerHeight - window.innerHeight) > threshold
        ) {
            warnUser();
        }
    }
    setInterval(detectDevTools, 1000);

    // Console deterrent banner
    const styleBig = 'color:#dc2626;font-size:28px;font-weight:700;font-family:system-ui;';
    const styleSmall = 'color:#374151;font-size:14px;font-weight:500;font-family:system-ui;';
    try {
        console.log('%cStop', styleBig);
        console.log('%cThis browser feature is intended for developers. Any tampering is monitored.', styleSmall);
    } catch(_) {}
})();
</script>
