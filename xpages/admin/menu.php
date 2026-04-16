<?php
/**
 * xPages — Admin menu
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

// Geçici dil tanımları (eğer tanımlı değilse)
if (!defined('_AM_XPAGES_MENU_MAIN')) {
    define('_AM_XPAGES_MENU_MAIN', 'Ana Sayfa');
    define('_AM_XPAGES_MENU_PAGES', 'Sayfalar');
    define('_AM_XPAGES_MENU_ADD_PAGE', 'Sayfa Ekle');
    define('_AM_XPAGES_MENU_FIELDS', 'Alanlar');
    define('_AM_XPAGES_MENU_ABOUT', 'Hakkında');
}

$adminmenu = [];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_MAIN,
    'link'  => 'admin/index.php',
    'icon'  => 'images/admin/home.png',
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_PAGES,
    'link'  => 'admin/pages.php',
    'icon'  => 'images/admin/pages.png',
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_ADD_PAGE,
    'link'  => 'admin/page_edit.php',
    'icon'  => 'images/admin/add.png',
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_FIELDS,
    'link'  => 'admin/fields.php',
    'icon'  => 'images/admin/fields.png',
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => 'images/admin/about.png',
];