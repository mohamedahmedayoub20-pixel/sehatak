<?php
include "../includes/auth.php";
include "../includes/database.php";

// 1. Language Logic
$language = "ar"; // Default
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $language = $_GET['lang'];
}

// 2. Translation Dictionary
$lang_data = [
    'en' => [
        'title' => 'Nurse Management - Dashboard',
        'welcome' => 'Welcome, ',
        'logout' => 'Logout',
        'dashboard' => 'Dashboard',
        'my_reservations' => 'My Reservations',
        'clinic_settings' => 'Settings',
        'profile' => 'Profile',
        'current_records' => 'Current Records',
        'search' => 'Search...',
        'name' => 'Name',
        'phone' => 'Phone Number',
        'diagnosis' => 'Diagnosis',
        'time' => 'Time',
        'control' => 'Control',
        'upcoming_today' => 'Upcoming Reservations for Today',
        'no_visits' => 'No new reservations today.',
        'specialist' => 'Cardiology Specialist',
        'address' => 'Address',
        'exp' => 'Years of Experience',
        'years' => 'Years',
        'working_hours' => 'Working Hours',
        'fees' => 'Consultation Fees',
        'currency' => 'EGP',
        'edit_data' => 'Edit Data',
        'preview' => 'Preview Clinic',
        'account_data' => 'Personal Account Data',
        'email' => 'Email',
        'password' => 'Password',
        'bio' => 'Bio (Visible to patients)',
        'save' => 'Save Changes',
        'warning' => 'Updating this data will change how you appear in patient searches.',
        'notifications' => 'Notifications',
        'clear_all' => 'Clear All'
    ],
    'ar' => [
        'title' => 'إدارة الممرض - لوحة التحكم',
        'welcome' => 'مرحباً بك، ',
        'logout' => 'تسجيل الخروج',
        'dashboard' => 'لوحة التحكم',
        'my_reservations' => 'حجوزاتي',
        'clinic_settings' => 'إعدادات',
        'profile' => 'الملف الشخصي',
        'current_records' => 'السجل الحالي',
        'search' => 'بحث...',
        'name' => 'الاسم',
        'phone' => 'رقم الهاتف',
        'diagnosis' => 'التشخيص',
        'time' => 'الوقت',
        'control' => 'تحكم',
        'upcoming_today' => 'قائمة الحجوزات القادمة لليوم',
        'no_visits' => 'لا توجد حجوزات جديدة اليوم.',
        'specialist' => 'اخصائي قلب',
        'address' => 'العنوان',
        'exp' => 'سنوات الخبرة',
        'years' => 'سنة',
        'working_hours' => 'مواعيد العمل',
        'fees' => 'حساب الكشف',
        'currency' => 'جنيه',
        'edit_data' => 'تعديل البيانات',
        'preview' => 'معاينة العيادة',
        'account_data' => 'بيانات الحساب الشخصي',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'bio' => 'نبذة تعريفية (تظهر للمرضى)',
        'save' => 'حفظ التغييرات',
        'warning' => 'تحديث هذه البيانات سيؤدي إلى تغيير طريقة ظهورك في بحث المرضى.',
        'notifications' => 'التنبيهات',
        'clear_all' => 'مسح الكل'
    ]
];

$t = $lang_data[$language]; // Shortcut for translation array

// 3. Auth & Database Logic
if ($_SESSION['user_type'] !== 'nurse' && $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php?lang=$language");
    exit();
}

$sql = "SELECT 
            `patient`.`name` AS patient_name, 
            `patient`.`phone_number` AS patient_phone,
            `nurse`.`name` AS nurse_name, 
            `visiting a nurse`.`visiting_dates`,
             `visiting a nurse`.visit_time
        FROM `visiting a nurse`
        JOIN `nurse` ON `nurse`.`nurse_id` = `visiting a nurse`.`nurse_id` 
        JOIN `patient` ON `patient`.`patient_id` = `visiting a nurse`.`patient_id`
        JOIN `user` ON `user`.`user_id` = `nurse`.`user_id`
        WHERE `user`.`user_id`= :user_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$visits = $stmt->fetchAll();

