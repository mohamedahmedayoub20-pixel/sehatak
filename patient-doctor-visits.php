<?php
include "includes/auth.php";
include "includes/database.php";

// 1. Language Handling
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] == 'ar' ? 'ar' : 'en';
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
$dir = ($lang == 'ar') ? 'rtl' : 'ltr';

// 2. Translations
$texts = [
    'en' => [
        'title' => 'My Visits - Sehatak',
        'welcome' => 'Welcome',
        'subtitle' => 'Below is your medical visit history',
        'visit_id' => 'ID',
        'doctor' => 'Doctor',
        'date' => 'Date & Time',
        'status' => 'Status',
        'action' => 'Action',
        'cancel' => 'Cancel Visit',
        'no_visits' => 'No visits found in your record.',
        'pending' => 'Awaiting Examination',
        'switch' => 'العربية',
        'back' => 'Back to Home'
    ],
    'ar' => [
        'title' => 'زياراتي - صحتك',
        'welcome' => 'مرحباً',
        'subtitle' => 'فيما يلي سجل زياراتك الطبية:',
        'visit_id' => 'الكود',
        'doctor' => 'الطبيب',
        'date' => 'التاريخ والوقت',
        'status' => 'الحالة',
        'action' => 'الإجراء',
        'cancel' => 'إلغاء الزيارة',
        'no_visits' => 'لا توجد زيارات مسجلة.',
        'pending' => 'في انتظار الفحص',
        'switch' => 'English',
        'back' => 'العودة للرئيسية'
    ]
];

$t = $texts[$lang];

// 3. Cancellation Handler (Self-POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_visit_id'])) {
    $cancelId = $_POST['cancel_visit_id'];
    try {
        $cancelStmt = $pdo->prepare("UPDATE `visiting a doctor` SET is_deleted = 1 WHERE `visiting_a_ doctor_id` = ?");
        $cancelStmt->execute([$cancelId]);
        // Refresh to see updated list
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

// 4. Main Logic: Load Data
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$today = date('Y-m-d');

try {
    $patientStmt = $pdo->prepare("SELECT patient_id, name FROM patient WHERE user_id = ?");
    $patientStmt->execute([$userId]);
    $patient = $patientStmt->fetch();

    if (!$patient) {
        die($lang == 'ar' ? "لم يتم العثور على ملف مريض." : "No patient profile found.");
    }

    $patientId = $patient['patient_id'];
    $patientName = $patient['name'];

    // Load only visits where is_deleted = 0
    $query = "
        SELECT v.`visiting_a_ doctor_id` AS visiting_a_doctor_id, v.diagnosis, v.visit_time, 
               d.name as doctor_name, d.specialization 
        FROM `visiting a doctor` v
        JOIN doctor d ON v.doctor_id = d.doctor_id
        WHERE v.patient_id = ? AND v.is_deleted = 0
        ORDER BY v.visit_time DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$patientId]);
    $visits = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3498db;
            --danger: #e74c3c;
            --dark: #2c3e50;
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

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 20px;
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
            font-family: inherit;
            transition: 0.2s;
        }

        .btn-cancel:hover {
            background: var(--danger);
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">
        <a href="index.php" class="btn-back">
            <span>&larr;</span> <?php echo $t['back']; ?>
        </a>
        <div class="header">
            <div>
                <h2><?php echo $t['welcome']; ?>, <?php echo htmlspecialchars($patientName); ?></h2>
                <p><?php echo $t['subtitle']; ?></p>
            </div>
            <a href="?lang=<?php echo ($lang == 'en' ? 'ar' : 'en'); ?>" class="lang-switch"><?php echo $t['switch']; ?></a>
        </div>

        <table>
            <thead>
                <tr>
                    <th><?php echo $t['visit_id']; ?></th>
                    <th><?php echo $t['doctor']; ?></th>
                    <th><?php echo $t['date']; ?></th>
                    <th><?php echo $t['status']; ?></th>
                    <th><?php echo $t['action']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visits as $visit):
                    $visitDate = date('Y-m-d', strtotime($visit['visit_time']));
                    $canCancel = ($visitDate >= $today);
                ?>
                    <tr>
                        <td>#<?php echo $visit['visiting_a_doctor_id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($visit['doctor_name']); ?></strong><br>
                            <small style="color: #94a3b8;"><?php echo htmlspecialchars($visit['specialization'] ?: 'General'); ?></small>
                        </td>
                        <td><?php echo date('Y-m-d h:i A', strtotime($visit['visit_time'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo (empty($visit['diagnosis']) || $visit['diagnosis'] == 'Pending Examination') ? 'status-pending' : ''; ?>">
                                <?php echo (empty($visit['diagnosis']) || $visit['diagnosis'] == 'Pending Examination') ? $t['pending'] : htmlspecialchars($visit['diagnosis']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($canCancel): ?>
                                <form method="POST" onsubmit="return confirm('<?php echo ($lang == 'ar' ? 'هل أنت متأكد؟' : 'Are you sure?'); ?>');">
                                    <input type="hidden" name="cancel_visit_id" value="<?php echo $visit['visiting_a_doctor_id']; ?>">
                                    <button type="submit" class="btn-cancel"><?php echo $t['cancel']; ?></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($visits) == 0): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 40px;"><?php echo $t['no_visits']; ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>