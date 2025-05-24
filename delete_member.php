<?php
// delete_member.php

$lang = $_GET['lang'] ?? 'en';
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    // Invalid or missing ID, redirect back to members list
    header("Location: members.php?lang=$lang");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=church_hr;charset=utf8", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
    $stmt->execute([$id]);

    // Redirect back to members list with success message
    header("Location: members.php?lang=$lang&deleted=1");
    exit;

} catch (PDOException $e) {
    echo "Error deleting member: " . $e->getMessage();
}
