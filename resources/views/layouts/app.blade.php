<!DOCTYPE html>
<html lang="es">
<head>
  <style>
    [x-cloak] {
      display: none !important;
    }
    </style>
    
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ohffice - Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
  <link rel="icon" type="image/png" href="{{ asset('images/logo Oh_trans.png') }}">

</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
  <main class="p-6 flex-1">
    @yield('content')
  </main>
  
  @yield('scripts')

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const hoy = new Date();
      if (hoy.getMonth() !== 8) return;

      // ── Festón de banderitas ──
      (function () {
        const ns  = 'http://www.w3.org/2000/svg';
        const W   = window.innerWidth;
        const H   = 110;
        const N   = Math.max(10, Math.floor(W / 68));
        const FW  = 18, FH = 30, FP = 6;
        const half = FH / 2, third = FW * 0.38;

        const P0 = { x: 0,     y: 12 };
        const P1 = { x: W / 2, y: 70 };
        const P2 = { x: W,     y: 12 };

        function bezier(t) {
          const mt = 1 - t;
          return {
            x: mt*mt*P0.x + 2*mt*t*P1.x + t*t*P2.x,
            y: mt*mt*P0.y + 2*mt*t*P1.y + t*t*P2.y
          };
        }

        function el(tag, attrs, text) {
          const e = document.createElementNS(ns, tag);
          Object.entries(attrs).forEach(([k, v]) => e.setAttribute(k, v));
          if (text !== undefined) e.textContent = text;
          return e;
        }

        const svg = document.createElementNS(ns, 'svg');
        svg.style.cssText = `position:fixed;top:0;left:0;width:${W}px;height:${H}px;z-index:9998;pointer-events:none;overflow:visible;`;

        svg.appendChild(el('path', {
          d: `M ${P0.x},${P0.y} Q ${P1.x},${P1.y} ${P2.x},${P2.y}`,
          stroke: '#5C3317', 'stroke-width': '2', fill: 'none'
        }));

        for (let i = 0; i <= N; i++) {
          const t    = i / N;
          const pt   = bezier(t);
          // Inclinación natural: bordes se inclinan, centro cuelga vertical
          const tilt = -22 * Math.sin((t - 0.5) * Math.PI);

          const g = el('g', { transform: `translate(${pt.x.toFixed(1)},${pt.y.toFixed(1)}) rotate(${tilt.toFixed(1)})` });

          g.appendChild(el('rect', { x: -FW/2,        y: half, width: FW,       height: half, fill: '#D52B1E' }));
          g.appendChild(el('rect', { x: -FW/2+third,  y: 0,    width: FW-third, height: half, fill: '#FFFFFF' }));
          g.appendChild(el('rect', { x: -FW/2,        y: 0,    width: third,    height: half, fill: '#0039A6' }));
          g.appendChild(el('text', {
            x: -FW/2 + third/2, y: half/2 + 3,
            'text-anchor': 'middle', fill: 'white', 'font-size': '8', 'font-family': 'serif'
          }, '★'));
          g.appendChild(el('rect', { x: -FW/2, y: 0, width: FW, height: FH, fill: 'none', stroke: 'white', 'stroke-width': '0.5' }));

          svg.appendChild(g);
        }

        document.body.appendChild(svg);
        document.querySelector('main').style.paddingTop = '115px';
      })();

      // ── Confetti de emojis ──
      const emojisChile = ['🍷', '🥩', '🥂', '🍺', '🎉', '🎊', '🌹', '⭐'];
      const shapes = emojisChile.map(e => confetti.shapeFromText({ text: e, scalar: 2 }));

      const fin = Date.now() + 15000;
      (function frame() {
        confetti({
          particleCount: 1,
          spread: 90,
          startVelocity: 12,
          gravity: 0.35,
          ticks: 300,
          origin: { x: Math.random(), y: -0.1 },
          shapes,
          scalar: 2.5
        });
        if (Date.now() < fin) requestAnimationFrame(frame);
      })();
    });
  </script>
</body>
</html>
