<?php

include "includes/auth.php";
include "includes/database.php";

$query = $pdo->query("SELECT * FROM `analysis laboratory`");
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

// عشان نعمل اي امر سكيول محتاجين 3 خطوات 
// 1- نكتب الامر في شكل نص عادي
// 2- نحوله الى امر سكيول جاهز للتنفيذ
// 3- ننفذه 

// مثال على الخطوة 1
//$query1 = $pdo->query("SELECT * FROM users");
// مثال على الخطوة 2
//$query1->execute();
// مثال على الخطوة 3
//$results1 = $query1->fetchAll(PDO::FETCH_ASSOC); 

$doctorQuery = $pdo->query("SELECT * FROM doctor ");
$doctorQuery->execute();
$doctors = $doctorQuery->fetchAll(PDO::FETCH_ASSOC);



requireLogin();
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
</head>

<body>
    <a href="index.php">Home</a>
    <p> Hello, <?php echo $_SESSION['user_email']; ?> | <?php echo $_SESSION['user_type']; ?> </p>

    <a href="logout.php">Logout</a>

    <h1>Services</h1>

    <?php
    foreach ($results as $row) {
        echo "Lab Name: " . $row['name'] . "<br>";
    }
    ?>

    <?php
    foreach ($doctors as $row) {
        echo "doctor Name: " . $row['name'] . "<br>";
    }
    ?>



</body>

</html>