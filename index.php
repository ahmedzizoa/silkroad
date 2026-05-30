<?php
/**
 * Bootstrap الموقع - نقطة الدخول الرئيسية
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// تعيين اللغة
$allowed_langs = ['ar', 'en', 'tr'];
$lang_code = isset($_GET['lang']) && in_array($_GET['lang'], $allowed_langs) 
             ? $_GET['lang'] 
             : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en');

$_SESSION['lang'] = $lang_code;

// تحميل ملف الترجمة
$lang_file = __DIR__ . '/languages/' . $lang_code . '.php';
if (!file_exists($lang_file)) {
    $lang_file = __DIR__ . '/languages/en.php';
}
$lang = require_once $lang_file;

// تحديد الصفحة المطلوبة
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// الصفحات المسموح بها (بما فيها صفحات الأدمن)



$allowed_pages = ['home', 'register', 'login', 'logout', 'ranking', 'guild_ranking', 'job_ranking', 'character', 'dashboard', 'download', 'donate', 'news', 'verify', 'change_password', 'forgot_password', 'reset_password', 'resend_verification', 'voting', 'my_characters', 'admin/login', 'admin/logout', 'admin/index', 'admin/accounts', 'admin/characters', 'admin/silk', 'admin/server', 'admin/website', 'admin/voting', 'admin/logs', 'admin/security_logs', 'admin/add_items_to_character'];




if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// ============================================
// نظام التحقق من صلاحيات الأدمن
// ============================================

// التحقق إذا كانت الصفحة من صفحات الأدمن
$is_admin_page = (strpos($page, 'admin/') === 0);

if ($is_admin_page) {
    // صفحات الأدمن تتطلب جلسة أدمن منفصلة
    if ($page !== 'admin/login') {
        // التحقق من وجود جلسة الأدمن
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: /?page=admin/login');
            exit();
        }
    }
} else {
    // الصفحات العادية المحمية (تتطلب تسجيل دخول لاعب عادي)
    $protected_pages = ['dashboard', 'donate', 'change_password'];
    
    if (in_array($page, $protected_pages) && !isset($_SESSION['username'])) {
        header('Location: ?page=login');
        exit();
    }
}

// تسجيل الخروج للاعب العادي
if ($page === 'logout') {
    session_destroy();
    header('Location: ?page=home');
    exit();
}

// المتغيرات اللي هتكون متاحة في كل الصفحات
$current_lang = $lang_code;

// عرض الصفحة المطلوبة — طبقة حماية إضافية ضد Path Traversal
$page_file = realpath(__DIR__ . '/pages/' . $page . '.php');
$pages_dir = realpath(__DIR__ . '/pages');
if ($page_file && strpos($page_file, $pages_dir) === 0 && file_exists($page_file)) {
    require_once $page_file;
} else {
    require_once __DIR__ . '/pages/home.php';
}
?>