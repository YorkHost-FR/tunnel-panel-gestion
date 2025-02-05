<?php
// index.php
session_start();

// Vérifie si l'utilisateur est connecté
if (isset($_SESSION['client_id'])) {
    // Redirige vers le tableau de bord s'il est connecté
    header("Location: dashboard.php");
} else {
    // Redirige vers la page de connexion s'il n'est pas connecté
    header("Location: login.php");
}
exit;
?>
