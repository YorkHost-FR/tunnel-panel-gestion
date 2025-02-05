<?php
// functions.php

require_once 'config.php';

function verifyClientLogin($email, $password) {
    $post = [
        'call'     => 'verifyClientLogin',
        'api_id'   => API_ID,
        'api_key'  => API_KEY,
        'email'    => $email,
        'password' => $password,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $data = curl_exec($ch);
    curl_close($ch);

    return json_decode($data, true);
}


function hasTunnelAccount($client_id) {
    $post = [
        'call'      => 'getClientAccounts',
        'api_id'    => API_ID,
        'api_key'   => API_KEY,
        'id'        => $client_id,
        'status'    => 'Active',
        'category_id' => '28', // Remplace X par l'ID de la catégorie des tunnels IP
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $data = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($data, true);
    // Debugging : Afficher la réponse brute de l'API pour vérifier le contenu
//echo "<pre>";
//print_r($response);
//echo "</pre>";
//echo "Client ID envoyé : " . $client_id . "<br>";
//die(); // Stopper l'exécution pour voir la réponse brute


    // Debugging : afficher l'erreur JSON si problème
    if ($response === null) {
        die("Erreur JSON : " . json_last_error_msg() . "<br>Réponse brute : " . htmlspecialchars($data));
    }

    if (!isset($response['success']) || $response['success'] !== true) {
        return false; // L'API a retourné une erreur
    }

    // Liste des produits Tunnel IP
    $tunnel_products = ['T-100MBPS', 'T-500MBPS', 'T-1GBPS', 'T-10GBPS'];

    // Vérifie si au moins un service actif correspond
    foreach ($response['accounts'] as $account) {
        foreach ($tunnel_products as $keyword) {
            if (stripos($account['name'], $keyword) !== false && $account['status'] === 'Active') {
                return true;
            }
        }
    }

    return false;
}


// Récupère la liste des tunnels IP du client
function getClientTunnels($client_id) {
    $post = [
        'call'      => 'getClientAccounts',
        'api_id'    => API_ID,
        'api_key'   => API_KEY,
        'id'        => $client_id,
        'status'    => 'Active',
        'category_id' => '28', // Remplace X par l'ID de la catégorie des tunnels IP
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $data = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($data, true);

    if (!isset($response['success']) || $response['success'] !== true) {
        return []; // Retourne un tableau vide en cas d'erreur
    }

    // Liste des noms de produits Tunnel IP
    $tunnel_products = ['T-100MBPS', 'T-500MBPS', 'T-1GBPS', 'T-10GBPS'];
    $tunnels = [];

    foreach ($response['accounts'] as $account) {
        foreach ($tunnel_products as $keyword) {
            if (stripos($account['name'], $keyword) !== false && strtolower($account['status']) === 'active') {
                $tunnels[] = [
                    'id' => $account['id'],
                    'name' => $account['name'],
                    'billingcycle' => $account['billingcycle'],
                    'total' => $account['total'],
                    'next_due' => $account['next_due'],
                    'product_id' => $account['product_id'],
                ];
            }
        }
    }

    return $tunnels; // Retourne tous les tunnels trouvés
}



// Configuration de MikroTik API
define('MIKROTIK_API_URL', 'http://83.150.218.150/rest/');
define('MIKROTIK_USER', 'admin');
define('MIKROTIK_PASSWORD', 'tiiy.qQ4woXwGGZMdmwt46e4Xr');

// Fonction pour envoyer des requêtes à l'API MikroTik
function mikrotikRequest($endpoint, $method = 'GET', $data = []) {
    $url = MIKROTIK_API_URL . $endpoint;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERPWD, MIKROTIK_USER . ":" . MIKROTIK_PASSWORD);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

function toggleTunnel($tunnel_name, $enable = true) {
    $endpoint = "interface";
    
    // Trouver l'interface du tunnel
    $interfaces = mikrotikRequest($endpoint);
    foreach ($interfaces as $interface) {
        if ($interface['name'] === $tunnel_name) {
            $interface_id = $interface['.id'];

            // Activer ou désactiver
            $action = $enable ? 'no' : 'yes';
            return mikrotikRequest("$endpoint/$interface_id", "POST", ['disabled' => $action]);
        }
    }
    return false;
}

function synchronizeWithMikrotik($tunnel_id, $tunnel_state) {
    $api_url = MIKROTIK_API_URL . "interface/";

    // Définition des paramètres pour MikroTik
    $post_data = [
        'name' => "tunnel-$tunnel_id",
        'type' => strtolower($tunnel_state['tunnel_type']),
        'disabled' => $tunnel_state['active'] ? 'no' : 'yes'
    ];

    // Ajout d'IPSec si applicable
    if ($tunnel_state['tunnel_type'] === "GRE" || $tunnel_state['tunnel_type'] === "EOIP") {
        $post_data['ipsec-secret'] = $tunnel_state['use_ipsec'] ? $tunnel_id : '';
    }

    // Requête API REST vers MikroTik
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_USERPWD, MIKROTIK_USER . ":" . MIKROTIK_PASSWORD);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}


function createMikroTikTunnel($tunnel_id, $tunnel_state) {
    $api_url = MIKROTIK_API_URL . "interface/eoip/add";
    $source_ip = "217.145.72.249";  // IP source du tunnel EOIP

    if (empty($tunnel_state['destination_ip'])) {
        error_log("[MikroTik] Erreur : Aucune IP de destination définie !");
        return ["error" => "Aucune IP de destination définie."];
    }

    // Vérifier si une valeur MTU a été définie, sinon mettre par défaut 1360
    $mtu_value = isset($tunnel_state['mtu']) ? intval($tunnel_state['mtu']) : 1360;

    // Définition des paramètres pour la création du tunnel EOIP
    $post_data = [
        "name" => "eoip-$tunnel_id",
        "mtu" => $mtu_value,
        "remote-address" => $tunnel_state['destination_ip'],
        "tunnel-id" => $tunnel_id,
        "keepalive" => "10s,10",
        "dscp" => "inherit",
        "clamp-tcp-mss" => "yes",
        "dont-fragment" => "no",
        "allow-fast-path" => $tunnel_state['allow_fast_path'] ? "yes" : "no",
        "disabled" => "false",
        "local-address" => $source_ip  // Spécification de l'IP source du tunnel EOIP
    ];

    // ✅ Si IPSec est activé, ajouter ipsec-secret
    if ($tunnel_state['use_ipsec']) {
        $post_data["ipsec-secret"] = strval($tunnel_id);
    }
    error_log("[MikroTik] Création du tunnel EOIP avec MTU : $mtu_value");

    // Création du tunnel EOIP sur MikroTik
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_USERPWD, MIKROTIK_USER . ":" . MIKROTIK_PASSWORD);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    error_log("[MikroTik] Tunnel EOIP créé. HTTP Code: $http_code, Réponse: " . print_r($response, true));

    $response_data = json_decode($response, true);

    // ✅ Réintégration de `addTunnelToBridge()` après la création
    if (isset($response_data['ret'])) {
        error_log("[MikroTik] Ajout du tunnel '$tunnel_id' au bridge...");

        // Ajouter automatiquement le tunnel au bridge
        $bridge_name = "bridge-217.145.72.0";
        $bridge_response = addTunnelToBridge("eoip-$tunnel_id", $bridge_name);

        error_log("[MikroTik] Réponse ajout bridge : " . print_r($bridge_response, true));
    } else {
        error_log("[MikroTik] Erreur : Impossible de créer le tunnel EOIP.");
    }

    return $response_data;
}




function addTunnelToBridge($tunnel_name, $bridge_name) {
    $api_url = MIKROTIK_API_URL . "interface/bridge/port/add";

    error_log("[MikroTik] Tentative d'ajout du tunnel '$tunnel_name' au bridge '$bridge_name'...");

    // Définition des paramètres pour ajouter le tunnel au bridge
    $post_data = [
        "interface" => $tunnel_name,
        "bridge" => $bridge_name
    ];

    // Requête API REST pour ajouter le tunnel au bridge
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_USERPWD, MIKROTIK_USER . ":" . MIKROTIK_PASSWORD);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    // Logs détaillés
    error_log("[MikroTik] HTTP Code (Bridge) : $http_code");
    error_log("[MikroTik] cURL Error : $curl_error");
    error_log("[MikroTik] Response : " . print_r($response, true));

    return json_decode($response, true);
}



