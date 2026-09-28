<?php
// api/login.php

// 1. Définir le type de contenu de la réponse en JSON
header("Content-Type: application/json; charset=UTF-8");

// 2. Inclure l'autoloader de Composer et notre configuration globale
require_once '../vendor/autoload.php';
require_once '../config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 3. Récupérer les données brutes envoyées dans le corps de la requête (JSON)
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Vérifier que l'email et le mot de passe sont bien fournis
if (!isset($data['email']) || !isset($data['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Données incomplètes. Email et mot de passe requis."]);
    exit();
}

$emailSaisi = $data['email'];
$passwordSaisi = $data['password'];

$userTrouve = null;

// 4. Parcourir notre tableau d'utilisateurs de test ($users provient de config.php)
foreach ($users as $user) {
    if ($user['email'] === $emailSaisi && $user['password'] === $passwordSaisi) {
        $userTrouve = $user;
        break; // On a trouvé l'utilisateur, on arrête la boucle
    }
}

// 5. Si les identifiants sont incorrects ➡ Erreur 401 Unauthorized
if (!$userTrouve) {
    http_response_code(401);
    echo json_encode(["error" => "Identifiants incorrects."]);
    exit();
}

// 6. Si les identifiants sont corrects ➡ Générer le jeton JWT
$tempsActuel = time();
$tempsExpiration = $tempsActuel + (15 * 60); // Expiration dans 15 minutes (15 * 60 secondes)

// Le "Payload" contient les informations publiques sur l'utilisateur
$payload = [
    "iss" => "http://tp-jwt.test",       // Émetteur du jeton
    "aud" => "http://tp-jwt.test",       // Audience du jeton
    "iat" => $tempsActuel,               // Date de création
    "exp" => $tempsExpiration,           // Date d'expiration
    "data" => [                          // Données personnalisées demandées par le sujet
        "id" => $userTrouve['id'],
        "email" => $userTrouve['email'],
        "role" => $userTrouve['role']
    ]
];

// Génération de la signature du JWT avec notre clé secrète (méthode HS256)
$jwt = JWT::encode($payload, JWT_SECRET, 'HS256');

// 7. Renvoyer la réponse avec le code HTTP 200 et le jeton
http_response_code(200);
echo json_encode([
    "message" => "Connexion réussie.",
    "token" => $jwt
]);
