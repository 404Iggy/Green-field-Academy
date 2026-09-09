<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'About Us';
?>
<?php include __DIR__ . '/header.php'; ?>

<!-- PAGE HERO -->
<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a> / About Us</div>
    <h1>Our Story &amp; Mission</h1>
    <p>Three decades of shaping curious minds and confident leaders.</p>
  </div>
</div>

<!-- MISSION & VISION -->
<section class="section section-alt">
  <div class="container">
    <div class="about-grid">
      <div class="about-visual"><img src="Gemini_Generated_Image_.png" alt="About Greenfield Academy"></div>
      <div>
        <span class="section-label">Who We Are</span>
        <h2 class="section-title">Greenfield Academy</h2>
        <div class="divider"></div>
        <p>Founded in 1994, Greenfield Academy began as a small primary school in Westlands, Nairobi. Over three decades we have grown into one of Kenya's most respected multi-level institutions, offering a seamless education from Early Years right through to University degree programmes.</p>
        <p class="mt-2">Our teaching philosophy centres on high expectations, genuine care for every learner and a belief that academic success and personal character development go hand in hand.</p>
      </div>
    </div>
  </div>
</section>

<!-- MISSION / VISION CARDS -->
<section class="section">
  <div class="container">
    <div class="levels-grid" style="grid-template-columns: repeat(auto-fit,minmax(280px,1fr))">
      <div class="level-card">
        <div class="icon">🎯</div>
        <h3>Our Mission</h3>
        <p>To provide an inclusive, high-quality education that empowers every student to realise their full potential — academically, socially and ethically.</p>
      </div>
      <div class="level-card">
        <div class="icon">🌟</div>
        <h3>Our Vision</h3>
        <p>To be Africa's most transformative learning community, producing graduates who lead with knowledge, integrity and compassion.</p>
      </div>
      <div class="level-card">
        <div class="icon">💚</div>
        <h3>Our Values</h3>
        <p>Excellence · Integrity · Curiosity · Respect · Community. These five pillars guide everything we do, from the classroom to the sports field.</p>
      </div>
    </div>
  </div>
</section>

<!-- LEADERSHIP -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center">
      <span class="section-label">School Leadership</span>
      <h2 class="section-title">Meet Our Leadership Team</h2>
      <div class="divider"></div>
    </div>
    <div class="levels-grid">
      <?php
      $leaders = [
        ['name'=>'Dr. Florence Waeni',      'role'=>'Director & Founder',         'icon'=>'👩‍🏫', 'bio'=>'PhD Education, University of Nairobi. 30+ years in educational leadership.'],
        ['name'=>'Mr. Samuel Kiprotich',  'role'=>'Deputy Principal – Academics', 'icon'=>'👨‍💼', 'bio'=>'MSc Mathematics, former Cambridge examiner and curriculum specialist.'],
        ['name'=>'Ms. Amina Odhiambo',    'role'=>'Dean of Students',             'icon'=>'👩‍💼', 'bio'=>'MA Counselling Psychology. Champion of student welfare and inclusion.'],
        ['name'=>'Prof. John Njoroge',    'role'=>'Dean – University Faculty',    'icon'=>'👨‍🎓', 'bio'=>'PhD Computer Science. Published researcher and technology entrepreneur.'],
      ];
      foreach($leaders as $l): ?>
      <div class="level-card">
        <div class="icon"><?= $l['icon'] ?></div>
        <h3><?= $l['name'] ?></h3>
        <p style="color:var(--green-mid);font-weight:600;font-size:.85rem;margin-bottom:.5rem"><?= $l['role'] ?></p>
        <p><?= $l['bio'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- QUICK FACTS -->
<section class="section section-dark">
  <div class="container">
    <div class="section-head center">
      <span class="section-label">By the Numbers</span>
      <h2 class="section-title" style="color:#fff">Greenfield in Figures</h2>
      <div class="divider"></div>
    </div>
    <div class="hero-stats">
      <div class="hero-stat"><strong>1994</strong><span>Year Founded</span></div>
      <div class="hero-stat"><strong>1,800+</strong><span>Active Students</span></div>
      <div class="hero-stat"><strong>180</strong><span>Teaching Staff</span></div>
      <div class="hero-stat"><strong>97%</strong><span>Average Pass Rate</span></div>
      <div class="hero-stat"><strong>12,000+</strong><span>Alumni Worldwide</span></div>
    </div>
  </div>
</section>

<div class="cta-band">
  <div class="container">
    <h2>Come Visit Our Campus</h2>
    <p>Book an open-day tour and see Greenfield Academy for yourself.</p>
    <a href="contact.php" class="btn-dark">Get In Touch</a>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>