<?php
include "includes/auth.php";

$language = "ar";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $language = $_GET['lang'];
}
?>

<html lang="<?php echo $language; ?>" dir="<?php echo $language === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sehatak - Home</title>
    <?php if ($language === 'ar') { ?>
        <link rel="stylesheet" href="css/hom-ar.css">
    <?php } else { ?>
        <link rel="stylesheet" href="css/hom.css">
    <?php } ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* The Professional Liquid Sweep Animation */
        .btn-sehatak {
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            z-index: 1;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-sehatak::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: all 0.6s;
            z-index: -1;
        }

        .btn-sehatak::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 0;
            background: #0ea5e9;
            /* Deep medical blue */
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            z-index: -2;
        }

        /* Hover States */
        .btn-sehatak:hover {
            color: white !important;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.4);
        }

        .btn-sehatak:hover::after {
            height: 100%;
        }

        .btn-sehatak:hover::before {
            left: 100%;
        }

        .btn-sehatak:active {
            transform: translateY(-1px);
        }

        /* Vision Button Variant (Softer Green for Wellness) */
        .btn-vision::after {
            background: #10b981;
        }

        .btn-vision:hover {
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
        }

        /* Floating Icon Animation */
        .icon-move {
            transition: transform 0.3s ease;
        }

        .btn-sehatak:hover .icon-move {
            transform: translateX(4px) rotate(10deg);
        }

        /* Background Decor */
        .blob {
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(14, 165, 233, 0.1);
            filter: blur(60px);
            border-radius: 50%;
            z-index: -1;
        }
    </style>
</head>

