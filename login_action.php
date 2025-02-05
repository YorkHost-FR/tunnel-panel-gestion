<?php
// login_action.php
session_start();
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $result = verifyClientLogin($email, $password);

    if (isset($result['success']) && $result['success'] === true) {
        $client_id = $result['client_id'];

        // Vérifie si le client a AU MOINS un service Tunnel IP actif
        if (!hasTunnelAccount($client_id)) {
            $error = "Vous n'avez pas de service Tunnel IP actif.";
            header("Location: login.php?error=" . urlencode($error));
            exit;
        }

        // Authentification réussie
        $_SESSION['client_id'] = $client_id;
        $_SESSION['mfa_status'] = $result['mfa_status'];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Identifiants incorrects. Veuillez réessayer.";
        header("Location: login.php?error=" . urlencode($error));
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>
