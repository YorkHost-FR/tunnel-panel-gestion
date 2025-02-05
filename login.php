<?php
// login.php
session_start();
if (isset($_SESSION['client_id'])) {
    header("Location: dashboard.php");
    exit;
}
$pageTitle = "Connexion - YORKHOST Panel";
include 'header.php';
?>

<div class="container d-flex align-items-center justify-content-center min-vh-100">
  <div class="login-card p-4">
    <div class="card-body text-center">
      <h3 class="mb-3 fw-bold text-white">Gestion de votre tunnel</h3>
      <p class="text-light">Connectez-vous avec les mots de passe de votre compte client.</p>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
          <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php endif; ?>

      <form action="login_action.php" method="post">
        <div class="mb-3">
          <input type="email" name="email" id="email" class="form-control form-control-lg rounded-pill" placeholder="Email" required>
        </div>
        <div class="mb-3">
          <input type="password" name="password" id="password" class="form-control form-control-lg rounded-pill" placeholder="Mot de passe" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary btn-lg rounded-pill">Se connecter</button>
        </div>
      </form>

      <div class="mt-3">
        <a href="#" class="text-light">Mot de passe oublié ?</a>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
