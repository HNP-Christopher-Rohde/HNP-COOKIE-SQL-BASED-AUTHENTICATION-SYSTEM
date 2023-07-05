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

// Funktion zum Generieren eines Tokens
function hnp_generate_token() {
    $token = bin2hex(random_bytes(32)); // Generiert einen zufälligen Token-Wert

    // Zeige Error Logs
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Überprüfen, ob das Cookie bereits gesetzt ist
    if (isset($_COOKIE['hnp_auth_token'])) {
        $cookieToken = $_COOKIE['hnp_auth_token'];
        echo "Das Cookie 'hnp_auth_token' ist bereits gesetzt.";
        hnp_redirectWithToken($cookieToken); // Weiterleitung zur Ziel-Domain mit dem vorhandenen Token-Parameter
        return; // Abbruch der Funktion, wenn das Cookie bereits gesetzt ist
    }

    // Speichern des Tokens in der MySQL-Datenbank auf der Auth-Domain
    $dbHost = 'XXXXXXXXXXXXXX'; // Hostname der MySQL-Datenbank auf der AUTH-Domain
    $dbUser = 'XXXXXXXXXXXXXX'; // Benutzername der MySQL-Datenbank
    $dbPass = 'XXXXXXXXXXXXXX'; // Passwort der MySQL-Datenbank
    $dbName = 'XXXXXXXXXXXXXX'; // Name der MySQL-Datenbank

    // Verbindung zur MySQL-Datenbank herstellen
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Überprüft die Verbindung auf Fehler
    if ($conn->connect_error) {
        die('Verbindungsfehler zur MySQL-Datenbank: ' . $conn->connect_error); // Beendet das Skript und zeigt die Fehlermeldung an
    }

    // Tabelle erstellen, falls sie nicht existiert
    hnp_create_hnp_tokens_table($conn);

    // SQL-Befehl zum Einfügen des Tokens in die Datenbank
    $sql = "INSERT INTO hnp_tokens (token) VALUES ('$token')";

    // Führt den SQL-Befehl aus
    if ($conn->query($sql) === TRUE) {
        // Cookie mit dem Token erstellen
        setcookie('hnp_auth_token', $token, [
            'expires' => time() + 3600,
            'path' => '/',
            'domain' => 'DOMAIN-A.COM',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None' // Hier wird das SameSite-Attribut auf "None" gesetzt
        ]);
        echo 'Cookie mit dem Token wurde erstellt und in die Datenbank gespeichert.';
        hnp_redirectWithToken($token); // Weiterleitung zur Ziel-Domain mit dem neuen Token-Parameter
        exit;
    } else {
        die('Fehler beim Speichern des Tokens in der Datenbank: ' . $conn->error); // Beendet das Skript und zeigt die Fehlermeldung an
    }

    // Schließe die Verbindung zur MySQL-Datenbank
    $conn->close();
}

// Funktion zum Erstellen der Tabelle 'hnp_tokens'
function hnp_create_hnp_tokens_table($conn) {
    $tableName = 'hnp_tokens';

    // SQL-Befehl zum Erstellen der Tabelle, falls sie nicht existiert
    $sql = "CREATE TABLE IF NOT EXISTS $tableName (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                token VARCHAR(64) NOT NULL
            )";

    // Führt den SQL-Befehl aus
    if ($conn->query($sql) === TRUE) {
        echo 'Die Tabelle ' . $tableName . ' wurde erfolgreich erstellt oder existiert bereits.';
    } else {
        die('Fehler beim Erstellen der Tabelle ' . $tableName . ': ' . $conn->error); // Beendet das Skript und zeigt die Fehlermeldung an
    }
}

// Funktion zur Weiterleitung zur Ziel-Domain mit dem Token-Parameter
function hnp_redirectWithToken($token)
{
    header('Location: https://DOMAIN-B.COM/index.php?token=' . urlencode($token));
    exit;
}

// Funktion zur Weiterleitung zur Ziel-Domain mit einem ungültigen Token
function hnp_redirectWithInvalidToken()
{
    // Generiere einen ungültigen Token-Wert
    $invalidToken = bin2hex(random_bytes(32));

    // Leite zur Ziel-Domain mit dem ungültigen Token weiter
    header('Location: https://DOMAIN-B.COM/index.php?token=' . urlencode($invalidToken));
    exit;
}

// Funktion zum Löschen des Cookies
function hnp_deleteCookie() {
    setcookie('hnp_auth_token', '', time() - 3600, '/', 'DOMAIN-A.COM', true, true); // Löscht das Cookie
    echo 'Cookie wurde gelöscht.';
    sleep(0); // Wartezeit von 0 Sekunden
    hnp_redirectWithDelay(0); // Weiterleitung zur selben Seite
}

// Funktion zur Weiterleitung zur selben Seite nach einer Verzögerung
function hnp_redirectWithDelay($delay) {
    $redirectUrl = $_SERVER['REQUEST_URI'];
    header("Refresh: $delay; url=$redirectUrl");
    exit;
}

// Überprüfen, ob der Button zum Generieren des Tokens geklickt wurde
if (isset($_POST['generate_token'])) {
    hnp_generate_token();
}

// Überprüfen, ob der Button zum Löschen des Cookies geklickt wurde
if (isset($_POST['delete_cookie'])) {
    hnp_deleteCookie();
}

// Überprüfen, ob der Button zur Weiterleitung mit ungültigem Token geklickt wurde
if (isset($_POST['redirect_with_invalid_token'])) {
    hnp_redirectWithInvalidToken();
}
?>

<?php
if (isset($_COOKIE['hnp_auth_token'])) {
    echo "Das Cookie 'hnp_auth_token' wurde gesetzt oder ist bereits vorhanden.";
} else {
    echo "Das Cookie 'hnp_auth_token' wurde nicht gefunden.";
}
?>

<!-- Formular mit Button zum Generieren des Tokens -->
<?php if (!isset($_COOKIE['hnp_auth_token'])) { ?>
<h4> Dieser Button werden nur angezeigt, falls das Cookie/ der Token noch nicht vorhanden ist: </h4>
<form method="post">
    <input type="hidden" name="generate_token" value="1" />
    <input type="submit" value="Token generieren" />
</form>
<?php } else { ?>
<h4> Diese Button wird nur angezeigt, falls das Cookie / der Token bereits vorhanden ist: </h4>
<form method="post">
    <input type="hidden" name="delete_cookie" value="1" />
    <input type="submit" value="Cookie löschen" />
</form>
</br><form method="post">
    <input type="hidden" name="redirect_with_token" value="1" />
    <input type="submit" value="Weiter" />
</form>
<?php }

// Überprüfen, ob der Button zur Weiterleitung mit Token geklickt wurde
if (isset($_POST['redirect_with_token'])) {
    hnp_redirectWithToken($_COOKIE['hnp_auth_token']);
}
?>
<h4> Ungültiger Token: </h4>
<p>Hierbei wird versucht eine Anfrage zu manipulieren, indem ein Token simuliert wird und eine korrekte Anfrage gestartet wird. Das System erkennt aber, dass der Token-Wert ungültig ist, weil er nicht in der Datenbank ist</p>
<form method="post">
    <input type="hidden" name="redirect_with_invalid_token" value="1" />
    <input type="submit" value="Weiter mit ungültigem Token" />
</form>
</body>
</html>