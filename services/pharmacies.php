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
    <title>خدمات الصيدليات - موقع صحتك</title>
    <?php if ($language === 'ar') { ?>
        <link rel="stylesheet" href="../css/hom-ar.css">

        <link rel="stylesheet" href="../css/services/pharmacies-ar.css">
    <?php } else { ?>
        <link rel="stylesheet" href="../css/hom.css">

        <link rel="stylesheet" href="../css/services/pharmacies.css">
    <?php } ?>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body id="mainBody">
    <!-- الصفحة الرئيسية -->

    <header>
        <div class="logo"><i class="fas fa-heartbeat"></i><a href="../index.php">الصفحه الرئيسيه</a> </div>
        <div class="search-container">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="ابحث عن الصيدليات..">
        </div>
        <div class="cart-icon" onclick="viewCart()">
            <span class="cart-symbol">🛒</span>
            <!-- عداد يظهر عدد العناصر في السلة، قيمته الابتدائية 0 ويتم تحديثها عبر JavaScript -->
            <span class="cart-count" id="cartCount">0</span>
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
            <div class="icon-circle">💊</div>
            <h1>الصيدلية الالكترونية</h1>
            <p>اطلب ادويتك اونلاين بسهولة و امان</p>
        </div>


        <!-- قسم أزرار التصفية حسب الفئة -->
        <section class="filters">
            <h3>تصفية حسب الفئة</h3> <!-- عنوان القسم -->
            <div class="filter-buttons">
                <!-- أزرار التصفية: كل زر ينادي دالة filterSelection عند النقر، ويرسل اسم الفئة والزر نفسه -->
                <button class="filter-btn active" onclick="filterSelection('all', this)">جميع الادوية</button>
                <button class="filter-btn" onclick="filterSelection('المعدة', this)"> علاج اضطرابات المعدة</button>
                <button class="filter-btn" onclick="filterSelection('البرد و الانفلونزا', this)"> علاج البرد و الانفلونزا</button>
                <button class="filter-btn" onclick="filterSelection('فتيامينات و مكملات', this)"> فتيامينات و مكملات</button>
                <button class="filter-btn" onclick="filterSelection('للالم', this)"> مسكن للالم</button>
                <button class="filter-btn" onclick="filterSelection('مضاد حيوي', this)"> مضاد حيوي</button>
            </div>
        </section>

        <!-- شبكة عرض بطاقات الأدوية (المنتجات) -->
        <div class="pharmacy-grid" id="pharmacyGrid">

            <!-- بطاقة منتج 1: بانادول اكسترا -->
            <div class="pharmacy-card" data-type="للالم" data-name="بانادول اكسترا" data-price="45">
                <!-- الجانب الأيمن: الصورة -->
                <div class="image-side">
                    <img src="../images/p.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <!-- الجانب الأوسط: تفاصيل المنتج -->
                <div class="details-side">
                    <h3>بانادول اكسترا</h3> <!-- اسم الدواء -->
                    <p class="spec">مسكن للالم</p> <!-- الفئة -->
                    <p class="spec">مسكن فعال للصداع و الالام المتوسطة و الحمي</p> <!-- وصف مختصر -->
                    <p class="rating">⭐ 4.8 <span class="rev-count">(156 تقييم)</span></p> <!-- التقييم -->
                    <p class="meta">📍 صيدلية العزبي، القاهرة</p> <!-- مكان التوفر -->
                    <p class="price-status">● متوفر <span class="price">45 جنيه</span></p> <!-- حالة التوفر والسعر -->
                </div>
                <!-- الجانب الأيسر: زر الإجراء (إضافة للسلة) -->
                <div class="actions-side">
                    <!-- عند النقر على الزر يتم استدعاء دالة addToCart مع اسم الدواء وسعره -->
                    <button class="btn-book" onclick="addToCart('بانادول اكسترا', 45)">اضف للسلة</button>
                </div>
            </div>

            <!-- بطاقة منتج 2: كونجستال -->
            <div class="pharmacy-card" data-type="البرد و الانفلونزا" data-name="كونجستال" data-price="35">
                <div class="image-side">
                    <img src="../images/c.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>كونجستال</h3>
                    <p class="spec">علاج البرد و الانفلونزا</p>
                    <p class="spec">علاج فعال لاعراض البرد و الزكام و الصداع</p>
                    <p class="rating">⭐ 4.7 <span class="rev-count">(203 تقييم)</span></p>
                    <p class="meta">📍 صيدلية سيف، الاسكندرية</p>
                    <p class="price-status">● متوفر <span class="price">35 جنيه</span></p>
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('كونجستال', 35)">اضف للسلة</button>
                </div>
            </div>

            <!-- بطاقة منتج 3: فيتامين سي 2000 -->
            <div class="pharmacy-card" data-type="فتيامينات و مكملات" data-name="فيتامين سي2000" data-price="95">
                <div class="image-side">
                    <img src="../images/o.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>فيتامين سي 2000</h3>
                    <p class="spec">فتيامينات و مكملات</p>
                    <p class="spec">فيتامين سي فوار لتقوية المناعة و الوقاية من الامراض</p>
                    <p class="rating">⭐ 4.9 <span class="rev-count">(312 تقييم)</span></p>
                    <p class="meta">📍 صيدلية النهدي، الجيزة</p>
                    <p class="price-status">● متوفر <span class="price">95 جنيه</span></p>
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('فيتامين سي2000', 95)">اضف للسلة</button>
                </div>
            </div>

            <!-- بطاقة منتج 4: انتينال -->
            <div class="pharmacy-card" data-type="المعدة" data-name="انتينال" data-price="28">
                <div class="image-side">
                    <img src="../images/a.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>انتينال</h3>
                    <p class="spec">علاج الاسهال</p>
                    <p class="spec">علاج فعال للاسهال و اضطرابات المعدة</p>
                    <p class="rating">⭐ 4.6 <span class="rev-count">(89 تقييم)</span></p>
                    <p class="meta">📍 صيدلية رشدي، طنطا</p>
                    <p class="price-status">● متوفر <span class="price">28 جنيه</span></p>
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('انتينال', 28)">اضف للسلة</button>
                </div>
            </div>

            <!-- بطاقة منتج 5: اوميجا 3 -->
            <div class="pharmacy-card" data-type="فتيامينات و مكملات" data-name="اوميجا 3" data-price="180">
                <div class="image-side">
                    <img src="../images/u.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>اوميجا3</h3>
                    <p class="spec">فتيامينات و مكملات</p>
                    <p class="spec">مكمل غذائي لصحة القلب و الدماغ</p>
                    <p class="rating">⭐ 4.8 <span class="rev-count">(267 تقييم)</span></p>
                    <p class="meta">📍 صيدلية الدواء، المنصورة</p>
                    <p class="price-status">● متوفر <span class="price">180 جنيه</span></p>
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('اوميجا3', 180)">اضف للسلة</button>
                </div>
            </div>
            <!-- بطاقة منتج 6: بروفين 400 -->
            <div class="pharmacy-card" data-type="للالم" data-name="بروفين400" data-price="32">
                <div class="image-side">
                    <img src="../images/b.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>بروفين400</h3>
                    <p class="spec">مسكن للالم</p>
                    <p class="spec">مسكن قوي لالام الاسنان و العضلات</p>
                    <p class="rating">⭐ 4.8 <span class="rev-count">(267 تقييم)</span></p>
                    <p class="meta">📍 صيدلية الطرشوبي، المنصورة</p>
                    <p class="price-status">● متوفر <span class="price">32 جنيه</span></p>
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('بروفين400', 32)">اضف للسلة</button>
                </div>
            </div>
            <!-- بطاقة منتج 7:  زيثروماكس -->
            <div class="pharmacy-card" data-type="مضاد حيوي" data-name="زيثروماكس" data-price="75">
                <div class="image-side">
                    <img src="../images/z.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>زيثروماكس</h3>
                    <p class="spec">مضاد حيوي</p>
                    <p class="spec">يستخدم لعلاج العدوي البكتيرية</p>
                    <p class="rating">⭐ 4.6 <span class="rev-count">(269 تقييم)</span></p>
                    <p class="meta">📍 صيدلية الدواء، المنصورة</p>
                    <p class="price-status">● متوفر <span class="price">75 جنيه</span></p>
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('زيثروماكس', 75)">اضف للسلة</button>
                </div>
            </div>
            <!-- بطاقة منتج 8:  دايجستين -->
            <div class="pharmacy-card" data-type="المعدة" data-name="دايجستين" data-price="28">
                <div class="image-side">
                    <img src="../images/d.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>دايجستين</h3>
                    <p class="spec"> علاج اضطرابات المعدة</p>
                    <p class="spec">يساعد علي تحسين الهضم و تقليل الانتفاج</p>
                    <p class="rating">⭐ 4.5 <span class="rev-count">(279 تقييم)</span></p>
                    <p class="meta">📍 صيدلية الشفاء، القاهرة</p>
                    <p class="price-status">● متوفر <span class="price">28 جنيه</span></p>
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('دايجستين', 28)">اضف للسلة</button>
                </div>
            </div>
            <!-- بطاقة منتج 9:  سبترين -->
            <div class="pharmacy-card" data-type="مضاد حيوي" data-name="سبترين" data-price="32">
                <div class="image-side">
                    <img src="../images/s.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>سبترين</h3>
                    <p class="spec">مضاد حيوي</p>
                    <p class="spec">يستخدم لعلاج بعض العدوي البكتيرية</p>
                    <p class="rating">⭐ 4.8 <span class="rev-count">(269 تقييم)</span></p>
                    <p class="meta">📍 صيدلية العزبي، المنصورة</p>
                    <p class="price-status">● متوفر <span class="price">32 جنيه</span></p>
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('سبترين', 32)">اضف للسلة</button>
                </div>
            </div>
            <!-- بطاقة منتج 10:  نوفالوج -->
            <div class="pharmacy-card" data-type="للالم" data-name="نوفالوج" data-price="42">
                <div class="image-side">
                    <img src="../images/f.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>نوفالوج</h3>
                    <p class="spec">مسكن للالم</p>
                    <p class="spec">مسكن فعال للصداع والام الجسم</p>
                    <p class="rating">⭐ 4.9 <span class="rev-count">(249 تقييم)</span></p>
                    <p class="meta">📍 صيدلية الفا، المنصورة</p>
                    <p class="price-status">● متوفر <span class="price">42 جنيه</span></p>
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('نوفالوج', 42)">اضف للسلة</button>
                </div>
            </div>
            <!-- بطاقة منتج 11:  اوستيوكير -->
            <div class="pharmacy-card" data-type="فتيامينات و مكملات" data-name="اوستيوكير" data-price="65">
                <div class="image-side">
                    <img src="../images/e.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>اوستيوكير</h3>
                    <p class="spec"> فتيامينات و مكملات</p>
                    <p class="spec">مكمل غذائي للعظام يحتوي علي كالسيوم و فيتامين د</p>
                    <p class="rating">⭐ 4.2 <span class="rev-count">(154 تقييم)</span></p>
                    <p class="meta">📍 صيدلية الفا، المنصورة</p>
                    <p class="price-status">● متوفر <span class="price">65 جنيه</span></p>
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('اوستيوكير', 65)">اضف للسلة</button>
                </div>
            </div>
            <!-- بطاقة منتج 12: كيتولاك (غير متوفر) -->
            <div class="pharmacy-card" data-type="للالم" data-name="كيتولاك" data-price="32">
                <div class="image-side">
                    <img src="../images/k.png.jpeg" alt="pharmacy" class="pharmacy-img">
                </div>
                <div class="details-side">
                    <h3>كيتولاك</h3>
                    <p class="spec">مسكن للالم</p>
                    <p class="spec">مسكن قوي للالام الشديدة و الالتهابات</p>
                    <p class="rating">⭐ 4.5 <span class="rev-count">(145 تقييم)</span></p>
                    <p class="meta">📍 صيدلية العزبي، القاهرة</p>
                    <p class="price-status">● غير متوفر <span class="price">32 جنيه</span></p> <!-- حالة غير متوفر -->
                </div>
                <div class="actions-side">
                    <button class="btn-book" onclick="addToCart('كيتولاك', 32)">اضف للسلة</button>
                </div>
            </div>
        </div>
    </main>

    <!-- كود JavaScript (وظائف التفاعل) -->
    <script>
        // مصفوفة لتخزين العناصر المضافة إلى السلة (كل عنصر هو object يحتوي على name و price)
        let cartItems = [];

        // دالة لإضافة عنصر إلى السلة
        function addToCart(name, price) {
            // نضيف الكائن الجديد إلى المصفوفة
            cartItems.push({
                name: name,
                price: price
            });
            // تحديث عداد السلة (الرقم الظاهر بجانب الأيقونة)
            updateCartCount();
            // إظهار رسالة تأكيد للمستخدم
            alert(`✅ تمت إضافة "${name}" إلى سلة التسوق`);
        }

        // دالة لتحديث عداد السلة بناءً على عدد العناصر في المصفوفة
        function updateCartCount() {
            document.getElementById('cartCount').textContent = cartItems.length;
        }

        // دالة لعرض محتويات السلة - تُستدعى عند النقر على أيقونة السلة
        function viewCart() {
            // إذا كانت السلة فارغة نعرض رسالة مناسبة
            if (cartItems.length === 0) {
                alert('🛒 سلة التسوق فارغة');
                return;
            }

            // إنشاء نص مفصل لعرض العناصر والمجموع الكلي
            let cartDetails = '🛍️ عناصر سلة التسوق:\n\n';
            let total = 0;

            // المرور على كل عنصر في المصفوفة وإضافته إلى النص
            cartItems.forEach((item, index) => {
                cartDetails += `${index + 1}. ${item.name}: ${item.price} جنيه\n`;
                total += item.price; // حساب المجموع الكلي
            });

            // إضافة المجموع الكلي وعدد العناصر في نهاية النص
            cartDetails += `\n💰 الإجمالي الكلي: ${total} جنيه`;
            cartDetails += `\n📦 عدد العناصر: ${cartItems.length}`;

            // عرض التفاصيل في نافذة منبثقة
            alert(cartDetails);
        }

        // دالة لعرض الملف الشخصي (محتفظ بها كما كانت في الكود الأصلي، لكنها غير مستخدمة حالياً)
        function viewProfile(name) {
            alert("جاري تحميل الملف الشخصي الكامل لـ " + name + "...");
        }

        // دالة تصفية العناصر حسب الفئة
        function filterSelection(type, btn) {
            // الحصول على جميع بطاقات المنتجات وأزرار التصفية
            const cards = document.getElementsByClassName("pharmacy-card");
            const buttons = document.getElementsByClassName("filter-btn");

            // إزالة الفئة 'active' من جميع الأزرار، ثم إضافتها للزر الذي تم ضغطه
            for (let b of buttons) {
                b.classList.remove("active");
            }
            btn.classList.add("active");

            // المرور على جميع البطاقات وإظهار/إخفاء حسب الفئة المختارة
            for (let card of cards) {
                // قراءة قيمة data-type من البطاقة (مع إزالة المسافات الزائدة)
                let cardType = card.getAttribute('data-type').trim();
                // إذا كان النوع "all" (كل الأدوية) أو مطابقاً لنوع البطاقة، نعرضها، وإلا نخفيها
                if (type === "all" || cardType === type) {
                    card.style.display = "flex"; // إظهار البطاقة (باستخدام flex كطريقة عرض)
                } else {
                    card.style.display = "none"; // إخفاء البطاقة
                }
            }
        }

        // إضافة مستمع حدث لحقل البحث (keyup) لتصفية الأدوية حسب الاسم أثناء الكتابة
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase(); // تحويل النص المدخل إلى أحرف صغيرة للمقارنة
            let cards = document.getElementsByClassName('pharmacy-card');

            // المرور على جميع البطاقات
            for (let card of cards) {
                // الحصول على اسم الدواء من data-name وتحويله إلى أحرف صغيرة
                let name = card.getAttribute('data-name').toLowerCase();
                // إذا كان الاسم يحتوي على النص المدخل نعرض البطاقة، وإلا نخفيها
                card.style.display = name.includes(filter) ? "flex" : "none";
            }
        });

        // عند تحميل الصفحة بالكامل
        window.onload = function() {
            // تفعيل فلتر "جميع الادوية" تلقائياً (بافتراض وجود زر active)
            const allButton = document.querySelector('.filter-btn.active');
            if (allButton) {
                filterSelection('all', allButton);
            }

            // تحديث عداد السلة ليكون القيمة الابتدائية 0
            updateCartCount();
        };
    </script>
</body>

</html>