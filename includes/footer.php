<!-- includes/footer.php -->
<!-- This is the shared footer template used by all pages -->
<!-- It contains the footer content and loads necessary JavaScript libraries -->

<!-- Footer section at the bottom of the page -->
<footer class="site-footer text-center py-5 mt-5">
    <div class="container">
        <!-- Footer logo -->
        <div class="footer-logo mb-3">
            <img src="images/logo-footer.svg" alt="HTU Martial Arts" class="logo-footer">
        </div>
        <!-- Copyright notice - displays current year dynamically -->
        <p class="mb-0">&copy; <?php echo date('Y'); ?> HTU Martial Arts All Rights Reserved</p>
    </div>
</footer>

<!-- Bootstrap JavaScript bundle - required for Bootstrap components (modals, dropdowns, etc) -->
<!-- Version 5.3.0 includes Popper.js for tooltips/popovers -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript file for interactive features -->
<!-- Contains functions for smooth scrolling, animations, and other interactions -->
<script src="js/ultimate-interactions.js"></script>

<!-- Close the HTML body and document tags -->
</body>
</html>
