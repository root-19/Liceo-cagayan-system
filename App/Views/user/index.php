<?php
require_once "../../Controller/Middleware.php";
Middleware::auth('user');
?>

<?php include_once "./layout/sidebar.php";?>

<div class="p-6 max-w-3xl mx-auto text-center">
    <h1 class="text-3xl font-bold mt-40" id="welcome-text">Welcome to Your Liceo de Cagayan University account!</h1>
    <p class="text-gray-700 mb-6">Hello, <strong id="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</p>
</div>

<!-- Confetti Script -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
    // Function to trigger confetti animation
    window.onload = function() {
        var duration = 5 * 1000;
        var end = Date.now() + duration;
        var defaults = {
            origin: { y: 0.7 }
        };
        
        // Get the position of the name (including first name and surname)
        const nameElement = document.getElementById("user-name");
        const nameRect = nameElement.getBoundingClientRect();
        const nameCenterX = nameRect.left + nameRect.width / 2;
        const nameCenterY = nameRect.top + nameRect.height / 2;

        // Create confetti around the name and surname
        (function frame() {
            confetti(Object.assign({}, defaults, {
                particleCount: 5,
                spread: 20,
                angle: Math.random() * 90 + 90,
                decay: 0.9,
                scalar: Math.random() + 0.5,
                origin: { x: nameCenterX / window.innerWidth, y: nameCenterY / window.innerHeight }
            }));

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        })();
    }
</script>
</body>
</html>