<body>
    <?php if ($language === 'ar') { ?>
        <header>
            <div class="logo"><!--<i class="fas fa-heartbeat"></i> صحتك -->
                <img width="64" src="images/new-logo.jpeg" alt="Sehatak Logo" />
            </div>
            <nav>
                <!--<a href="index.php">الرئيسية</a>
                <a href="services.php">الخدمات</a>
                <a href="#">الأطباء</a>-->

                <a class="btn-sehatak" href="sehatak-vision.html">رؤيتنا</a>
                <a class="btn-sehatak btn-vision" href="about.html">من نحن</a>

            </nav>

            <div class="auth-buttons" style="display: flex; align-items: center; gap: 15px;">

                <div class="lang-dropdown">
                    <button class="lang-btn">
                        <?php echo ($language == 'ar') ? 'العربية' : 'English'; ?>
                        <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-right: 5px;"></i>
                    </button>
                    <div class="lang-content">
                        <a href="?lang=ar">العربية</a>
                        <a href="?lang=en">English</a>
                    </div>
                </div>

                <?php if (isLoggedIn()): ?>
                    <p style="margin: 0; font-size: 0.9rem;">
                        مرحباً، <?php echo $_SESSION['user_name']; ?> | <?php echo $_SESSION['user_type']; ?>
                    </p>
                    <a href="logout.php?lang=<?php echo $language ?>" class="btn btn-logout">تسجيل الخروج</a>
                <?php else: ?>
                    <a href="#" class="btn btn-guest">تصفح الموقع</a>
                    <a href="login.php?lang=<?php echo $language ?>" class="btn btn-login">دخول المرضى</a>
                <?php endif; ?>
            </div>
        </header>

        <section class="hero">
            <h1>رعاية شاملة من أجل صحة أفضل</h1>
            <p>احصل على خدمات طبية عالمية المستوى وأنت في منزلك.</p>
        </section>

        <main class="services-section" id="services">
            <div class="section-title">
                <h2>خدماتنا المتخصصة</h2>
                <p>نقدم رعاية عالية الجودة في مختلف المجالات الطبية.</p>
            </div>

            <div class="services-grid">
                <div class="service-card" onclick="openService('doctors')">
                    <i class="fas fa-stethoscope"></i>
                    <h3>استشارات عامة</h3>
                    <p>فحوصات روتينية وخدمات تشخيصية لجميع الفئات العمرية.</p>
                </div>
                <div class="service-card" onclick="openService('pharmacies')">
                    <i class="fas fa-pills"></i>
                    <h3>خدمات الصيدلية</h3>
                    <p>تجديد سهل للوصفات الطبية وخيارات التوصيل للمنزل.</p>
                </div>
                <div class="service-card" onclick="openService('nurses')">
                    <i class="fas fa-user-nurse"></i>
                    <h3>خدمات الممرضين</h3>
                    <p>رعاية مهنية ودعم طبي في المنزل.</p>
                </div>
                <div class="service-card" onclick="openService('labs')">
                    <i class="fas fa-microscope"></i>
                    <h3>التقارير المخبرية</h3>
                    <p>احجز تحاليلك عبر الإنترنت واطلع على النتائج من خلال بوابة المريض.</p>
                </div>
            </div>

            <div class="portal-access">
                <div class="portal-box">
                    <h3><i class="fas fa-user-circle"></i> دخول الزوار</h3>
                    <p>هل أنت جديد هنا؟ يمكنك تصفح مكتبتنا الصحية، والتحقق من توفر الأطباء، ومعرفة مواقعنا دون الحاجة لإنشاء حساب.</p>
                </div>
                <div class="portal-box">
                    <h3><i class="fas fa-lock"></i> بوابة المريض</h3>
                    <p>قم بتسجيل الدخول لعرض تاريخك الطبي، وإدارة مواعيدك، ومراسلة طبيبك بشكل آمن.</p>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2026 صحتك للخدمات الطبية والرعاية الصحية. جميع الحقوق محفوظة.</p>
        </footer>
    <?php } else { ?>

        <header>
            <div class="logo">
                <!--<i class="fas fa-heartbeat"></i> Sehatak-->
                <img width="64" src="images/new-logo.jpeg" alt="Sehatak Logo" />
            </div>
            <nav>
                <!--<a href="index.php">Home</a>
                <a href="services.php">Services</a>
                <a href="#">Doctors</a>-->
                <a class="btn-sehatak btn-vision" href="sehatak-vision.html">Our Vision</a>
                <a class="btn-sehatak" href="about.html">About Us</a>
            </nav>
            <div class="auth-buttons" style="display: flex; align-items: center; gap: 15px;">

                <div class="lang-dropdown">
                    <button class="lang-btn">
                        <?php echo ($language == 'ar') ? ' العربية' : 'English'; ?>
                        <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-right: 5px;"></i>
                    </button>
                    <div class="lang-content">
                        <a href="?lang=ar">العربية</a>
                        <a href="?lang=en"> English</a>
                    </div>
                </div>

                <?php if (isLoggedIn()): ?>
                    <p> Hello, <?php echo $_SESSION['user_name']; ?> | <?php echo $_SESSION['user_email']; ?> | <?php echo $_SESSION['user_type']; ?> </p>
                    <a href="logout.php" class="btn btn-logout">Logout</a>
                <?php else: ?>
                    <a href="#" class="btn btn-guest">Browse Website</a>
                    <a href="login.php" class="btn btn-login">Patient Login</a>
                <?php endif; ?>
            </div>
        </header>

        <section class="hero">
            <h1>Comprehensive Care for a Healthier You</h1>
            <p>Access world-class medical services from the comfort of your home.</p>
        </section>

        <main class="services-section" id="services">
            <div class="section-title">
                <h2>Our Specialized Services</h2>
                <p>Providing high-quality care across various medical fields.</p>
            </div>

            <div class="services-grid">
                <div class="service-card" onclick="openService('doctors')">
                    <i class="fas fa-stethoscope"></i>
                    <h3>General Consultation</h3>
                    <p>Routine check-ups and diagnostic services for all age groups.</p>
                </div>
                <div class="service-card" onclick="openService('pharmacies')">
                    <i class="fas fa-pills"></i>
                    <h3>Pharmacy Services</h3>
                    <p>Easy prescription refills and home delivery options.</p>
                </div>
                <div class="service-card" onclick="openService('nurses')">
                    <i class="fas fa-user-md"></i>
                    <h3>Specialist Access</h3>
                    <p>Direct access to Cardiologists, Neurologists, and more.</p>
                </div>
                <div class="service-card" onclick="openService('labs')">
                    <i class="fas fa-microscope"></i>
                    <h3>Lab Reports</h3>
                    <p>Book tests online and view your results in the patient portal.</p>
                </div>
            </div>

            <div class="portal-access">
                <div class="portal-box">
                    <h3><i class="fas fa-user-circle"></i> Guest Access</h3>
                    <p>New here? You can still browse our health library, check doctor availability, and view our locations without an account.</p>
                </div>
                <div class="portal-box">
                    <h3><i class="fas fa-lock"></i> Patient Portal</h3>
                    <p>Login to view your medical history, manage appointments, and message your doctor securely.</p>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2026 Sehatak for HealthCare Medical Services. All rights reserved.</p>
        </footer>

    <?php } ?>

    <script>
        function openService(service) {
            if (service === 'doctors') {
                window.location.href = 'services/doctors.php?lang=<?php echo $language; ?>';
            } else if (service === 'pharmacies') {
                window.location.href = 'services/pharmacies.php?lang=<?php echo $language; ?>';
            } else if (service === 'nurses') {
                window.location.href = 'services/nurses.php?lang=<?php echo $language; ?>';
            } else if (service === 'labs') {
                window.location.href = 'services/labs.php?lang=<?php echo $language; ?>';
            }
        }
    </script>

</body>

</html>