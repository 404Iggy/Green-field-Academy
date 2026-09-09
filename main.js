/* Greenfield Academy — main.js*/

//  Hamburger menu toggle for mobile nav 
const hamburger = document.getElementById('hamburger');
const nav = document.querySelector('.main-nav');
if (hamburger && nav) {
  hamburger.addEventListener('click', () => {
    nav.classList.toggle('open');
    hamburger.classList.toggle('open');
  });
}

//Level tabs (academics page) 
const levelTabs   = document.querySelectorAll('.level-tab');
const levelPanels = document.querySelectorAll('.level-panel');
if (levelTabs.length && levelPanels.length) {
  levelTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const level = tab.dataset.level;
      if (!level) return;
      levelTabs.forEach(t  => t.classList.toggle('active', t === tab));
      levelPanels.forEach(p => p.classList.toggle('hidden', p.dataset.level !== level));
    });
  });
}

// Respect reduced-motion preference 
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

document.addEventListener('DOMContentLoaded', () => {

  //  1. SCROLL PROGRESS BAR 
  const progressBar = document.createElement('div');
  progressBar.id = 'scroll-progress';
  document.body.appendChild(progressBar);

  //  2. STICKY HEADER SHRINK 
  const siteHeader = document.querySelector('.site-header');

  //  3. PARALLAX HERO TEXT
  const hero = document.querySelector('.hero');

  //  4. FLOATING LEAF PARTICLES 
  // Inject a canvas behind the hero for ambient leaf/dot drift
  let canvas, ctx, particles = [];
  if (hero && !prefersReduced) {
    canvas = document.createElement('canvas');
    canvas.id = 'leaf-canvas';
    hero.insertBefore(canvas, hero.firstChild);
    ctx = canvas.getContext('2d');

    const resize = () => {
      canvas.width  = hero.offsetWidth;
      canvas.height = hero.offsetHeight;
    };
    resize();
    window.addEventListener('resize', resize);

    const LEAF_COUNT = 22;
    const SHAPES = ['●', '◆', '✦', '❋'];

    for (let i = 0; i < LEAF_COUNT; i++) {
      particles.push({
        x:     Math.random() * (canvas.width  || 1200),
        y:     Math.random() * (canvas.height || 700),
        size:  4 + Math.random() * 10,
        speedX: (Math.random() - 0.5) * 0.4,
        speedY: -0.3 - Math.random() * 0.5,
        opacity: 0.06 + Math.random() * 0.18,
        rotation: Math.random() * 360,
        rotSpeed: (Math.random() - 0.5) * 0.8,
        shape: SHAPES[Math.floor(Math.random() * SHAPES.length)],
      });
    }

    const drawParticles = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      particles.forEach(p => {
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate((p.rotation * Math.PI) / 180);
        ctx.globalAlpha = p.opacity;
        ctx.fillStyle   = '#c9a84c';
        ctx.font        = `${p.size * 2}px sans-serif`;
        ctx.textAlign   = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(p.shape, 0, 0);
        ctx.restore();

        p.x        += p.speedX;
        p.y        += p.speedY;
        p.rotation += p.rotSpeed;

        // Wrap around
        if (p.y < -20)              p.y = canvas.height + 20;
        if (p.x < -20)              p.x = canvas.width + 20;
        if (p.x > canvas.width + 20) p.x = -20;
      });
      requestAnimationFrame(drawParticles);
    };
    drawParticles();
  }

  // 5. STAGGERED CARD REVEALS 
  // Cards inside grids animate in one by one with a delay cascade
  const STAGGER_SELECTORS = [
    '.levels-grid .level-card',
    '.news-grid .news-card',
    '.courses-grid .course-card',
    '.values-grid .value-item',
    '.timeline-grid .timeline-card',
    '.project-grid .project-card',
    '.preference-grid .preference-card',
    '.portal-actions .portal-action-card',
    '.hero-stats .hero-stat',
  ];

  if (!prefersReduced) {
    STAGGER_SELECTORS.forEach(selector => {
      document.querySelectorAll(selector).forEach((el, i) => {
        el.classList.add('sa-card');
        el.style.setProperty('--sa-delay', `${i * 90}ms`);
      });
    });
  }

  //  6. SECTION CINEMATIC REVEALS 
  // Different entry directions for visual variety
  const cinematicMap = [
    { selector: '.section-label',   cls: 'sa-fade-up',    delay: 0   },
    { selector: '.section-title',   cls: 'sa-fade-up',    delay: 80  },
    { selector: '.divider',         cls: 'sa-expand',     delay: 160 },
    { selector: '.section-sub',     cls: 'sa-fade-up',    delay: 200 },
    { selector: '.about-visual',    cls: 'sa-fade-left',  delay: 0   },
    { selector: '.contact-info-card', cls: 'sa-fade-right', delay: 0 },
    { selector: '.form-card',       cls: 'sa-fade-left',  delay: 80  },
    { selector: '.page-hero h1',    cls: 'sa-zoom',       delay: 0   },
    { selector: '.page-hero p',     cls: 'sa-fade-up',    delay: 120 },
    { selector: '.breadcrumb',      cls: 'sa-fade-up',    delay: 0   },
    { selector: '.hero-cta',        cls: 'sa-fade-up',    delay: 200 },
    { selector: '.hero-badge',      cls: 'sa-zoom',       delay: 0   },
    { selector: '.cta-band h2',     cls: 'sa-zoom',       delay: 0   },
    { selector: '.cta-band p',      cls: 'sa-fade-up',    delay: 100 },
    { selector: '.cta-band a',      cls: 'sa-fade-up',    delay: 180 },
    { selector: '.portal-tile',     cls: 'sa-fade-up',    delay: 0   },
    { selector: '.portal-panel',    cls: 'sa-fade-up',    delay: 0   },
    { selector: '.portal-panel-full', cls: 'sa-fade-up',  delay: 0   },
    { selector: '.alert',           cls: 'sa-zoom',       delay: 0   },
  ];

  if (!prefersReduced) {
    cinematicMap.forEach(({ selector, cls, delay }) => {
      document.querySelectorAll(selector).forEach(el => {
        // Don't double-animate staggered cards
        if (!el.classList.contains('sa-card')) {
          el.classList.add(cls);
          if (delay) el.style.setProperty('--sa-delay', `${delay}ms`);
        }
      }); 
    });
  }

  //  7. STAT COUNTER ANIMATION 
  // Numbers in .hero-stat count up when they scroll into view
  const animateCounter = (el) => {
    const strong = el.querySelector('strong');
    if (!strong || strong.dataset.counted) return;
    strong.dataset.counted = '1';

    const raw   = strong.textContent.trim();
    const suffix = raw.replace(/[\d,]/g, '');   // e.g. '+', '%', 'K'
    const num    = parseFloat(raw.replace(/[^0-9.]/g, '')) || 0;
    const dur    = 1400;
    const start  = performance.now();

    const tick = (now) => {
      const t       = Math.min((now - start) / dur, 1);
      const eased   = 1 - Math.pow(1 - t, 3);           // ease-out cubic
      const current = Math.round(eased * num);
      strong.textContent = current.toLocaleString() + suffix;
      if (t < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };

  //  8. SECTION COLOUR TINT on scroll 
  // Header background subtly shifts tint as you scroll into dark sections
  const darkSections = document.querySelectorAll('.section-dark, .cta-band, .hero');
  let tintActive = false;

  //  MASTER SCROLL HANDLER 
  const onScroll = () => {
    const scrollY = window.scrollY;
    const docH    = document.documentElement.scrollHeight - window.innerHeight;

    // Progress bar
    if (progressBar) {
      progressBar.style.width = docH > 0 ? `${(scrollY / docH) * 100}%` : '0%';
    }

    // Header shrink
    if (siteHeader) {
      siteHeader.classList.toggle('header-shrunk', scrollY > 80);
    }

    // Parallax hero text
    if (hero && !prefersReduced) {
      const heroContent = hero.querySelector('.hero-content');
      if (heroContent) {
        heroContent.style.transform = `translateY(${scrollY * 0.28}px)`;
        heroContent.style.opacity   = Math.max(0, 1 - scrollY / 500);
      }
    }

    // Header tint near dark sections
    if (siteHeader) {
      let nearDark = false;
      darkSections.forEach(sec => {
        const r = sec.getBoundingClientRect();
        if (r.top < 120 && r.bottom > 0) nearDark = true;
      });
      if (nearDark !== tintActive) {
        tintActive = nearDark;
        siteHeader.classList.toggle('header-near-dark', nearDark);
      }
    }
  };

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll(); // run once on load

  //  INTERSECTION OBSERVER: trigger reveals + counters when elements scroll into view
  const allAnimated = document.querySelectorAll(
    '.sa-fade-up, .sa-fade-left, .sa-fade-right, .sa-zoom, .sa-expand, .sa-card'
  );

  const statEls = document.querySelectorAll('.hero-stat');

  const revealObserver = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('sa-visible');
      obs.unobserve(entry.target);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  if (!prefersReduced) {
    allAnimated.forEach(el => revealObserver.observe(el));
  } else {
    allAnimated.forEach(el => el.classList.add('sa-visible'));
  }

  if (statEls.length) {
    const statObserver = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        setTimeout(() => animateCounter(el), 100);
        obs.unobserve(el);
      });
    }, { threshold: 0.4, rootMargin: '0px 0px -40px 0px' });

    statEls.forEach(el => statObserver.observe(el));
  }

  //  9. CURSOR GLOW (desktop only) ───────────────────────────
  if (!prefersReduced && window.innerWidth > 900) {
    const glow = document.createElement('div');
    glow.id = 'cursor-glow';
    document.body.appendChild(glow);

    let mx = -200, my = -200;
    let cx = -200, cy = -200;

    document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });

    const moveGlow = () => {
      cx += (mx - cx) * 0.08;
      cy += (my - cy) * 0.08;
      glow.style.transform = `translate(${cx}px, ${cy}px) translate(-50%, -50%)`;
      requestAnimationFrame(moveGlow);
    };
    moveGlow();

    // Pulse on interactive elements
    document.querySelectorAll('a, button, .level-card, .news-card, .course-card, .portal-action-card').forEach(el => {
      el.addEventListener('mouseenter', () => glow.classList.add('glow-active'));
      el.addEventListener('mouseleave', () => glow.classList.remove('glow-active'));
    });
  }

  //  10. SECTION ACTIVE HIGHLIGHT in nav 
  const sections = document.querySelectorAll('section[id], div[id]');
  const navLinks  = document.querySelectorAll('.main-nav a');

  if (sections.length && navLinks.length) {
    const activeObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const id = entry.target.id;
          navLinks.forEach(a => {
            a.classList.toggle('nav-section-active',
              a.getAttribute('href') === `#${id}`);
          });
        }
      });
    }, { threshold: 0.4 });
    sections.forEach(s => activeObserver.observe(s));
  }

});