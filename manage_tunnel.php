<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['client_id']) || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$remote_ip = "217.145.72.249";
$gateway_ip = "217.145.72.1";
$netmask = "255.255.255.0";  // ou /24

$tunnel_id = intval($_GET['id']);
$tunnel_state_file = "tunnels/tunnel_$tunnel_id.json";
// Valeurs par défaut si le fichier n'existe pas
$default_tunnel_state = [
    'active' => false,
    'use_ipsec' => true,
    'tunnel_type' => 'EOIP',
    'tunnel_id' => $tunnel_id,
    'ipsec_id' => $tunnel_id,
    'destination_ip' => '',  // IP vide par défaut
    'allow_fast_path' => false,
    'mtu' => 1360
];
if (!file_exists($tunnel_state_file)) {
    // Si le fichier n'existe pas, le créer avec les valeurs par défaut
    file_put_contents($tunnel_state_file, json_encode($default_tunnel_state, JSON_PRETTY_PRINT));
    error_log("[MikroTik] Fichier JSON généré avec les valeurs par défaut.");
}


// Vérifier si le dossier "tunnels" existe, sinon le créer
if (!is_dir("tunnels")) {
    mkdir("tunnels", 0777, true);
}

// Liste des types de tunnels disponibles (EOIP par défaut)
$tunnel_types = ['EOIP', 'GRE', 'VXLAN'];

// Définition des valeurs par défaut
$tunnel_state = [
    'active' => false,
    'use_ipsec' => true, // IPSec activé par défaut
    'allow_fast_path' => false, // Désactivé si IPSec est activé
    'tunnel_type' => 'EOIP', // EOIP par défaut
    'tunnel_id' => $tunnel_id,
    'ipsec_id' => $tunnel_id,
    'destination_ip' => '',
    'mtu' => 1360 // Ajout de la valeur par défaut du MTU
];

// Charger l'état du tunnel à partir du fichier JSON
$tunnel_state = json_decode(file_get_contents($tunnel_state_file), true);
// Vérifier si le fichier a bien été chargé et que l'état est valide
if (json_last_error() !== JSON_ERROR_NONE) {
    // Si le JSON est corrompu, recréer le fichier avec les valeurs par défaut
    file_put_contents($tunnel_state_file, json_encode($default_tunnel_state, JSON_PRETTY_PRINT));
    error_log("[MikroTik] Fichier JSON corrompu, recréé avec les valeurs par défaut.");
}


// Vérifier si une requête POST est envoyée
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_tunnel'])) {
        error_log("[MikroTik] Bouton 'Activer/Désactiver' cliqué pour le tunnel $tunnel_id...");
    
        if ($tunnel_state['active']) {
            deleteMikroTikTunnel($tunnel_id);
            $tunnel_state['active'] = false;
            error_log("[MikroTik] Tunnel EOIP $tunnel_id désactivé.");
        } else {
            createMikroTikTunnel($tunnel_id, $tunnel_state);
            $tunnel_state['active'] = true;
            error_log("[MikroTik] Tunnel EOIP $tunnel_id activé.");
        }
    
        // Enregistrer immédiatement l'état mis à jour
        file_put_contents($tunnel_state_file, json_encode($tunnel_state, JSON_PRETTY_PRINT));
    
        // ✅ Redirection vers temp_redirect.php pour vider le POST
        header("Location: temp_redirect.php?id=$tunnel_id");
        exit;
    }

    
    
    
    if (isset($_POST['tunnel_type']) && in_array($_POST['tunnel_type'], $tunnel_types)) {
        $tunnel_state['tunnel_type'] = $_POST['tunnel_type'];

        // Désactiver IPSec si VXLAN est sélectionné
        if ($tunnel_state['tunnel_type'] === "VXLAN") {
            $tunnel_state['use_ipsec'] = false;
            $tunnel_state['allow_fast_path'] = true;
        } else {
            $tunnel_state['use_ipsec'] = true;
            $tunnel_state['allow_fast_path'] = false;
        }

        file_put_contents($tunnel_state_file, json_encode($tunnel_state));
    }
    
    if (isset($_POST['toggle_ipsec'])) {
        $tunnel_state['use_ipsec'] = !$tunnel_state['use_ipsec'];
    
        // Si IPSec est activé, on désactive automatiquement fast path
        if ($tunnel_state['use_ipsec']) {
            $tunnel_state['allow_fast_path'] = false;
        } else {
            $tunnel_state['allow_fast_path'] = true;
        }
    
        // Enregistrer immédiatement l'état mis à jour
        file_put_contents($tunnel_state_file, json_encode($tunnel_state, JSON_PRETTY_PRINT));
    
        // ✅ Rediriger vers la page temporaire pour vider le POST
        header("Location: temp_redirect.php?id=$tunnel_id");
        exit;
    }
    
    

    if (isset($_POST['destination_ip'])) {
        $tunnel_state['destination_ip'] = filter_var($_POST['destination_ip'], FILTER_VALIDATE_IP) ?: $tunnel_state['destination_ip'];
        file_put_contents($tunnel_state_file, json_encode($tunnel_state));
        error_log("[MikroTik] IP de destination mise à jour : " . $tunnel_state['destination_ip']);
    }
    
    if (isset($_POST['mtu'])) {
        $tunnel_state['mtu'] = intval($_POST['mtu']);
        file_put_contents($tunnel_state_file, json_encode($tunnel_state));
        error_log("[MikroTik] MTU mis à jour : " . $tunnel_state['mtu']);
    }
    
}

