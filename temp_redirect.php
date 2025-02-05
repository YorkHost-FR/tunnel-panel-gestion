<?php
// Assurez-vous que l'action a bien été effectuée avant de rediriger
if (isset($_GET['id'])) {
    $tunnel_id = $_GET['id'];
    // Redirection vers la page de gestion du tunnel après 2 secondes
    header("Refresh: 2; url=manage_tunnel.php?id=$tunnel_id");
    exit;
} else {
    // Si l'ID est manquant, redirige vers une page d'erreur ou vers la page d'accueil
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirection en cours...</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJZf+2Rw0ndCi98bsiUjrS5g0Zy9tPzXgxljRU9SktHgP6BTo6FF7I2m45zA" crossorigin="anonymous">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="text-center">
        <div class="alert alert-info">
            <h2>Merci pour votre action !</h2>
            <p>Votre action a été prise en compte. Vous allez être redirigé vers la gestion du tunnel dans quelques secondes.</p>
            <p><strong>Patientez...</strong></p>
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gyb3+P98bXwQH51kRk4xAcWQ8nR6lWvX9cZq6BgbgR4Nhc4Xw6" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0gpsaVu6dUkFMA94p95p6U5q6J6aL7H4zH09s/4w5xx2fmQG" crossorigin="anonymous"></script>

</body>
</html>
