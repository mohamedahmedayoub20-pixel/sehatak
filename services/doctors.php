<?php
include "../includes/auth.php";
include "../includes/database.php";

$language = "ar";
$specFilter = 'all';

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $language = $_GET['lang'];
}

if (isset($_GET['spec']) && !empty($_GET['spec'])) {
    $specFilter = $_GET['spec'];
}

$specQuery = $pdo->prepare("SELECT DISTINCT specialization FROM doctor");
$specQuery->execute();
$specializations = $specQuery->fetchAll(PDO::FETCH_ASSOC);

$doctorsQuery = $pdo->prepare("SELECT doctor_id, name,specialization, phone_number,work_shift, gender, years_of_experience,delivery_service, deleted, deleted_by, user_id,
(SELECT address from user where user.user_id = doctor.user_id) as 'address'
FROM doctor WHERE deleted = 0");
$doctorsQuery->execute();
$doctors = $doctorsQuery->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="<?php echo $language; ?>" dir="<?php echo $language === 'ar' ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <!-- إعدادات متجاوبة مع جميع الشاشات -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خدمات الأطباء - موقع صحتك</title>
    <?php if ($language === 'ar') { ?>
        <link rel="stylesheet" href="../css/hom-ar.css">

        <link rel="stylesheet" href="../css/services/doctors-ar.css">
    <?php } else { ?>
        <link rel="stylesheet" href="../css/hom.css">

        <link rel="stylesheet" href="../css/services/doctors.css">
    <?php } ?>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body id="mainBody">
    <!-- الصفحة الرئيسية -->
    <div id="homePage">
        <?php if ($language === 'ar') { ?>

            <header>
                <div class="logo"><i class="fas fa-heartbeat"></i><a href="../index.php">الصفحه الرئيسيه</a> </div>
                <div class="search-container">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" placeholder="ابحث عن الدكاترة بالاسم..">
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

            <main>
                <div class="hero">
                    <div class="icon-circle">🩺</div>
                    <h1>ابحث عن طبيبك</h1>
                    <p style="color:white;">موقع صحتك</p>
                </div>
                <!-- أزرار التصفية حسب التخصص -->
                <section class="filters">
                    <h3>تصفية حسب التخصص</h3>
                    <div class="filter-buttons">

                        <button class="filter-btn <?php echo ($specFilter === 'all') ? 'active' : ''; ?>" onclick="filterSelection('all', this)">
                            جميع الاطباء
                        </button>

                        <?php foreach ($specializations as $spec): ?>
                            <button class="filter-btn <?php echo ($spec['specialization'] === $specFilter) ? 'active' : ''; ?>" onclick="filterSelection('<?php echo $spec['specialization']; ?>', this)">
                                <?php echo $spec['specialization']; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- شبكة بطاقات الأطباء -->
                <div class="doctor-grid" id="doctorGrid">

                    <?php foreach ($doctors as $doc): ?>
                        <div class="doctor-card" data-type="<?php echo $doc['specialization']; ?>" data-name="<?php echo $doc['name']; ?>">

                            <div class="image-side">
                                <?php if ($doc['gender'] === 'male') { ?>
                                    <img src="../images/Doctor-Male.jpeg" alt="طبيب" class="doctor-img">
                                <?php } else { ?>
                                    <img src="../images/Doctor-Female.jpeg" alt="طبيبة" class="doctor-img">
                                <?php } ?>
                            </div>
                            <div class="details-side">
                                <h3>د. <?php echo $doc['name']; ?></h3>
                                <p class="spec"><?php echo $doc['specialization']; ?></p>
                                <p class="rating">⭐ 4.9 <span class="rev-count">(127 تقييم)</span></p>
                                <p class="meta">📍 <?php echo $doc['address']; ?> | 🕒 <?php echo $doc['years_of_experience']; ?> سنة خبرة</p>
                                <p class="price-status">● <?php echo $doc['delivery_service']; ?> <span class="price">500 جنيه</span></p>
                            </div>
                            <div class="actions-side">
                                <button class="btn-book" onclick="bookNow(
                                    <?php echo $doc['doctor_id']; ?>,
                                    '<?php echo addslashes($doc['name']); ?>', 
                                    '<?php echo $doc['specialization']; ?>', 
                                    '<?php echo $doc['work_shift']; ?>'
                                )">احجز الآن</button>
                                <button class="btn-profile" onclick="viewProfile(
                                <?php echo $doc['doctor_id']; ?>,
                                '<?php echo addslashes($doc['name']); ?>',
                                '<?php echo $doc['specialization']; ?>',
                                '<?php echo $doc['phone_number']; ?>',
                                '<?php echo addslashes($doc['address']); ?>',
                                '<?php echo $doc['years_of_experience']; ?>',
                                '<?php echo ($doc['gender'] === 'male') ? '../images/Doctor-Male.jpeg' : '../images/Doctor-Female.jpeg'; ?>',
                                '<?php echo $doc['work_shift']; ?>'
                                )">عرض الملف</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        <?php } else { ?>
            <!-- رأس الصفحة -->
            <header>
                <div class="logo"><i class="fas fa-heartbeat"></i><a href="../index.php">Home</a> </div>
                <div class="search-container">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" placeholder="Doctor search using name, specialization, etc.">
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
                        <a href="logout.php?lang=<?php echo $language ?>" class="btn btn-logout">Logout</a>
                    <?php else: ?>
                        <a href="#" class="btn btn-guest">Browse Website</a>
                        <a href="login.php?lang=<?php echo $language ?>" class="btn btn-login">Patient Login</a>
                    <?php endif; ?>
                </div>
            </header>

            <main>
                <div class="hero">
                    <div class="icon-circle">🩺</div>
                    <h1>ابحث عن طبيبك</h1>
                    <p>موقع صحتك</p>
                </div>
                <!-- أزرار التصفية حسب التخصص -->
                <section class="filters">
                    <h3>تصفية حسب التخصص</h3>
                    <div class="filter-buttons">
                        <button class="filter-btn <?php echo ($specFilter === 'all') ? 'active' : ''; ?>" onclick="filterSelection('all', this)">
                            All Doctors
                        </button>

                        <?php foreach ($specializations as $spec): ?>
                            <button class="filter-btn <?php echo ($spec['specialization'] === $specFilter) ? 'active' : ''; ?>" onclick="filterSelection('<?php echo $spec['specialization']; ?>', this)">
                                <?php echo $spec['specialization']; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- شبكة بطاقات الأطباء -->
                <div class="doctor-grid" id="doctorGrid">
                    <?php foreach ($doctors as $doc): ?>
                        <div class="doctor-card" data-type="<?php echo $doc['specialization']; ?>" data-name="<?php echo $doc['name']; ?>">

                            <div class="image-side">
                                <?php if ($doc['gender'] === 'male') { ?>
                                    <img src="../images/Doctor-Male.jpeg" alt="طبيب" class="doctor-img">
                                <?php } else { ?>
                                    <img src="../images/Doctor-Female.jpeg" alt="طبيبة" class="doctor-img">
                                <?php } ?>
                            </div>
                            <div class="details-side">
                                <h3>د. <?php echo $doc['name']; ?></h3>
                                <p class="spec"><?php echo $doc['specialization']; ?></p>
                                <p class="rating">⭐ 4.9 <span class="rev-count">(127 تقييم)</span></p>
                                <p class="meta">📍 <?php echo $doc['address']; ?> | 🕒 <?php echo $doc['years_of_experience']; ?> سنة خبرة</p>
                                <p class="price-status">● <?php echo $doc['delivery_service']; ?> <span class="price">500 جنيه</span></p>
                            </div>
                            <div class="actions-side">
                                <button class="btn-book" onclick="bookNow(
                                    <?php echo $doc['doctor_id']; ?>,
                                    '<?php echo addslashes($doc['name']); ?>', 
                                    '<?php echo $doc['specialization']; ?>', 
                                    '<?php echo $doc['work_shift']; ?>'
                                )">احجز الآن</button>
                                <button class="btn-profile" onclick="viewProfile(
                                <?php echo $doc['doctor_id']; ?>,
                                '<?php echo addslashes($doc['name']); ?>',
                                '<?php echo $doc['specialization']; ?>',
                                '<?php echo $doc['phone_number']; ?>',
                                '<?php echo addslashes($doc['address']); ?>',
                                '<?php echo $doc['years_of_experience']; ?>',
                                '<?php echo ($doc['gender'] === 'male') ? '../images/Doctor-Male.jpeg' : '../images/Doctor-Female.jpeg'; ?>',
                                '<?php echo $doc['work_shift']; ?>'
                                )">عرض الملف</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>

        <?php } ?>
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
                <span>المساعد الطبي</span>
                <button class="close-chat" onclick="toggleChat()">✖</button>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="message bot-message">مرحباً! أنا مساعدك الطبي. كيف يمكنني مساعدتك اليوم؟</div>
            </div>
            <div class="chat-input-area">
                <input type="text" id="chatInput" placeholder="اكتب رسالتك..." onkeypress="if(event.key==='Enter') sendMessage()">
                <button onclick="sendMessage()">إرسال</button>
            </div>
        </div>
    </div>
    <!-- ========= نهاية البوت ========= -->

    <div id="invoiceModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-modal" id="closeInvoiceBtn">&times;</span>
            <div id="invoiceDetails"></div>
        </div>
    </div>

    <!-- كود الجافا سكريبت (الوظائف الأساسية + البوت) -->
    <script>
        function viewProfile(id, name, specialty, phone, address, experience, image, shift) {
            // 1. إخفاء الصفحة الرئيسية وإظهار صفحة البروفايل
            document.getElementById('homePage').style.display = 'none';
            document.getElementById('profilePage').style.display = 'block';
            document.getElementById('mainBody').classList.add('profile-mode');

            // 2. تحديث محتوى صفحة البروفايل بالبيانات المستلمة
            document.getElementById('profileContainer').innerHTML = `
                <div class="profile-header">
                    <div class="profile-image-large">
                        <img src="${image}" alt="${name}">
                    </div>
                    <h2>د. ${name}</h2>
                    <p class="specialty-badge">${specialty}</p>
                </div>
        
                <div class="profile-info">
                    <div class="info-row">
                        <span class="info-label">رقم الهاتف :</span>
                        <span class="info-value phone">${phone}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">العنوان :</span>
                        <span class="info-value">${address}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">سنوات الخبرة :</span>
                        <span class="info-value">${experience} سنة</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">مواعيد العمل :</span>
                        <span class="info-value">${shift}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">حساب الكشف :</span>
                        <span class="info-value price">500 جنيه</span>
                    </div>
                </div>
        
                <div class="profile-actions">
                    <button class="btn-back" onclick="goHome()">← العودة</button>
                    <button class="btn-book-large" onclick="bookNow(${id}, '${name}', '${specialty}', '${shift}')">احجز الآن</button>
                </div>
            `;
        }

        // دالة العودة
        function goHome() {
            document.getElementById('profilePage').style.display = 'none';
            document.getElementById('homePage').style.display = 'block';
            document.getElementById('mainBody').classList.remove('profile-mode');
        }

        // ----- دالة الحجز (محاكاة) -----
        function bookNow(id, name, specialty, shift) {
            // التحقق من تسجيل الدخول عبر PHP (كما في كودك الأصلي)
            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) { ?>

                // إنشاء محتوى الفاتورة باستخدام البيانات الممرة للدالة
                const invoiceHTML = `
                    <div class="invoice-icon">🧾</div>
                    <h2>تأكيد الحجز</h2>
                    <div class="doctor-name">د. ${name}</div>
                    <div class="doctor-spec" style="color: #666; margin-bottom: 10px;">${specialty}</div>
                    <div class="visit-date">📅 موعد الكشف: حسب جدول الطبيب</div>
                    <div class="visit-time">🕒 الفترة: ${shift}</div>
                    <div class="confirm-msg">✅ تم تسجيل طلب حجزك بنجاح 😊</div>
                    <div class="payment-note">
                        <strong>💰 الدفع:</strong> يتم دفع قيمة الكشف نقداً <strong>عند موعد الكشف</strong> في العيادة.
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
                window.location.href = "login.php?lang=<?php echo $language ?>";
            <?php } ?>
        }

        // دالة إغلاق المودال (تأكد من وجودها)
        function closeInvoice() {
            document.getElementById('invoiceModal').style.display = 'none';
        }


        function bookNows(name) {
            alert("تم إلغاء طلب الحجز للدكتور " + name + " بنجاح!");
        }



        // ----- دالة إنشاء صفحة الملف الشخصي -----

        /*
        function displayProfilePage(name, doctorData) {
            document.getElementById('homePage').style.display = 'none';
            document.getElementById('profilePage').style.display = 'block';
            document.getElementById('mainBody').classList.add('profile-mode');

            let scheduleHTML = '';
            doctorData.schedule.forEach(item => {
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
                        <img src="${doctorData.image}" alt="${doctorData.name}">
                    </div>
                    <h2>${doctorData.name}</h2>
                    <p class="specialty-badge">${doctorData.specialty}</p>
                </div>
                <div class="profile-info">
                    <div class="info-row">
                        <span class="info-label">رقم الهاتف  :</span>
                        <span class="info-value phone">${doctorData.phone}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">العنوان  :</span>
                        <span class="info-value">${doctorData.location}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">سنوات الخبرة  :</span>
                        <span class="info-value">${doctorData.experience}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">حساب الكشف  :</span>
                        <span class="info-value price">${doctorData.price}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">التقييم  :</span>
                        <span class="info-value">${doctorData.rating}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">التاريخ  :</span>
                        <span class="info-value">${doctorData.visitDate}</span>
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
        }*/

        // ----- دالة تصفية الأطباء حسب التخصص -----
        function filterSelection(type, btn) {
            const cards = document.getElementsByClassName("doctor-card");
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
            let cards = document.getElementsByClassName('doctor-card');
            for (let card of cards) {
                let name = card.getAttribute('data-name').toLowerCase();
                card.style.display = name.includes(filter) ? "flex" : "none";
            }
        });

        //  كود البوت الذكي 
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

            // الكلمات المفتاحية للمواضيع غير الطبية
            const unrelatedKeywords = [
                'الاطباء'
            ];

            for (let keyword of unrelatedKeywords) {
                if (msg.includes(keyword)) {
                    return "عذرًا، أنا متخصص فقط في الاستفسارات الطبية وحجز الأطباء. هل تريد المساعدة في حجز طبيب أو الاستفسار عن التخصصات؟";
                }
            }

            // الردود الطبية
            if (msg.includes('السلام عليكم') || msg.includes('تحية') || msg.includes('مرحبا')) {
                return 'وعليكم السلام! كيف يمكنني مساعدتك في العثور على طبيب؟';
            } else if (msg.includes('السعر') || msg.includes('الأسعار') || msg.includes('التكلفة')) {
                return 'أسعار الكشف تبدأ من 350 جنيه حسب التخصص والطبيب. يمكنك الاطلاع على البطاقات لمعرفة التفاصيل.';
            } else if (msg.includes('احجز') || msg.includes('الحجز')) {
                return 'للحجز، اضغط على زر "احجز الآن" في بطاقة الطبيب الذي تريده.';
            } else if (msg.includes('الغاء') || msg.includes('الغاء')) {
                return 'للغاء، اضغط على زر "الغاء الحجز" في بطاقة الطبيب الذي تريده.';
            } else if (msg.includes('المواعيد') || msg.includes('المواعيد')) {
                return ' اضغط على زر " عرض الملف" في بطاقة الطبيب الذي تريده و سيظهر المواعيد ';
            } else if (msg.includes('اطفال') || msg.includes('الأطفال')) {
                return 'لدينا أطباء أطفال متميزون مثل د. هدي كمال ود. احمد حسن. يمكنك تصفية القائمة بالضغط على زر "اخصائي الأطفال".';
            } else if (msg.includes('قلب') || msg.includes('القلب')) {
                return 'أطباء القلب المتوفرون: د. سارة محمد ود. عمر محمد. تصفح القائمة لمعرفة المواعيد.';
            } else if (msg.includes('جلدية') || msg.includes('الجلدية')) {
                return 'الدكتورة مني علي متخصصة في الجلدية، يمكنك حجز موعد معها من خلال بطاقتها.';
            } else if (msg.includes('عظام') || msg.includes('العظام')) {
                return 'جراح العظام المتاح: د. محمود سعيد. تحقق من ملفه الشخصي للمواعيد.';
            } else if (msg.includes('قلب واوعية دموية') || msg.includes('القلب و اوعية دموية')) {
                return '  قلب واوعية دموية: د.شريف سامي. تحقق من ملفه الشخصي للمواعيد.';
            } else if (msg.includes('انف واذن وحنجرة') || msg.includes('الانف واذن وحنجرة')) {
                return '  انف واذن وحنجرة: د.مصطفي خالد. تحقق من ملفه الشخصي للمواعيد.';
            } else if (msg.includes('اطفال وحديثي الولادة') || msg.includes('الاطفال وحديثي الولادة')) {
                return '  اطفال وحديثي الولادة: د.ياسمين احمد فؤاد. تحقق من ملفه الشخصي للمواعيد.';
            } else if (msg.includes('علاج طبيعي') || msg.includes('العلاج الطبيعي')) {
                return '  علاج طبيعي: د.مروة اشرف. تحقق من ملفه الشخصي للمواعيد.';
            } else if (msg.includes('اعصاب') || msg.includes('الأعصاب')) {
                return 'د. خالد فتحي متخصص في الأعصاب. يمكنك الاطلاع على ملفه للحجز.';
            } else if (msg.includes('عام') || msg.includes('طبيب عام')) {
                return 'الدكتورة نهي يوسف طبيبة عامة متاحة للحجز. زر صفحتها للمزيد.';
            } else if (msg.includes('شكرا') || msg.includes('جزاك')) {
                return 'الشكر لله، نحن في خدمتك دائمًا.';
            } else if (msg.includes('التخصصات') || msg.includes('تخصص')) {
                return 'التخصصات المتوفرة: أطفال، قلب، أعصاب، جلدية، عظام، طب عام.';
            } else if (msg.includes('العودة') || msg.includes('الرئيسية')) {
                return 'يمكنك العودة للصفحة الرئيسية بالضغط على رابط "العودة للرئيسية" في الأعلى.';
            } else {
                return 'شكرًا لتواصلك. أنا هنا للإجابة عن استفساراتك حول الأطباء والمواعيد. هل تريد معرفة الأسعار أو التخصصات؟';
            }
        }

        document.getElementById('closeInvoiceBtn').addEventListener('click', closeInvoice);

        window.addEventListener('click', function(e) {

            const modal = document.getElementById('invoiceModal');

            if (e.target === modal) closeInvoice();

        });
    </script>

</body>



</html>