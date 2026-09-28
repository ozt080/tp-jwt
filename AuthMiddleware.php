<?php
// AuthMiddleware.php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    // On utilise la constante JWT_SECRET définie dans notre fichier config.php
    private static $secretKey = JWT_SECRET;

    /* 1. Vérifie le jeton JWT envoyé dans les en-têtes HTTP */
    public static function verifyToken() {
                // Récupération de tous les en-têtes
        $headers = getallheaders();
        $authHeader = null;

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            // Si Apache a déplacé l'en-tête suite au .htaccess
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        // Étape 2.1 & 2.2 : Vérifier que le format est bien "Bearer <TOKEN>"
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401); // Unauthorized
            echo json_encode(["error" => "Accès refusé. Jeton manquant ou format invalide."]);
            exit(); // On bloque la requête immédiatement
        }

                // Extraction du jeton (ce qui se trouve après "Bearer ")
        $token = $matches[1];

        try {
            // Étape 2.3 : Valider la signature du token avec la clé secrète du serveur
            $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));
            
            // Étape 2.5 : Si le token est valide ➡ On extrait les données du payload
            return (array) $decoded->data; 

        } catch (Exception $e) {

            // Étape 2.4 : Si le token est expiré ou invalide ➡ Erreur HTTP 401
            http_response_code(401);
            echo json_encode(["error" => "Jeton invalide ou expiré."]);
            exit();
        }
    }

    /* 2. Vérifie si l'utilisateur possède le rôle requis (RBAC) */
    public static function requireRole($user, $roleAttendu) {
        // Si le rôle enregistré dans le JWT ne correspond pas au rôle demandé par la route
        if (!isset($user['role']) || $user['role'] !== $roleAttendu) {
            http_response_code(403); // Forbidden
            echo json_encode(["error" => "Accès interdit. Droits insuffisants."]);
            exit(); // Blocage de la requête
        }
    }
}