$todaysVisits = array_filter($visits, function ($visit) {
    $visitDate = new DateTime($visit['visit_time']);
    $today = new DateTime();
    return $visitDate->format('Y-m-d') === $today->format('Y-m-d');
});
?>

<!DOCTYPE html>
<html lang="<?php echo $language; ?>" dir="<?php echo ($language == 'ar') ? 'rtl' : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap');

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8fafc;
        }

        .nav-item-active {
            background-color: #4ade80;
            color: white;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            height: 18px;
            width: 18px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            border: 2px solid white;
        }

        .custom-shadow {
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">

    <header class="bg-white border-b sticky top-0 z-50">

        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-bold text-gray-800"><?php echo $t['dashboard']; ?></h1>
                <div class="hidden md:flex items-center gap-2 text-sm text-gray-500 <?php echo $language == 'ar' ? 'mr-4' : 'ml-4'; ?>">
                    <span><?php echo $t['welcome']; ?></span>
                    <span class="font-bold text-blue-600"><?php echo $_SESSION['user_name']; ?></span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Language Switcher -->
                <a href="?lang=<?php echo $language == 'ar' ? 'en' : 'ar'; ?>" class="text-sm font-bold text-gray-600 hover:text-blue-600 px-2">
                    <?php echo $language == 'ar' ? 'English' : 'العربية'; ?>
                </a>

                <?php if (count($todaysVisits) > 0): ?>

                    <div class="relative cursor-pointer group" onclick="toggleNotifications()">
                        <div class="p-2 bg-gray-100 rounded-full hover:bg-gray-200 transition-all">
                            <i class="fa-solid fa-bell text-gray-600"></i>
                        </div>
                        <span id="notif-count" class="notification-badge"><?php echo count($todaysVisits); ?></span>

                        <div id="notif-dropdown" class="hidden absolute <?php echo $language == 'ar' ? 'left-0' : 'right-0'; ?> mt-3 w-80 bg-white rounded-xl shadow-xl border overflow-hidden">
                            <div class="p-4 bg-gray-50 border-b font-bold flex justify-between items-center">
                                <span><?php echo $t['notifications']; ?></span>
                                <span class="text-xs text-blue-500 cursor-pointer"><?php echo $t['clear_all']; ?></span>
                            </div>
                            <div class="max-h-64 overflow-y-auto" id="notification-list">
                                <?php foreach ($todaysVisits as $visit): ?>
                                    <div class="p-4 border-b hover:bg-gray-50 transition-all">
                                        <div class="text-sm font-medium"><?php echo $visit['patient_name']; ?></div>
                                        <div class="text-xs text-gray-500"><?php echo $visit['visiting_dates']; ?> - <?php echo (new DateTime($visit['visit_time']))->format('H:i'); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="../logout.php?lang=<?php echo $language ?>" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-700 transition-all"><?php echo $t['logout']; ?></a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col lg:flex-row gap-8">
        <aside class="lg:w-64 flex-shrink-0">
            <nav class="flex flex-col gap-2 sticky top-24">
                <a href="../index.php?lang=<?php echo $language ?>" class="logo" style="display: flex; align-items: center; gap: 8px; padding: 12px; border-radius: 12px; font-weight: bold; color: #4ade80;">
                    <img width="64" src="../images/new-logo.jpeg" alt="Sehatak Logo" />
                </a>
                <button onclick="switchTab('dashboard')" id="btn-dashboard" class="nav-btn nav-item-active flex items-center gap-3 p-3 rounded-xl font-medium transition-all">
                    <i class="fa-solid fa-chart-line w-5"></i> <?php echo $t['dashboard']; ?>
                </button>
                <button onclick="switchTab('reservations')" id="btn-reservations" class="nav-btn hover:bg-white flex items-center gap-3 p-3 rounded-xl text-gray-600 font-medium transition-all">
                    <i class="fa-solid fa-calendar-day w-5"></i> <?php echo $t['my_reservations']; ?>
                </button>
                <?php if ($_SESSION['user_type'] == 'nurse') : ?>
                    <button onclick="switchTab('settings')" id="btn-settings" class="nav-btn hover:bg-white flex items-center gap-3 p-3 rounded-xl text-gray-600 font-medium transition-all">
                        <i class="fa-solid fa-house-medical w-5"></i> <?php echo $t['clinic_settings']; ?>
                    </button>
                <?php endif; ?>
                <button onclick="switchTab('profile')" id="btn-profile" class="nav-btn hover:bg-white flex items-center gap-3 p-3 rounded-xl text-gray-600 font-medium transition-all">
                    <i class="fa-solid fa-user-doctor w-5"></i> <?php echo $t['profile']; ?>
                </button>
            </nav>
        </aside>

        <main class="flex-grow">
            <!-- Dashboard View -->
            <div id="tab-dashboard" class="tab-content">
                <div class="bg-white rounded-2xl custom-shadow overflow-hidden">
                    <div class="p-6 border-b flex justify-between items-center">
                        <h2 class="text-lg font-bold"><?php echo $t['current_records']; ?></h2>
                        <input type="text" placeholder="<?php echo $t['search']; ?>" class="text-sm p-2 border rounded-lg bg-gray-50">
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full <?php echo $language == 'ar' ? 'text-right' : 'text-left'; ?>">
                            <thead class="bg-gray-50 border-b text-gray-600">
                                <tr>
                                    <th class="p-4 font-semibold text-sm"><?php echo $t['name']; ?></th>
                                    <th class="p-4 font-semibold text-sm"><?php echo $t['phone']; ?></th>
                                    <th class="p-4 font-semibold text-sm"><?php echo $t['time']; ?></th>
                                    <th class="p-4 font-semibold text-sm"><?php echo $t['time']; ?></th>
                                    <th class="p-4 font-semibold text-sm"><?php echo $t['control']; ?></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <?php foreach ($visits as $visit): ?>
                                    <tr class="hover:bg-gray-50 transition-all">
                                        <td class="p-4 text-sm font-medium"><?php echo $visit['patient_name']; ?></td>
                                        <td class="p-4 text-sm text-gray-500"><?php echo $visit['patient_phone']; ?></td>
                                        <td class="p-4 text-sm"><?php echo $visit['visit_time']; ?></td>
                                        <td class="p-4 text-sm"><?php echo $visit['visiting_dates']; ?></td>
                                        <td class="p-4 text-sm">
                                            <div class="flex gap-2">
                                                <button class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg"><i class="fa-solid fa-pen-to-square"></i></button>
                                                <button class="p-2 text-red-500 hover:bg-red-50 rounded-lg"><i class="fa-solid fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reservations Tab -->
            <div id="tab-reservations" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Summary Stats for Reservations -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-500 p-6 rounded-2xl text-white shadow-lg">
                            <p class="text-blue-100 text-sm font-medium"><?php echo $t['upcoming_today']; ?></p>
                            <h3 class="text-3xl font-bold mt-1"><?php echo count($todaysVisits); ?></h3>
                        </div>
                    </div>

                    <!-- Today's Detailed List -->
                    <div class="bg-white rounded-2xl custom-shadow overflow-hidden">
                        <div class="p-6 border-b">
                            <h2 class="text-lg font-bold flex items-center gap-2">
                                <i class="fa-solid fa-clock text-blue-500"></i>
                                <?php echo $t['upcoming_today']; ?>
                            </h2>
                        </div>

                        <div class="p-6">
                            <?php if (empty($todaysVisits)): ?>
                                <div class="text-center py-12">
                                    <i class="fa-solid fa-calendar-check text-4xl text-gray-200 mb-3"></i>
                                    <p class="text-gray-500"><?php echo $t['no_visits']; ?></p>
                                </div>
                            <?php else: ?>
                                <div class="space-y-4">
                                    <?php foreach ($todaysVisits as $visit): ?>
                                        <div class="flex items-center justify-between p-4 border rounded-xl hover:border-blue-200 hover:bg-blue-50 transition-all">
                                            <div class="flex items-center gap-4">
                                                <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                                                    <?php echo mb_substr($visit['patient_name'], 0, 1, 'utf-8'); ?>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-gray-800"><?php echo $visit['patient_name']; ?></h4>
                                                    <p class="text-sm text-gray-500"><?php echo $visit['patient_phone']; ?></p>
                                                    <p class="text-sm text-gray-500"><?php echo $visit['visiting_dates']; ?></p>
                                                </div>
                                            </div>
                                            <div class="<?php echo $language == 'ar' ? 'text-left' : 'text-right'; ?>">
                                                <span class="text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                                    <?php echo date('h:i A', strtotime($visit['visit_time'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings / Clinic Tab -->
            <div id="tab-settings" class="tab-content hidden">
                <div class="bg-white rounded-2xl custom-shadow overflow-hidden">
                    <div class="p-8 text-center bg-white border-b">
                        <h2 class="text-2xl font-bold text-gray-800 mt-4"><?php echo $_SESSION['user_name']; ?></h2>
                        <span class="inline-block mt-2 bg-blue-50 text-blue-600 px-6 py-1 rounded-full text-sm font-bold"><?php echo $t['specialist']; ?></span>
                    </div>

                    <div class="p-8 space-y-6 max-w-2xl mx-auto">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                                <span class="text-gray-600 font-medium"><?php echo $t['phone']; ?> :</span>
                                <span class="text-blue-900 font-bold tracking-wider">01008730718</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                                <span class="text-gray-600 font-medium"><?php echo $t['address']; ?> :</span>
                                <span class="text-gray-700">Cairo</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                                <span class="text-gray-600 font-medium"><?php echo $t['exp']; ?> :</span>
                                <span class="text-gray-700">4 <?php echo $t['years']; ?></span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-50">
                                <span class="text-gray-600 font-medium"><?php echo $t['fees']; ?> :</span>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg font-bold">500 <?php echo $t['currency']; ?></span>
                            </div>
                        </div>
                        <div class="pt-6 flex gap-3">
                            <button class="flex-grow bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition-all"><?php echo $t['edit_data']; ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Tab -->
            <div id="tab-profile" class="tab-content hidden">
                <div class="bg-white rounded-2xl custom-shadow p-8">
                    <h2 class="text-xl font-bold mb-8 flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-blue-500"></i> <?php echo $t['account_data']; ?>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700"><?php echo $t['email']; ?></label>
                            <input type="email" value="<?php echo $_SESSION['user_email']; ?>" class="w-full p-3 bg-gray-50 border rounded-xl">
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-bold text-gray-700"><?php echo $t['bio']; ?></label>
                            <textarea rows="4" class="w-full p-3 bg-gray-50 border rounded-xl"></textarea>
                        </div>
                    </div>
                    <div class="mt-8">
                        <button class="bg-gray-800 text-white px-8 py-3 rounded-xl font-bold hover:bg-black transition-all"><?php echo $t['save']; ?></button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleNotifications() {
            document.getElementById('notif-dropdown').classList.toggle('hidden');
        }

        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.classList.remove('nav-item-active');
                btn.classList.add('text-gray-600', 'hover:bg-white');
            });
            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.add('nav-item-active');
            activeBtn.classList.remove('text-gray-600', 'hover:bg-white');
        }
    </script>
</body>

</html>