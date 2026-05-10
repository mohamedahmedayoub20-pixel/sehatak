<?php
include "../includes/auth.php";
include "../includes/database.php";

$language = "ar";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $language = $_GET['lang'];
}

// التأكد من أن المستخدم مسؤول (Admin)
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php?lang=$language");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'add-user') {
        // document.getElementById('formAction
        $firstName = $_POST['firstName'];
        $lastName  = $_POST['lastName'];
        $email     = $_POST['email'];
        $address   = $_POST['address'];
        $password  = $_POST['password'];
        $confirm   = $_POST['passwordConfirm'];
        $userType  = $_POST['userType'];

        try {

            if (!isset($email)) {
                $error = "Email is required";
            }

            if (!isset($password)) {
                $error = "password is required";
            }

            if (!isset($confirm)) {
                $error = "password confirm is required";
            }

            if (isset($password) && isset($confirm) && $password != $confirm) {
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

                    $error = '';
                    //header("Location: index.php");
                    // exit();
                } else {
                    $error = "Failed to create account";
                }
            }
        } catch (PDOException $e) {
            $error = "خطأ في القاعدة: " . $e->getMessage();
        }
    } else if ($action == 'update-user-status') {
        $userId = $_POST['userId'];
        $newStatus = $_POST['userType']; // هنا نستخدم userType لتحديد الحالة الجديدة

        try {
            $stmt = $pdo->prepare("UPDATE user SET account_status = ? WHERE user_id = ?");
            $result = $stmt->execute([$newStatus, $userId]);

            if ($result) {
                $error = '';
            } else {
                $error = "Failed to update user status";
            }
        } catch (PDOException $e) {
            $error = "خطأ في القاعدة: " . $e->getMessage();
        }
    } else if ($action == 'update-user-type') {
        $userId = $_POST['userId'];
        $newType = $_POST['userType'];

        try {
            // 1. Define the mapping between account_type and the database table name
            $typeToTable = [
                'doctor'              => 'doctor',
                'nurse'               => 'nurse',
                'patient'             => 'patient',
                'pharmacy'            => 'pharmacy',
                'analysis laboratory' => 'analysis laboratory' // Note: underscore for DB table name
            ];

            // 2. If the new type is one of the specialized roles, check/insert profile
            if (array_key_exists($newType, $typeToTable)) {
                $tableName = $typeToTable[$newType];

                // Check if profile exists in the specific table
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM `$tableName` WHERE user_id = ?");
                $checkStmt->execute([$userId]);
                $exists = $checkStmt->fetchColumn();

                if (!$exists) {
                    // Get the user's name to populate the new profile
                    $userStmt = $pdo->prepare("SELECT name FROM user WHERE user_id = ?");
                    $userStmt->execute([$userId]);
                    $userData = $userStmt->fetch();
                    $userName = $userData ? $userData['name'] : 'New Profile';

                    // Insert into the corresponding table
                    // Using backticks for table name to handle spaces/reserved words safely
                    $insertStmt = $pdo->prepare("INSERT INTO `$tableName` (user_id, name, deleted) VALUES (?, ?, '0')");
                    $insertStmt->execute([$userId, $userName]);
                }
            }

            // 3. Update the main user table
            $stmt = $pdo->prepare("UPDATE user SET account_type = ? WHERE user_id = ?");
            $result = $stmt->execute([$newType, $userId]);

            if ($result) {
                $error = '';
            } else {
                $error = "Failed to update user type";
            }
        } catch (PDOException $e) {
            $error = "خطأ في القاعدة: " . $e->getMessage();
        }
    } else {
        $error = "Unknown action";
    }
}

