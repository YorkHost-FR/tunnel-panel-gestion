<?php
session_start();
if (isset($_SESSION['client_id'])) {
    header("Location: dashboard.php");
    exit;
}
$pageTitle = "Connexion - YORKHOST Panel";
include 'header.php';
?>

<style>
    /* Arrière-plan avec un dégradé */
    body {
        background: linear-gradient(135deg, #1a5e63, #00bfb2);
        font-family: 'Arial', sans-serif;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.1); /* Légère transparence */
        padding: 2rem;
        border-radius: 15px;
        backdrop-filter: blur(10px); /* Effet de flou */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    .login-card h3 {
        color: #ffffff;
        font-size: 1.8rem;
        font-weight: bold;
    }

    .login-card p {
        color: #d1d1d1;
        font-size: 1rem;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: #fff;
        font-size: 1rem;
    }

    .form-control::placeholder {
        color: #ccc;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.3);
        color: #fff;
        border: none;
        outline: none;
    }

    .input-group-text {
        background: transparent;
        border: none;
        color: #fff;
    }

    .btn-primary {
        background: #028090;
        border: none;
        padding: 0.75rem;
        font-size: 1.2rem;
        border-radius: 50px;
        transition: background 0.3s ease-in-out;
    }

    .btn-primary:hover {
        background: #026a74;
    }

    .text-light a {
        color: #d1d1d1;
        text-decoration: none;
        transition: color 0.3s;
    }

    .text-light a:hover {
        color: #ffffff;
    }
</style>

<div class=" d-flex align-items-center justify-content-center min-vh-100">
    <div class="login-card">
        <h3 class="mb-3">Gestion de votre tunnel</h3>
        <p>Connectez-vous avec les identifiants de votre compte client.</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="login_action.php" method="post">
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="email" name="email" id="email" class="form-control form-control-lg rounded-pill" placeholder="Email" required>
            </div>
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control form-control-lg rounded-pill" placeholder="Mot de passe" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill">Se connecter</button>
            </div>
        </form>

        <div class="mt-3">
            <a href="https://clients.yorkhost.fr/index.php/root&action=passreminder" class="text-light">Mot de passe oublié ?</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
