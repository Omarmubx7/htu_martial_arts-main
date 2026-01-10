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

<?php
// ============================================================
// BOOTSTRAP COMPONENT (SITE-WIDE): TOAST NOTIFICATIONS
// Any page can call addFlashToast('Message', 'success|danger|warning|info')
// ============================================================
$__toasts = function_exists('popFlashToasts') ? popFlashToasts() : [];
if (!empty($__toasts)):
?>
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2100;">
        <?php foreach ($__toasts as $__toast):
            $type = isset($__toast['type']) ? (string)$__toast['type'] : 'info';
            $msg  = isset($__toast['message']) ? (string)$__toast['message'] : '';
            $bgClass = 'text-bg-' . htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
        ?>
            <div class="toast align-items-center <?php echo $bgClass; ?> border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4500">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Bootstrap JavaScript bundle - required for Bootstrap components (modals, dropdowns, etc) -->
<!-- Version 5.3.0 includes Popper.js for tooltips/popovers -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Auto-show any toast markup we rendered above.
document.addEventListener('DOMContentLoaded', function () {
    if (!window.bootstrap) return;
    document.querySelectorAll('.toast').forEach(function (el) {
        try {
            var toast = bootstrap.Toast.getOrCreateInstance(el);
            toast.show();
        } catch (e) {
            // Ignore; toast is optional.
        }
    });
});
</script>

<!-- Custom JavaScript file for interactive features -->
<!-- Contains functions for smooth scrolling, animations, and other interactions -->
<script src="js/ultimate-interactions.js"></script>

<!-- Close the HTML body and document tags -->
</body>
</html>
