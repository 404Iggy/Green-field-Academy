<?php
// header.php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= sanitize($pageTitle ?? SITE_NAME) ?> — <?= SITE_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="main.css">
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
  <div class="topbar-inner">
    <span>📍 <?= SITE_ADDRESS ?></span>
    <span>📞 <?= SITE_PHONE ?></span>
    <span>✉️ <?= SITE_EMAIL ?></span>
  </div>
</div>

<!-- MAIN NAVIGATION -->
<header class="site-header">
  <div class="header-inner">

    <!-- Logo -->
    <a href="index.php" class="logo">
      <div class="logo-mark"><img src="logo.jpg" alt="Logo"></div>
      <div class="logo-text">
        <span class="logo-name"><?= SITE_NAME ?></span>
        <span class="logo-sub">Early Years · O Level · A Level · University</span>
      </div>
    </a>

    <!-- Nav links -->
    <nav class="main-nav">
      <a href="index.php"          class="<?= $currentPage==='index'      ? 'active':'' ?>">Home</a>
      <a href="about.php"          class="<?= $currentPage==='about'      ? 'active':'' ?>">About</a>
      <a href="portfolio.php"      class="<?= $currentPage==='portfolio'  ? 'active':'' ?>">Our director</a>
      <a href="academics.php"      class="<?= $currentPage==='academics'  ? 'active':'' ?>">Academics</a>
      <a href="admissions.php"     class="<?= $currentPage==='admissions' ? 'active':'' ?>">Admissions</a>
      <a href="contact.php"        class="<?= $currentPage==='contact'    ? 'active':'' ?>">Contact</a>
      <?php if(isLoggedIn()): ?>
        <?php $portalLabel = $user['role'] === 'student' ? 'Student Portal' : 'Teacher Portal'; ?>
        <a href="portal.php"       class="<?= $currentPage==='portal'     ? 'active':'' ?>"><?= sanitize($portalLabel) ?></a>
        <a href="logout.php" class="btn-nav-outline">Log Out</a>
      <?php else: ?>
        <a href="login.php" class="btn-nav">Portal Login</a>
      <?php endif; ?>
    </nav>

    <!-- Mobile toggle -->
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>

  </div>
</header>