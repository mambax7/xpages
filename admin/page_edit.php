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

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('page_edit.php');
}

$pageHandler  = xpages_get_handler('page');
$fieldHandler = xpages_get_handler('field');
$valueHandler = xpages_get_handler('fieldvalue');

if (!$pageHandler || !$fieldHandler || !$valueHandler) {
    echo '<div style="margin:18px 0;padding:14px 16px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:6px">xPages handler unavailable.</div>';
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

    // Note: title/body/*_desc/header/footer_code are authored HTML from a
    // WYSIWYG editor and must preserve tags — use getText() which keeps
    // the raw string (Request::getString() runs through FilterInput which
    // can collapse whitespace in HTML-heavy bodies).
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
        // Header/footer code is raw HTML/JS — preserve byte-for-byte.
        $newHeader = Request::getText('header_code', '', 'POST');
        $newFooter = Request::getText('footer_code', '', 'POST');
        $oldHeader = (string)$page->getVar('header_code', 'n');
        $oldFooter = (string)$page->getVar('footer_code', 'n');

        // Audit trail — these fields render via {nofilter} on the public
        // page, so every modification is security-relevant even when the
        // actor is a legitimate webmaster. Logged via XoopsLogger so the
        // entry appears in XOOPS's extra-info log for post-incident review.
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
        echo '<div style="color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;padding:12px;border-radius:6px;margin-bottom:16px">' . _AM_XPAGES_SAVE_ERROR . '</div>';
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

// ── Form ──────────────────────────────────────────────────────────────────────
$page = $pageId ? $pageHandler->get($pageId) : $pageHandler->create();
if (!$page) {
    redirect_header('pages.php', 3, _AM_XPAGES_PAGE_NOT_FOUND);
    exit;
}

$existingValues = $pageId ? $valueHandler->getValuesForPage($pageId) : array();
$extraFields    = $pageId ? $fieldHandler->getFieldsForPage($pageId, false) : $fieldHandler->getGlobalFields(false);

$allPages = $pageHandler->getObjects() ?: array();
$blockedParentIds = array_flip($descendantIds);
?>

<h3><?= $pageId ? _AM_XPAGES_EDIT_PAGE : _AM_XPAGES_ADD_PAGE ?></h3>

<style>
    /* Kill the <li>::marker bullets the admin theme re-applies on tab <li>
       items. Parent `list-style:none` alone isn't enough — some admin CSS
       resets set `::marker { content: '\2022'; }` on generic `li` selectors
       which win over the parent's `list-style` shorthand. Belt-and-suspenders: */
    .xp-tabs{display:flex;gap:3px;padding:0;margin:0 0 0;list-style:none;list-style-type:none;list-style-image:none;border-bottom:2px solid #4472c4}
    .xp-tabs li{list-style:none;list-style-type:none;list-style-image:none}
    .xp-tabs li::marker{content:'';display:none}
    .xp-tabs li a{display:block;padding:7px 16px;background:#e8eef8;border:1px solid #b8c8e8;border-bottom:none;text-decoration:none;color:#333;border-radius:4px 4px 0 0;font-size:13px}
    .xp-tabs li.active a{background:#4472c4;color:#fff;font-weight:600}
    .xp-tab-pane{display:none;border:1px solid #4472c4;border-top:none;padding:18px;background:#fff;margin-bottom:20px}
    .xp-tab-pane.active{display:block}
    .xpages-field{margin-bottom:14px}
    .xpages-field label{display:block;font-weight:600;margin-bottom:4px;font-size:13px}
    .xpages-field input[type=text],.xpages-field input[type=url],
    .xpages-field input[type=email],.xpages-field input[type=number],
    .xpages-field select,.xpages-field textarea{width:100%;box-sizing:border-box;padding:6px 8px;border:1px solid #ced4da;border-radius:4px;font-size:13px}
    .xpages-field .xpf-desc{color:#6c757d;font-size:12px;display:block;margin-top:3px}
    .xpages-field .req{color:#dc3545}
    .xp-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .formButton{background:#4472c4;color:#fff;border:none;padding:8px 22px;cursor:pointer;border-radius:5px;font-size:13px}
    .formButton:hover{background:#3461b0}
    .xpages-file-field{background:#f8f9fa;padding:10px;border-radius:6px;border:1px solid #dee2e6}
    .xpages-current-file{background:#fff;padding:8px;border-radius:4px;margin-top:8px;font-size:12px}
    .xpages-radio-group label{display:inline-block;margin-right:15px;font-weight:normal}
</style>

<form method="post" action="page_edit.php" enctype="multipart/form-data" id="xpages-edit-form">
    <input type="hidden" name="op" value="save">
    <input type="hidden" name="page_id" value="<?= $pageId ?>">
    <?= $GLOBALS['xoopsSecurity']->getTokenHTML() ?>

    <ul class="xp-tabs" id="xpTabList">
        <li class="active"><a href="#tab-main" onclick="xpShowTab(this,'tab-main');return false"><?= _AM_XPAGES_TAB_MAIN ?></a></li>
        <li><a href="#tab-seo"  onclick="xpShowTab(this,'tab-seo');return false"><?= _AM_XPAGES_TAB_SEO ?></a></li>
        <li><a href="#tab-adv"  onclick="xpShowTab(this,'tab-adv');return false"><?= _AM_XPAGES_TAB_ADVANCED ?></a></li>
        <?php if (!empty($extraFields)): ?>
            <li><a href="#tab-extra" onclick="xpShowTab(this,'tab-extra');return false"><?= _AM_XPAGES_TAB_EXTRA ?></a></li>
        <?php endif; ?>
    </ul>

    <!-- TAB: Genel Bilgiler -->
    <div id="tab-main" class="xp-tab-pane active">
        <div class="xpages-field">
            <label><?= _AM_XPAGES_PAGE_TITLE ?> <span class="req">*</span></label>
            <input type="text" name="title" value="<?= htmlspecialchars((string)$page->getVar('title', 'n'), ENT_QUOTES) ?>" required>
        </div>
        <div class="xpages-field">
            <label><?= _AM_XPAGES_PAGE_ALIAS ?></label>
            <input type="text" name="alias" value="<?= htmlspecialchars((string)$page->getVar('alias', 'n'), ENT_QUOTES) ?>" placeholder="<?= _AM_XPAGES_ALIAS_PLACEHOLDER ?>">
            <small class="xpf-desc"><?= _AM_XPAGES_ALIAS_HELP ?></small>
        </div>
        <div class="xpages-field">
            <label><?= _AM_XPAGES_SHORT_DESC ?></label>
            <textarea name="short_desc" rows="3"><?= htmlspecialchars((string)$page->getVar('short_desc', 'n'), ENT_QUOTES) ?></textarea>
        </div>
        <div class="xpages-field">
            <label><?= _AM_XPAGES_BODY ?></label>
            <textarea name="body" rows="16"><?= htmlspecialchars((string)$page->getVar('body', 'n'), ENT_QUOTES) ?></textarea>
        </div>
        <div class="xp-row">
            <div class="xpages-field">
                <label><?= _AM_XPAGES_STATUS ?></label>
                <select name="page_status">
                    <option value="1" <?= $page->getVar('page_status') == 1 ? 'selected' : '' ?>><?= _AM_XPAGES_ACTIVE ?></option>
                    <option value="0" <?= $page->getVar('page_status') == 0 ? 'selected' : '' ?>><?= _AM_XPAGES_INACTIVE ?></option>
                </select>
            </div>
            <div class="xpages-field">
                <label><?= _AM_XPAGES_PAGE_ORDER ?></label>
                <input type="number" name="menu_order" value="<?= (int)$page->getVar('menu_order') ?>" min="0">
            </div>
        </div>
        <div class="xp-row">
            <div class="xpages-field">
                <label><input type="checkbox" name="show_in_menu" value="1" <?= $page->getVar('show_in_menu') ? 'checked' : '' ?>> <?= _AM_XPAGES_SHOW_IN_MENU ?></label>
            </div>
            <div class="xpages-field">
                <label><input type="checkbox" name="show_in_nav" value="1" <?= $page->getVar('show_in_nav') ? 'checked' : '' ?>> <?= _AM_XPAGES_SHOW_IN_NAV ?></label>
            </div>
        </div>
        <div class="xpages-field">
            <label><?= _AM_XPAGES_PARENT_PAGE ?></label>
            <select name="parent_id">
                <option value="0"><?= _AM_XPAGES_NO_PARENT ?></option>
                <?php foreach ($allPages as $ap):
                    $apId = (int)$ap->getVar('page_id');
                    if ($apId === $pageId || isset($blockedParentIds[$apId])) continue; ?>
                    <option value="<?= $ap->getVar('page_id') ?>" <?= (int)$page->getVar('parent_id') === (int)$ap->getVar('page_id') ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string)$ap->getVar('title'), ENT_QUOTES) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- TAB: SEO -->
    <div id="tab-seo" class="xp-tab-pane">
        <div class="xpages-field">
            <label><?= _AM_XPAGES_META_TITLE ?></label>
            <input type="text" name="meta_title" value="<?= htmlspecialchars((string)$page->getVar('meta_title', 'n'), ENT_QUOTES) ?>" maxlength="255">
            <small class="xpf-desc"><?= _AM_XPAGES_META_TITLE_HELP ?></small>
        </div>
        <div class="xpages-field">
            <label><?= _AM_XPAGES_META_KEYWORDS ?></label>
            <textarea name="meta_keywords" rows="2"><?= htmlspecialchars((string)$page->getVar('meta_keywords', 'n'), ENT_QUOTES) ?></textarea>
        </div>
        <div class="xpages-field">
            <label><?= _AM_XPAGES_META_DESC ?></label>
            <textarea name="meta_desc" rows="3"><?= htmlspecialchars((string)$page->getVar('meta_desc', 'n'), ENT_QUOTES) ?></textarea>
            <small class="xpf-desc"><?= _AM_XPAGES_META_DESC_HELP ?></small>
        </div>
        <div class="xp-row">
            <div class="xpages-field">
                <label><input type="checkbox" name="noindex" value="1" <?= $page->getVar('noindex') ? 'checked' : '' ?>> <?= _AM_XPAGES_NOINDEX ?></label>
            </div>
            <div class="xpages-field">
                <label><input type="checkbox" name="nofollow" value="1" <?= $page->getVar('nofollow') ? 'checked' : '' ?>> <?= _AM_XPAGES_NOFOLLOW ?></label>
            </div>
        </div>
        <div class="xpages-field">
            <label><?= _AM_XPAGES_REDIRECT_URL ?></label>
            <input type="url" name="redirect_url" value="<?= htmlspecialchars((string)$page->getVar('redirect_url', 'n'), ENT_QUOTES) ?>" placeholder="https://">
            <small class="xpf-desc"><?= _AM_XPAGES_REDIRECT_HELP ?></small>
        </div>
    </div>

    <!-- TAB: Gelişmiş -->
    <div id="tab-adv" class="xp-tab-pane">
        <?php if ($canUseAdvancedCode): ?>
            <div role="alert" class="xp-adv-warning" style="background:#fff3cd;border:1px solid #ffc107;border-left:4px solid #dc3545;padding:12px 14px;margin-bottom:16px;border-radius:6px;font-size:13px;color:#3b2b00">
                <strong>⚠ </strong><?= _AM_XPAGES_ADVANCED_CODE_WARNING ?>
            </div>
            <div class="xpages-field">
                <label><?= _AM_XPAGES_HEADER_CODE ?></label>
                <textarea name="header_code" rows="5" style="font-family:monospace"><?= htmlspecialchars((string)$page->getVar('header_code', 'n'), ENT_QUOTES) ?></textarea>
                <small class="xpf-desc"><?= _AM_XPAGES_HEADER_CODE_HELP ?></small>
            </div>
            <div class="xpages-field">
                <label><?= _AM_XPAGES_FOOTER_CODE ?></label>
                <textarea name="footer_code" rows="5" style="font-family:monospace"><?= htmlspecialchars((string)$page->getVar('footer_code', 'n'), ENT_QUOTES) ?></textarea>
                <small class="xpf-desc"><?= _AM_XPAGES_FOOTER_CODE_HELP ?></small>
            </div>
        <?php else: ?>
            <div class="xpages-field">
                <label><?= _AM_XPAGES_HEADER_CODE ?></label>
                <div style="padding:10px 12px;background:#f8f9fa;border:1px solid #dee2e6;border-radius:6px;color:#6c757d">
                    <?= _AM_XPAGES_ADVANCED_CODE_RESTRICTED ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($pageId): ?>
            <div class="xpages-field">
                <label><?= _AM_XPAGES_MANAGE_FIELDS_FOR_PAGE ?></label>
                <a href="fields.php?page_id=<?= $pageId ?>" style="background:#4472c4;color:#fff;padding:6px 14px;text-decoration:none;border-radius:4px;font-size:13px"><?= _AM_XPAGES_MENU_FIELDS ?></a>
            </div>
            <div class="xpages-field">
                <label><?= _AM_XPAGES_GALLERY_TITLE ?></label>
                <a href="gallery.php?page_id=<?= $pageId ?>" style="background:#f59e0b;color:#fff;padding:6px 14px;text-decoration:none;border-radius:4px;font-size:13px;display:inline-block"><?= _AM_XPAGES_GALLERY_MANAGE ?></a>
                <small class="xpf-desc"><?= _AM_XPAGES_GALLERY_MANAGE_HELP ?></small>
            </div>
        <?php else: ?>
            <div class="xpages-field">
                <label><?= _AM_XPAGES_GALLERY_TITLE ?></label>
                <button type="button" disabled style="background:#ccc;padding:6px 14px;border-radius:4px;border:none"><?= _AM_XPAGES_GALLERY_SAVE_FIRST ?></button>
                <small class="xpf-desc"><?= _AM_XPAGES_GALLERY_SAVE_FIRST_HELP ?></small>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB: İlave Alanlar -->
    <?php if (!empty($extraFields)): ?>
        <div id="tab-extra" class="xp-tab-pane">
            <?php foreach ($extraFields as $field):
                $fid = (int)$field->getVar('field_id');
                $val = $existingValues[$fid] ?? (string)$field->getVar('field_default', 'n');
                echo xpages_render_field_input($field, $val);
            endforeach; ?>
        </div>
    <?php endif; ?>

    <br>
    <input type="submit" value="<?= _AM_XPAGES_SAVE ?>" class="formButton">
    &nbsp;<a href="pages.php" style="color:#6c757d;font-size:13px"><?= _AM_XPAGES_CANCEL ?></a>
</form>

<script>
    function xpShowTab(el, id) {
        document.querySelectorAll('.xp-tab-pane').forEach(function(p){ p.classList.remove('active'); });
        document.querySelectorAll('#xpTabList li').forEach(function(l){ l.classList.remove('active'); });
        document.getElementById(id).classList.add('active');
        el.parentElement.classList.add('active');
    }
</script>

<?php
xoops_cp_footer();
?>
