<?php
// api/admin.php

// 1. Définir le type de contenu de la réponse en JSON
header("Content-Type: application/json; charset=UTF-8");

// 2. Inclure le middleware de sécurité
require_once '../AuthMiddleware.php';

// 3. Sécurisation globale : vérification du jeton (Erreur 401 si invalide)
$currentUser = AuthMiddleware::verifyToken();

// 4. Sécurisation par rôle : restriction stricte au rôle 'MANAGER' (Erreur 403 si insuffisant)
AuthMiddleware::requireRole($currentUser, 'MANAGER');

// 5. Logique de la route si les deux étapes de sécurité passent
http_response_code(200);
echo json_encode([
    "status" => "Succès",
    "message" => "Accès autorisé au back-office pour " . $currentUser['email']
]);
