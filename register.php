<?php

include "includes/auth.php";
include "includes/database.php";

$language = "en";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $language = $_GET['lang'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $passwordConfirm = $_POST['passwordConfirm'];
    //$userType = $_POST['userType'];
    $userType = 'patient';

    if (!isset($email)) {
        $error = "Email is required";
    }

    if (!isset($password)) {
        $error = "password is required";
    }

    if (!isset($passwordConfirm)) {
        $error = "password confirm is required";
    }

    if (isset($password) && isset($passwordConfirm) && $password != $passwordConfirm) {
        $error = "password != confirm password";
    }

    if (!isset($userType)) {
        $error = "user type is required";
    }

    if (!isset($error)) {
        // status => 'active','inactive','suspended','deleted','archived','pending','blocked'

        $stmt = $pdo->prepare("INSERT INTO user (name, email, address, password, account_type, account_status) VALUES (?, ?, ?, ?, ?, ? )");
        $result = $stmt->execute([
            $firstName . " " . $lastName,
            $email,
            $address,
            $password,
            $userType,
            'active'
        ]);

        if ($result) {

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $firstName . " " . $lastName;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_status'] = 'active';
            $_SESSION['user_type'] = $userType;
            header("Location: index.php");
            exit();
        } else {
            $error = "Failed to create account";
        }
    }
} ?>

