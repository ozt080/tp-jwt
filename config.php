<?php
// config.php

// Clé secrète globale pour signer et valider les jetons JWT
define('JWT_SECRET', 'ma_cle_secrete_geotech_super_securisee_2026');

// Tableau des utilisateurs de test demandé par le sujet
$users = [
    [
        "id" => 1,
        "email" => "user1@geotech.fr",
        "password" => "password123",
        "role" => "MANAGER"
    ],
    [
        "id" => 2,
        "email" => "tech1@geotech.fr",
        "password" => "secure456",
        "role" => "TECHNICIEN"
    ]
];
