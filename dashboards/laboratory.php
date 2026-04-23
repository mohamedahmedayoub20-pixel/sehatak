<?php
include "../includes/auth.php";

$language = "ar";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $language = $_GET['lang'];
}

// $_SESSION['user_type'] => 'doctor','admin','user','patient','nurse','pharmacy','analysis laboratory'

if ($_SESSION['user_type'] !== 'analysis laboratory' && $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php?lang=$language");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة معمل التحاليل</title>
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
            /* هامش علوي وسفلي 30px، وتوسيط أفقي تلقائي */
        }

        /* قسم نموذج الإدخال */
        .form-section {
            background: white;
            /* خلفية بيضاء */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            /* ظل خفيف لإبراز القسم */
            margin-bottom: 30px;
            /* مسافة سفلية 30px قبل الجدول */
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
            /* تغيير الخلفية إلى درجة أغمق قليلاً */
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
            /* توزيع ثابت للأعمدة (لا يعتمد على المحتوى) */
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

    <!--  الشريط العلوي (الهيدر) -->
    <div class="top-bar">
        <h2>إدارة معمل التحاليل</h2>

        <?php if (isLoggedIn()): ?>
            <div class="welcome">مرحباً بك ي <?php echo $_SESSION['user_name']; ?>
                <a href="../logout.php?lang=<?php echo $language ?>" class="btn btn-logout">تسجيل الخروج</a>
            </div>
        <?php endif; ?>
    </div>

    <!--  الحاوية الرئيسية للمحتوى-->
    <div class="container">

        <!-- قسم نموذج الإدخال-->
        <div class="form-section">

            <!-- شبكة الحقول  -->
            <div class="form-grid">
                <!-- حقل الاسم -->
                <input type="text" id="name" placeholder="الاسم">
                <!-- حقل العنوان -->
                <input type="text" id="address" placeholder="العنوان">
                <!-- حقل التاريخ (نوع date) -->
                <input type="date" id="date">
                <!-- حقل نوع التحليل -->
                <input type="text" id="analysisType" placeholder="نوع التحليل">
                <!-- حقل حالة التحليل -->
                <input type="text" id="status" placeholder="حالة التحليل">
                <!-- حقل نتيجة التحليل -->
                <input type="text" id="result" placeholder="نتيجة التحليل">
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

        <!-- قسم الجدول لعرض البيانات-->
        <div class="table-section">
            <table>
                <!-- رأس الجدول (عناوين الأعمدة) -->
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>العنوان</th>
                        <th>التاريخ</th>
                        <th>نوع التحليل</th>
                        <th>حالة التحليل</th>
                        <th>نتيجة التحليل</th>
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
            // جلب البيانات المخزنة تحت مفتاح "labData"، إذا لم توجد نستخدم مصفوفة فارغة
            let data = JSON.parse(localStorage.getItem("labData")) || [];
            let table = document.getElementById("tableBody");
            table.innerHTML = ""; // تفريغ الجدول قبل إعادة البناء

            // التكرار على كل عنصر في البيانات
            data.forEach((item, index) => {
                // إضافة صف جديد إلى الجدول (مع إضافة كلاس deleted-row إذا كان العنصر محذوفاً)
                table.innerHTML += `
                <tr class="${item.deleted ? 'deleted-row' : ''}">
                    <td>${item.name}</td>
                    <td>${item.address}</td>
                    <td>${item.date}</td>
                    <td>${item.analysisType}</td>
                    <td>${item.status}</td>
                    <td>${item.result}</td>
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
            let address = document.getElementById("address").value;
            let date = document.getElementById("date").value;
            let analysisType = document.getElementById("analysisType").value;
            let status = document.getElementById("status").value;
            let result = document.getElementById("result").value;
            let delivery = document.getElementById("delivery").value;
            let discount = document.getElementById("discount").value;
            let deleted = document.getElementById("deleted").checked; // boolean
            let deletedBy = document.getElementById("deletedBy").value;

            // التحقق من إدخال الاسم (حقل إلزامي)
            if (name === "") return alert("من فضلك ادخل الاسم");

            // جلب البيانات الموجودة من localStorage
            let data = JSON.parse(localStorage.getItem("labData")) || [];

            // إنشاء كائن جديد يمثل العنصر المضاف
            let newItem = {
                name,
                address,
                date,
                analysisType,
                status,
                result,
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
            localStorage.setItem("labData", JSON.stringify(data));

            // تفريغ حقول الإدخال
            clearFields();

            // إعادة تحميل الجدول لعرض البيانات المحدثة
            loadData();
        }

        // دالة تعديل عنصر: تملأ الحقول ببيانات العنصر المحدد
        function editItem(index) {
            // جلب البيانات من localStorage
            let data = JSON.parse(localStorage.getItem("labData"));
            let item = data[index]; // العنصر المطلوب تعديله

            // وضع قيم العنصر في حقول الإدخال
            document.getElementById("name").value = item.name;
            document.getElementById("address").value = item.address;
            document.getElementById("date").value = item.date;
            document.getElementById("analysisType").value = item.analysisType;
            document.getElementById("status").value = item.status;
            document.getElementById("result").value = item.result;
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
            let data = JSON.parse(localStorage.getItem("labData"));
            data.splice(index, 1); // إزالة العنصر ذي index المحدد
            localStorage.setItem("labData", JSON.stringify(data)); // حفظ التغييرات
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