<html lang="<?php echo $language; ?>" dir="<?php echo $language === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صحتك</title>

    <?php if ($language === 'ar') { ?>
        <link rel="stylesheet" href="css/signup-ar.css">
    <?php } else { ?>
        <link rel="stylesheet" href="css/signup.css">
    <?php } ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
        .name h4,
        .password h4,
        .confirm_password h4,
        .account-box h3 {
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
        .quest p,
        .quest a,
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
                <a href="https://metmans.edu.eg/" target="_blank">
                    <img src="images/لوجو_المعهد.jpg" alt="لوجو المعهد">
                </a>
                <a href="https://mohesr.gov.eg/ar/" target="_blank">
                    <img src="images/لوجو_وزارة_التعليم_العالي.jpeg" alt="لوجو وزارة التعليم العالي">
                </a>
                <a href="signup.html">
                    <img src="images/لوجو_صحتك.jpg" alt="لوجو صحتك">
                </a>
            </div>
            <section class="data">
                <div class="item">
                    <img src="images/doctor1.jpg" alt="صورة دكتور">
                </div>
                <div class="item">
                    <form class="Information" method="post">
                        <div class="text">
                            <label>تسجيل حساب جديد</label>
                            <h5>لدينا بعض المعلومات المطلوبة لتسجيل حسابك.</h5>
                        </div>
                        <div class="signup1">
                            <div class="name">
                                <h4>الاسم الأول</h4>
                                <input name="firstName" type="text" required>
                            </div>
                            <div class="name">
                                <h4>الاسم الأخير</h4>
                                <input name="lastName" type="text" required>
                            </div>
                        </div>
                        <div class="signup2">
                            <div class="email">
                                <h4>الأيميل</h4>
                                <input name="email" type="email" placeholder="" required>
                            </div>
                            <div class="email">
                                <h4>العنوان</h4>
                                <input name="address" type="text" placeholder="" required>
                            </div>
                        </div>
                        <div class="password">
                            <h4>الباسورد</h4>
                            <input name="password" type="password" placeholder="">
                        </div>
                        <div class="confirm_password">
                            <h4>تأكيد الباسورد</h4>
                            <input name="passwordConfirm" type="password" placeholder="">
                        </div>
                        <?php /*
                        <div class="account-box">
                            <h3>نوع الحساب</h3>
                            <select name="userType">
                                <option value="" disabled selected></option>
                                <option value="doctor">طبيب</option>
                                <option value="nurse">ممرض</option>
                                <option value="pharmacy">صيدليه</option>
                                <option value="analysis laboratory">معمل تحاليل</option>
                                <option value="patient">مريض</option>
                            </select>
                        </div>*/ ?>
                        <div class="Privacy_Policies">
                            <input name="privacy" type="checkbox">
                            <h6>موافق علي شروط <a href="#">شروط الاستخدام</a> و <a href="#">الخصوصيه </a></h6>
                        </div>
                        <button type="submit">تسجيل الحساب</button>
                        <div class="quest">
                            <p>لديك حساب مسجل بالفعل ؟ <a href="login.php?lang=<?php echo $language ?? 'en'; ?>" target="_blank">تسجيل دخول</a></p>
                        </div>
                        <div class="login">
                            <h3>او تسجيل حساب بواسطه</h3>
                        </div>
                        <div class="social-icons">
                            <a href="https://www.facebook.com/?locale=ar_AR" target="_blank" class="face"><i class="fa-brands fa-facebook"></i></a>
                            <a href="https://www.google.com/" target="_blank" class="goog"><i class="fa-brands fa-google"></i></a>
                            <a href="https://www.instagram.com/" target="_blank" class="insta"><i class="fa-brands fa-instagram"></i></a>
                        </div>
                    </form>
                </div>
            </section>
        </section>
    <?php } else { ?>

        <section class="form">
            <div class="logo">
                <a href="https://metmans.edu.eg/" target="_blank">
                    <img src="images/لوجو_المعهد.jpg" alt="لوجو المعهد">
                </a>
                <a href="https://mohesr.gov.eg/ar/" target="_blank">
                    <img src="images/لوجو_وزارة_التعليم_العالي.jpeg" alt="لوجو وزارة التعليم العالي">
                </a>
                <a href="signup.html">
                    <img src="images/لوجو_صحتك.jpg" alt="لوجو صحتك">
                </a>
            </div>
            <section class="data">
                <div class="item">
                    <img src="images/doctor1.jpg" alt="صورة دكتور">
                </div>
                <div class="item">
                    <form class="Information" method="post">
                        <div class="text">
                            <label>Sign up</label>
                            <h5>let's get you all stup so you can access your account.</h5>
                        </div>
                        <div class="signup1">
                            <div class="name">
                                <h4>First Name</h4>
                                <input name="firstName" type="text" required>
                            </div>
                            <div class="name">
                                <h4>Last Name</h4>
                                <input name="lastName" type="text" required>
                            </div>
                        </div>
                        <div class="signup2">
                            <div class="email">
                                <h4>Email</h4>
                                <input name="email" type="email" placeholder="" required>
                            </div>
                            <div class="email">
                                <h4>Address</h4>
                                <input name="address" type="text" placeholder="" required>
                            </div>
                        </div>
                        <div class="password">
                            <h4>Password</h4>
                            <input name="password" type="password" placeholder="">
                        </div>
                        <div class="confirm_password">
                            <h4>Confirm Password</h4>
                            <input name="passwordConfirm" type="password" placeholder="">
                        </div>
                        <div class="account-box">
                            <h3>Account type</h3>
                            <select name="userType">
                                <option value="" disabled selected></option>
                                <option value="doctor">Doctor</option>
                                <option value="nurse">Nurse</option>
                                <option value="pharmacy">pharmacy</option>
                                <option value="analysis laboratory">Analysis laboratory</option>
                                <option value="patient">Patient</option>
                            </select>
                        </div>
                        <div class="Privacy_Policies">
                            <input name="privacy" type="checkbox">
                            <h6>I agree to all the <a href="#">Terms</a> and <a href="#">Privacy Policies</a></h6>
                        </div>
                        <button type="submit">Create account</button>
                        <div class="quest">
                            <p>Already have an account? <a href="login.php?lang=<?php echo $language ?? 'en'; ?>" target="_blank">Login</a></p>
                        </div>
                        <div class="login">
                            <h3>Or sign up with</h3>
                        </div>
                        <div class="social-icons">
                            <a href="https://www.facebook.com/?locale=ar_AR" target="_blank" class="face"><i class="fa-brands fa-facebook"></i></a>
                            <a href="https://www.google.com/" target="_blank" class="goog"><i class="fa-brands fa-google"></i></a>
                            <a href="https://www.instagram.com/" target="_blank" class="insta"><i class="fa-brands fa-instagram"></i></a>
                        </div>
                    </form>
                </div>
            </section>
        </section>
    <?php } ?>
</body>

</html>