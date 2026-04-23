<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "sehatak-new-db";
/*
هو الكلاس المسؤول عن إدارة الاتصال بقاعدة البيانات باستخدام PDO (PHP Data Objects).
يتم إنشاء كائن PDO في هذا الكود، ويتم ضبطه ليرتبط بقاعدة البيانات المحددة باستخدام المتغيرات $host، $dbname، $username، و $password.
يتم تعيين خاصية ERRMODE إلى ERRMODE_EXCEPTION لتمكين رمي الاستثناءات في حالة حدوث أخطاء في الاتصال أو الاستعلامات، مما يسهل التعامل مع الأخطاء بشكل أفضل.

*/
try { // PDO => PHP Data Objects
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
