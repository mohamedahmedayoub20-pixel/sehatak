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
    <title>خدمات معامل التحاليل - موقع صحتك</title>
    <?php if ($language === 'ar') { ?>
        <link rel="stylesheet" href="../css/hom-ar.css">

        <link rel="stylesheet" href="../css/services/labs-ar.css">
    <?php } else { ?>
        <link rel="stylesheet" href="../css/hom.css">

        <link rel="stylesheet" href="../css/services/labs.css">
    <?php } ?>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body id="mainBody">
    <!-- الصفحة الرئيسية -->

    <header>
        <div class="logo"><i class="fas fa-heartbeat"></i><a href="../index.php">الصفحه الرئيسيه</a> </div>
        <div class="search-container">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="ابحث عن معمل التحاليل او التحليل بالاسم..">
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

    <!-- المحتوى الرئيسي -->
    <main class="content-wrapper">
        <div class="hero">
            <div class="icon-circle">🧪</div>
            <h1>التحاليل الطبية</h1>
            <p>احجز تحاليلك في أقرب معمل معتمد</p>
        </div>

        <!-- قسم التصفية (أزرار الفئات) -->
        <section class="filter-section">
            <h2>تصفية حسب الفئة</h2>
            <div class="tags-container" id="filterTags">
                <button class="tag active" data-category="all">جميع التحاليل</button>
                <button class="tag" data-category="الدم">تحاليل الدم</button>
                <button class="tag" data-category="الدهون">تحاليل الدهون</button>
                <button class="tag" data-category="السكر">تحاليل السكر</button>
                <button class="tag" data-category="الفيتامينات">تحاليل الفيتامينات</button>
                <button class="tag" data-category="الفيروسات">تحاليل الفيروسات</button>
                <button class="tag" data-category="الكبد">تحاليل الكبد</button>
                <button class="tag" data-category="الكلى">تحاليل الكلى</button>
                <button class="tag" data-category="الهرمونات">تحاليل الهرمونات</button>
                <button class="tag" data-category="الحديد">تحاليل الحديد</button>
                <button class="tag" data-category="الكالسيوم">تحاليل الكالسيوم</button>
                <button class="tag" data-category="الدم">تحاليل سيولة الدم</button>
                <button class="tag" data-category="الحمل">تحاليل الحمل</button>

            </div>
        </section>

        <!-- شبكة بطاقات التحاليل -->
        <div class="analysis-grid" id="analysisGrid">
            <!-- بطاقة 1: صورة دم كاملة (CBC) -->
            <article class="analysis-card" data-category="الدم">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل صورة دم كاملة (CBC)</h3>
                        <p>قياس مستويات خلايا الدم الحمراء والبيضاء والصفائح الدموية</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل الدم</span>
                    <div class="detail-item">📍 معامل المختبر - القاهرة، مصر الجديدة</div>
                    <div class="detail-item">⏱️ مدة التحليل: 24 ساعة</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('صورة دم كاملة')">احجز الآن</button>
                    <span class="price">150 جنيه</span>
                </div>
            </article>

            <!-- بطاقة 2: السكر التراكمي -->
            <article class="analysis-card" data-category="السكر">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل السكر التراكمي (HbA1c)</h3>
                        <p>قياس متوسط مستوى السكر في الدم خلال 3 شهور</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل السكر</span>
                    <div class="detail-item">📍 معامل البرج - الإسكندرية، سموحة</div>
                    <div class="detail-item">⏱️ مدة التحليل: 3 ساعات</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('السكر التراكمي')">احجز الآن</button>
                    <span class="price">200 جنيه</span>
                </div>
            </article>

            <!-- بطاقة 3: وظائف الكلى -->
            <article class="analysis-card" data-category="الكلى">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل وظائف الكلى</h3>
                        <p>فحص شامل لوظائف الكلى والكرياتينين واليوريا</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل الكلى</span>
                    <div class="detail-item">📍 معامل الفا - الجيزة، المهندسين</div>
                    <div class="detail-item">⏱️ مدة التحليل: 6 ساعات</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('وظائف الكلى')">احجز الآن</button>
                    <span class="price">180 جنيه</span>
                </div>
            </article>

            <!-- بطاقة 4: وظائف الكبد -->
            <article class="analysis-card" data-category="الكبد">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل وظائف الكبد</h3>
                        <p>فحص إنزيمات الكبد والبيليروبين والبروتين</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل الكبد</span>
                    <div class="detail-item">📍 معامل الحكمة - القاهرة، مدينة نصر</div>
                    <div class="detail-item">⏱️ مدة التحليل: 12 ساعة</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('وظائف الكبد')">احجز الآن</button>
                    <span class="price">250 جنيه</span>
                </div>
            </article>

            <!-- بطاقة 5: الغدة الدرقية -->
            <article class="analysis-card" data-category="الهرمونات">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل الغدة الدرقية (TSH)</h3>
                        <p>قياس هرمون الغدة الدرقية المحفز</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل الهرمونات</span>
                    <div class="detail-item">📍 معامل كايرو لاب - طنطا، وسط البلد</div>
                    <div class="detail-item">⏱️ مدة التحليل: 24 ساعة</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('الغدة الدرقية')">احجز الآن</button>
                    <span class="price">170 جنيه</span>
                </div>
            </article>

            <!-- بطاقة 6: فيتامين د -->
            <article class="analysis-card" data-category="الفيتامينات">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل فيتامين د</h3>
                        <p>قياس مستوى فيتامين د في الدم</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل الفيتامينات</span>
                    <div class="detail-item">📍 معامل البرج - الإسكندرية، ميامي</div>
                    <div class="detail-item">⏱️ مدة التحليل: 48 ساعة</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('فيتامين د')">احجز الآن</button>
                    <span class="price">250 جنيه</span>
                </div>
            </article>

            <!-- بطاقة 7: كورونا PCR -->
            <article class="analysis-card" data-category="الفيروسات">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل كورونا PCR</h3>
                        <p>الكشف عن فيروس كورونا المستجد</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل الفيروسات</span>
                    <div class="detail-item">📍 معامل المختبر - القاهرة، الزمالك</div>
                    <div class="detail-item">⏱️ مدة التحليل: 6 ساعات</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('كورونا PCR')">احجز الآن</button>
                    <span class="price">300 جنيه</span>
                </div>
            </article>

            <!-- بطاقة 8: الكوليسترول الكامل -->
            <article class="analysis-card" data-category="الدهون">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل الكوليسترول الكامل</h3>
                        <p>قياس مستويات الكوليسترول والدهون الثلاثية</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل الدهون</span>
                    <div class="detail-item">📍 معامل الفا - المنصورة، الجلاء</div>
                    <div class="detail-item">⏱️ مدة التحليل: 12 ساعة</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('الكوليسترول الكامل')">احجز الآن</button>
                    <span class="price">190 جنيه</span>
                </div>
            </article>
            <!-- بطاقة 9:  الحديد -->
            <article class="analysis-card" data-category="الحديد">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل الحديد </h3>
                        <p>قياس مستوي الحديد في الدم للكشف عن الانيميا</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل الحديد</span>
                    <div class="detail-item">📍 معامل الفا - المنصورة، الجلاء</div>
                    <div class="detail-item">⏱️ مدة التحليل: 12 ساعة</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('الحديد')">احجز الآن</button>
                    <span class="price">180 جنيه</span>
                </div>
            </article>
            <!-- بطاقة 10:  الكالسيوم -->
            <article class="analysis-card" data-category="الكالسيوم">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل الكالسيوم </h3>
                        <p>قياس مستوي الكالسيوم في الدم </p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل الكالسيوم</span>
                    <div class="detail-item">📍 معامل الفا القاهرة - مدينة نصر </div>
                    <div class="detail-item">⏱️ مدة التحليل: 12 ساعة</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('الكالسيوم')">احجز الآن</button>
                    <span class="price">170 جنيه</span>
                </div>
            </article>
            <!-- بطاقة 11:  سيولة الدم -->
            <article class="analysis-card" data-category="سيولة الدم">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل سيولة الدم </h3>
                        <p>قياس سرعة تجلط الدم</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل سيولة الدم</span>
                    <div class="detail-item">📍 معامل الفا القاهرة - مدينة نصر </div>
                    <div class="detail-item">⏱️ مدة التحليل: 6 ساعة</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('سيولة الدم')">احجز الآن</button>
                    <span class="price">160 جنيه</span>
                </div>
            </article>
            <!-- بطاقة 12:   الحمل -->
            <article class="analysis-card" data-category="الحمل">
                <div class="card-top">
                    <div class="card-icon-box">🧪</div>
                    <div class="card-title-info">
                        <h3>تحليل الحمل </h3>
                        <p>الكشف عن وجود هرمون الحمل في الدم</p>
                    </div>
                </div>
                <div class="card-details">
                    <span class="badge">تحاليل الحمل</span>
                    <div class="detail-item">📍 معامل الفا القاهرة - مدينة نصر </div>
                    <div class="detail-item">⏱️ مدة التحليل: 6 ساعة</div>
                </div>
                <div class="card-action">
                    <button class="book-btn" onclick="bookAnalysis('الحمل')">احجز الآن</button>
                    <span class="price">150 جنيه</span>
                </div>
            </article>
        </div>
    </main>

    <!--  بداية بوت المحادثة الذكي (بدون العلامة الحمراء)  -->
    <div class="chatbot-container" id="chatbotContainer">
        <!-- أيقونة البوت فقط (بدون Badge) -->
        <div class="chat-icon" id="chatIcon" onclick="toggleChat()">
            🤖
        </div>
        <!-- نافذة المحادثة -->
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <span>المساعد الطبي للتحاليل</span>
                <button class="close-chat" onclick="toggleChat()">✖</button>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="message bot-message">مرحباً! أنا مساعدك في منصة التحاليل الطبية. كيف يمكنني مساعدتك اليوم؟</div>
            </div>
            <div class="chat-input-area">
                <input type="text" id="chatInput" placeholder="اكتب رسالتك..." onkeypress="if(event.key==='Enter') sendMessage()">
                <button onclick="sendMessage()">إرسال</button>
            </div>
        </div>
    </div>
    <!--  نهاية البوت  -->

    <!-- الجافا سكريبت (الكود الأصلي + البوت) -->
    <script>
        // ----- الكود الأصلي للصفحة -----
        //  العودة للرئيسية (إعادة ضبط التصفية والتمرير للأعلى)
        document.getElementById('backToHome').addEventListener('click', function(e) {
            e.preventDefault(); // منع السلوك الافتراضي للرابط
            filterByCategory('all'); // عرض كل البطاقات
            window.scrollTo({
                top: 0,
                behavior: 'smooth' // تمرير سلس للأعلى
            });
            updateActiveTag('all'); // تحديث الزر النشط إلى "جميع التحاليل"
        });

        //  دالة الحجز: تظهر رسالة تأكيد مؤقتة
        function bookAnalysis(testName) {

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != null && $_SESSION['user_id'] != '') { ?>
                // إنشاء عنصر div للرسالة
                const message = document.createElement('div');
                message.className = 'booking-message';
                message.textContent = `✅ تم حجز تحليل ${testName} بنجاح! سنتواصل معك قريباً`;

                // إضافة الرسالة إلى جسم الصفحة
                document.body.appendChild(message);

                // إزالة الرسالة بعد 3 ثوانٍ
                setTimeout(() => {
                    message.remove();
                }, 3000);
            <?php } else { ?>
                alert("يرجى تسجيل الدخول أولاً لإتمام الحجز.");
                window.location.href = "../login.php";
            <?php } ?>
        }

        // دالة التصفية حسب الفئة 
        function filterByCategory(category) {
            const cards = document.querySelectorAll('.analysis-card'); // كل البطاقات
            let visibleCount = 0;

            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block'; // إظهار البطاقة
                    visibleCount++;
                } else {
                    card.style.display = 'none'; // إخفاء البطاقة
                }
            });

            // التعامل مع حالة عدم وجود نتائج
            const existingMessage = document.querySelector('.no-results');
            if (visibleCount === 0) {
                if (!existingMessage) {
                    const noResults = document.createElement('div');
                    noResults.className = 'no-results';
                    noResults.textContent = '❌ لا توجد تحاليل متاحة في هذه الفئة';
                    document.getElementById('analysisGrid').appendChild(noResults);
                }
            } else {
                if (existingMessage) {
                    existingMessage.remove(); // إزالة رسالة "لا توجد نتائج" إن وُجدت
                }
            }
        }

        //  تحديث الزر النشط (تمييز الفئة المختارة) 
        function updateActiveTag(activeCategory) {
            const tags = document.querySelectorAll('.tag');
            tags.forEach(tag => {
                if (tag.dataset.category === activeCategory) {
                    tag.classList.add('active');
                } else {
                    tag.classList.remove('active');
                }
            });
        }

        // إضافة مستمعي الأحداث لأزرار التصفية
        document.querySelectorAll('.tag').forEach(tag => {
            tag.addEventListener('click', function() {
                const category = this.dataset.category;
                filterByCategory(category);
                updateActiveTag(category);
            });
        });

        //  وظيفة البحث المباشر (حسب الاسم أو الوصف أو الموقع) 
        document.querySelector('.search-bar input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim(); // نص البحث بعد تنظيفه
            const cards = document.querySelectorAll('.analysis-card');
            let visibleCount = 0;

            cards.forEach(card => {
                // استخراج النصوص من البطاقة
                const title = card.querySelector('h3').textContent.toLowerCase();
                const description = card.querySelector('.card-title-info p').textContent.toLowerCase();
                const location = card.querySelector('.detail-item').textContent.toLowerCase(); // أول detail-item

                // شرط المطابقة: إذا كان النص موجوداً في أي من الحقول أو كان حقل البحث فارغاً
                if (title.includes(searchTerm) || description.includes(searchTerm) || location.includes(searchTerm) || searchTerm === '') {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // رسالة "لا توجد نتائج" خاصة بالبحث
            const existingMessage = document.querySelector('.no-results');
            if (visibleCount === 0 && searchTerm !== '') {
                if (!existingMessage) {
                    const noResults = document.createElement('div');
                    noResults.className = 'no-results';
                    noResults.textContent = `❌ لا توجد نتائج لـ "${searchTerm}"`;
                    document.getElementById('analysisGrid').appendChild(noResults);
                }
            } else {
                if (existingMessage) {
                    existingMessage.remove();
                }
            }
        });

        //  تهيئة الصفحة عند التحميل 
        document.addEventListener('DOMContentLoaded', function() {
            filterByCategory('all'); // عرض كل البطاقات
            console.log('✅ المنصة الطبية جاهزة للعمل');
        });

        // تأثير بسيط عند النقر على البطاقة (مع تجاهل زر الحجز) 
        document.querySelectorAll('.analysis-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.classList.contains('book-btn')) { // إذا لم يكن الضغط على زر الحجز
                    this.style.transform = 'scale(0.99)'; // تصغير طفيف
                    setTimeout(() => {
                        this.style.transform = 'scale(1)'; // إعادة للحجم الطبيعي
                    }, 200);
                }
            });
        });

        // ========= كود البوت الذكي (بدون badge) =========
        // دالة تبديل إظهار/إخفاء نافذة المحادثة
        function toggleChat() {
            const chatWindow = document.getElementById('chatWindow');
            if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
                chatWindow.style.display = 'flex';
            } else {
                chatWindow.style.display = 'none';
            }
        }

        // دالة إرسال الرسالة ومعالجتها
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

        // دالة إضافة رسالة إلى نافذة المحادثة
        function addMessage(text, sender) {
            const messagesDiv = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', sender === 'user' ? 'user-message' : 'bot-message');
            messageDiv.textContent = text;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        // دالة الرد التلقائي بناءً على الكلمات المفتاحية
        function getAutoReply(userMessage) {
            const msg = userMessage.toLowerCase();

            // الكلمات المفتاحية للمواضيع غير المرتبطة بالتحاليل
            const unrelatedKeywords = [
                'خاص ب التحاليل'
            ];

            for (let keyword of unrelatedKeywords) {
                if (msg.includes(keyword)) {
                    return "عذرًا، أنا متخصص فقط في الاستفسارات المتعلقة بالتحاليل الطبية. هل تريد المساعدة في حجز تحليل أو معرفة معلومات عن تحليل معين؟";
                }
            }

            // الردود الخاصة بالتحاليل
            if (msg.includes('السلام عليكم') || msg.includes('تحية') || msg.includes('مرحبا')) {
                return 'وعليكم السلام! كيف يمكنني مساعدتك في حجز التحاليل الطبية؟';
            } else if (msg.includes('السعر') || msg.includes('الأسعار') || msg.includes('التكلفة')) {
                return 'أسعار التحاليل تبدأ من 150 جنيه. يمكنك الاطلاع على البطاقات لمعرفة التفاصيل.';
            } else if (msg.includes('احجز') || msg.includes('الحجز')) {
                return 'للحجز، اضغط على زر "احجز الآن" في بطاقة التحليل الذي تريده.';
            } else if (msg.includes('الدم') || msg.includes('صورة دم') || msg.includes('cbc')) {
                return 'تحليل صورة دم كاملة (CBC) متوفر في معامل المختبر بسعر 150 جنيه.';
            } else if (msg.includes('السكر') || msg.includes('التراكمي') || msg.includes('hba1c')) {
                return 'تحليل السكر التراكمي متوفر في معامل البرج بسعر 200 جنيه.';
            } else if (msg.includes('كلى') || msg.includes('الكلى') || msg.includes('وظائف الكلى')) {
                return 'تحليل وظائف الكلى متوفر في معامل الفا بسعر 180 جنيه.';
            } else if (msg.includes('حديد') || msg.includes('الحديد')) {
                return 'تحليل الحديد متوفر في معامل الفا بسعر 180 جنيه.';
            } else if (msg.includes('كالسيوم') || msg.includes('الكالسيوم')) {
                return 'تحليل الكالسيوم متوفر في معامل الفا بسعر 170 جنيه.';
            } else if (msg.includes('سيولة') || msg.includes('السيولة') || msg.includes('سيولة الدم ')) {
                return 'تحليل سيولة الدم متوفر في معامل الفا بسعر 160 جنيه.';
            } else if (msg.includes('حمل') || msg.includes('الحمل')) {
                return 'تحليل الحمل متوفر في معامل الفا بسعر 150 جنيه.';
            } else if (msg.includes('كبد') || msg.includes('الكبد') || msg.includes('وظائف الكبد')) {
                return 'تحليل وظائف الكبد متوفر في معامل الحكمة بسعر 250 جنيه.';
            } else if (msg.includes('غدة') || msg.includes('درقية') || msg.includes('tsh')) {
                return 'تحليل الغدة الدرقية متوفر في معامل كايرو لاب بسعر 170 جنيه.';
            } else if (msg.includes('فيتامين د') || msg.includes('vitamin d')) {
                return 'تحليل فيتامين د متوفر في معامل البرج بسعر 250 جنيه.';
            } else if (msg.includes('كورونا') || msg.includes('pcr')) {
                return 'تحليل كورونا PCR متوفر في معامل المختبر بسعر 300 جنيه.';
            } else if (msg.includes('كوليسترول') || msg.includes('دهون')) {
                return 'تحليل الكوليسترول الكامل متوفر في معامل الفا بسعر 190 جنيه.';
            } else if (msg.includes('الفئات') || msg.includes('التصنيفات')) {
                return 'الفئات المتوفرة: تحاليل الدم، السكر، الكلى، الكبد، الهرمونات، الفيتامينات، الفيروسات، الدهون.';
            } else if (msg.includes('شكرا') || msg.includes('جزاك')) {
                return 'الشكر لله، نحن في خدمتك دائمًا.';
            } else {
                return 'شكرًا لتواصلك. أنا هنا للإجابة عن استفساراتك حول التحاليل والأسعار. هل تريد معرفة سعر تحليل معين أو فئة معينة؟';
            }
        }
    </script>
</body>

</html>