</div><!-- End of admin-content -->
        </div><!-- End of admin-main -->
    </div><!-- End of admin-wrapper -->
    
    <!-- JavaScript -->
    <script src="/comtech/assets/js/admin.js"></script>
    
    <?php
    // Page-specific JavaScript
    $current_page = basename($_SERVER['PHP_SELF']);
    switch ($current_page) {
        case 'dashboard.php':
            echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
            break;
        case 'orders.php':
            // Any order-specific JS
            break;
        case 'products.php':
            // Any product-specific JS
            break;
    }
    ?>
</body>
</html>