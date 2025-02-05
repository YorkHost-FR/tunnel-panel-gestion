<?php
// dashboard.php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['client_id']) || !hasTunnelAccount($_SESSION['client_id'])) {
    session_destroy();
    header("Location: login.php?error=" . urlencode("Accès refusé. Vous n'avez pas de service Tunnel IP actif."));
    exit;
}

$pageTitle = "Dashboard - Gestion des Tunnels";
include 'header.php';

$tunnels = getClientTunnels($_SESSION['client_id']);
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">👨‍💻 Espace Client - Gestion des Tunnels IP</h2>
        <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <?php if (empty($tunnels)): ?>
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-circle"></i> Vous n'avez aucun tunnel IP actif.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($tunnels as $tunnel): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-lg border-0 p-3 mb-4">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($tunnel['name']); ?></h5>
                            <p class="text-muted"><i class="fas fa-id-badge"></i> ID : <?= htmlspecialchars($tunnel['id']); ?></p>
                            <p><i class="fas fa-calendar-alt"></i> Prochaine Facture : <strong><?= htmlspecialchars($tunnel['next_due']); ?></strong></p>
                            <p><i class="fas fa-money-bill"></i> Prix : <strong><?= htmlspecialchars($tunnel['total']); ?>€</strong></p>
                            <p><i class="fas fa-sync-alt"></i> Cycle de Facturation : <strong><?= htmlspecialchars($tunnel['billingcycle']); ?></strong></p>
                            <div class="d-flex justify-content-between mt-3">
                                <a href="manage_tunnel.php?id=<?= $tunnel['id']; ?>" class="btn btn-primary"><i class="fas fa-tools"></i> Gérer</a>
                                <a href="coming_soon.php" class="btn btn-warning"><i class="fas fa-tools"></i> Commander des IPv4</a>
                            </div>

                        </div>
                        
                        <a href="https://clients.yorkhost.fr/index.php/clientarea/services/tunnel-ip/<?= htmlspecialchars($tunnel['id']); ?>" class="btn btn-success"><i class="fas fa-sign-out-alt"></i> Voir sur l'espace client</a>

                    </div>
                </div>
                
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
