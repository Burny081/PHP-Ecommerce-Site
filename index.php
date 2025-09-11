<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our E-commerce Site</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary-color: #ff5722; /* A vibrant orange-red for accents */
            --secondary-color: #333; /* Dark color for text and headings */
            --accent-color: #ff9800; /* A slightly warmer orange for highlights */
            --light-color: #f8f9fa; /* Off-white for background */
            --dark-color: #212529; /* Very dark color for contrast */
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            color: var(--secondary-color);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            line-height: 1.6;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            height: 70px;
        }

        .nav-logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-color);
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .nav-logo:hover {
            transform: scale(1.05);
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }

        .nav-link {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 0;
        }

        .hero-section {
            width: 100%;
            height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: url('https://images.unsplash.com/photo-1522204523234-8729aa6e993f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQwMjJ8MHwxfHNlYXJjaHwxNXx8c2hvcHBpbmclMjB3ZWJzaXRlfGVufDB8fHx8MTY5OTQ1MDY3MHww&ixlib=rb-4.0.3&q=80&w=1080') no-repeat center center/cover;
            color: white;
            position: relative;
            padding: 0 2rem;
            animation: fadeIn 2s ease-in-out;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .hero-content {
            z-index: 2;
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            margin-bottom: 1rem;
            font-weight: 800;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.2);
            animation-delay: 0.5s;
        }

        .hero-content p {
            font-size: clamp(1rem, 2.5vw, 1.5rem);
            max-width: 600px;
            margin-bottom: 2rem;
            font-weight: 400;
            animation-delay: 1s;
        }

        .cta-button {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation-delay: 1.5s;
        }

        .cta-button:hover {
            background-color: #e64a19;
            transform: translateY(-5px);
        }

        .section-header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .section-header h2 {
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            position: relative;
            display: inline-block;
        }

        .section-header p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            max-width: 700px;
            margin: 0 auto;
            color: var(--secondary-color);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            padding: 0 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            min-height: 300px; /* Ensure consistent card height */
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .product-image-container {
            width: 100%;
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .product-card-content {
            padding: 1.5rem;
        }

        .product-card h3 {
            font-size: 1.5rem;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .product-card a {
            text-decoration: none;
            color: var(--primary-color);
            font-weight: bold;
            margin-top: 1rem;
            display: inline-block;
            transition: color 0.3s ease;
        }
        
        .product-card a:hover {
            color: #e64a19;
        }

        .footer {
            background-color: var(--dark-color);
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }
        
        .footer p {
            margin: 0;
            font-size: 0.9rem;
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
            <div class="hero-content">
                <h1 class="animate__animated animate__fadeInDown">Welcome to Our Shop!</h1>
                <p class="animate__animated animate__fadeInUp">Discover amazing deals and a seamless shopping experience. Your satisfaction is our priority.</p>
                <a href="products.php" class="cta-button animate__animated animate__zoomIn">Shop Now</a>
            </div>
        </section>

        <section class="sales-section">
            <div class="section-header">
                <h2>Explore Our Top Categories</h2>
                <p>Find what you're looking for with our curated selection of top-selling products.</p>
            </div>
            <div class="product-grid">
                <div class="product-card animate__animated animate__fadeInUp">
                    <div class="product-image-container" style="background-image: url('https://images.unsplash.com/photo-1594938361719-eb66487e47f7?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQwMjJ8MHwxfHNlYXJjaHwxOHx8ZmFzaGlvbnxlbnwwfHx8fDE2OTk0NTEyODd8MA&ixlib=rb-4.0.3&q=80&w=1080');"></div>
                    <div class="product-card-content">
                        <h3>Trendy Apparel</h3>
                        <a href="products.php">Shop Now <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="product-card animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="product-image-container" style="background-image: url('https://images.unsplash.com/photo-1542496658-e1451e06f521?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQwMjJ8MHwxfHNlYXJjaHwxNHx8ZWxlY3Ryb25pY3N8ZW58MHx8fHwxNjk5NDUxMjg3fDA&ixlib=rb-4.0.3&q=80&w=1080');"></div>
                    <div class="product-card-content">
                        <h3>Latest Electronics</h3>
                        <a href="products.php">Shop Now <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="product-card animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="product-image-container" style="background-image: url('https://images.unsplash.com/photo-1616046221708-3079a49257d0?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w0NTQwMjJ8MHwxfHNlYXJjaHw3fHxob21lJTIwZGVjb3J8ZW58MHx8fHwxNjk5NDUxMjkyfDA&ixlib=rb-4.0.3&q=80&w=1080');"></div>
                    <div class="product-card-content">
                        <h3>Home & Decor</h3>
                        <a href="products.php">Shop Now <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <section class="sales-section">
            <div class="section-header">
                <h2>Why Choose Us?</h2>
                <p>We are committed to providing the best shopping experience for our customers.</p>
            </div>
            <div class="product-grid">
                <div class="product-card animate__animated animate__fadeInUp animate__delay-3s">
                    <i class="fas fa-shipping-fast" style="font-size: 3rem; color: var(--accent-color);"></i>
                    <div class="product-card-content">
                        <h3>Fast Shipping</h3>
                        <p>Get your orders delivered to your doorstep in no time with our express shipping.</p>
                    </div>
                </div>
                <div class="product-card animate__animated animate__fadeInUp animate__delay-4s">
                    <i class="fas fa-headset" style="font-size: 3rem; color: var(--accent-color);"></i>
                    <div class="product-card-content">
                        <h3>24/7 Support</h3>
                        <p>Our dedicated support team is always here to assist you with any questions.</p>
                    </div>
                </div>
                <div class="product-card animate__animated animate__fadeInUp animate__delay-5s">
                    <i class="fas fa-shield-alt" style="font-size: 3rem; color: var(--accent-color);"></i>
                    <div class="product-card-content">
                        <h3>Secure Payments</h3>
                        <p>Shop with confidence using our secure and trusted payment methods.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> OurShop. All Rights Reserved, Bongni Tambe Nestor.</p>
    </footer>
</body>
</html>