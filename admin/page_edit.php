<?php
/**
 * xPages — Admin sayfa ekleme/düzenleme
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

use Xmf\Request;

include_once '../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';
xpages_admin_boot();

xoops_cp_header();
xpages_admin_register_css();

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('page_edit.php');
}

$pageHandler  = xpages_get_handler('page');
$fieldHandler = xpages_get_handler('field');
$valueHandler = xpages_get_handler('fieldvalue');

if (!$pageHandler || !$fieldHandler || !$valueHandler) {
    echo '<div class="xp-alert xp-alert--error">xPages handler unavailable.</div>';
    xoops_cp_footer();
    exit;
}

$pageId = Request::getInt('page_id', 0,      'REQUEST');
$op     = Request::getCmd('op',      'edit', 'POST');

// "Advanced code" = raw HTML/JS injected into <head> / before </body>.
// Gate on module-admin rights rather than hard-coded group id 1: a site
// may rebind "webmaster" to a different group id, and `getGroups()`
// returns every group the user belongs to, not a privilege level.
$canUseAdvancedCode = is_object($xoopsUser)
    && is_object($xoopsModule)
    && $xoopsUser->isAdmin($xoopsModule->getVar('mid'));

$descendantIds = [];
if ($pageId) {
    xpages_collect_descendant_ids($pageHandler, $pageId, $descendantIds);
}

// ── Kaydet ────────────────────────────────────────────────────────────────────
if ($op === 'save') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header('pages.php', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit;
    }

    if (!is_object($GLOBALS['xoopsUser'])) {
        redirect_header('pages.php', 3, _AM_XPAGES_SAVE_ERROR);
        exit;
    }

    $page = $pageId ? $pageHandler->get($pageId) : $pageHandler->create();
    if (!$page) {
        redirect_header('pages.php', 3, _AM_XPAGES_PAGE_NOT_FOUND);
        exit;
    }

    $parentId = Request::getInt('parent_id', 0, 'POST');
    if ($pageId && $parentId > 0 && ($parentId === $pageId || in_array($parentId, $descendantIds, true))) {
        redirect_header('page_edit.php?page_id=' . $pageId, 3, _AM_XPAGES_PARENT_INVALID);
        exit;
    }

    $page->setVar('title',         Request::getText('title',        '',    'POST'));
    $page->setVar('body',          Request::getText('body',         '',    'POST'));
    $page->setVar('short_desc',    Request::getText('short_desc',   '',    'POST'));
    $page->setVar('page_status',   Request::getInt('page_status',   1,     'POST'));
    $page->setVar('menu_order',    Request::getInt('menu_order',    0,     'POST'));
    $page->setVar('show_in_menu',  Request::getInt('show_in_menu',  0,     'POST'));
    $page->setVar('show_in_nav',   Request::getInt('show_in_nav',   0,     'POST'));
    $page->setVar('parent_id',     $parentId);
    $page->setVar('meta_title',    Request::getString('meta_title',    '', 'POST'));
    $page->setVar('meta_keywords', Request::getText('meta_keywords',   '', 'POST'));
    $page->setVar('meta_desc',     Request::getText('meta_desc',       '', 'POST'));
    $page->setVar('noindex',       Request::hasVar('noindex',  'POST') ? 1 : 0);
    $page->setVar('nofollow',      Request::hasVar('nofollow', 'POST') ? 1 : 0);
    $page->setVar('redirect_url',  xpages_normalize_url(Request::getString('redirect_url', '', 'POST')));

    if ($canUseAdvancedCode) {
        $newHeader = Request::getText('header_code', '', 'POST');
        $newFooter = Request::getText('footer_code', '', 'POST');
        $oldHeader = (string)$page->getVar('header_code', 'n');
        $oldFooter = (string)$page->getVar('footer_code', 'n');

        if (($newHeader !== $oldHeader || $newFooter !== $oldFooter)
            && is_object($GLOBALS['xoopsLogger'])
        ) {
            $GLOBALS['xoopsLogger']->addExtra(
                'xpages',
                sprintf(
                    'header/footer_code modified on page_id=%d by uid=%d (header %d→%d bytes, footer %d→%d bytes)',
                    (int)($pageId ?: 0),
                    (int)$GLOBALS['xoopsUser']->getVar('uid'),
                    strlen($oldHeader),
                    strlen($newHeader),
                    strlen($oldFooter),
                    strlen($newFooter)
                )
            );
        }

        $page->setVar('header_code',  $newHeader);
        $page->setVar('footer_code',  $newFooter);
    } elseif ($pageId) {
        $page->setVar('header_code',  $page->getVar('header_code', 'n'));
        $page->setVar('footer_code',  $page->getVar('footer_code', 'n'));
    } else {
        $page->setVar('header_code',  '');
        $page->setVar('footer_code',  '');
    }
    $page->setVar('uid',          (int)($GLOBALS['xoopsUser']->getVar('uid') ?? 0));

    $rawAlias = trim(Request::getString('alias', '', 'POST'));
    if ('' === $rawAlias) {
        $rawAlias = (string)$page->getVar('title', 'n');
    }
    $page->setVar('alias', $pageHandler->generateAlias($rawAlias, $pageId));

    if (!$pageId) {
        $page->setVar('create_date', time());
    }
    $page->setVar('update_date', time());

    if (!$pageHandler->insert($page)) {
        echo '<div class="xp-alert xp-alert--error">' . _AM_XPAGES_SAVE_ERROR . '</div>';
    } else {
        $savedId = (int)$page->getVar('page_id');

        $values = [];
        $extraFieldsInput = Request::getArray('extra_fields', [], 'POST');
        foreach ($extraFieldsInput as $fid => $val) {
            $values[(int)$fid] = is_array($val) ? implode('|', $val) : (string)$val;
        }

        $uploadDir = XOOPS_UPLOAD_PATH . '/xpages/';
        xpages_ensure_upload_dir($uploadDir);

        if (!empty($_FILES['extra_files'])) {
            foreach ($_FILES['extra_files']['name'] as $fid => $fileName) {
                if (empty($fileName)) continue;

                if ($_FILES['extra_files']['error'][$fid] === UPLOAD_ERR_OK) {
                    $field = $fieldHandler->get((int)$fid);
                    if ($field && $field->getVar('field_type') === 'file') {
                        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'zip'];

                        if (in_array($ext, $allowedExts, true) && xpages_upload_is_allowed($_FILES['extra_files']['tmp_name'][$fid], $ext)) {
                            $randomPart = bin2hex(random_bytes(6));
                            $newFileName = 'page_' . $savedId . '_field_' . $fid . '_' . $randomPart . '.' . $ext;
                            if (move_uploaded_file($_FILES['extra_files']['tmp_name'][$fid], $uploadDir . $newFileName)) {
                                $values[(int)$fid] = $newFileName;
                            }
                        }
                    }
                }
            }
        }

        $valueHandler->saveValuesForPage($savedId, $values);

        redirect_header('pages.php', 2, _AM_XPAGES_PAGE_SAVED);
        exit;
    }
}

// ── Form (render path) ────────────────────────────────────────────────────────
$page = $pageId ? $pageHandler->get($pageId) : $pageHandler->create();
if (!$page) {
    redirect_header('pages.php', 3, _AM_XPAGES_PAGE_NOT_FOUND);
    exit;
}

$existingValues = $pageId ? $valueHandler->getValuesForPage($pageId) : [];
$extraFields    = $pageId ? $fieldHandler->getFieldsForPage($pageId, false) : $fieldHandler->getGlobalFields(false);

// Parent-page options, pre-filtered so the template just iterates.
$allPages         = $pageHandler->getObjects() ?: [];
$blockedParentIds = array_flip($descendantIds);
$parentOptions    = [];
foreach ($allPages as $ap) {
    $apId = (int)$ap->getVar('page_id');
    if ($apId === $pageId || isset($blockedParentIds[$apId])) {
        continue;
    }
    $parentOptions[] = [
        'id'       => $apId,
        'title'    => (string)$ap->getVar('title'),
        'selected' => ((int)$page->getVar('parent_id') === $apId),
    ];
}

// Pre-render extra-field inputs via the existing helper (xpages_render_
// field_input returns HTML). 6g will convert that helper into its own
// template; for now we pass the assembled HTML to the page_edit template
// via a {nofilter} slot.
$extraFieldsHtml = '';
foreach ($extraFields as $field) {
    $fid             = (int)$field->getVar('field_id');
    $val             = $existingValues[$fid] ?? (string)$field->getVar('field_default', 'n');
    $extraFieldsHtml .= xpages_render_field_input($field, $val);
}

xpages_admin_render('xpages_admin_page_edit.tpl', [
    'form_title'                   => $pageId ? _AM_XPAGES_EDIT_PAGE : _AM_XPAGES_ADD_PAGE,
    'page_id'                      => $pageId,
    'page'                         => [
        'title'         => (string)$page->getVar('title',         'n'),
        'alias'         => (string)$page->getVar('alias',         'n'),
        'short_desc'    => (string)$page->getVar('short_desc',    'n'),
        'body'          => (string)$page->getVar('body',          'n'),
        'status'        => (int)   $page->getVar('page_status'),
        'menu_order'    => (int)   $page->getVar('menu_order'),
        'show_in_menu'  => (bool)  $page->getVar('show_in_menu'),
        'show_in_nav'   => (bool)  $page->getVar('show_in_nav'),
        'parent_id'     => (int)   $page->getVar('parent_id'),
        'meta_title'    => (string)$page->getVar('meta_title',    'n'),
        'meta_keywords' => (string)$page->getVar('meta_keywords', 'n'),
        'meta_desc'     => (string)$page->getVar('meta_desc',     'n'),
        'noindex'       => (bool)  $page->getVar('noindex'),
        'nofollow'      => (bool)  $page->getVar('nofollow'),
        'redirect_url'  => (string)$page->getVar('redirect_url',  'n'),
        'header_code'   => (string)$page->getVar('header_code',   'n'),
        'footer_code'   => (string)$page->getVar('footer_code',   'n'),
    ],
    'parent_options'               => $parentOptions,
    'can_use_advanced_code'        => $canUseAdvancedCode,
    'has_extra_fields'             => (count($extraFields) > 0),
    'extra_fields_html'            => $extraFieldsHtml,

    // Labels (controller-assigned so the template doesn't depend on the
    // _AM_* constants being in scope at render time).
    'label_tab_main'               => _AM_XPAGES_TAB_MAIN,
    'label_tab_seo'                => _AM_XPAGES_TAB_SEO,
    'label_tab_advanced'           => _AM_XPAGES_TAB_ADVANCED,
    'label_tab_extra'              => _AM_XPAGES_TAB_EXTRA,
    'label_page_title'             => _AM_XPAGES_PAGE_TITLE,
    'label_page_alias'             => _AM_XPAGES_PAGE_ALIAS,
    'alias_placeholder'            => _AM_XPAGES_ALIAS_PLACEHOLDER,
    'alias_help'                   => _AM_XPAGES_ALIAS_HELP,
    'label_short_desc'             => _AM_XPAGES_SHORT_DESC,
    'label_body'                   => _AM_XPAGES_BODY,
    'label_status'                 => _AM_XPAGES_STATUS,
    'label_active'                 => _AM_XPAGES_ACTIVE,
    'label_inactive'               => _AM_XPAGES_INACTIVE,
    'label_page_order'             => _AM_XPAGES_PAGE_ORDER,
    'label_show_in_menu'           => _AM_XPAGES_SHOW_IN_MENU,
    'label_show_in_nav'            => _AM_XPAGES_SHOW_IN_NAV,
    'label_parent_page'            => _AM_XPAGES_PARENT_PAGE,
    'label_no_parent'              => _AM_XPAGES_NO_PARENT,
    'label_meta_title'             => _AM_XPAGES_META_TITLE,
    'meta_title_help'              => _AM_XPAGES_META_TITLE_HELP,
    'label_meta_keywords'          => _AM_XPAGES_META_KEYWORDS,
    'label_meta_desc'              => _AM_XPAGES_META_DESC,
    'meta_desc_help'               => _AM_XPAGES_META_DESC_HELP,
    'label_noindex'                => _AM_XPAGES_NOINDEX,
    'label_nofollow'               => _AM_XPAGES_NOFOLLOW,
    'label_redirect_url'           => _AM_XPAGES_REDIRECT_URL,
    'redirect_help'                => _AM_XPAGES_REDIRECT_HELP,
    'label_advanced_code_warning'  => _AM_XPAGES_ADVANCED_CODE_WARNING,
    'label_advanced_code_restricted' => _AM_XPAGES_ADVANCED_CODE_RESTRICTED,
    'label_header_code'            => _AM_XPAGES_HEADER_CODE,
    'header_code_help'             => _AM_XPAGES_HEADER_CODE_HELP,
    'label_footer_code'            => _AM_XPAGES_FOOTER_CODE,
    'footer_code_help'             => _AM_XPAGES_FOOTER_CODE_HELP,
    'label_manage_fields'          => _AM_XPAGES_MANAGE_FIELDS_FOR_PAGE,
    'label_menu_fields'            => _AM_XPAGES_MENU_FIELDS,
    'label_gallery'                => _AM_XPAGES_GALLERY_TITLE,
    'label_gallery_manage'         => _AM_XPAGES_GALLERY_MANAGE,
    'gallery_manage_help'          => _AM_XPAGES_GALLERY_MANAGE_HELP,
    'label_gallery_save_first'     => _AM_XPAGES_GALLERY_SAVE_FIRST,
    'gallery_save_first_help'      => _AM_XPAGES_GALLERY_SAVE_FIRST_HELP,
    'label_save'                   => _AM_XPAGES_SAVE,
    'label_cancel'                 => _AM_XPAGES_CANCEL,
]);

xoops_cp_footer();
