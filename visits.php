<?php
include "includes/auth.php";
include "includes/database.php";

// 1. Language Handling
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] == 'ar' ? 'ar' : 'en';
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
$dir = ($lang == 'ar') ? 'rtl' : 'ltr';

// 2. Translations (Updated for Laboratory)
$texts = [
    'en' => [
        'title' => 'My Appointments - Sehatak',
        'welcome' => 'Welcome',
        'subtitle' => 'Below is your medical visit history',
        'doctor_section' => 'Doctor Appointments',
        'nurse_section' => 'Nurse Appointments',
        'lab_section' => 'Laboratory Appointments',
        'visit_id' => 'ID',
        'provider' => 'Provider / Test Type',
        'date' => 'Date & Time',
        'status' => 'Status',
        'action' => 'Action',
        'cancel' => 'Cancel',
        'no_visits' => 'No visits found.',
        'pending' => 'Awaiting Examination',
        'switch' => 'العربية',
        'back' => 'Back to Home'
    ],
    'ar' => [
        'title' => 'زياراتي - صحتك',
        'welcome' => 'مرحباً',
        'subtitle' => 'فيما يلي سجل زياراتك الطبية:',
        'doctor_section' => 'مواعيد الأطباء',
        'nurse_section' => 'مواعيد التمريض',
        'lab_section' => 'مواعيد المختبرات',
        'visit_id' => 'الكود',
        'provider' => 'مقدم الخدمة / نوع التحليل',
        'date' => 'التاريخ والوقت',
        'status' => 'الحالة',
        'action' => 'الإجراء',
        'cancel' => 'إلغاء',
        'no_visits' => 'لا توجد زيارات مسجلة.',
        'pending' => 'في انتظار الفحص',
        'switch' => 'English',
        'back' => 'العودة للرئيسية'
    ]
];
$t = $texts[$lang];