$pageTitle = "Gestion du Tunnel $tunnel_id";
include 'header.php';
?>

<div class="container mt-5">
    <h2 class="text-center">🛠️ Gestion du Tunnel</h2>

    <div class="card shadow-lg border-0 p-4 mt-4">
        <div class="card-body">
            <h4 class="card-title">Tunnel #<?= htmlspecialchars($tunnel_id); ?></h4>
            
            <form method="post">
                <label for="tunnel_type" class="form-label">Type de Tunnel :</label>
                <select name="tunnel_type" id="tunnel_type" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($tunnel_types as $type): ?>
                        <option value="<?= $type; ?>" <?= ($tunnel_state['tunnel_type'] === $type) ? 'selected' : ''; ?>>
                            <?= $type; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <p><strong>Statut :</strong> 
                <span class="badge <?= $tunnel_state['active'] ? 'bg-success' : 'bg-danger'; ?>">
                    <?= $tunnel_state['active'] ? 'Actif' : 'Inactif'; ?>
                </span>
            </p>
            <hr>
            <h4>Configuration Réseau</h4>
            <p><strong>Tunnel Remote IP :</strong> <?= $remote_ip; ?></p>
            <p><strong>Gateway :</strong> <?= $gateway_ip; ?></p>
            <p><strong>Netmask :</strong> <?= $netmask; ?> (ou /24)</p>

            <hr>


            <p><strong>ID du Tunnel :</strong> <?= $tunnel_state['tunnel_id']; ?></p>

            <?php if ($tunnel_state['use_ipsec']): ?>

<?php endif; ?>


            <form method="post">
                <button type="submit" name="toggle_tunnel" class="btn btn-lg <?= $tunnel_state['active'] ? 'btn-danger' : 'btn-success'; ?>" <?= ($tunnel_state['tunnel_type'] !== 'EOIP') ? 'disabled' : ''; ?>>
                    <?= $tunnel_state['active'] ? 'Désactiver' : 'Activer'; ?> le tunnel
                </button>
            </form>

            <?php if ($tunnel_state['tunnel_type'] !== "VXLAN" && $tunnel_state['active']): ?>
    <hr>
    <p><strong>Utiliser IPSec :</strong> 
        <span class="badge <?= $tunnel_state['use_ipsec'] ? 'bg-info' : 'bg-secondary'; ?>">
            <?= $tunnel_state['use_ipsec'] ? 'Activé' : 'Désactivé'; ?>
        </span>
    </p>

    <form method="post">
        <button type="submit" name="toggle_ipsec" class="btn btn-lg <?= $tunnel_state['use_ipsec'] ? 'btn-warning' : 'btn-primary'; ?>">
            <?= $tunnel_state['use_ipsec'] ? 'Désactiver' : 'Activer'; ?> IPSec
        </button>
    </form>

    <?php if ($tunnel_state['use_ipsec']): ?>
        <div class="alert alert-warning mt-3">
            ⚠️ IPSec est activé ! Assurez-vous que <strong>Allow Fast Path</strong> reste <strong>désactivé</strong> pour éviter les problèmes de connexion.
        </div>
    <?php endif; ?>
<?php endif; ?>


            <hr>
            <form method="post" id="autoSaveForm">
    <label for="destination_ip" class="form-label">IP de votre routeur :</label>
    <input type="text" name="destination_ip" class="form-control" 
        value="<?= htmlspecialchars($tunnel_state['destination_ip']); ?>" 
        <?= $tunnel_state['active'] ? 'disabled' : ''; ?> required>

    <label for="mtu" class="form-label mt-3">MTU :</label>
    <input type="number" name="mtu" class="form-control" 
        value="<?= htmlspecialchars($tunnel_state['mtu']); ?>" 
        <?= $tunnel_state['active'] ? 'disabled' : ''; ?> required>
</form>


        </div>
        <?php if ($tunnel_state['active']): ?>
    <div class="alert alert-warning mt-3">
        ⚠️ Le tunnel est actuellement <strong>actif</strong>. Vous ne pouvez pas modifier l'IP de votre routeur ni le MTU. 
        Désactivez le tunnel pour les modifier.
    </div>
<?php endif; ?>

    </div>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Retour</a>
</div>
<script>
    document.querySelectorAll('input, select').forEach(element => {
        element.addEventListener('change', function() {
            this.form.submit();  // Envoie le formulaire dès qu'un champ est modifié
        });
    });
</script>

<?php include 'footer.php'; ?>
