<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Contact Us';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $error = 'Name, email and message are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = db()->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $success = "Thank you, {$name}! Your message has been sent. We will reply as soon as possible.";
        } catch(PDOException $e) {
            $error = 'Could not send your message. Please try again.';
        }
    }
}
?>
<?php include __DIR__ . '/header.php'; ?>


<div class="page-hero">
  <div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a> / Contact</div>
    <h1>Get In Touch</h1>
    <p>We'd love to hear from you — questions, visits or general enquiries.</p>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="contact-grid">

      <!-- INFO CARD -->
      <div class="contact-info-card">
        <h3>Contact Information</h3>

        <div class="contact-item">
          <div class="contact-item-icon">📍</div>
          <div>
            <strong>Address</strong>
            <p><?= SITE_ADDRESS ?></p>
          </div>
        </div>

        <div class="contact-item">
          <div class="contact-item-icon">📞</div>
          <div>
            <strong>Phone</strong>
            <p><?= SITE_PHONE ?></p>
            <p>Mon – Fri, 7:30 AM – 5:00 PM</p>
          </div>
        </div>

        <div class="contact-item">
          <div class="contact-item-icon">✉️</div>
          <div>
            <strong>Email</strong>
            <p><?= SITE_EMAIL ?></p>
          </div>
        </div>

        <div class="contact-item">
          <div class="contact-item-icon">🕐</div>
          <div>
            <strong>Office Hours</strong>
            <p>Mon – Fri: 7:30 AM – 5:30 PM</p>
            <p>Saturday: 9:00 AM – 1:00 PM</p>
          </div>
        </div>

        <div style="margin-top:2rem;padding-top:2rem;border-top:1px solid rgba(255,255,255,.15)">
          <p style="color:rgba(255,255,255,.7);font-size:.88rem">Follow us on social media for the latest news and events from the Greenfield community.</p>
          <div style="display:flex;gap:.75rem;margin-top:1rem"> 
            <a href="https://www.instagram.com/iggypingythingy/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://www.youtube.com/@therophinefieldgroupofschools" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
            <a href="https://www.tiktok.com/@iggypingythingy?lang=en" target="_blank" rel="noopener noreferrer" aria-label="Tiktok"><i class="fa-brands fa-tiktok"></i></a>
            <style> a:hover { color: var(--gold-light);transform: scale(1.1);transition: .2s } </style>
          </div>
        </div>
      </div>

      <!-- CONTACT FORM -->
      <div class="form-card" style="max-width:100%;margin:0">
        <h3 style="color:var(--green-dark);margin-bottom:.25rem">Send Us a Message</h3>
        <p style="margin-bottom:1.75rem">Fill in the form and we'll get back to you within 24 hours.</p>

        <?php if($success): ?><div class="alert alert-success"><?= sanitize($success) ?></div><?php endif; ?>
        <?php if($error):   ?><div class="alert alert-error"><?= sanitize($error) ?></div><?php endif; ?>

        <form method="POST" action="">
          <div class="form-row">
            <div class="form-group">
              <label for="name">Your Name *</label>
              <input type="text" id="name" name="name" placeholder="Full name" required
                     value="<?= sanitize($_POST['name'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" placeholder="your@email.com" required
                     value="<?= sanitize($_POST['email'] ?? '') ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" placeholder="What is this regarding?"
                   value="<?= sanitize($_POST['subject'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="message">Message *</label>
            <textarea id="message" name="message" rows="6" placeholder="Write your message here..." required><?= sanitize($_POST['message'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn-submit">Send Message</button>
        </form>
      </div>

    </div>
  </div>
</section>

<!-- MAP PLACEHOLDER -->
<div style="width:100%;height:380px;border-top:1px solid #e8e8e8;position:relative;overflow:hidden;">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4319.821776495832!2d36.94692998369194!3d-1.2642037092286584!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f1522a28052cd%3A0x5ef23d32c864b5d9!2sRophine%20Field%20International%20School!5e0!3m2!1sen!2ske!4v1781688116126!5m2!1sen!2ske" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  <!-- Replace label -->
  <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(13,43,31,.82);color:#fff;padding:.6rem 1.25rem;font-size:.8rem;display:flex;align-items:center;gap:.5rem;backdrop-filter:blur(4px);">
    <span>📍</span>
    <span><?= SITE_ADDRESS ?> — <em style="opacity:.75">All are welcome to our prestigious school!</em></span>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>