<?php // includes/footer.php ?>

<footer class="site-footer">
  <div class="footer-inner">

    <div class="footer-brand">
      <div class="footer-logo">
        <div class="logo-mark sm">G</div>
        <span><?= SITE_NAME ?></span>
      </div>
      <p><?= SITE_TAGLINE ?></p>
      <div class="footer-social">
        <a href="https://www.instagram.com/iggypingythingy/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="https://www.youtube.com/@therophinefieldgroupofschools" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
        <a href="https://www.tiktok.com/@iggypingythingy?lang=en" target="_blank" rel="noopener noreferrer" aria-label="Tiktok"><i class="fa-brands fa-tiktok"></i></a>
      </div>
    </div>

    <div class="footer-links">
      <h4>Quick Links</h4>
      <a href="index.php">Home</a>
      <a href="about.php">About Us</a>
      <a href="portfolio.php">Our Director</a>
      <a href="academics.php">Academics</a>
      <a href="admissions.php">Admissions</a>
      <a href="contact.php">Contact</a>
    </div>

    <div class="footer-links">
      <h4>Levels</h4>
      <a href="academics.php?level=early_years">Early Years</a>
      <a href="academics.php?level=o_level">O Level</a>
      <a href="academics.php?level=a_level">A Level</a>
      <a href="academics.php?level=university">University</a>
    </div>

    <div class="footer-contact">
      <h4>Contact Us</h4>
      <p>📍 <?= SITE_ADDRESS ?></p>
      <p>📞 <a href="tel:<?= str_replace([' ', '(', ')', '-'], '', SITE_PHONE) ?>"><?= SITE_PHONE ?></a></p>
      <p>✉️ <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></p>
    </div>

  </div>
  <div class="footer-bottom">
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
  </div>
</footer>

<script src="main.js"></script>
</body>
</html>