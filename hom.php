<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- عنوان الصفحة -->
    <title>إدارة الدكتور</title>

    <link rel="stylesheet" href="css/home.css">

</head>

<body>
    <!-- الشريط العلوي (الهيدر) -->
    <div class="top-bar">
        <h2>إدارة الدكتور</h2>
        <div class="welcome">مرحباً بك ي دكتور</div>
    </div>
    <!-- الحاوية الرئيسية -->
    <div class="container">
        <!-- قسم نموذج الإدخال -->
        <div class="form-section">

            <!-- شبكة حقول النموذج (grid) -->
            <div class="form-grid">
                <!-- حقل الاسم -->
                <input type="text" id="name" placeholder="الاسم">
                <!-- حقل رقم الهاتف -->
                <input type="text" id="phone" placeholder="رقم الهاتف">
                <!-- حقل وردية العمل -->
                <input type="text" id="shift" placeholder="وردية العمل">
                <!-- حقل الجنس -->
                <input type="text" id="gender" placeholder="الجنس">
                <!-- حقل التشخيص -->
                <input type="text" id="diagnosis" placeholder="التشخيص">
                <!-- حقل سنوات الخبرة (رقم) -->
                <input type="number" id="experience" placeholder="سنوات الخبرة">
                <!-- حقل خدمة التوصيل -->
                <input type="text" id="delivery" placeholder="خدمة التوصيل">

                <!-- خانة اختيار (checkbox) لتحديد حالة الحذف -->
                <div class="checkbox-box">
                    <label>
                        <input type="checkbox" id="deleted">
                        تم الحذف
                    </label>
                </div>

                <!-- حقل إضافي: تم الحذف بواسطة (اختياري) -->
                <input type="text" id="deletedBy" placeholder="تم الحذف بواسطة (اختياري)">
            </div>

            <!-- زر الإضافة / التعديل -->
            <button class="add-btn" onclick="addItem()">إضافة</button>

        </div>

        <!-- قسم الجدول لعرض البيانات -->
        <div class="table-section">
            <table>
                <!-- رأس الجدول يحتوي على أسماء الأعمدة -->
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>رقم الهاتف</th>
                        <th>وردية العمل</th>
                        <th>الجنس</th>
                        <th>التشخيص</th>
                        <th>سنوات الخبرة</th>
                        <th>خدمة التوصيل</th>
                        <th>تم الحذف</th>
                        <th>تم الحذف بواسطة</th>
                        <th>تحكم</th> <!-- أزرار التعديل والحذف -->
                    </tr>
                </thead>
                <!-- جسم الجدول سيتم ملؤه بواسطة JavaScript -->
                <tbody id="tableBody"></tbody>
            </table>
        </div>

    </div>

    <!-- كود JavaScript الخاص بالصفحة -->
    <script>
        // متغير لتحديد ما إذا كنا في وضع التعديل (يحمل index العنصر الجاري تعديله)
        let editIndex = null;

        // دالة تحميل وعرض البيانات من localStorage إلى الجدول
        function loadData() {
            // جلب البيانات من localStorage، إذا لم توجد نستخدم مصفوفة فارغة
            let data = JSON.parse(localStorage.getItem("doctorData")) || [];
            let table = document.getElementById("tableBody");
            table.innerHTML = ""; // تفريغ الجدول قبل إعادة البناء

            // المرور على كل عنصر في البيانات وإنشاء صف (row) جديد
            data.forEach((item, index) => {
                // إضافة صف للجدول، مع إضافة كلاس deleted-row إذا كان العنصر محذوفاً
                table.innerHTML += `
                <tr class="${item.deleted ? 'deleted-row' : ''}">
                    <td>${item.name}</td>
                    <td>${item.phone}</td>
                    <td>${item.shift}</td>
                    <td>${item.gender}</td>
                    <td>${item.diagnosis}</td>
                    <td>${item.experience}</td>
                    <td>${item.delivery}</td>
                    <td>${item.deleted ? "✔" : "✖"}</td>   <!-- علامة صح أو خطأ -->
                    <td>${item.deletedBy || ""}</td>        <!-- عرض اسم من حذف (إن وجد) -->
                    <td>
                        <button class="edit" onclick="editItem(${index})">تعديل</button>
                        <button class="delete" onclick="deleteItem(${index})">حذف</button>
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
            let gender = document.getElementById("gender").value;
            let diagnosis = document.getElementById("diagnosis").value;
            let experience = document.getElementById("experience").value;
            let delivery = document.getElementById("delivery").value;
            let deleted = document.getElementById("deleted").checked; // boolean
            let deletedBy = document.getElementById("deletedBy").value;

            // التحقق من إدخال الاسم (حقل إلزامي)
            if (name === "") return alert("من فضلك ادخل الاسم");

            // جلب البيانات الموجودة
            let data = JSON.parse(localStorage.getItem("doctorData")) || [];

            // إنشاء كائن العنصر الجديد
            let newItem = {
                name,
                phone,
                shift,
                gender,
                diagnosis,
                experience,
                delivery,
                deleted,
                deletedBy
            };

            // إذا كان editIndex = null فهذا معناه إضافة جديدة، وإلا تعديل على العنصر ذي index المحدد
            if (editIndex === null) {
                data.push(newItem); // إضافة للقائمة
            } else {
                data[editIndex] = newItem; // استبدال العنصر القديم
                editIndex = null; // إعادة تعيين المتغير بعد التعديل
            }

            // حفظ المصفوفة في localStorage
            localStorage.setItem("doctorData", JSON.stringify(data));

            // تفريغ الحقول بعد الإضافة/التعديل
            clearFields();

            // إعادة تحميل الجدول
            loadData();
        }

        // دالة تعديل عنصر: تملأ الحقول ببيانات العنصر المختار
        function editItem(index) {
            let data = JSON.parse(localStorage.getItem("doctorData"));
            let item = data[index];

            // وضع القيم في حقول الإدخال
            document.getElementById("name").value = item.name;
            document.getElementById("phone").value = item.phone;
            document.getElementById("shift").value = item.shift;
            document.getElementById("gender").value = item.gender;
            document.getElementById("diagnosis").value = item.diagnosis;
            document.getElementById("experience").value = item.experience;
            document.getElementById("delivery").value = item.delivery;
            document.getElementById("deleted").checked = item.deleted;
            document.getElementById("deletedBy").value = item.deletedBy;

            // تخزين index العنصر الذي يتم تعديله
            editIndex = index;
        }

        // دالة حذف عنصر نهائياً من المصفوفة
        function deleteItem(index) {
            let data = JSON.parse(localStorage.getItem("doctorData"));
            data.splice(index, 1); // إزالة العنصر بال index المحدد
            localStorage.setItem("doctorData", JSON.stringify(data)); // حفظ التغييرات
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

        // عند تحميل الصفحة، قم بتحميل البيانات وعرضها
        window.onload = loadData;
    </script>

</body>

</html>