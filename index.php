<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Home';

// Fetch latest news
$news = db()->query("SELECT * FROM news ORDER BY published_at DESC LIMIT 3")->fetchAll();
?>
<?php include __DIR__ . '/header.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-badge">🎓 SCHOOL MOTTO:NULLA SINE FRUCTU</div>
    <h1>Where Every Student<br><span>Finds Their Greatness</span></h1>
    <p>From Early Years to University — we nurture curiosity, build character,<br>and launch futures that make a difference.</p>
    <div class="hero-cta">
      <a href="admissions.php" class="btn-primary">Apply Now</a>
      <a href="about.php" class="btn-secondary">Discover Our Story</a>
    </div>
    <div class="hero-stats">
      <div class="hero-stat"><strong>1,800+</strong><span>Students Enrolled</span></div>
      <div class="hero-stat"><strong>4</strong><span>Academic Levels</span></div>
      <div class="hero-stat"><strong>97%</strong><span>Pass Rate</span></div>
      <div class="hero-stat"><strong>30+</strong><span>Years of Excellence</span></div>
    </div>
  </div>
</section>

<!-- ACADEMIC LEVELS -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center">
      <span class="section-label">Our Academic Pathway</span>
      <h2 class="section-title">A Complete Journey of Learning</h2>
      <div class="divider"></div>
      <p class="section-sub">We offer a seamless academic pathway from the earliest years of childhood all the way through to university graduation.</p>
    </div>
    <div class="levels-grid">
      <div class="level-card">
        <div class="icon">🌱</div>
        <h3>Early Years</h3>
        <p>Play-based, Montessori-inspired learning for ages 2–7. Building confidence, creativity and a love for discovery.</p>
        <a href="academics.php?level=early_years">Explore Programme →</a>
      </div>
      <div class="level-card">
        <div class="icon">🔬</div>
        <h3>O Level (IGCSE)</h3>
        <p>Internationally recognised IGCSE programme for students aged 11–16, spanning Sciences, Humanities and Arts.</p>
        <a href="academics.php?level=o_level">Explore Programme →</a>
      </div>
      <div class="level-card">
        <div class="icon">⚗️</div>
        <h3>A Level</h3>
        <p>Rigorous Cambridge A-Level courses preparing students for competitive university admission worldwide.</p>
        <a href="academics.php?level=a_level">Explore Programme →</a>
      </div>
      <div class="level-card">
        <div class="icon">🏛️</div>
        <h3>University</h3>
        <p>Degree programmes in Science, Business and Education — accredited and career-focused for the modern world.</p>
        <a href="academics.php?level=university">Explore Programme →</a>
      </div>
    </div>
  </div>
</section>

<!-- WHY GREENFIELD -->
<section class="section">
  <div class="container">
    <div class="about-grid">
      <div>
        <span class="section-label">Why Choose Us</span>
        <h2 class="section-title">Excellence Is Our Standard</h2>
        <div class="divider"></div>
        <p>At Greenfield Academy we believe every child is capable of extraordinary things when given the right environment, the right teachers and the right opportunities.</p>
        <div class="values-grid mt-3">
          <div class="value-item">
            <h4>🏆 Academic Rigour</h4>
            <p>Cambridge-aligned curriculum with outstanding exam results year after year.</p>
          </div>
          <div class="value-item">
            <h4>🌍 Global Perspective</h4>
            <p>International exchange programmes and partnerships with leading universities.</p>
          </div>
          <div class="value-item">
            <h4>🤝 Character First</h4>
            <p>We develop the whole person — intellect, empathy and leadership.</p>
          </div>
          <div class="value-item">
            <h4>🔭 Innovation Hub</h4>
            <p>State-of-the-art labs, a maker space and a STEM centre for hands-on learning.</p>
          </div>
        </div>
        <a href="about.php" class="btn-primary mt-4" style="display:inline-block">Learn More About Us</a>
      </div>
      <div class="about-visual"><img src="Gemini_Generated_Image_.png" alt="About Greenfield Academy"></div>
    </div>
  </div>
</section>

<!-- LATEST NEWS -->
<?php if($news): ?>
<section class="section section-alt">
  <div class="container">
    <div class="section-head center">
      <span class="section-label">Latest Updates</span>
      <h2 class="section-title">News &amp; Announcements</h2>
      <div class="divider"></div>
    </div>
    <div class="news-grid">
      <?php foreach($news as $item): ?>
      <div class="news-card">
        <div class="news-card-img">📰</div>
        <div class="news-card-body">
          <p class="news-date"><?= date('d M Y', strtotime($item['published_at'])) ?></p>
          <h3><?= sanitize($item['title']) ?></h3>
          <p><?= sanitize($item['excerpt']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA BAND -->
<div class="cta-band">
  <div class="container">
    <h2>Ready to Join Greenfield Academy?</h2>
    <p>Applications are open for all levels — Early Years, O Level, A Level and University.</p>
    <a href="admissions.php" class="btn-dark">Start Your Application</a>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>