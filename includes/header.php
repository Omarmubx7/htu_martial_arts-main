<?php
// Keep bootstrapping in one place.
// This ensures session + DB connection + helpers are ready before any HTML output.
require_once __DIR__ . '/init.php';
?>
<!-- includes/header.php -->
<!-- This is the shared header template used by all pages -->
<!-- It includes the HTML head section, meta tags, and navigation navbar -->
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Character encoding for the webpage -->
  <meta charset="UTF-8">
  <!-- Viewport meta tag ensures proper scaling on mobile devices -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Page title - shows in browser tab and search results -->
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . " - HTU Martial Arts" : "HTU Martial Arts"; ?></title>
  
  <!-- SEO: Added meta description for better indexing -->
  <meta name="description" content="HTU Martial Arts offers expert-led Jiu-jitsu, Karate, Muay Thai, and Self-Defence training with flexible schedules, modern facilities, and tailored memberships.">
  <!-- SEO: Added keywords to help search engines understand page content -->
  <meta name="keywords" content="martial arts, jiu-jitsu, karate, muay thai, self-defence, fitness, gym, classes, training, HTU Martial Arts">
  <!-- Open Graph tags for social sharing -->
  <!-- SEO: OG title for rich link previews -->
  <meta property="og:title" content="HTU Martial Arts">
  <!-- SEO: OG description aligned with brand messaging -->
  <meta property="og:description" content="Train with elite instructors across Jiu-jitsu, Karate, and Muay Thai. Flexible schedules and membership plans for every goal.">
  <!-- SEO: OG image for social cards -->
  <meta property="og:image" content="images/logo-social.png">
  <!-- SEO: OG type to declare site nature -->
  <meta property="og:type" content="website">
  <!-- SEO: OG URL with sanitized current URL -->
  <meta property="og:url" content="<?php echo htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
  
  <!-- SEO: Canonical link to prevent duplicate content -->
  <link rel="canonical" href="<?php echo htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
  
  <!-- Existing SEO meta description retained for compatibility -->
  <meta name="robots" content="index, follow">
  
  <!-- Twitter card tags for Twitter sharing -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="HTU Martial Arts">
  <meta name="twitter:description" content="Train with elite instructors across Jiu-jitsu, Karate, and Muay Thai. Flexible schedules and membership plans for every goal.">
  <meta name="twitter:image" content="images/logo-social.png">
  
  <!-- Canonical URL prevents duplicate content issues in search engines -->
  <meta property="og:url" content="<?php echo htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
  
  <!-- Theme color for browser address bar (mobile) -->
  <meta name="theme-color" content="#e3342f">

  <!-- Favicon - the icon shown in browser tab -->
  <link rel="icon" type="image/png" href="images/favicon.png">
  <link rel="shortcut icon" type="image/png" href="images/favicon.png">
  <link rel="apple-touch-icon" href="images/logo-touch.png">
  
  <!-- Fonts: Teko (headings), Exo 2 (body), Roboto Mono (numbers) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Teko:wght@600&family=Exo+2:wght@400;600&family=Roboto+Mono:wght@400;600&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS framework for responsive layout and components -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons - provides icon library for UI elements -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  
  <!-- Custom CSS for sport theme styling - add time parameter to bust cache -->
  <link href="css/sport-theme.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>


<!-- Navigation bar - fixed at top of page with semi-transparent background -->
<!-- Uses CSS backdrop-filter for frosted glass effect -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-glass">
  <div class="container">
    <!-- Logo and brand text -->
    <a class="navbar-brand d-flex align-items-center" href="index.php" aria-label="HTU Martial Arts" style="font-weight: 700; font-size: 1.5rem; text-transform: uppercase;">
      <!-- Logo image with inverted colors (white on dark background) -->
      <img src="images/logo-desktop.svg" alt="HTU Martial Arts Logo" style="height: 45px; margin-right: 12px;" class="navbar-logo">
      <!-- Brand text with text shadow for readability -->
      <span class="navbar-brand-text">HTU MARTIAL ARTS</span>
    </a>
    
    <!-- Mobile menu toggle button - appears on small screens -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: rgba(213, 6, 6, 0.78);">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Navigation links - collapse on mobile, expand on larger screens -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <!-- Classes link -->
        <li class="nav-item"><a class="nav-link" href="classes_premium.php">Classes</a></li>
        <!-- Memberships/Prices link -->
        <li class="nav-item"><a class="nav-link" href="prices.php">Memberships</a></li>
        
        <!-- If user is logged in, show Account and Logout links -->
        <?php if(isset($_SESSION['user_id'])): ?>
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link fw-bold" href="admin.php">Admin Dashboard</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Account</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <!-- If user is NOT logged in, show Login and Join Now links -->
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <!-- Join Now button with special styling - handled by CSS class -->
          <li class="nav-item"><a class="nav-link join-btn" href="prices.php">Join Now</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- JavaScript for navbar scroll effect - changes colors when user scrolls down -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navbar scroll effect is now handled by ultimate-interactions.js
    // This inline script can be removed or kept for compatibility
});
</script>
