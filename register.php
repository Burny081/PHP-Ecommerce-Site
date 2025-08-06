
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer un compte</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d1b2a 0%, #1b263b 100%);
            color: #e0e6ed;
            min-height: 100vh;
        }
        .card {
            background: #1b263b;
            border: none;
            box-shadow: 0 8px 32px #0004;
            color: #e0e6ed;
        }
        .form-label, .form-control, .form-control:focus {
            color: #e0e6ed;
            background: #0d1b2a;
            border-color: #274690;
        }
        .btn-primary {
            background: #274690;
            border: none;
        }
        .btn-primary:hover {
            background: #1b98e0;
        }
        .fade-in {
            animation: fadeIn 1.2s cubic-bezier(.4,2,.6,1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: none; }
        }
    </style>
</head>
<body>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card p-4 fade-in" style="max-width: 400px; width:100%;">
        <?php
        $conn = new mysqli("localhost", "root", "Nestor667", "ecommerce");
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $email    = $_POST['email'];
            $password = $_POST['password'];
            $role     = 'user';
            // Check for duplicate username or email
            $check = $conn->query("SELECT id FROM users WHERE username = '$username' OR email = '$email' LIMIT 1");
            if ($check && $check->num_rows > 0) {
                echo '<div class="alert alert-danger">Ce nom d\'utilisateur ou cet email existe déjà.</div>';
            } else {
                $sql = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
                if ($conn->query($sql) === TRUE) {
                    header('Location: login.php');
                    exit;
                } else {
                    echo '<div class="alert alert-danger">Erreur : ' . $conn->error . '</div>';
                }
            }
        }
        ?>
        <form method="post">
            <h2 class="mb-4 text-center">Créer un compte</h2>
            <div class="mb-3">
                <label class="form-label">Nom d'utilisateur</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
        </form>
    </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