// 3. Cancellation Handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Doctor Cancellation
    if (isset($_POST['cancel_visit_id'])) {
        $pdo->prepare("UPDATE `visiting a doctor` SET is_deleted = 1 WHERE `visiting_a_ doctor_id` = ?")
            ->execute([$_POST['cancel_visit_id']]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    // Nurse Cancellation
    if (isset($_POST['cancel_nurse_visit_id'])) {
        $pdo->prepare("UPDATE `visiting a nurse` SET is_deleted = 1 WHERE `visiting_a_ nurse_id` = ?")
            ->execute([$_POST['cancel_nurse_visit_id']]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    // Laboratory Cancellation
    if (isset($_POST['cancel_lab_visit_id'])) {
        $pdo->prepare("UPDATE `visiting the analysis laboratory` SET is_deleted = 1 WHERE `visiting_the_analysis_laboratory_id` = ?")
            ->execute([$_POST['cancel_lab_visit_id']]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// 4. Data Loading
$userId = $_SESSION['user_id'];
$today = date('Y-m-d');

try {
    $patientStmt = $pdo->prepare("SELECT patient_id, name FROM patient WHERE user_id = ?");
    $patientStmt->execute([$userId]);
    $patient = $patientStmt->fetch();
    if (!$patient) die("Profile not found.");
    $patientId = $patient['patient_id'];

    // Fetch Doctor Visits
    $docQuery = "SELECT v.`visiting_a_ doctor_id` AS id, v.diagnosis, v.visit_time, d.name, d.specialization 
                 FROM `visiting a doctor` v JOIN doctor d ON v.doctor_id = d.doctor_id
                 WHERE v.patient_id = ? AND v.is_deleted = 0 ORDER BY v.visit_time DESC";
    $stmtD = $pdo->prepare($docQuery);
    $stmtD->execute([$patientId]);
    $doctorVisits = $stmtD->fetchAll();

    // Fetch Nurse Visits
    $nurseQuery = "SELECT v.`visiting_a_ nurse_id` AS id, v.visit_time, n.name 
                   FROM `visiting a nurse` v JOIN nurse n ON v.nurse_id = n.nurse_id 
                   WHERE v.patient_id = ? AND v.is_deleted = 0 ORDER BY v.visit_time DESC";
    $stmtN = $pdo->prepare($nurseQuery);
    $stmtN->execute([$patientId]);
    $nurseVisits = $stmtN->fetchAll();

    // Fetch Laboratory Visits (Based on image_dbefc5.png)
    $labQuery = "SELECT v.visiting_the_analysis_laboratory_id AS id, v.type_of_analysis, v.status_of_analysis, v.visit_time, l.name 
                 FROM `visiting the analysis laboratory` v 
                 JOIN `analysis laboratory` l ON v.analysis_laboratory_id = l.analysis_laboratory_id
                 WHERE v.patient_id = ? AND v.is_deleted = 0 ORDER BY v.visit_time DESC";
    $stmtL = $pdo->prepare($labQuery);
    $stmtL->execute([$patientId]);
    $labVisits = $stmtL->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">

<head>
    <meta charset="UTF-8">
    <title><?php echo $t['title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3498db;
            --danger: #e74c3c;
            --bg: #f8fafc;
            --text: #475569;
        }

        body {
            font-family: <?php echo ($lang == 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif"); ?>;
            background-color: var(--bg);
            color: var(--text);
            padding: 40px 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 20px;
        }

        .section-title {
            margin: 40px 0 20px;
            padding-<?php echo $lang == 'ar' ? 'right' : 'left'; ?>: 10px;
            border-<?php echo $lang == 'ar' ? 'right' : 'left'; ?>: 5px solid var(--primary);
            color: var(--primary);
            font-size: 1.2rem;
            font-weight: 700;
        }


        /* Add this to your <style> section */
        .btn-back {
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .btn-back:hover {
            color: var(--primary);
        }

        /* Flip arrow for Arabic */
        .btn-back span {
            display: inline-block;
            transform: <?php echo ($lang == 'ar' ? 'rotate(180deg)' : 'rotate(0deg)'); ?>;
        }


        .lang-switch {
            text-decoration: none;
            color: var(--primary);
            font-weight: 600;
            padding: 8px 16px;
            border: 1px solid var(--primary);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #f1f5f9;
            padding: 15px;
            text-align: <?php echo ($lang == 'ar' ? 'right' : 'left'); ?>;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef9c3;
            color: #854d0e;
        }

        .btn-cancel {
            background: none;
            color: var(--danger);
            border: 1px solid var(--danger);
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
            font-family: inherit;
        }

        .btn-cancel:hover {
            background: var(--danger);
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">
        <a href="index.php" class="btn-back"><span>&larr;</span> <?php echo $t['back']; ?></a>

        <div class="header">
            <div>
                <h2><?php echo $t['welcome']; ?>, <?php echo htmlspecialchars($patient['name']); ?></h2>
                <p><?php echo $t['subtitle']; ?></p>
            </div>
            <a href="?lang=<?php echo ($lang == 'en' ? 'ar' : 'en'); ?>" class="lang-switch"><?php echo $t['switch']; ?></a>
        </div>

        <h3 class="section-title"><?php echo $t['doctor_section']; ?></h3>
        <table>
            <thead>
                <tr>
                    <th><?php echo $t['visit_id']; ?></th>
                    <th><?php echo $t['provider']; ?></th>
                    <th><?php echo $t['date']; ?></th>
                    <th><?php echo $t['status']; ?></th>
                    <th><?php echo $t['action']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctorVisits as $visit):
                    $canCancel = (date('Y-m-d', strtotime($visit['visit_time'])) >= $today); ?>
                    <tr>
                        <td>#<?php echo $visit['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($visit['name']); ?></strong><br><small><?php echo htmlspecialchars($visit['specialization'] ?: 'General'); ?></small></td>
                        <td><?php echo date('Y-m-d h:i A', strtotime($visit['visit_time'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo (empty($visit['diagnosis']) || $visit['diagnosis'] == 'Pending Examination') ? 'status-pending' : ''; ?>">
                                <?php echo (empty($visit['diagnosis']) || $visit['diagnosis'] == 'Pending Examination') ? $t['pending'] : htmlspecialchars($visit['diagnosis']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($canCancel): ?>
                                <form method="POST" onsubmit="return confirm('<?php echo ($lang == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?'); ?>');">
                                    <input type="hidden" name="cancel_visit_id" value="<?php echo $visit['id']; ?>">
                                    <button type="submit" class="btn-cancel"><?php echo $t['cancel']; ?></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach;
                if (empty($doctorVisits)) echo "<tr><td colspan='5' align='center'>{$t['no_visits']}</td></tr>"; ?>
            </tbody>
        </table>

        <h3 class="section-title"><?php echo $t['lab_section']; ?></h3>
        <table>
            <thead>
                <tr>
                    <th><?php echo $t['visit_id']; ?></th>
                    <th><?php echo $t['provider']; ?></th>
                    <th><?php echo $t['date']; ?></th>
                    <th><?php echo $t['status']; ?></th>
                    <th><?php echo $t['action']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($labVisits as $lv):
                    $canCancelL = (date('Y-m-d', strtotime($lv['visit_time'])) >= $today); ?>
                    <tr>
                        <td>#<?php echo $lv['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($lv['name']); ?></strong><br><small><?php echo htmlspecialchars($lv['type_of_analysis']); ?></small></td>
                        <td><?php echo date('Y-m-d h:i A', strtotime($lv['visit_time'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo ($lv['status_of_analysis'] == 'Routine') ? 'status-pending' : ''; ?>">
                                <?php echo htmlspecialchars($lv['status_of_analysis']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($canCancelL): ?>
                                <form method="POST" onsubmit="return confirm('<?php echo ($lang == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?'); ?>');">
                                    <input type="hidden" name="cancel_lab_visit_id" value="<?php echo $lv['id']; ?>">
                                    <button type="submit" class="btn-cancel"><?php echo $t['cancel']; ?></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach;
                if (empty($labVisits)) echo "<tr><td colspan='5' align='center'>{$t['no_visits']}</td></tr>"; ?>
            </tbody>
        </table>

        <h3 class="section-title"><?php echo $t['nurse_section']; ?></h3>
        <table>
            <thead>
                <tr>
                    <th><?php echo $t['visit_id']; ?></th>
                    <th><?php echo $t['provider']; ?></th>
                    <th><?php echo $t['date']; ?></th>
                    <th><?php echo $t['action']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nurseVisits as $v):
                    $canCancelN = (date('Y-m-d', strtotime($v['visit_time'])) >= $today); ?>
                    <tr>
                        <td>#<?php echo $v['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($v['name']); ?></strong></td>
                        <td><?php echo date('Y-m-d h:i A', strtotime($v['visit_time'])); ?></td>
                        <td>
                            <?php if ($canCancelN): ?>
                                <form method="POST" onsubmit="return confirm('<?php echo ($lang == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?'); ?>');">
                                    <input type="hidden" name="cancel_nurse_visit_id" value="<?php echo $v['id']; ?>">
                                    <button type="submit" class="btn-cancel"><?php echo $t['cancel']; ?></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach;
                if (empty($nurseVisits)) echo "<tr><td colspan='4' align='center'>{$t['no_visits']}</td></tr>"; ?>
            </tbody>
        </table>
    </div>

</body>

</html>