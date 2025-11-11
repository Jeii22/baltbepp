<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Server Error</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root { color-scheme: light dark; }
    body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; margin: 0; padding: 0; }
    .wrap { min-height: 100vh; display: grid; place-items: center; }
    .card { max-width: 720px; margin: 24px; padding: 24px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.08); background: rgba(255,255,255,0.85); backdrop-filter: blur(6px); box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
    h1 { margin: 0 0 12px; font-size: 24px; font-weight: 600; }
    p { margin: 0 0 8px; line-height: 1.6; }
    .muted { opacity: 0.7; font-size: 14px; }
    .actions { margin-top: 16px; display: flex; gap: 8px; }
    .btn { display: inline-block; padding: 10px 14px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid #0f766e; color: white; background: #0ea5e9; }
    .btn.secondary { background: transparent; color: #0f766e; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Something went wrong</h1>
      <p>{{ $message ?? 'An unexpected error occurred. Please try again later.' }}</p>
      <p class="muted">If this continues, please contact support and include the time of the error.</p>
      <div class="actions">
        <a class="btn" href="/">Go to Home</a>
        <a class="btn secondary" href="javascript:location.reload()">Reload</a>
      </div>
    </div>
  </div>
</body>
</html>
