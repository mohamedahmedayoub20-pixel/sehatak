<?php
include "../includes/auth.php";

$language = "ar";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $language = $_GET['lang'];
}

// $_SESSION['user_type'] => 'doctor','admin','user','patient','nurse','pharmacy','analysis laboratory'

if ($_SESSION['user_type'] !== 'nurse' && $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php?lang=$language");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الممرض</title>
    <style>
        /* إعادة تعيين الهوامش والحشوات الافتراضية لجميع العناصر */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            /* حساب عرض العنصر بحيث يشمل الحدود */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* تحديد الخط الافتراضي */
        }

        /* تنسيق جسم الصفحة */
        body {
            background-color: #f4f7fa;
            /* خلفية رمادية فاتحة مريحة للعين */
            direction: rtl;
            /* اتجاه النص من اليمين لليسار (لدعم العربية) */
        }

        /* تنسيق الشريط العلوي (الهيدر) */
        .top-bar {
            background: white;
            /* خلفية بيضاء */
            padding: 20px 40px;
            display: flex;
            /* استخدام flexbox للترتيب الأفقي */
            justify-content: space-between;
            /* توزيع العناصر بحيث يكون أولها في أقصى اليمين وآخرها في أقصى اليسار */
            align-items: center;
            /* محاذاة العناصر عمودياً في المنتصف */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            /* ظل خفيف أسفل الشريط */
        }

        /* تنسيق عنوان الصفحة داخل الشريط العلوي */
        .top-bar h2 {
            font-size: 22px;
            /* حجم الخط 22 بكسل */
            color: #1e2b3c;
            /* لون داكن (أزرق مائل للرمادي) */
        }

        /* تنسيق نص الترحيب */
        .welcome {
            color: #555;
            /* لون رمادي متوسط */
        }

        /* الحاوية الرئيسية التي تحتوي النموذج والجدول */
        .container {
            width: 95%;
            /* عرض 95% من الشاشة (ترك هوامش جانبية) */
            margin: 30px auto;
        }

        /* قسم نموذج الإدخال */
        .form-section {
            background: white;
            /* خلفية بيضاء */
            padding: 30px;
            border-radius: 10px;
            /* زوايا دائرية 10px */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            /* ظل خفيف لإبراز القسم */
            margin-bottom: 30px;
        }

        /* شبكة الحقول (ترتيب المدخلات في صفوف وأعمدة) */
        .form-grid {
            display: grid;
            /* تحويل العنصر إلى شبكة */
            grid-template-columns: repeat(3, 1fr);
            /* تقسيم الشبكة إلى 3 أعمدة متساوية العرض */
            gap: 15px;
            /* فجوة 15px بين الخلايا */
            margin-bottom: 20px;
            /* مسافة سفلية قبل زر الإضافة */
        }

        /* تنسيق جميع حقول الإدخال داخل الشبكة */
        .form-grid input {
            padding: 8px 10px;
            border: 1px solid #ddd;
            /* حد رمادي فاتح */
            border-radius: 6px;
            /* زوايا دائرية قليلاً */
            font-size: 13px;
            /* حجم الخط */
            height: 35px;
            /* ارتفاع ثابت لجميع الحقول */
        }

        /* تنسيق خانة الاختيار (checkbox) */
        .checkbox-box {
            display: flex;
            align-items: center;
            /* محاذاة عمودية في المنتصف */
            font-size: 13px;
            /* حجم الخط */
        }

        /* تنسيق زر الإضافة (أو التحديث) */
        .add-btn {
            width: 100%;
            /* الزر يأخذ العرض الكامل */
            padding: 14px;
            background-color: #4caf50;
            /* خلفية خضراء (لون النجاح) */
            color: white;
            /* لون النص أبيض */
            border: none;
            /* إزالة الحدود */
            border-radius: 8px;
            /* زوايا دائرية 8px */
            font-size: 15px;
            /* حجم الخط */
            cursor: pointer;
            /* مؤشر الفأرة يصبح يد */
        }

        /* تأثير عند تمرير الماوس على الزر */
        .add-btn:hover {
            background-color: #45a049;
        }

        /* قسم الجدول (عرض البيانات) */
        .table-section {
            background: white;
            /* خلفية بيضاء */
            padding: 25px;
            border-radius: 10px;
            /* زوايا دائرية 10px */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            /* ظل خفيف */
        }

        /* تنسيق الجدول نفسه */
        table {
            width: 100%;
            /* الجدول يأخذ العرض الكامل */
            border-collapse: collapse;
            /* دمج الحدود المتجاورة */
            table-layout: fixed;
            /* توزيع ثابت للأعمدة */
            text-align: center;
            /* محاذاة النص في الوسط */
        }

        /* تنسيق خلايا الرأس (عناوين الأعمدة) */
        table th {
            background-color: #f8f9fa;
            /* خلفية رمادية فاتحة جداً */
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
            /* حد سفلي رمادي */
            font-size: 13px;
            /* حجم الخط */
            text-align: center;
            /* توسيط النص أفقياً */
            vertical-align: middle;
            /* توسيط النص عمودياً */
            word-wrap: break-word;
            /* كسر الكلمات الطويلة إذا تجاوزت */
        }

        /* تنسيق خلايا البيانات (الصفوف) */
        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            /* حد سفلي رمادي فاتح */
            font-size: 13px;
            /* حجم الخط */
        }

        /* تنسيق خاص للصفوف التي تحمل علامة "محذوف" */
        .deleted-row {
            background-color: #f8d7da;
            /* خلفية حمراء فاتحة (لون تحذيري) */
        }

        /* تنسيق زر التعديل */
        .edit {
            background-color: orange;
            /* خلفية برتقالية */
            color: white;
            /* نص أبيض */
            border: none;
            /* بلا حدود */
            padding: 5px 10px;
            border-radius: 6px;
            /* زوايا دائرية */
            cursor: pointer;
            /* مؤشر يد */
        }

        /* تنسيق زر الحذف */
        .delete {
            background-color: red;
            /* خلفية حمراء */
            color: white;
            /* نص أبيض */
            border: none;
            /* بلا حدود */
            padding: 5px 10px;
            border-radius: 6px;
            /* زوايا دائرية */
            cursor: pointer;
            /* مؤشر يد */
        }
    </style>

    <style>
        .btn {
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-logout {
            background: #be2525;
            color: white;
        }
    </style>
</head>

<body>

    <!-- الشريط العلوي (الهيدر)-->
    <div class="top-bar">
        <h2>إدارة الممرض</h2>

        <?php if (isLoggedIn()): ?>
            <div class="welcome">مرحباً بك ي ممرض <?php echo $_SESSION['user_name']; ?>
                <a href="../logout.php?lang=<?php echo $language ?>" class="btn btn-logout">تسجيل الخروج</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- الحاوية الرئيسية للمحتوى -->
    <div class="container">

        <!--  قسم نموذج الإدخال  -->
        <div class="form-section">

            <!-- شبكة الحقول -->
            <div class="form-grid">
                <!-- حقل الاسم -->
                <input type="text" id="name" placeholder="الاسم">
                <!-- حقل رقم الهاتف -->
                <input type="text" id="phone" placeholder="رقم الهاتف">
                <!-- حقل وردية العمل -->
                <input type="text" id="shift" placeholder="وردية العمل">
                <!-- حقل سنوات الخبرة (رقم) -->
                <input type="number" id="experience" placeholder="سنوات الخبرة">
                <!-- حقل مواعيد الزيارة -->
                <input type="text" id="visitTime" placeholder="مواعيد الزيارة">
                <!-- حقل خدمة التوصيل -->
                <input type="text" id="delivery" placeholder="خدمة التوصيل">
                <!-- حقل نسبة الخصم (رقم) -->
                <input type="number" id="discount" placeholder="نسبة الخصم %">

                <!-- خانة اختيار لتحديد ما إذا كان العنصر محذوفاً -->
                <div class="checkbox-box">
                    <label>
                        <input type="checkbox" id="deleted">
                        تم الحذف
                    </label>
                </div>

                <!-- حقل اختياري: اسم الشخص الذي قام بالحذف -->
                <input type="text" id="deletedBy" placeholder="تم الحذف بواسطة (اختياري)">
            </div>

            <!-- زر الإضافة / التحديث (يستدعي دالة addItem) -->
            <button class="add-btn" onclick="addItem()">إضافة</button>

        </div>

        <!-- ==================== قسم الجدول لعرض البيانات ==================== -->
        <div class="table-section">
            <table>
                <!-- رأس الجدول (عناوين الأعمدة) -->
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>رقم الهاتف</th>
                        <th>وردية العمل</th>
                        <th>سنوات الخبرة</th>
                        <th>مواعيد الزيارة</th>
                        <th>خدمة التوصيل</th>
                        <th>نسبة الخصم</th>
                        <th>تم الحذف</th>
                        <th>تم الحذف بواسطة</th>
                        <th>تحكم</th> <!-- الأزرار (تعديل / حذف) -->
                    </tr>
                </thead>
                <!-- جسم الجدول (سيتم ملؤه بواسطة JavaScript) -->
                <tbody id="tableBody"></tbody>
            </table>
        </div>

    </div>

    <!-- كود JavaScript -->
    <script>
        // متغير لتحديد ما إذا كنا في وضع التعديل (يحمل index العنصر الجاري تعديله)
        let editIndex = null;

        // دالة تحميل البيانات من localStorage وعرضها في الجدول
        function loadData() {
            // جلب البيانات المخزنة تحت مفتاح "nurseData"، إذا لم توجد نستخدم مصفوفة فارغة
            let data = JSON.parse(localStorage.getItem("nurseData")) || [];
            let table = document.getElementById("tableBody");
            table.innerHTML = ""; // تفريغ الجدول قبل إعادة البناء

            // التكرار على كل عنصر في البيانات
            data.forEach((item, index) => {
                // إضافة صف جديد إلى الجدول (مع إضافة كلاس deleted-row إذا كان العنصر محذوفاً)
                table.innerHTML += `
                <tr class="${item.deleted ? 'deleted-row' : ''}">
                    <td>${item.name}</td>
                    <td>${item.phone}</td>
                    <td>${item.shift}</td>
                    <td>${item.experience}</td>
                    <td>${item.visitTime}</td>
                    <td>${item.delivery}</td>
                    <td>${item.discount}%</td>                     <!-- إضافة علامة النسبة المئوية -->
                    <td>${item.deleted ? "✔" : "✖"}</td>           <!-- علامة صح أو خطأ -->
                    <td>${item.deletedBy || ""}</td>                <!-- عرض اسم من حذف (إن وجد) -->
                    <td>
                        <button class="edit" onclick="editItem(${index})">تعديل</button>   <!-- زر تعديل -->
                        <button class="delete" onclick="deleteItem(${index})">حذف</button> <!-- زر حذف -->
                    </td>
                </tr>`;
            });
        }

        // دالة إضافة عنصر جديد أو تحديث عنصر موجود (حسب قيمة editIndex)
        function addItem() {
            // قراءة القيم من حقول الإدخال
            let name = document.getElementById("name").value;
            let phone = document.getElementById("phone").value;
            let shift = document.getElementById("shift").value;
            let experience = document.getElementById("experience").value;
            let visitTime = document.getElementById("visitTime").value;
            let delivery = document.getElementById("delivery").value;
            let discount = document.getElementById("discount").value;
            let deleted = document.getElementById("deleted").checked; // boolean
            let deletedBy = document.getElementById("deletedBy").value;

            // التحقق من إدخال الاسم (حقل إلزامي)
            if (name === "") return alert("من فضلك ادخل الاسم");

            // جلب البيانات الموجودة من localStorage
            let data = JSON.parse(localStorage.getItem("nurseData")) || [];

            // إنشاء كائن جديد يمثل العنصر المضاف
            let newItem = {
                name,
                phone,
                shift,
                experience,
                visitTime,
                delivery,
                discount,
                deleted,
                deletedBy
            };

            // إذا كان editIndex == null فهذا يعني إضافة جديدة، وإذا كان له قيمة فهو تعديل
            if (editIndex === null) {
                data.push(newItem); // إضافة إلى نهاية المصفوفة
            } else {
                data[editIndex] = newItem; // استبدال العنصر القديم
                editIndex = null; // إعادة تعيين متغير التعديل
            }

            // حفظ المصفوفة مرة أخرى في localStorage (بعد تحويلها لـ JSON)
            localStorage.setItem("nurseData", JSON.stringify(data));

            // تفريغ حقول الإدخال
            clearFields();

            // إعادة تحميل الجدول لعرض البيانات المحدثة
            loadData();
        }

        // دالة تعديل عنصر: تملأ الحقول ببيانات العنصر المحدد
        function editItem(index) {
            // جلب البيانات من localStorage
            let data = JSON.parse(localStorage.getItem("nurseData"));
            let item = data[index]; // العنصر المطلوب تعديله

            // وضع قيم العنصر في حقول الإدخال
            document.getElementById("name").value = item.name;
            document.getElementById("phone").value = item.phone;
            document.getElementById("shift").value = item.shift;
            document.getElementById("experience").value = item.experience;
            document.getElementById("visitTime").value = item.visitTime;
            document.getElementById("delivery").value = item.delivery;
            document.getElementById("discount").value = item.discount;
            document.getElementById("deleted").checked = item.deleted;
            document.getElementById("deletedBy").value = item.deletedBy;

            // تخزين index العنصر الذي يتم تعديله
            editIndex = index;
        }

        // دالة حذف عنصر نهائياً من المصفوفة
        function deleteItem(index) {
            // جلب البيانات
            let data = JSON.parse(localStorage.getItem("nurseData"));
            data.splice(index, 1); // إزالة العنصر ذي index المحدد
            localStorage.setItem("nurseData", JSON.stringify(data)); // حفظ التغييرات
            loadData(); // إعادة تحميل الجدول
        }

        // دالة تفريغ جميع حقول الإدخال (النصية و checkbox)
        function clearFields() {
            // اختيار جميع المدخلات داخل .form-grid
            document.querySelectorAll(".form-grid input").forEach(input => {
                if (input.type === "checkbox") {
                    input.checked = false; // إلغاء تحديد checkbox
                } else {
                    input.value = ""; // تفريغ الحقول النصية
                }
            });
        }

        // عند تحميل الصفحة، قم بتشغيل loadData لتحميل البيانات وعرضها
        window.onload = loadData;
    </script>

</body>

</html>