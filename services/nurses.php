<?php
include "../includes/auth.php";

$language = "ar";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $language = $_GET['lang'];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $language; ?>" dir="<?php echo $language === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <!-- إعدادات متجاوبة مع جميع الشاشات -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خدمات التمريض - موقع صحتك</title>
    <?php if ($language === 'ar') { ?>
        <link rel="stylesheet" href="../css/hom-ar.css">

        <link rel="stylesheet" href="../css/services/nurses-ar.css">
    <?php } else { ?>
        <link rel="stylesheet" href="../css/hom.css">

        <link rel="stylesheet" href="../css/services/nurses.css">
    <?php } ?>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body id="mainBody">
    <!-- الصفحة الرئيسية -->

    <div id="homePage">
        <header>
            <div class="logo"><i class="fas fa-heartbeat"></i><a href="../index.php">الصفحه الرئيسيه</a> </div>
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="ابحث عن الممرض..">
            </div>


            <div class="auth-buttons" style="display: flex; align-items: center; gap: 15px;">

                <div class="lang-dropdown">
                    <button class="lang-btn">
                        <?php echo ($language == 'ar') ? '🇪🇬 العربية' : '🇬🇧 English'; ?>
                        <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-right: 5px;"></i>
                    </button>
                    <div class="lang-content">
                        <a href="?lang=ar">🇪🇬 العربية</a>
                        <a href="?lang=en">🇬🇧 English</a>
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


        <!-- المحتوى الرئيسي للصفحة -->
        <main>
            <div class="hero">
                <div class="icon-circle">🩺</div>
                <h1>خدمات التمريض المنزلي</h1>
                <p>ممرضين محترفين لخدمتك في المنزل</p>
            </div>

            <!-- أزرار التصفية حسب التخصص -->
            <section class="filters">
                <h3>تصفية حسب التخصص</h3>
                <div class="filter-buttons">
                    <button class="filter-btn active" onclick="filterSelection('all', this)">جميع الممرضين</button>
                    <button class="filter-btn" onclick="filterSelection('اطفال', this)">تمريض الأطفال</button>
                    <button class="filter-btn" onclick="filterSelection('حالات_حرجة', this)">حالات حرجة</button>
                    <button class="filter-btn" onclick="filterSelection('عام', this)">تمريض منزلي عام</button>
                    <button class="filter-btn" onclick="filterSelection('الامومة و الطفولة', this)">رعاية الامومة و الطفولة</button>
                    <button class="filter-btn" onclick="filterSelection('كبار', this)">رعاية كبار السن</button>
                    <button class="filter-btn" onclick="filterSelection('العمليات', this)">رعاية ما بعد العمليات</button>
                    <button class="filter-btn" onclick="filterSelection('السكري', this)">رعاية مرضي السكري</button>
                </div>
            </section>

            <!-- شبكة بطاقات الممرضين -->
            <div class="nurse-grid" id="nurseGrid">
                <!-- بطاقة 1: فاطمة احمد حسن -->
                <div class="nurse-card" data-type="عام" data-name="فاطمة احمد حسن">
                    <div class="image-side">
                        <img src="../images/nurse-female.jpeg" alt="ممرضة" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. فاطمة احمد حسن</h3>
                        <p class="spec">تمريض منزلي عام</p>
                        <p class="rating">⭐ 4.9 <span class="rev-count">(156 تقييم)</span></p>
                        <p class="meta">📍 القاهرة، مصر الجديدة | 🕒 8 سنوات</p>
                        <p class="price-status">● متاح الآن <span class="price">80 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('فاطمة احمد حسن')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('فاطمة احمد حسن')">عرض الملف</button>
                    </div>
                </div>

                <!-- بطاقة 2: محمد سعيد علي -->
                <div class="nurse-card" data-type="كبار" data-name="محمد سعيد علي">
                    <div class="image-side">
                        <img src="../images/nurse-male.jpeg" alt="ممرض" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. محمد سعيد علي</h3>
                        <p class="spec">رعاية كبار السن</p>
                        <p class="rating">⭐ 4.9 <span class="rev-count">(203 تقييم)</span></p>
                        <p class="meta">📍 الإسكندرية، سموحة | 🕒 12 سنة</p>
                        <p class="price-status">● متاح الآن <span class="price">85 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('محمد سعيد علي')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('محمد سعيد علي')">عرض الملف</button>
                    </div>
                </div>

                <!-- بطاقة 3: نورا خالد ابراهيم -->
                <div class="nurse-card" data-type="اطفال" data-name="نورا خالد ابراهيم">
                    <div class="image-side">
                        <img src="../images/nurse-female.jpeg" alt="ممرضة" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. نورا خالد ابراهيم</h3>
                        <p class="spec">تمريض الأطفال</p>
                        <p class="rating">⭐ 4.9 <span class="rev-count">(178 تقييم)</span></p>
                        <p class="meta">📍 الجيزة، المهندسين | 🕒 10 سنوات</p>
                        <p class="price-status">● متاح الآن <span class="price">85 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('نورا خالد ابراهيم')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('نورا خالد ابراهيم')">عرض الملف</button>
                    </div>
                </div>

                <!-- بطاقة 4: احمد محمود حسين -->
                <div class="nurse-card" data-type="العمليات" data-name="احمد محمود حسين">
                    <div class="image-side">
                        <img src="../images/nurse-male.jpeg" alt="ممرض" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. احمد محمود حسين</h3>
                        <p class="spec">رعاية ما بعد العمليات</p>
                        <p class="rating">⭐ 4.7 <span class="rev-count">(134 تقييم)</span></p>
                        <p class="meta">📍 القاهرة، مدينة نصر | 🕒 15 سنة</p>
                        <p class="price-status">● متاح الآن <span class="price">100 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('احمد محمود حسين')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('احمد محمود حسين')">عرض الملف</button>
                    </div>
                </div>

                <!-- بطاقة 5: مريم يوسف عبد الله -->
                <div class="nurse-card" data-type="حالات_حرجة" data-name="مريم يوسف عبد الله">
                    <div class="image-side">
                        <img src="../images/nurse-female.jpeg" alt="ممرضة" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. مريم يوسف عبد الله</h3>
                        <p class="spec">تمريض الحالات الحرجة</p>
                        <p class="rating">⭐ 4.9 <span class="rev-count">(156 تقييم)</span></p>
                        <p class="meta">📍 القاهرة، مدينة نصر | 🕒 10 سنوات</p>
                        <p class="price-status">● متاح الآن <span class="price">120 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('مريم يوسف عبد الله')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('مريم يوسف عبد الله')">عرض الملف</button>
                    </div>
                </div>

                <!-- بطاقة 6: عمر طارق فتحي -->
                <div class="nurse-card" data-type="عام" data-name="عمر طارق فتحي">
                    <div class="image-side">
                        <img src="../images/nurse-male.jpeg" alt="ممرض" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. عمر طارق فتحي</h3>
                        <p class="spec">تمريض منزلي عام</p>
                        <p class="rating">⭐ 4.6 <span class="rev-count">(112 تقييم)</span></p>
                        <p class="meta">📍 الإسكندرية، ميامي | 🕒 7 سنوات</p>
                        <p class="price-status">● متاح الآن <span class="price">75 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('عمر طارق فتحي')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('عمر طارق فتحي')">عرض الملف</button>
                    </div>
                </div>

                <!-- بطاقة 7: هند رمضان صالح -->
                <div class="nurse-card" data-type="الامومة و الطفولة" data-name="هند رمضان صالح">
                    <div class="image-side">
                        <img src="../images/nurse-female.jpeg" alt="ممرضة" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. هند رمضان صالح</h3>
                        <p class="spec">رعاية الامومة و الطفولة</p>
                        <p class="rating">⭐ 4.8 <span class="rev-count">(167 تقييم)</span></p>
                        <p class="meta">📍 القاهرة، الزمالك | 🕒 11 سنة</p>
                        <p class="price-status">● متاح الآن <span class="price">95 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('هند رمضان صالح')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('هند رمضان صالح')">عرض الملف</button>
                    </div>
                </div>
                <!--بطاقة 8: سارة محمد عبداللة -->
                <div class="nurse-card" data-type="السكري" data-name="سارة محمد عبداللة">
                    <div class="image-side">
                        <img src="../images/nurse-female.jpeg" alt="ممرضة" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. سارة محمد عبداللة</h3>
                        <p class="spec">رعاية الامومة و الطفولة</p>
                        <p class="rating">⭐ 4.8 <span class="rev-count">(98 تقييم)</span></p>
                        <p class="meta">📍 الجيزة، الدقي | 🕒 5 سنة</p>
                        <p class="price-status">● متاح الآن <span class="price">90 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('سارة محمد عبداللة')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('سارة محمد عبداللة')">عرض الملف</button>
                    </div>
                </div>

                <!-- بطاقة 9:احمد خالد عمر -->
                <div class="nurse-card" data-type="اطفال" data-name="احمد خالد عمر">
                    <div class="image-side">
                        <img src="../images/nurse-male.jpeg" alt="ممرض" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. احمد خالد عمر</h3>
                        <p class="spec">تمريض اطفال</p>
                        <p class="rating">⭐ 4.7 <span class="rev-count">(145 تقييم)</span></p>
                        <p class="meta">📍 القاهرة، مدينة نصر | 🕒 10 سنوات</p>
                        <p class="price-status">● متاح الآن <span class="price">95 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('احمد خالد عمر')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('احمد خالد عمر')">عرض الملف</button>
                    </div>
                </div>
                <!-- بطاقة 10: عمر مصطفي السيد-->
                <div class="nurse-card" data-type="الامومة و الطفولة" data-name="عمر مصطفي السيد">
                    <div class="image-side">
                        <img src="../images/nurse-male.jpeg" alt="ممرض" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. عمر مصطفي السيد</h3>
                        <p class="spec">رعاية الامومة و الطفولة</p>
                        <p class="rating">⭐ 4.7 <span class="rev-count">(112 تقييم)</span></p>
                        <p class="meta">📍 القاهرة، المعادي | 🕒 9 سنوات</p>
                        <p class="price-status">● متاح الآن <span class="price">90 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('عمر مصطفي السيد')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('عمر مصطفي السيد')">عرض الملف</button>
                    </div>
                </div>
                <!--بطاقة 11: اية كمال عبدالرحمن -->
                <div class="nurse-card" data-type="حالات_حرجة" data-name="اية كمال عبدالرحمن">
                    <div class="image-side">
                        <img src="../images/nurse-female.jpeg" alt="ممرضة" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. اية كمال عبدالرحمن</h3>
                        <p class="spec">حالات حرجة</p>
                        <p class="rating">⭐ 4.8 <span class="rev-count">(87 تقييم)</span></p>
                        <p class="meta">📍 الجيزة، فيصل | 🕒 6 سنة</p>
                        <p class="price-status">● متاح الآن <span class="price">100 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('اية كمال عبدالرحمن')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('اية كمال عبدالرحمن')">عرض الملف</button>
                    </div>
                </div>
                <!-- بطاقة 12: كريم عادل محمد -->
                <div class="nurse-card" data-type="السكري" data-name="كريم عادل محمد">
                    <div class="image-side">
                        <img src="../images/nurse-male.jpeg" alt="ممرض" class="nurse-img">
                    </div>
                    <div class="details-side">
                        <h3>م. كريم عادل محمد</h3>
                        <p class="spec">رعاية مرضي السكري</p>
                        <p class="rating">⭐ 4.7 <span class="rev-count">(145 تقييم)</span></p>
                        <p class="meta">📍 المنصورة، الجلاء | 🕒 9 سنوات</p>
                        <p class="price-status">● متاح الآن <span class="price">85 جنيه</span></p>
                    </div>
                    <div class="actions-side">
                        <button class="btn-book" onclick="bookNow('كريم عادل محمد')">احجز الآن</button>
                        <button class="btn-profile" onclick="viewProfile('كريم عادل محمد')">عرض الملف</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- صفحة الملف الشخصي (مخفية في البداية) -->
    <div id="profilePage" style="display: none;">
        <div class="profile-container" id="profileContainer"></div>
    </div>

    <!-- بداية بوت المحادثة الذكي  -->
    <div class="chatbot-container" id="chatbotContainer">
        <!-- أيقونة البوت مع علامة الإشعار (Badge) -->
        <div class="chat-icon" id="chatIcon" onclick="toggleChat()">
            🤖
            <span class="badge" id="chatBadge">1</span>
        </div>
        <!-- نافذة المحادثة -->
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <span>المساعد التمريضي</span>
                <button class="close-chat" onclick="toggleChat()">✖</button>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="message bot-message">مرحباً! أنا مساعدك للخدمات التمريضية. كيف يمكنني مساعدتك اليوم؟</div>
            </div>
            <div class="chat-input-area">
                <input type="text" id="chatInput" placeholder="اكتب رسالتك..." onkeypress="if(event.key==='Enter') sendMessage()">
                <button onclick="sendMessage()">إرسال</button>
            </div>
        </div>
    </div>
    <!-- نهاية البوت-->

    <div id="invoiceModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-modal" onclick="closeInvoice()">&times;</span>
            <div id="invoiceDetails"></div>
        </div>
    </div>

    <!-- كود الجافا سكريبت (الوظائف الأساسية + البوت) -->
    <script>
        // ----- بيانات الممرضين الكاملة -----
        const nursesData = {
            'فاطمة احمد حسن': {
                name: 'م. فاطمة احمد حسن',
                specialty: 'تمريض منزلي عام',
                phone: '+20 112 345 6789',
                location: 'القاهرة، مصر الجديدة',
                price: '80 جنيه',
                rating: '4.9 (156 تقييم)',
                experience: '8 سنوات',
                visitDate: 'الاثنين 15 مارس 2026',
                image: '../images/nurse-female.jpeg',
                schedule: [{
                        day: 'الأحد',
                        date: '14 مارس',
                        time: '9 ص - 3 م'
                    },
                    {
                        day: 'الإثنين',
                        date: '15 مارس',
                        time: '4 م - 10 م'
                    },
                    {
                        day: 'الأربعاء',
                        date: '17 مارس',
                        time: '9 ص - 3 م'
                    },
                    {
                        day: 'الخميس',
                        date: '18 مارس',
                        time: '4 م - 10 م'
                    }
                ]
            },
            'محمد سعيد علي': {
                name: 'م. محمد سعيد علي',
                specialty: 'رعاية كبار السن',
                phone: '+20 115 678 1234',
                location: 'الإسكندرية، سموحة',
                price: '85 جنيه',
                rating: '4.9 (203 تقييم)',
                experience: '12 سنة',
                visitDate: 'السبت 20 مارس 2026',
                image: '../images/nurse-male.jpeg',
                schedule: [{
                        day: 'السبت',
                        date: '20 مارس',
                        time: '10 ص - 4 م'
                    },
                    {
                        day: 'الإثنين',
                        date: '22 مارس',
                        time: '10 ص - 4 م'
                    },
                    {
                        day: 'الأربعاء',
                        date: '24 مارس',
                        time: '5 م - 11 م'
                    },
                    {
                        day: 'الجمعة',
                        date: '26 مارس',
                        time: '2 م - 8 م'
                    }
                ]
            },
            'نورا خالد ابراهيم': {
                name: 'م. نورا خالد ابراهيم',
                specialty: 'تمريض الأطفال',
                phone: '+20 106 543 2109',
                location: 'الجيزة، المهندسين',
                price: '85 جنيه',
                rating: '4.9 (178 تقييم)',
                experience: '10 سنوات',
                visitDate: 'الأحد 21 مارس 2026',
                image: '../images/nurse-female.jpeg',
                schedule: [{
                        day: 'الأحد',
                        date: '21 مارس',
                        time: '8 ص - 2 م'
                    },
                    {
                        day: 'الثلاثاء',
                        date: '23 مارس',
                        time: '8 ص - 2 م'
                    },
                    {
                        day: 'الخميس',
                        date: '25 مارس',
                        time: '3 م - 9 م'
                    },
                    {
                        day: 'السبت',
                        date: '27 مارس',
                        time: '10 ص - 4 م'
                    }
                ]
            },
            'احمد محمود حسين': {
                name: 'م. احمد محمود حسين',
                specialty: 'رعاية ما بعد العمليات',
                phone: '+20 122 987 6543',
                location: 'القاهرة، مدينة نصر',
                price: '100 جنيه',
                rating: '4.7 (134 تقييم)',
                experience: '15 سنة',
                visitDate: 'الأحد 14 مارس 2026',
                image: '../images/nurse-male.jpeg',
                schedule: [{
                        day: 'الأحد',
                        date: '14 مارس',
                        time: '10 ص - 6 م'
                    },
                    {
                        day: 'الثلاثاء',
                        date: '16 مارس',
                        time: '10 ص - 6 م'
                    },
                    {
                        day: 'الخميس',
                        date: '18 مارس',
                        time: '10 ص - 6 م'
                    },
                    {
                        day: 'الجمعة',
                        date: '19 مارس',
                        time: '2 م - 8 م'
                    }
                ]
            },
            'مريم يوسف عبد الله': {
                name: 'م. مريم يوسف عبد الله',
                specialty: 'تمريض الحالات الحرجة',
                phone: '+20 127 456 7890',
                location: 'القاهرة، مدينة نصر',
                price: '120 جنيه',
                rating: '4.9 (156 تقييم)',
                experience: '10 سنوات',
                visitDate: 'الإثنين 22 مارس 2026',
                image: '../images/nurse-female.jpeg',
                schedule: [{
                        day: 'الإثنين',
                        date: '22 مارس',
                        time: '9 ص - 5 م'
                    },
                    {
                        day: 'الأربعاء',
                        date: '24 مارس',
                        time: '9 ص - 5 م'
                    },
                    {
                        day: 'الخميس',
                        date: '25 مارس',
                        time: '4 م - 10 م'
                    },
                    {
                        day: 'السبت',
                        date: '27 مارس',
                        time: '11 ص - 5 م'
                    }
                ]
            },
            'عمر طارق فتحي': {
                name: 'م. عمر طارق فتحي',
                specialty: 'تمريض منزلي عام',
                phone: '+20 114 789 0123',
                location: 'الإسكندرية، ميامي',
                price: '75 جنيه',
                rating: '4.6 (112 تقييم)',
                experience: '7 سنوات',
                visitDate: 'الأحد 14 مارس 2026',
                image: '../images/nurse-male.jpeg',
                schedule: [{
                        day: 'الأحد',
                        date: '14 مارس',
                        time: '9 ص - 3 م'
                    },
                    {
                        day: 'الثلاثاء',
                        date: '16 مارس',
                        time: '4 م - 10 م'
                    },
                    {
                        day: 'الخميس',
                        date: '18 مارس',
                        time: '9 ص - 3 م'
                    },
                    {
                        day: 'الجمعة',
                        date: '19 مارس',
                        time: '12 م - 6 م'
                    }
                ]
            },
            'هند رمضان صالح': {
                name: 'م. هند رمضان صالح',
                specialty: 'رعاية الامومة و الطفولة',
                phone: '+20 128 345 6789',
                location: 'القاهرة، الزمالك',
                price: '95 جنيه',
                rating: '4.8 (167 تقييم)',
                experience: '11 سنة',
                visitDate: 'السبت 20 مارس 2026',
                image: '../images/nurse-female.jpeg',
                schedule: [{
                        day: 'السبت',
                        date: '20 مارس',
                        time: '9 ص - 3 م'
                    },
                    {
                        day: 'الإثنين',
                        date: '22 مارس',
                        time: '9 ص - 3 م'
                    },
                    {
                        day: 'الأربعاء',
                        date: '24 مارس',
                        time: '4 م - 10 م'
                    },
                    {
                        day: 'الخميس',
                        date: '25 مارس',
                        time: '4 م - 10 م'
                    }
                ]
            },
            'سارة محمد عبداللة': {
                name: 'م.سارة محمد عبداللة',
                specialty: 'رعاية الامومة و الطفولة',
                phone: '+20 128 345 6789',
                location: 'الجيزة، الدقي',
                price: '90 جنيه',
                rating: '4.8 (98 تقييم)',
                experience: '5 سنة',
                visitDate: 'السبت 20 مارس 2026',
                image: '../images/nurse-female.jpeg',
                schedule: [{
                        day: 'السبت',
                        date: '20 مارس',
                        time: '9 ص - 3 م'
                    },
                    {
                        day: 'الإثنين',
                        date: '22 مارس',
                        time: '9 ص - 3 م'
                    },
                    {
                        day: 'الأربعاء',
                        date: '24 مارس',
                        time: '4 م - 10 م'
                    },
                    {
                        day: 'الخميس',
                        date: '25 مارس',
                        time: '4 م - 10 م'
                    }
                ]
            },
            'احمد خالد عمر': {
                name: 'م.احمد خالد عمر',
                specialty: 'تمريض الأطفال',
                phone: '+20 106 543 2109',
                location: 'القاهرة، مدينة نصر',
                price: '95 جنيه',
                rating: '4.9 (145 تقييم)',
                experience: '10 سنوات',
                visitDate: 'الأحد 21 مارس 2026',
                image: '../images/nurse-male.jpeg',
                schedule: [{
                        day: 'الأحد',
                        date: '21 مارس',
                        time: '8 ص - 2 م'
                    },
                    {
                        day: 'الثلاثاء',
                        date: '23 مارس',
                        time: '8 ص - 2 م'
                    },
                    {
                        day: 'الخميس',
                        date: '25 مارس',
                        time: '3 م - 9 م'
                    },
                    {
                        day: 'السبت',
                        date: '27 مارس',
                        time: '10 ص - 4 م'
                    }
                ]
            },
            'عمر مصطفي خالد': {
                name: 'م.عمر مصطفي خالد',
                specialty: 'رعاية الامومة و الطفولة',
                phone: '+20 128 345 6789',
                location: 'القاهرة، المعادي',
                price: '90 جنيه',
                rating: '4.8 (98 تقييم)',
                experience: '9 سنة',
                visitDate: 'السبت 20 مارس 2026',
                image: '../images/nurse-male.jpeg',
                schedule: [{
                        day: 'السبت',
                        date: '20 مارس',
                        time: '9 ص - 3 م'
                    },
                    {
                        day: 'الإثنين',
                        date: '22 مارس',
                        time: '9 ص - 3 م'
                    },
                    {
                        day: 'الأربعاء',
                        date: '24 مارس',
                        time: '4 م - 10 م'
                    },
                    {
                        day: 'الخميس',
                        date: '25 مارس',
                        time: '4 م - 10 م'
                    }
                ]
            },
            'اية كمال عبدالرحمن': {
                name: 'م. اية كمال عبدالرحمن',
                specialty: 'تمريض الحالات الحرجة',
                phone: '+20 127 456 7890',
                location: 'الجيزة،  فيصل',
                price: '100 جنيه',
                rating: '4.9 (87 تقييم)',
                experience: '6 سنوات',
                visitDate: 'الإثنين 22 مارس 2026',
                image: '../images/nurse-female.jpeg',
                schedule: [{
                        day: 'الإثنين',
                        date: '22 مارس',
                        time: '9 ص - 5 م'
                    },
                    {
                        day: 'الأربعاء',
                        date: '24 مارس',
                        time: '9 ص - 5 م'
                    },
                    {
                        day: 'الخميس',
                        date: '25 مارس',
                        time: '4 م - 10 م'
                    },
                    {
                        day: 'السبت',
                        date: '27 مارس',
                        time: '11 ص - 5 م'
                    }
                ]
            },
            'كريم عادل محمد': {
                name: 'م. كريم عادل محمد',
                specialty: 'رعاية مرضي السكري',
                phone: '+20 110 987 6543',
                location: 'المنصورة، الجلاء',
                price: '85 جنيه',
                rating: '4.7 (145 تقييم)',
                experience: '9 سنوات',
                visitDate: 'الأحد 21 مارس 2026',
                image: '../images/nurse-male.jpeg',
                schedule: [{
                        day: 'الأحد',
                        date: '21 مارس',
                        time: '10 ص - 4 م'
                    },
                    {
                        day: 'الثلاثاء',
                        date: '23 مارس',
                        time: '5 م - 9 م'
                    },
                    {
                        day: 'الأربعاء',
                        date: '24 مارس',
                        time: '10 ص - 4 م'
                    },
                    {
                        day: 'الخميس',
                        date: '25 مارس',
                        time: '5 م - 9 م'
                    }
                ]
            }
        };

        // ----- دالة العودة للصفحة الرئيسية -----
        function goHome() {
            document.getElementById('profilePage').style.display = 'none';
            document.getElementById('homePage').style.display = 'block';
            document.getElementById('mainBody').classList.remove('profile-mode');
        }

        // ----- دالة الحجز (محاكاة) -----
        function bookNow(name) {
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != null && $_SESSION['user_id'] != '') { ?>
                //alert("تم إرسال طلب الحجز للممرض(ة) " + name + " بنجاح! سيتواصل معك فريق التمريض فوراً.");
                const nurse = nursesData[name];
                if (!nurse) return;

                const invoiceHTML = `
                    <div class="invoice-icon">🧾</div>
                    <h2>تأكيد الحجز</h2>
                    <div class="doctor-name">${nurse.name}</div>
                    <div class="visit-date">📅 تاريخ الزيارة: ${nurse.visitDate}</div>
                    <div class="visit-time">🕒 الساعة: ${nurse.visitTime}</div>
                    <div class="confirm-msg">✅ تم تأكيد حجزك بنجاح 😊</div>
                    <div class="payment-note">
                        <strong>💰 الدفع:</strong> سيتم دفع قيمة الكشف نقداً أو ببطاقة الصراف <strong>عند موعد الكشف</strong>.
                    </div>
                    <div class="invoice-footer">
                        <p>رقم الحجز: #${Math.floor(Math.random() * 100000)}</p>
                        <p>شكراً لثقتك في موقع صحتك</p>
                    </div>
                    <button class="btn-close-invoice" onclick="closeInvoice()">إغلاق</button>
                `;
                document.getElementById('invoiceDetails').innerHTML = invoiceHTML;
                document.getElementById('invoiceModal').style.display = 'flex';
            <?php } else { ?>
                alert("يرجى تسجيل الدخول أولاً لإتمام الحجز.");
                window.location.href = "../login.php";
            <?php } ?>
        }

        function bookNows(name) {
            alert("تم إلغاء طلب الحجز للممرض(ة) " + name + " بنجاح!");
        }

        // ----- دالة عرض الملف الشخصي -----
        function viewProfile(name) {
            const cleanName = name.trim();
            const nurseData = nursesData[cleanName];
            if (nurseData) {
                displayProfilePage(name, nurseData);
            } else {
                alert('عذراً، لم يتم العثور على بيانات هذا الممرض');
            }
        }

        function closeInvoice() {
            document.getElementById('invoiceModal').style.display = 'none';
        }

        // ----- دالة إنشاء صفحة الملف الشخصي -----
        function displayProfilePage(name, nurseData) {
            var homePage = document.getElementById('homePage');
            var profilePage = document.getElementById('profilePage');
            var mainBody = document.getElementById('mainBody');

            if (homePage) {
                homePage.style.display = 'none';
            }

            if (profilePage) {
                profilePage.style.display = 'block';
            }

            if (mainBody) {
                mainBody.classList.add('profile-mode');
            }

            let scheduleHTML = '';
            nurseData.schedule.forEach(item => {
                scheduleHTML += `
                    <div class="schedule-item">
                        <div class="schedule-day">${item.day}</div>
                        <div class="schedule-date">${item.date}</div>
                        <div class="schedule-time">${item.time}</div>
                    </div>
                `;
            });

            document.getElementById('profileContainer').innerHTML = `
                <div class="profile-header">
                    <div class="profile-image-large">
                        <img src="${nurseData.image}" alt="${nurseData.name}">
                    </div>
                    <h2>${nurseData.name}</h2>
                    <p class="specialty-badge">${nurseData.specialty}</p>
                </div>
                <div class="profile-info">
                    <div class="info-row">
                        <span class="info-label">رقم الهاتف  :</span>
                        <span class="info-value phone">${nurseData.phone}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">العنوان  :</span>
                        <span class="info-value">${nurseData.location}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">سنوات الخبرة  :</span>
                        <span class="info-value">${nurseData.experience}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">حساب الكشف  :</span>
                        <span class="info-value price">${nurseData.price}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">التقييم  :</span>
                        <span class="info-value">${nurseData.rating}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">التاريخ  :</span>
                        <span class="info-value">${nurseData.visitDate}</span>
                    </div>
                </div>
                <div class="schedule-section">
                    <h3>مواعيد المتاحة</h3>
                    <div class="schedule-grid">
                        ${scheduleHTML}
                    </div>
                </div>
                <div class="profile-actions">
                    <button class="btn-back" onclick="goHome()">← العودة</button>
                    <button class="btn-book-large" onclick="bookNow('${name}')">احجز الآن</button>
                    <button class="btn-book-large" onclick="bookNows('${name}')">الغاء الحجز</button>
                </div>
            `;
        }

        // ----- دالة تصفية الممرضين حسب التخصص -----
        function filterSelection(type, btn) {
            const cards = document.getElementsByClassName("nurse-card");
            const buttons = document.getElementsByClassName("filter-btn");
            for (let b of buttons) b.classList.remove("active");
            btn.classList.add("active");
            for (let card of cards) {
                card.style.display = (type === "all" || card.getAttribute("data-type") === type) ? "flex" : "none";
            }
        }

        // ----- البحث بالاسم -----
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let cards = document.getElementsByClassName('nurse-card');
            for (let card of cards) {
                let name = card.getAttribute('data-name').toLowerCase();
                card.style.display = name.includes(filter) ? "flex" : "none";
            }
        });

        // ========= كود البوت الذكي =========
        function toggleChat() {
            const chatWindow = document.getElementById('chatWindow');
            const badge = document.getElementById('chatBadge');
            if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
                chatWindow.style.display = 'flex';
                if (badge) badge.style.display = 'none'; // إخفاء البadge عند فتح المحادثة
            } else {
                chatWindow.style.display = 'none';
            }
        }

        function sendMessage() {
            const input = document.getElementById('chatInput');
            const messageText = input.value.trim();
            if (messageText === '') return;

            addMessage(messageText, 'user');
            setTimeout(() => {
                const reply = getAutoReply(messageText);
                addMessage(reply, 'bot');
            }, 500);
            input.value = '';
        }

        function addMessage(text, sender) {
            const messagesDiv = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', sender === 'user' ? 'user-message' : 'bot-message');
            messageDiv.textContent = text;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function getAutoReply(userMessage) {
            const msg = userMessage.toLowerCase();

            // الكلمات المفتاحية للمواضيع غير المرتبطة بالتمريض
            const unrelatedKeywords = [
                'ممرضين'
            ];

            for (let keyword of unrelatedKeywords) {
                if (msg.includes(keyword)) {
                    return "عذرًا، أنا متخصص فقط في الاستفسارات المتعلقة بخدمات التمريض المنزلي. هل تريد المساعدة في حجز ممرض أو الاستفسار عن الخدمات؟";
                }
            }

            // الردود التمريضية
            if (msg.includes('السلام عليكم') || msg.includes('تحية') || msg.includes('مرحبا')) {
                return 'وعليكم السلام! كيف يمكنني مساعدتك في العثور على ممرض؟';
            } else if (msg.includes('السعر') || msg.includes('الأسعار') || msg.includes('التكلفة')) {
                return 'أسعار الخدمة تبدأ من 75 جنيه حسب التخصص والممرض. يمكنك الاطلاع على البطاقات لمعرفة التفاصيل.';
            } else if (msg.includes('احجز') || msg.includes('الحجز')) {
                return 'للحجز، اضغط على زر "احجز الآن" في بطاقة الممرض الذي تريده.';
            } else if (msg.includes('الغاء') || msg.includes('الغاء')) {
                return 'للغاء، اضغط على زر "الغاء الحجز" في بطاقة الممرضين الذي تريده.';
            } else if (msg.includes('المواعيد') || msg.includes('المواعيد')) {
                return '  اضغط على زر " عرض الملف" في بطاقة الممرضين الذي تريده و سيظهر المواعيد';
            } else if (msg.includes('اطفال') || msg.includes('الأطفال')) {
                return 'لدينا ممرضات متخصصات في تمريض الأطفال مثل أ. نورا خالد. يمكنك تصفية القائمة بالضغط على زر "تمريض الأطفال".';
            } else if (msg.includes('كبار') || msg.includes('كبير') || msg.includes('مسنين')) {
                return 'رعاية كبار السن متوفرة مع ممرضين ذوي خبرة مثل أ. محمد سعيد علي.';
            } else if (msg.includes('منزلي') || msg.includes('منزلية')) {
                return 'خدمات التمريض المنزلي العام متوفرة بعدة ممرضين، يمكنك رؤية جميع الممرضين في الصفحة الرئيسية.';
            } else if (msg.includes('حالات حرجة') || msg.includes('حرجة')) {
                return 'تمريض الحالات الحرجة متوفر مع أ. مريم يوسف. تحقق من ملفها للحجز.';
            } else if (msg.includes('عمليات') || msg.includes('ما بعد العمليات')) {
                return 'رعاية ما بعد العمليات متوفرة مع أ. احمد محمود. تصفح ملفه للمواعيد.';
            } else if (msg.includes('سكري') || msg.includes('السكري')) {
                return 'رعاية مرضى السكري متوفرة مع أ. كريم عادل. يمكنك حجزه من خلال البطاقة.';
            } else if (msg.includes('امومة') || msg.includes('طفولة')) {
                return 'رعاية الأمومة والطفولة متوفرة مع أ. هند رمضان. زوري صفحتها للمزيد.';
            } else if (msg.includes('شكرا') || msg.includes('جزاك')) {
                return 'الشكر لله، نحن في خدمتك دائمًا.';
            } else if (msg.includes('التخصصات') || msg.includes('تخصص')) {
                return 'التخصصات المتوفرة: تمريض أطفال، حالات حرجة، تمريض عام، رعاية أمومة وطفولة، رعاية كبار سن، رعاية ما بعد العمليات، رعاية مرضى السكري.';
            } else if (msg.includes('العودة') || msg.includes('الرئيسية')) {
                return 'يمكنك العودة للصفحة الرئيسية بالضغط على رابط "العودة للرئيسية" في الأعلى.';
            } else {
                return 'شكرًا لتواصلك. أنا هنا للإجابة عن استفساراتك حول خدمات التمريض. هل تريد معرفة الأسعار أو التخصصات؟';
            }
        }
    </script>
</body>

</html>