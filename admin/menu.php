<?php
/**
 * xPages — Admin menu
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

use Xmf\Module\Admin;

// İkon yolunu al (basit yöntem)
$pathIcon32 = Admin::menuIconPath('');
$adminmenu = array();


$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_MAIN,
    'link'  => 'admin/index.php',
    'icon' => $pathIcon32 . '/home.png'
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_PAGES,
    'link'  => 'admin/pages.php',
    'icon' => $pathIcon32 . '/content.png'
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_ADD_PAGE,
    'link'  => 'admin/page_edit.php',
    'icon' => $pathIcon32 . '/add.png'
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_FIELDS,
    'link'  => 'admin/fields.php',
    'icon' => $pathIcon32 . '/administration.png'
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_ABOUT,
    'link'  => 'admin/about.php',
    'icon' => $pathIcon32 . '/about.png'
];
?>
