<?php
if (isset($_SESSION['error'])) {
    $errorMessage = $_SESSION['error']['message'];
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Toastify({
                text: "<?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>",
                duration: 8000,
                close: true,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                style: {
                    background: "#f44336",
                    color: "#ffffff",
                    fontSize: "16px",
                    padding: "16px 24px",
                    borderRadius: "8px",
                    boxShadow: "0 4px 12px rgba(0,0,0,0.15)"
                }
            }).showToast();
        });
    </script>
<?php
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success']['message'];
?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Toastify({
                text: "<?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>",
                duration: 8000,
                close: true,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                style: {
                    background: "#4CAF50",
                    color: "#ffffff",
                    fontSize: "16px",
                    padding: "16px 24px",
                    borderRadius: "8px",
                    boxShadow: "0 4px 12px rgba(0,0,0,0.15)"
                }
            }).showToast();
        });
    </script>
<?php
    unset($_SESSION['success']);
}
?>
