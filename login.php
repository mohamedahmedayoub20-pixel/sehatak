<?php

include "includes/auth.php";
include "includes/database.php";

$language = "en";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $language = $_GET['lang'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Invalid credentials";
    } else {
        $userQuery = $pdo->prepare("SELECT * FROM user WHERE email = :mail AND password = :psw");
        $userQuery->execute(['mail' => $email, 'psw' => $password]);
        $user = $userQuery->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            $error = "Invalid credentials";
        } else {
            // user_id name email address password account_type account_status deleted
            // status => 'active','inactive','suspended','deleted','archived','pending','blocked'

            if ($user['account_status'] == 'suspended' || $user['account_status'] == 'blocked' || $user['account_status'] == 'deleted') {
                $error = "Your account is " . $user['account_status'] . ". Please contact support.";
                session_destroy();
            } else {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['account_type'];
                $_SESSION['user_status'] = $user['account_status'];
                $_SESSION['user_name'] = $user['name'];
                //$_SESSION['user_phone'] = $user['phone'];
                // $_SESSION['user_type'] => 'doctor','admin','user','patient','nurse','pharmacy','analysis laboratory'

                if ($_SESSION['user_type'] == 'doctor') {
                    header("Location: dashboards/doctor.php?lang=" . ($language ?? 'en'));
                } else if ($_SESSION['user_type'] == 'admin') {
                    header("Location: dashboards/admin.php?lang=" . ($language ?? 'en'));
                } else if ($_SESSION['user_type'] == 'nurse') {
                    header("Location: dashboards/nurse.php?lang=" . ($language ?? 'en'));
                } else if ($_SESSION['user_type'] == 'pharmacy') {
                    header("Location: dashboards/pharmacy.php?lang=" . ($language ?? 'en'));
                } else if ($_SESSION['user_type'] == 'analysis laboratory') {
                    header("Location: dashboards/laboratory.php?lang=" . ($language ?? 'en'));
                } else {
                    header("Location: index.php?lang=" . ($language ?? 'en'));
                }
                exit();
            }
        }
    }
} ?>

<html lang="<?php echo $language; ?>" dir="<?php echo $language === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صحتك</title>

    <?php if ($language === 'ar') { ?>
        <link rel="stylesheet" href="css/form-ar.css">
    <?php } else { ?>
        <link rel="stylesheet" href="css/form.css">
    <?php } ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!--<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">


    <style>
        body {
            margin: 0;
            background-color: rgb(227, 232, 234);
            /* Apply Cairo globally */
            font-family: 'Cairo', sans-serif !important;
        }

        .Information {
            font-family: 'Cairo', sans-serif !important;
            font-weight: 700 !important;
            /* Bold for the main title */
            font-size: 25px !important;
            color: rgb(0, 0, 0);
        }

        .text h5 {
            font-size: 14px !important;
            /* Increased from 12px for Arabic legibility */
            font-weight: 400 !important;
            /* Arabic needs at least 400 to look clear */
            color: rgba(0, 0, 0, 0.611);
        }

        .email h4,
        .password h4 {
            font-size: 15px !important;
            font-weight: 600 !important;
            /* Semi-bold for labels */
            color: rgba(0, 0, 0, 0.86);
        }

        button {
            font-family: 'Cairo', sans-serif !important;
            font-weight: 700 !important;
            font-size: 17px !important;
        }

        /* Fix for the small text at the bottom */
        .Forgot_password h6,
        .Forgot_password a,
        p,
        p a,
        .login h3 {
            font-family: 'Cairo', sans-serif !important;
            font-weight: 400 !important;
            font-size: 12px !important;
            /* 9px is too small for Arabic script */
        }
    </style>
</head>

