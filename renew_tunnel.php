<?php
// renew_tunnel.php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['client_id']) || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$tunnel_id = intval($_GET['id']);
$pageTitle = "Renouvellement de Tunnel";
include 'header.php';
?>

<div class="container mt-5">
    <h2 class="text-center">Renouvellement de votre Tunnel</h2>
    <p class="text-center">Ce service sera renouvelé automatiquement.</p>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-success">Retour</a>
    </div>
</div>

<?php include 'footer.php'; ?>
