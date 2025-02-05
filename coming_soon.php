<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fonctionnalités à venir</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJZf+2Rw0ndCi98bsiUjrS5g0Zy9tPzXgxljRU9SktHgP6BTo6FF7I2m45zA" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Font Awesome -->

    <style>
        /* Styling général */
        body {
            background-color: #1e3c72;
            font-family: 'Arial', sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            border: none;
            border-radius: 15px;
            background-color: rgb(196, 198, 202);
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .card-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: rgb(0, 76, 191); /* Couleur principale */
        }

        .card-text {
            font-size: 1.2rem;
            color: #58716f; /* Couleur secondaire */
        }

        .alert {
            background-color: #028090; /* Couleur d'accentuation */
            color: white;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 2rem;
        }

        .btn-contact {
            background-color: #00bfb2; /* Couleur principale */
            border: none;
            color: white;
            padding: 1rem 2rem;
            font-size: 1.2rem;
            border-radius: 10px;
            margin-top: 2rem;
            transition: background-color 0.3s ease;
        }

        .btn-contact:hover {
            background-color: #028090; /* Couleur d'accentuation */
            cursor: pointer;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.9rem;
            color: #6c757d;
        }

        /* Style du bouton de retour */
        .btn-back {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .btn-back:hover {
            color: #00bfb2; /* Couleur principale au survol */
        }
    </style>
</head>

<body>

<!-- Bouton de retour en haut à droite -->
<button class="btn-back" onclick="window.history.back();">
    <i class="fas fa-arrow-left"></i> Revenir en arrière
</button>

<div class="container">
    <div class="card">
        <h1 class="card-title"><i class="fas fa-cogs"></i> Fonctionnalités à venir</h1>
        <p class="card-text">
            Nous avons de nouvelles fonctionnalités en développement ! Cette fonctionnalité sera bientôt disponible, restez connectés.
        </p>

        <div class="alert alert-info">
            🚧 **En développement** : Nous travaillons activement pour améliorer nos services. Plus d'options à venir.
        </div>

        <a href="https://discord.gg/yorkhost" class="btn-contact">
            <i class="fas fa-envelope"></i> Contactez-nous
        </a>
    </div>
</div>

<!-- Bootstrap 5 JS & Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gyb3+P98bXwQH51kRk4xAcWQ8nR6lWvX9cZq6BgbgR4Nhc4Xw6" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0gpsaVu6dUkFMA94p95p6U5q6J6aL7H4zH09s/4w5xx2fmQG" crossorigin="anonymous"></script>

</body>

</html>
