<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'About the Director';
?>
<?php include __DIR__ . '/header.php'; ?>

<section class="hero">
  <div class="hero-content">
    <div class="hero-badge">Director Profile</div>
    <h1>Dr. Florence Waeni<br><span>Leading Greenfield Academy with vision and care</span></h1>
    <p>Dr. Waeni brings over 30 years of educational leadership, curriculum innovation, and student-centered excellence to our school.</p>
    <div class="hero-cta">
      <a href="#overview" class="btn-primary">Discover Her Vision</a>
      <a href="#values" class="btn-secondary">Our School Values</a>
    </div>
    <div class="hero-stats">
      <div class="hero-stat"><strong>30+</strong><span>Years in Education</span></div>
      <div class="hero-stat"><strong>4</strong><span>Academic Levels</span></div>
      <div class="hero-stat"><strong>1</strong><span>Community Mission</span></div>
      <div class="hero-stat"><strong>100%</strong><span>Student Growth Focus</span></div>
    </div>
  </div>
</section>

<section id="overview" class="section section-alt">
  <div class="container">
    <div class="section-head center">
      <span class="section-label">Director</span>
      <h2 class="section-title">About Dr. Florence Waeni</h2>
      <div class="divider"></div>
      <p class="section-sub">A visionary leader with deep experience in curriculum design, school culture, and student success.</p>
    </div>

    <div class="about-grid">
      <div class="profile-card">
        <h3>Leadership Journey</h3>
        <p>Dr. Waeni started her career as a classroom teacher and went on to become a respected principal, curriculum advisor, and educational consultant. Her work blends academic excellence with strong pastoral care, ensuring learners feel supported while they stretch their ambitions.</p>

        <div class="preference-grid">
          <div class="preference-card">
            <h4>Education & Experience</h4>
            <p>PhD in Education from the University of Nairobi, 30+ years leading schools, and a background in international curriculum development.</p>
          </div>
          <div class="preference-card">
            <h4>Her Focus</h4>
            <p>Student wellbeing, teacher development, community partnerships, and learning environments that inspire curiosity.</p>
          </div>
          <div class="preference-card">
            <h4>Personal Mission</h4>
            <p>To nurture confident learners who are equipped to succeed academically, creatively, and ethically in a changing world.</p>
          </div>
        </div>
      </div>

      <div class="about-visual portfolio-visual">
        <!-- <img src="Director.png" alt="Dr. Grace Mwangi, Director of Greenfield Academy"> -->
      </div>
    </div>
  </div>
</section>

<section id="values" class="section">
  <div class="container">
    <div class="section-head center">
      <span class="section-label">Vision</span>
      <h2 class="section-title">Our School Values</h2>
      <div class="divider"></div>
      <p class="section-sub">The values that guide every classroom, every teacher, and every student experience at Greenfield Academy.</p>
    </div>

    <div class="timeline-grid">
      <div class="timeline-card">
        <h4>Excellence</h4>
        <p>We set high standards and support every learner to meet them with confidence, curiosity, and care.</p>
      </div>
      <div class="timeline-card">
        <h4>Community</h4>
        <p>Partnership with families, teachers, and the wider community is central to our school’s success.</p>
      </div>
      <div class="timeline-card">
        <h4>Innovation</h4>
        <p>We encourage creative thinking, modern learning practices, and a future-ready approach to education.</p>
      </div>
      <div class="timeline-card">
        <h4>Integrity</h4>
        <p>Respect, honesty, and empathy are woven into every decision and every day at our school.</p>
      </div>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <div class="section-head center">
      <span class="section-label">Achievements</span>
      <h2 class="section-title">What We’ve Built Together</h2>
      <div class="divider"></div>
      <p class="section-sub">Under Dr. Waeni’s leadership, the school has grown into a trusted learning community across early years, O Level, A Level, and university preparation.</p>
    </div>

    <div class="project-grid">
      <article class="project-card">
        <h3>Strong Academic Results</h3>
        <p>Improved exam performance and personalised support have helped learners reach higher standards across every level.</p>
      </article>
      <article class="project-card">
        <h3>Enriched School Life</h3>
        <p>Extra-curricular activities, wellbeing programs, and values-led events are part of everyday life at Greenfield.</p>
      </article>
      <article class="project-card">
        <h3>Teacher Development</h3>
        <p>Regular professional growth, mentoring, and collaborative planning ensure teachers are empowered and effective.</p>
      </article>
    </div>
  </div>
</section>

<section class="section section-dark">
  <div class="container">
    <div class="section-head center">
      <span class="section-label">Connect</span>
      <h2 class="section-title">Meet the Director in Person</h2>
      <div class="divider"></div>
      <p class="section-sub">If you would like to learn more about our leadership approach or schedule a school visit, we’re happy to welcome you.</p>
    </div>
    <div style="text-align:center; margin-top: 2rem;">
      <a href="contact.php" class="btn-dark">Contact the School</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
