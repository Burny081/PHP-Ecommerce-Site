<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our E-commerce Site</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            height: 60px;
        }

        .nav-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1877f2;
            text-decoration: none;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 1.5rem;
            margin: 0;
            padding: 0;
        }

        .nav-link {
            color: #606770;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: #1877f2;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 4rem 2rem;
            text-align: center;
        }

        .hero-section {
            max-width: 800px;
        }

        .hero-section h1 {
            font-size: 3rem;
            color: #1c1e21;
            margin-bottom: 1rem;
        }

        .hero-section p {
            font-size: 1.2rem;
            color: #606770;
            margin-bottom: 2rem;
        }

        .cta-button {
            background-color: #1877f2;
            color: white;
            padding: 14px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.2s;
            display: inline-block;
        }

        .cta-button:hover {
            background-color: #166fe5;
            transform: translateY(-2px);
        }

        .features-section {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 4rem;
            flex-wrap: wrap;
        }

        .feature-item {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            width: 250px;
            text-align: center;
        }

        .feature-item i {
            font-size: 2.5rem;
            color: #1877f2;
            margin-bottom: 1rem;
        }

        .feature-item h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .feature-item p {
            font-size: 0.95rem;
            color: #606770;
        }

        .footer {
            background-color: #fff;
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
            color: #606770;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <a href="index.php" class="nav-logo">OurShop</a>
                <ul class="nav-menu">
                    <li><a href="products.php" class="nav-link"><i class="fas fa-box-open"></i> Products</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="register.php" class="nav-link"><i class="fas fa-user-plus"></i> Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <section class="hero-section">
            <h1>Welcome to Our Shop!</h1>
            <p>Discover amazing products and enjoy a seamless shopping experience. Your satisfaction is our priority.</p>
            <a href="products.php" class="cta-button">Shop Now</a>
        </section>

        <section class="features-section">
            <div class="feature-item">
                <i class="fas fa-shipping-fast"></i>
                <h3>Fast Shipping</h3>
                <p>Get your orders delivered to your doorstep in no time.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                <h3>24/7 Support</h3>
                <p>Our support team is always here to help you with any questions.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure Payments</h3>
                <p>Shop with confidence using our secure payment methods.</p>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> OurShop. All Rights Reserved.</p>
    </footer>
</body>
</html>