<body>

    <?php if ($language === 'ar') { ?>

        <section class="form">
            <div class="logo">
                <a href="Form.html">
                    <img src="images/لوجو_صحتك.jpg" alt="لوجو صحتك">
                </a>
                <a href="https://mohesr.gov.eg/ar/" target="_blank">
                    <img src="images/لوجو_وزارة_التعليم_العالي.jpeg" alt="لوجو وزارة التعليم العالي">
                </a>
                <a href="https://metmans.edu.eg/" target="_blank">
                    <img src="images/لوجو_المعهد.jpg" alt="لوجو المعهد">
                </a>
            </div>
            <section class="data">
                <div class="item">
                    <form method="post" class="Information">
                        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
                        <div class="text">
                            <label>تسجيل دخول</label>
                            <h5>تسجيل الدخول الي حسابك في سيستم صحتك</h5>
                        </div>
                        <div class="email">
                            <h4>الايميل</h4>
                            <input name="email" type="email" placeholder="" required>
                        </div>
                        <div class="password">
                            <h4>الباسورد</h4>
                            <input name="password" type="password" placeholder="" required>
                        </div>
                        <div class="forget">
                            <div class="Forgot_Password">
                                <input type="checkbox">
                                <h6>تذكرني</h6>
                            </div>
                            <div class="Forgot_password">
                                <a href="#">نسيت كلمة المرور؟</a>
                            </div>
                        </div>
                        <button type="submit">تسجيل</button>
                        <div class="quest">
                            <p>ليس لديك حساب ؟ <a href="register.php?lang=<?php echo $language ?? 'en'; ?>" target="_blank">تسجيل حساب جديد</a></p>
                        </div>
                        <div class="login">
                            <h3>او تسجيل الدخول بواسطه</h3>
                        </div>
                        <div class="social-icons">
                            <a href="https://www.facebook.com/?locale=ar_AR" target="_blank" class="face"><i class="fa-brands fa-facebook"></i></a>
                            <a href="https://www.google.com/" target="_blank" class="goog"><i class="fa-brands fa-google"></i></a>
                            <a href="https://www.instagram.com/" target="_blank" class="insta"><i class="fa-brands fa-instagram"></i></a>
                        </div>
                    </form>
                </div>
                <div class="item">
                    <img src="images/doctor1.jpg" alt="صورة دكتور">
                </div>
            </section>
        </section>
    <?php } else { ?>
        <section class="form">
            <div class="logo">
                <a href="Form.html">
                    <img src="images/لوجو_صحتك.jpg" alt="لوجو صحتك">
                </a>
                <a href="https://mohesr.gov.eg/ar/" target="_blank">
                    <img src="images/لوجو_وزارة_التعليم_العالي.jpeg" alt="لوجو وزارة التعليم العالي">
                </a>
                <a href="https://metmans.edu.eg/" target="_blank">
                    <img src="images/لوجو_المعهد.jpg" alt="لوجو المعهد">
                </a>
            </div>
            <section class="data">
                <div class="item">
                    <form method="post" class="Information">
                        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
                        <div class="text">
                            <label>Login</label>
                            <h5>Login to your account in sehatak system</h5>
                        </div>
                        <div class="email">
                            <h4>Email</h4>
                            <input name="email" type="email" placeholder="" required>
                        </div>
                        <div class="password">
                            <h4>Password</h4>
                            <input name="password" type="password" placeholder="" required>
                        </div>
                        <div class="forget">
                            <div class="Forgot_Password">
                                <input type="checkbox">
                                <h6>Remember me</h6>
                            </div>
                            <div class="Forgot_password">
                                <a href="#">Forget your password?</a>
                            </div>
                        </div>
                        <button type="submit">Login</button>
                        <div class="quest">
                            <p>Don't have an account? <a href="register.php?lang=<?php echo $language ?? 'en'; ?>" target="_blank">Register new account</a></p>
                        </div>
                        <div class="login">
                            <h3>Or login with</h3>
                        </div>
                        <div class="social-icons">
                            <a href="https://www.facebook.com/?locale=ar_AR" target="_blank" class="face"><i class="fa-brands fa-facebook"></i></a>
                            <a href="https://www.google.com/" target="_blank" class="goog"><i class="fa-brands fa-google"></i></a>
                            <a href="https://www.instagram.com/" target="_blank" class="insta"><i class="fa-brands fa-instagram"></i></a>
                        </div>
                    </form>
                </div>
                <div class="item">
                    <img src="images/doctor1.jpg" alt="صورة دكتور">
                </div>
            </section>
        </section>
    <?php } ?>

</body>

</html>