function deleteMikroTikTunnel($tunnel_id) {
    $tunnel_name = "eoip-$tunnel_id";

    error_log("[MikroTik] Suppression du tunnel EOIP pour '$tunnel_name'...");

    // Récupérer l'ID du tunnel EOIP
    $api_url = MIKROTIK_API_URL . "interface/eoip";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_USERPWD, MIKROTIK_USER . ":" . MIKROTIK_PASSWORD);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    error_log("[MikroTik] Réponse récupération tunnels: " . print_r($response, true));

    $response_data = json_decode($response, true);

    if (!empty($response_data)) {
        foreach ($response_data as $tunnel) {
            if ($tunnel['name'] === $tunnel_name) {
                $tunnel_id_mikrotik = trim($tunnel['.id']); // ✅ Laisser `*3C` intact

                error_log("[MikroTik] ID trouvé pour suppression: $tunnel_id_mikrotik");

                // Supprimer le tunnel EOIP avec `*ID`
                $delete_tunnel_url = MIKROTIK_API_URL . "interface/eoip/$tunnel_id_mikrotik";
                error_log("[MikroTik] URL de suppression : $delete_tunnel_url");

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $delete_tunnel_url);
                curl_setopt($ch, CURLOPT_USERPWD, MIKROTIK_USER . ":" . MIKROTIK_PASSWORD);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $delete_response = curl_exec($ch);
                curl_close($ch);

                error_log("[MikroTik] Tunnel EOIP supprimé : " . print_r($delete_response, true));
                return true;
            }
        }
    }

    error_log("[MikroTik] Erreur : Tunnel EOIP '$tunnel_name' introuvable.");
    return false;
}






?>