$userQuery = $pdo->prepare("SELECT user_id,name,email,address,account_type,account_status,deleted,deleted_by,
(SELECT nurse_id from nurse where nurse.user_id = user.user_id) AS 'nurse_profile_id',
(SELECT patient_id from patient where patient.user_id = user.user_id) AS 'patient_profile_id',
(SELECT doctor_id from doctor where doctor.user_id = user.user_id) AS 'doctor_profile_id',
(SELECT pharmacy_id from pharmacy where pharmacy.user_id = user.user_id) AS 'pharmacy_profile_id',
(SELECT analysis_laboratory_id from `analysis laboratory` where `analysis laboratory`.user_id = user.user_id) AS 'lab_profile_id'
FROM user");
$userQuery->execute();
$users = $userQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch all types of visits (Doctor, Nurse, Lab) as "Orders"
$ordersQuery = $pdo->prepare("
    SELECT 'doctor' as type, v.`visiting_a_ doctor_id` as id, u.name as patient_name, d.name as provider_name, v.visit_time, v.is_deleted
    FROM `visiting a doctor` v
    JOIN patient p ON v.patient_id = p.patient_id
    JOIN user u ON p.user_id = u.user_id
    JOIN doctor d ON v.doctor_id = d.doctor_id
    
    UNION ALL
    
    SELECT 'nurse' as type, n_v.`visiting_a_ nurse_id` as id, u.name as patient_name, n.name as provider_name, n_v.visit_time, n_v.is_deleted
    FROM `visiting a nurse` n_v
    JOIN patient p ON n_v.patient_id = p.patient_id
    JOIN user u ON p.user_id = u.user_id
    JOIN nurse n ON n_v.nurse_id = n.nurse_id
    
    UNION ALL
    
    SELECT 'lab' as type, l_v.`visiting_the_analysis_laboratory_id` as id, u.name as patient_name, al.name as provider_name, l_v.visit_time, 0 as is_deleted
    FROM `visiting the analysis laboratory` l_v
    JOIN patient p ON l_v.patient_id = p.patient_id
    JOIN user u ON p.user_id = u.user_id
    JOIN `analysis laboratory` al ON l_v.analysis_laboratory_id = al.analysis_laboratory_id
    
    ORDER BY visit_time DESC
");
$ordersQuery->execute();
$allOrders = $ordersQuery->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - الإدارة</title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #f4f7fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-bg);
            direction: rtl;
        }

        /* الهيدر */
        .top-bar {
            background: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .top-bar h2 {
            font-size: 20px;
            color: var(--primary-color);
        }

        .welcome {
            display: flex;
            align-items: center;
            gap: 15px;
            color: #555;
        }

        /* أزرار التنقل الرئيسية */
        .nav-tabs {
            background: white;
            padding: 10px 40px;
            display: flex;
            gap: 10px;
            border-bottom: 1px solid #ddd;
        }

        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: #eee;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }

        .tab-btn.active {
            background: var(--accent-color);
            color: white;
        }

        /* الحاوية */
        .container {
            width: 95%;
            margin: 20px auto;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        /* تنسيق الجداول */
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        th {
            background: #f8f9fa;
            color: var(--primary-color);
        }

        /* الأزرار */
        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
            transition: 0.3s;
        }

        .btn-add {
            background: var(--success-color);
            color: white;
        }

        .btn-edit {
            background: var(--warning-color);
            color: white;
        }

        .btn-delete {
            background: var(--danger-color);
            color: white;
        }

        .btn-status {
            background: var(--accent-color);
            color: white;
        }

        .btn-logout {
            background: #be2525;
            color: white;
        }

        .disabled-user {
            opacity: 0.6;
            background-color: #f9f9f9;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 11px;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-disabled {
            background: #f8d7da;
            color: #721c24;
        }

        /* نافذة الإضافة والتعديل (Modal) */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <div class="top-bar">
        <h2>لوحة تحكم المسؤول</h2>
        <div class="welcome">
            <span>مرحباً، <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="../logout.php?lang=<?php echo $language ?>" class="btn btn-logout">تسجيل الخروج</a>
        </div>
    </div>

    <!-- أزرار التنقل الرئيسية -->
    <div class="nav-tabs">
        <button class="tab-btn active" onclick="showSection('users-section')">إدارة المستخدمين</button>
        <button class="tab-btn" onclick="showSection('orders-section')">إدارة الطلبات</button>
    </div>

    <div class="container">

        <?php if (isset($error) && !empty($error)): ?>
            <div class="card" style="background: #f8d7da; color: #721c24; margin-bottom: 20px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- قسم إدارة المستخدمين -->
        <div id="users-section" class="content-section active">
            <div class="card">
                <div class="card-header">
                    <h3>المستخدمين المسجلين</h3>
                    <button class="btn btn-add" onclick="openUserModal()">+ إضافة مستخدم جديد</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $row): ?>
                            <tr class="<?php echo ($row['deleted'] != 0) ? 'disabled-user' : ''; ?>">
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['account_type']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo ($row['account_status'] != 'active') ? 'status-disabled' : 'status-active'; ?>">
                                        <?php echo htmlspecialchars($row['account_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-edit" onclick="editUser(<?php echo htmlspecialchars($row['user_id']); ?>, '<?php echo htmlspecialchars($row['name']); ?>')">تعديل</button>
                                    <button class="btn btn-status" onclick="toggleUser(<?php echo htmlspecialchars($row['user_id']); ?>, '<?php echo htmlspecialchars($row['name']); ?>')">
                                        تغيير الحالة
                                    </button>

                                    <?php if ($row['account_type'] == 'doctor') {
                                        if (isset($row['doctor_profile_id']) && $row['doctor_profile_id'] > 0) { ?>
                                            <button class="btn" onclick="openDoctorProfile(<?php echo htmlspecialchars($row['doctor_profile_id']); ?>)">عرض الملف </button>
                                        <?php } else { ?>
                                            <button class="btn" disabled>لا يوجد ملف</button>
                                        <?php }
                                    } else if ($row['account_type'] == 'nurse') { ?>
                                        <?php if (isset($row['nurse_profile_id']) && $row['nurse_profile_id'] > 0) { ?>
                                            <button class="btn" onclick="openNurseProfile(<?php echo htmlspecialchars($row['nurse_profile_id']); ?>)">عرض الملف </button>
                                        <?php } else { ?>
                                            <button class="btn" disabled>لا يوجد ملف</button>
                                        <?php }
                                    } else if ($row['account_type'] == 'pharmacy') {
                                        if (isset($row['pharmacy_profile_id']) && $row['pharmacy_profile_id'] > 0) { ?>
                                            <button class="btn" onclick="openPharmacyProfile(<?php echo htmlspecialchars($row['pharmacy_profile_id']); ?>)">عرض الملف </button>
                                        <?php } else { ?>
                                            <button class="btn" disabled>لا يوجد ملف</button>
                                        <?php }
                                    } else if ($row['account_type'] == 'analysis laboratory') {
                                        if (isset($row['lab_profile_id']) && $row['lab_profile_id'] > 0) { ?>
                                            <button class="btn" onclick="openLabProfile(<?php echo htmlspecialchars($row['lab_profile_id']); ?>)">عرض الملف </button>
                                        <?php } else { ?>
                                            <button class="btn" disabled>لا يوجد ملف</button>
                                        <?php }
                                    } else if ($row['account_type'] == 'patient') {
                                        if (isset($row['patient_profile_id']) && $row['patient_profile_id'] > 0) { ?>
                                            <button class="btn" onclick="openPatientProfile(<?php echo htmlspecialchars($row['patient_profile_id']); ?>)">عرض الملف </button>
                                        <?php } else { ?>
                                            <button class="btn" disabled>لا يوجد ملف</button>
                                    <?php }
                                    } ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- قسم إدارة الطلبات -->
        <div id="orders-section" class="content-section">
            <div class="card">
                <div class="card-header">
                    <h3>قائمة الزيارات والطلبات (أطباء، ممرضين، معامل)</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>النوع</th>
                            <th>اسم المريض</th>
                            <th>مقدم الخدمة</th>
                            <th>التاريخ والوقت</th>
                            <th>الحالة</th>
                            <!--<th>التحكم</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allOrders as $order): ?>
                            <tr class="<?php echo ($order['is_deleted'] != 0) ? 'disabled-user' : ''; ?>">
                                <td>
                                    <?php
                                    if ($order['type'] == 'doctor') echo "زيارة طبيب";
                                    elseif ($order['type'] == 'nurse') echo "زيارة ممرض";
                                    else echo "تحليل معمل";
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($order['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['provider_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['visit_time']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo ($order['is_deleted'] == 0) ? 'status-active' : 'status-disabled'; ?>">
                                        <?php echo ($order['is_deleted'] == 0) ? 'نشط' : 'ملغي'; ?>
                                    </span>
                                </td>
                                <?php /*<td>
                                    <button class="btn btn-delete" onclick="alert('حذف الطلب رقم: <?php echo $order['id']; ?>')">إلغاء الزيارة</button>
                                </td>*/ ?>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($allOrders)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">لا توجد طلبات حالياً</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal المستخدمين -->
    <div id="userModal" class="modal">
        <div class="modal-content" style="width: 500px;">
            <h3 id="userModalTitle">إضافة مستخدم جديد</h3>
            <hr><br>

            <form method="POST" id="userForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="userId" id="formUserId" value="">

                <div class="signup1" style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div class="name" style="flex: 1;">
                        <h4>الاسم الأول</h4>
                        <input name="firstName" id="firstName" type="text" style="width: 100%;" required>
                    </div>
                    <div class="name" style="flex: 1;">
                        <h4>الاسم الأخير</h4>
                        <input name="lastName" id="lastName" type="text" style="width: 100%;" required>
                    </div>
                </div>

                <div class="signup2" style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div class="email" style="flex: 1;">
                        <h4>الأيميل</h4>
                        <input name="email" id="email" type="email" style="width: 100%;" required>
                    </div>
                    <div class="email" style="flex: 1;">
                        <h4>العنوان</h4>
                        <input name="address" id="address" type="text" style="width: 100%;" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <h4>الباسورد</h4>
                    <input name="password" id="password" type="password" style="width: 100%;" required>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <h4>تأكيد الباسورد</h4>
                    <input name="passwordConfirm" id="passwordConfirm" type="password" style="width: 100%;" required>
                </div>

                <div class="account-box" style="margin-bottom: 20px;">
                    <h3>نوع الحساب</h3>
                    <select name="userType" id="userType" style="width: 100%; padding: 8px;" required>
                        <option value="" disabled selected>اختر النوع...</option>
                        <option value="doctor">طبيب</option>
                        <option value="nurse">ممرض</option>
                        <option value="pharmacy">صيدليه</option>
                        <option value="analysis laboratory">معمل تحاليل</option>
                        <option value="patient">مريض</option>
                        <option value="admin">مسؤل</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-add">حفظ البيانات</button>
                    <button type="button" class="btn btn-delete" onclick="closeModal('userModal')">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <div id="userStatusChangeModal" class="modal">
        <div class="modal-content" style="width: 500px;">
            <h3 id="userStatusChangeModalTitle">تغيير حالة المستخدم</h3>
            <hr><br>

            <form method="POST" id="userStatusForm">
                <input type="hidden" name="action" id="statusFormAction" value="update-user-status">
                <input type="hidden" name="userId" id="statusFormUserId" value="">

                <div class="account-box" style="margin-bottom: 20px;">
                    <h3>حاله الحساب</h3>
                    <select name="userType" id="userType" style="width: 100%; padding: 8px;" required>
                        <option value="" disabled selected>اختر النوع...</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                        <option value="suspended">معلق</option>
                        <option value="deleted">محذوف</option>
                        <option value="archived">مؤرشف</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="blocked">محظور</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-add">حفظ البيانات</button>
                    <button type="button" class="btn btn-delete" onclick="closeModal('userStatusChangeModal')">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <div id="userTypeChangeModal" class="modal">
        <div class="modal-content" style="width: 500px;">
            <h3 id="userTypeChangeModalTitle">تغيير نوع الحساب</h3>
            <hr><br>

            <form method="POST" id="userTypeForm">
                <input type="hidden" name="action" id="typeFormAction" value="update-user-type">
                <input type="hidden" name="userId" id="typeFormUserId" value="">

                <div class="account-box" style="margin-bottom: 20px;">
                    <h3>نوع الحساب</h3>
                    <select name="userType" id="userType" style="width: 100%; padding: 8px;" required>
                        <option value="" disabled selected>اختر النوع...</option>
                        <option value="doctor">طبيب</option>
                        <option value="nurse">ممرض</option>
                        <option value="pharmacy">صيدليه</option>
                        <option value="analysis laboratory">معمل تحاليل</option>
                        <option value="patient">مريض</option>
                        <option value="admin">مسؤل</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-add">حفظ البيانات</button>
                    <button type="button" class="btn btn-delete" onclick="closeModal('userTypeChangeModal')">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal الطلبات -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <h3 id="orderModalTitle">إضافة طلب</h3>
            <hr><br>
            <div class="form-group">
                <label>اسم العميل</label>
                <input type="text" id="orderCustomer">
            </div>
            <div class="form-group">
                <label>المنتج / الخدمة</label>
                <input type="text" id="orderService">
            </div>
            <div class="form-group">
                <label>حالة الطلب</label>
                <select id="orderStatus">
                    <option value="قيد المعالجة">قيد المعالجة</option>
                    <option value="مكتمل">مكتمل</option>
                    <option value="ملغي">ملغي</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-add" id="saveOrderBtn" onclick="saveOrder()">حفظ</button>
                <button class="btn btn-delete" onclick="closeModal('orderModal')">إلغاء</button>
            </div>
        </div>
    </div>

    <script>
        function openDoctorProfile(profileId) {
            alert("فتح ملف الطبيب: " + profileId);
        }

        function openNurseProfile(profileId) {
            alert("فتح ملف الممرض: " + profileId);
        }

        function openPharmacyProfile(profileId) {
            alert("فتح ملف الصيدلية: " + profileId);
        }

        function openLabProfile(profileId) {
            alert("فتح ملف المعمل: " + profileId);
        }

        function openPatientProfile(profileId) {
            alert("فتح ملف المريض: " + profileId);
        }

        let orders = JSON.parse(localStorage.getItem('site_orders')) || [{
            id: 101,
            customer: "محمد حسن",
            service: "كشف منزلي",
            date: "2023-10-25",
            status: "قيد المعالجة"
        }];

        let editUserId = null;
        let editOrderId = null;

        // تبديل الأقسام
        function showSection(sectionId) {
            document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
            event.target.classList.add('active');
        }

        function openUserModal() {
            editUserId = null;

            document.getElementById('userModalTitle').innerText = "إضافة مستخدم";
            document.getElementById('formAction').value = "add-user";
            document.getElementById('firstName').value = "";
            document.getElementById('lastName').value = "";
            document.getElementById('email').value = "";
            document.getElementById('address').value = "";
            document.getElementById('password').value = "";
            document.getElementById('passwordConfirm').value = "";
            document.getElementById('userModal').style.display = 'flex';
        }

        function editUser(userId, userName) {
            document.getElementById('userTypeChangeModalTitle').innerText = "تغيير حالة المستخدم: " + userName;
            document.getElementById('typeFormUserId').value = userId;
            document.getElementById('userTypeChangeModal').style.display = 'flex';
        }

        function saveUser() {
            const name = document.getElementById('userName').value;
            const email = document.getElementById('userEmail').value;
            const role = document.getElementById('userRole').value;

            if (!name || !email) return alert("يرجى ملء كافة الحقول");

            if (editUserId) {
                const index = users.findIndex(u => u.id === editUserId);
                users[index] = {
                    ...users[index],
                    name,
                    email,
                    role
                };
            } else {
                users.push({
                    id: Date.now(),
                    name,
                    email,
                    role,
                    active: true
                });
            }
            closeModal('userModal');
            renderUsers();
        }

        function toggleUser(userId, userName) {
            document.getElementById('userStatusChangeModalTitle').innerText = "تغيير حالة المستخدم: " + userName;
            document.getElementById('statusFormUserId').value = userId;
            document.getElementById('userStatusChangeModal').style.display = 'flex';
        }

        // --- إدارة الطلبات ---
        function renderOrders() {
            const tbody = document.getElementById('ordersTableBody');
            tbody.innerHTML = '';
            orders.forEach(order => {
                tbody.innerHTML += `
                    <tr>
                        <td>#${order.id}</td>
                        <td>${order.customer}</td>
                        <td>${order.service}</td>
                        <td>${order.date}</td>
                        <td>${order.status}</td>
                        <td>
                            <button class="btn btn-edit" onclick="editOrder(${order.id})">تعديل</button>
                            <button class="btn btn-delete" onclick="deleteOrder(${order.id})">حذف</button>
                        </td>
                    </tr>
                `;
            });
            localStorage.setItem('site_orders', JSON.stringify(orders));
        }

        function openOrderModal() {
            editOrderId = null;
            document.getElementById('orderModalTitle').innerText = "إضافة طلب";
            document.getElementById('orderCustomer').value = "";
            document.getElementById('orderService').value = "";
            document.getElementById('orderModal').style.display = 'flex';
        }

        function editOrder(id) {
            const order = orders.find(o => o.id === id);
            editOrderId = id;
            document.getElementById('orderModalTitle').innerText = "تعديل الطلب";
            document.getElementById('orderCustomer').value = order.customer;
            document.getElementById('orderService').value = order.service;
            document.getElementById('orderStatus').value = order.status;
            document.getElementById('orderModal').style.display = 'flex';
        }

        function saveOrder() {
            const customer = document.getElementById('orderCustomer').value;
            const service = document.getElementById('orderService').value;
            const status = document.getElementById('orderStatus').value;

            if (!customer || !service) return alert("يرجى ملء كافة الحقول");

            if (editOrderId) {
                const index = orders.findIndex(o => o.id === editOrderId);
                orders[index] = {
                    ...orders[index],
                    customer,
                    service,
                    status
                };
            } else {
                orders.push({
                    id: Math.floor(1000 + Math.random() * 9000),
                    customer,
                    service,
                    status,
                    date: new Date().toISOString().split('T')[0]
                });
            }
            closeModal('orderModal');
            renderOrders();
        }

        function deleteOrder(id) {
            if (confirm("هل أنت متأكد من حذف هذا الطلب؟")) {
                orders = orders.filter(o => o.id !== id);
                renderOrders();
            }
        }

        // أدوات عامة
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = "none";
            }
        }

        // التحميل الأولي
        window.onload = () => {
            renderOrders();
        };
    </script>
</body>

</html>