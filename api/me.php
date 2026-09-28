<?php
// api/me.php

// 1. Définir le type de contenu de la réponse en JSON
header("Content-Type: application/json; charset=UTF-8");

// 2. Inclure le middleware de sécurité (il se trouve un dossier en arrière)
require_once '../AuthMiddleware.php';

// 3. Sécurisation globale : on appelle la méthode de vérification
// Si le token est absent, expiré ou faux, le middleware coupera le script avec un 401
$currentUser = AuthMiddleware::verifyToken();

// 4. Si le middleware a laissé passer la requête, on affiche les infos de l'utilisateur
http_response_code(200);
echo json_encode([
    "status" => "Succès",
    "user" => [
        "id" => $currentUser['id'],
        "email" => $currentUser['email'],
        "role" => $currentUser['role']
    ]
]);
