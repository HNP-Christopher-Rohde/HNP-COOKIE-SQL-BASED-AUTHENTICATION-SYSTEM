<!DOCTYPE html>
<!--
Name: HNP Cookie & Token Auth-System
Description: Erstellt ein Cookie-basiertes SQL Authentifizierung-System. Domain- und Serverübergreifend.
Author: Homepage-nach-Preis
Version: 1.0
Author URI: https://homepage-nach-preis.de/
License: Creative Commons Non-Commercial - CC-NC 4.0
-->
<html>
<head>
    <title>HNP Auth-System</title>
</head>
<body>
    <h2>HNP Cookie & Token Auth-System</h2>
	
<?php

function checkTokenAndSetCookie() {
    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        // Zeige Error Logs
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Überprüft den Token in der Datenbank
        $dbHost = 'XXXXXXXXXXXXXX'; // Hostname der MySQL-Datenbank auf der AUTH-Domain
        $dbUser = 'XXXXXXXXXXXXXX'; // Benutzername der MySQL-Datenbank
        $dbPass = 'XXXXXXXXXXXXXX'; // Passwort der MySQL-Datenbank
        $dbName = 'XXXXXXXXXXXXXX'; // Name der MySQL-Datenbank

        // Verbindung zur MySQL-Datenbank herstellen
        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        // Überprüft die Verbindung auf Fehler
        if ($conn->connect_error) {
            die('Verbindungsfehler zur MySQL-Datenbank: ' . $conn->connect_error);
        }

        // Überprüft den Token in der Datenbanktabelle
        $tableName = 'hnp_tokens'; // Name der Tabelle, in der die Tokens gespeichert sind
        $sql = "SELECT * FROM $tableName WHERE token = '$token'";

        // Führt die Datenbankabfrage aus
        $result = $conn->query($sql);

        // Überprüft das Ergebnis der Datenbankabfrage
        if ($result && $result->num_rows > 0) {
            // Token ist gültig, erstellt das Cookie
            setcookie('hnp_auth_token', $token, [
                'expires' => time() + 3600,
                'path' => '/',
                'domain' => 'DOMAIN-B.COM',
                'secure' => true,
                'httponly' => false,
                'samesite' => 'None' // Hier wird das SameSite-Attribut auf "None" gesetzt
            ]);

            echo 'Authentifizierung erfolgreich. Bitte warten Sie...';

            // JavaScript-Weiterleitung nach 3 Sekunden
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "index.html";
                    }, 3000);
                  </script>';

            exit;
        } else {
            echo 'Ungültiger Token oder Token nicht gefunden. Authentifizierung fehlgeschlagen.';

            // Button "Trotzdem weiter"
            echo '</br></br><button onclick="window.location.href = \'index.html\';">Trotzdem weiter</button></br>(Nicht vegessen, den Logout zuvor zu betätigen, falls bereits eine erfolgreiche Authentifizierung stattfand)';

            // Button "Zurück"
            echo '</br></br><button onclick="window.history.back();">Zurück</button>';
        }

        // Schließt die Verbindung zur MySQL-Datenbank
        $conn->close();
    } else {
        echo 'Token-Parameter fehlt.';
    }
}

// Funktion aufrufen
checkTokenAndSetCookie();
?>
</body>
</html>