<?php
require_once 'functions.php';

// Récupérer les infos système de MikroTik
$response = mikrotikRequest("system/resource");

if ($response) {
    echo "<h2>📡 MikroTik System Info</h2>";
    echo "📌 Nom de la carte : " . htmlspecialchars($response['board-name']) . "<br>";
    echo "⏳ Uptime : " . htmlspecialchars($response['uptime']) . "<br>";
    echo "⚡ Charge CPU : " . htmlspecialchars($response['cpu-load']) . "%<br>";
    echo "💾 Mémoire disponible : " . htmlspecialchars($response['free-memory']) . " MB<br>";
} else {
    echo "❌ Erreur : Impossible d'accéder aux infos MikroTik.";
}
?>
