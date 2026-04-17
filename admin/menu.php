<?php
/**
 * xPages — Admin menu
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

use Xmf\Module\Admin;

// İkon yolunu al (basit yöntem)
$pathIcon32 = Admin::menuIconPath('');

$adminmenu = [];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_MAIN,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_PAGES,
    'link'  => 'admin/pages.php',
    'icon' => $pathIcon32 . '/content.png'
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_ADD_PAGE,
    'link'  => 'admin/page_edit.php',
    'icon'  => $pathIcon32 . '/add.png',
];

$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_FIELDS,
    'link'  => 'admin/fields.php',
    'icon'  => $pathIcon32 . '/insert_table_row.png',
];

// Admin menü bağlantılarına galeriyi ekleyin
$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_GALLERY,
    'link'  => 'admin/gallery.php',
    'icon'  => $pathIcon32 . '/photo.png',
];


$adminmenu[] = [
    'title' => _AM_XPAGES_MENU_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];
