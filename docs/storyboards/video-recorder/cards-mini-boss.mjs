import fs from 'fs';
const astro = fs.readFileSync(new URL('./astro.html', import.meta.url), 'utf8');
const cards = {
  'mb-intro': { kicker: 'A walkthrough', title: 'Face the <span class="g">Mini Boss</span>', sub: 'Every module ends with a boss exam. Watch Kim finish Module 1\'s last lesson, then take it on.', chips: ['Last lesson', 'Mini Boss', 'Next module'] },
  'mb-outro': { kicker: 'TechLab', title: 'Boss <span class="g">beaten.</span>', sub: 'Clear the lessons, beat the Mini Boss, unlock the next module. All the way to the Final Boss.', chips: ['Learn', 'Beat the boss', 'Unlock'] },
};
for (const [k, c] of Object.entries(cards)) {
  const chips = c.chips ? `<div class="chips">${c.chips.map((t, i) => `<span style="animation-delay:${1.1 + i * .25}s;background:${['#73b6ff', '#ffc94a', '#b99cff'][i]}">${t}</span>`).join('<i>→</i>')}</div>`
    : `<div class="chips"><span style="animation-delay:1.1s;background:${c.color}">${c.chip}</span></div>`;
  fs.writeFileSync(new URL(`./${k}.html`, import.meta.url), `<!doctype html><html><head><meta charset="utf-8">
<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@600;700&family=Space+Grotesk:wght@500;700&display=block" rel="stylesheet">
<style>
html,body{margin:0;height:100%;overflow:hidden}
body{background:radial-gradient(1200px 700px at 50% 35%,#24307a 0%,#11164a 55%,#080b2c 100%);color:#fff;font-family:'Space Grotesk',sans-serif;display:flex;align-items:center;justify-content:center}
.stars{position:fixed;inset:0;background-image:radial-gradient(2px 2px at 10% 20%,#fff8,transparent),radial-gradient(2px 2px at 80% 30%,#fff9,transparent),radial-gradient(1.5px 1.5px at 30% 80%,#fff7,transparent),radial-gradient(2px 2px at 65% 70%,#fff6,transparent),radial-gradient(1.5px 1.5px at 90% 85%,#fff8,transparent),radial-gradient(1.5px 1.5px at 45% 12%,#fff8,transparent),radial-gradient(2px 2px at 20% 55%,#fff5,transparent);animation:tw 3s ease-in-out infinite alternate}
@keyframes tw{to{opacity:.55}}
.wrap{position:relative;text-align:center;max-width:1250px;padding:0 40px}
.top{display:flex;align-items:center;justify-content:center;gap:18px;margin-bottom:26px;animation:up .7s cubic-bezier(.2,.9,.3,1.3) both}
.top img{width:64px;height:64px;border-radius:14px}
.top b{font:700 34px 'Space Grotesk';letter-spacing:.02em}
.astro{width:190px;margin:0 auto 18px;animation:pop .8s cubic-bezier(.2,.9,.3,1.4) .15s both, bob 2.4s ease-in-out 1s infinite}
.astro svg{width:100%;height:auto;overflow:visible}
.k{font:700 22px 'Space Grotesk';letter-spacing:.3em;text-transform:uppercase;color:#ffc94a;animation:up .6s .35s both}
h1{font:700 104px/1.05 'Pixelify Sans';margin:12px 0 18px;animation:up .7s .5s cubic-bezier(.2,.9,.3,1.2) both}
.b{color:#73b6ff}.g{color:#ffc94a}.p{color:#b99cff}
p{font:500 30px/1.4 'Space Grotesk';color:#c9d4ff;margin:0 auto;max-width:1100px;animation:up .6s .75s both}
.chips{margin-top:38px;display:flex;justify-content:center;align-items:center;gap:16px}
.chips span{font:700 20px 'Space Grotesk';letter-spacing:.14em;text-transform:uppercase;color:#0b1030;padding:12px 20px;border-radius:12px;animation:pop .5s cubic-bezier(.2,.9,.3,1.5) both;box-shadow:0 5px 0 rgba(0,0,0,.3)}
.chips i{font-style:normal;color:#8d9ad6;font-size:28px}
@keyframes up{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:none}}
@keyframes pop{from{opacity:0;transform:scale(.4)}to{opacity:1;transform:none}}
@keyframes bob{50%{transform:translateY(-10px)}}
</style></head><body><div class="stars"></div><div class="wrap">
<div class="top"><img src="logo.png"><b>TechLab</b></div>
<div class="astro">${astro}</div>
<div class="k">${c.kicker}</div><h1>${c.title}</h1><p>${c.sub}</p>${chips}
</div></body></html>`);
}
console.log('cards written');
