<?php
// admin_edit_product.php - Just a redirect to admin_add_product.php with id parameter
if (isset($_GET['id'])) {
    header('Location: admin_add_product.php?id=' . intval($_GET['id']));
} else {
    header('Location: admin_dashboard.php');
}
exit;
?>
