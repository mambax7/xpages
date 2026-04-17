<?php
/**
 * xPages — English language file (admin)
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

// Page form
define('_AM_XPAGES_ADD_PAGE',      'Add New Page');
define('_AM_XPAGES_EDIT_PAGE',     'Edit Page');
define('_AM_XPAGES_PAGE_TITLE',    'Title');
define('_AM_XPAGES_PAGE_ALIAS',    'URL Alias');
define('_AM_XPAGES_ALIAS_HELP',    'Auto-generated from title if left blank. Example: about-us');
define('_AM_XPAGES_SHORT_DESC',    'Short Description');
define('_AM_XPAGES_BODY',          'Content');
define('_AM_XPAGES_STATUS',        'Status');
define('_AM_XPAGES_ACTIVE',        'Active');
define('_AM_XPAGES_INACTIVE',      'Inactive');
define('_AM_XPAGES_PAGE_ORDER',    'Sort Order');
define('_AM_XPAGES_SHOW_IN_MENU',  'Show in Menu');
define('_AM_XPAGES_SHOW_IN_NAV',   'Show in Navigation');
define('_AM_XPAGES_PARENT_PAGE',   'Parent Page');
define('_AM_XPAGES_NO_PARENT',     '— No Parent —');
define('_AM_XPAGES_PAGE_STATUS',   'Status');

// SEO
define('_AM_XPAGES_TAB_MAIN',          'General');
define('_AM_XPAGES_TAB_SEO',           'SEO');
define('_AM_XPAGES_TAB_ADVANCED',      'Advanced');
define('_AM_XPAGES_TAB_EXTRA',         'Extra Fields');
define('_AM_XPAGES_META_TITLE',        'Meta Title');
define('_AM_XPAGES_META_TITLE_HELP',   'Page title will be used if left blank.');
define('_AM_XPAGES_META_KEYWORDS',     'Meta Keywords');
define('_AM_XPAGES_META_DESC',         'Meta Description');
define('_AM_XPAGES_META_DESC_HELP',    'Short description shown in search engines. Recommended: 150-160 characters.');
define('_AM_XPAGES_NOINDEX',           'Do not index this page (noindex)');
define('_AM_XPAGES_NOFOLLOW',          'Do not follow links on this page (nofollow)');
define('_AM_XPAGES_REDIRECT_URL',      'Redirect URL');
define('_AM_XPAGES_REDIRECT_HELP',     'If set, the page will redirect to this address.');
define('_AM_XPAGES_PARENT_INVALID',    'A page cannot be set as the parent of itself or one of its descendants.');

// Advanced
define('_AM_XPAGES_HEADER_CODE',       'Header Code (<head>)');
define('_AM_XPAGES_HEADER_CODE_HELP',  'Code to be injected into this page\'s <head> tag only (CSS, meta, etc.)');
define('_AM_XPAGES_FOOTER_CODE',       'Footer Code (</body>)');
define('_AM_XPAGES_FOOTER_CODE_HELP',  'Code to be injected before this page\'s </body> tag only (JS, etc.)');
define('_AM_XPAGES_MANAGE_FIELDS_FOR_PAGE', 'Manage Fields for This Page');
define('_AM_XPAGES_ADVANCED_CODE_RESTRICTED', 'Only website webmasters can edit header/footer code.');

// Extra field form
define('_AM_XPAGES_ADD_FIELD',             'Add Field');
define('_AM_XPAGES_EDIT_FIELD',            'Edit Field');
define('_AM_XPAGES_FIELD_SETTINGS',        'Field Settings');
define('_AM_XPAGES_FIELD_NAME',            'Field Name (system)');
define('_AM_XPAGES_FIELD_NAME_HELP',       'Lowercase letters, numbers and underscore. Use {$xpages.extra_fields.field_name} in templates.');
define('_AM_XPAGES_FIELD_LABEL',           'Label (display name)');
define('_AM_XPAGES_FIELD_TYPE',            'Field Type');
define('_AM_XPAGES_FIELD_OPTIONS',         'Options (for select/radio)');
define('_AM_XPAGES_FIELD_OPTIONS_HELP',    'One option per line or JSON: {"value":"Label"}');
define('_AM_XPAGES_FIELD_DEFAULT',         'Default Value');
define('_AM_XPAGES_FIELD_DEFAULT_HELP',    'Default value used when the field is left empty.');
define('_AM_XPAGES_FIELD_DESC',            'Description / Help Text');
define('_AM_XPAGES_FIELD_ORDER',           'Sort Order');
define('_AM_XPAGES_FIELD_STATUS',          'Field Status');
define('_AM_XPAGES_FIELD_REQUIRED',        'Required Field');
define('_AM_XPAGES_FIELD_SHOW_IN_TPL',     'Show in Template');
define('_AM_XPAGES_GLOBAL_FIELDS',         'Global Fields (All Pages)');
define('_AM_XPAGES_NO_FIELDS',             'No fields added yet.');
define('_AM_XPAGES_FIELDS',                'Fields');
define('_AM_XPAGES_BACK_TO_PAGE',          '← Back to Page');

// Field types
define('_AM_XPAGES_FIELD_TYPE_TEXT',     'Text (single line)');
define('_AM_XPAGES_FIELD_TYPE_TEXTAREA', 'Text (multi-line)');
define('_AM_XPAGES_FIELD_TYPE_EDITOR',   'Rich Text Editor');
define('_AM_XPAGES_FIELD_TYPE_IMAGE',    'Image');
define('_AM_XPAGES_FIELD_TYPE_FILE',     'File');
define('_AM_XPAGES_FIELD_TYPE_URL',      'Link (URL)');
define('_AM_XPAGES_FIELD_TYPE_EMAIL',    'E-mail');
define('_AM_XPAGES_FIELD_TYPE_TEL',      'Phone');
define('_AM_XPAGES_FIELD_TYPE_DATE',     'Date');
define('_AM_XPAGES_FIELD_TYPE_NUMBER',   'Number');
define('_AM_XPAGES_FIELD_TYPE_CHECKBOX', 'Checkbox');
define('_AM_XPAGES_FIELD_TYPE_SELECT',   'Dropdown List');
define('_AM_XPAGES_FIELD_TYPE_RADIO',    'Radio Button');
define('_AM_XPAGES_FIELD_TYPE_COLOR',    'Color');
define('_AM_XPAGES_FIELD_TYPE_CODE',     'Code / HTML');
define('_AM_XPAGES_FIELD_TYPE_FILE_IMG', '📎 File/Image');

// Buttons and actions
define('_AM_XPAGES_SAVE',              'Save');
define('_AM_XPAGES_CANCEL',            'Cancel');
define('_AM_XPAGES_EDIT',              'Edit');
define('_AM_XPAGES_DELETE',            'Delete');
define('_AM_XPAGES_YES',               'Yes');
define('_AM_XPAGES_NO',                'No');
define('_AM_XPAGES_ACTIONS',           'Actions');
define('_AM_XPAGES_BROWSE',            'Browse');

// Messages
define('_AM_XPAGES_PAGE_SAVED',          'Page saved successfully.');
define('_AM_XPAGES_PAGE_DELETED',        'Page deleted.');
define('_AM_XPAGES_FIELD_SAVED',         'Field saved successfully.');
define('_AM_XPAGES_FIELD_DELETED',       'Field deleted.');
define('_AM_XPAGES_SAVE_ERROR',          'An error occurred while saving.');
define('_AM_XPAGES_PAGE_NOT_FOUND',      'Page not found.');
define('_AM_XPAGES_FIELD_NAME_EXISTS',   'This field name is already in use. Please choose a different name.');
define('_AM_XPAGES_DELETE_CONFIRM',      'Are you sure you want to delete the page "%s"?');
define('_AM_XPAGES_FIELD_DELETE_CONFIRM', 'Are you sure you want to delete the field "%s"?');
define('_AM_XPAGES_INVALID_FILE_TYPE',   'Invalid file type. Allowed: jpg, png, gif, webp, pdf, doc, docx, zip');

// Dashboard
define('_AM_XPAGES_DASHBOARD',          'xPages Control Panel');
define('_AM_XPAGES_QUICK_ACTIONS',      'Quick Actions');
define('_AM_XPAGES_RECENT_PAGES',       'Recent Pages');
define('_AM_XPAGES_NO_PAGES',           'No pages yet');
define('_AM_XPAGES_CREATE_FIRST',       '+ Create First Page');
define('_AM_XPAGES_TITLE',              'Title');
define('_AM_XPAGES_CREATED',            'Created');
define('_AM_XPAGES_HITS',               'Views');
define('_AM_XPAGES_VIEW',               'View');
define('_AM_XPAGES_ALL_PAGES',          'View All Pages');
define('_AM_XPAGES_VIEW_ALL',           'View All');
define('_AM_XPAGES_SEE_ALL_PAGES',      'See all pages →');
define('_AM_XPAGES_NO_STATS',           'No statistics yet');
define('_AM_XPAGES_NO_PAGES_YET',       'No pages added yet');

// Dashboard widget labels
define('_AM_XPAGES_WIDGET_STATS',       '📊 Quick Statistics');
define('_AM_XPAGES_WIDGET_RECENT',      '🆕 Recently Added Pages');
define('_AM_XPAGES_WIDGET_POPULAR',     '🔥 Most Read');
define('_AM_XPAGES_WIDGET_MONTHLY',     '📈 Monthly Page Statistics');
define('_AM_XPAGES_WIDGET_QUICK',       '⚡ Quick Actions');
define('_AM_XPAGES_WIDGET_SYSINFO',     'ℹ️ System Information');

// Stat cards
define('_AM_XPAGES_STAT_TOTAL_PAGES',   'Total Pages');
define('_AM_XPAGES_STAT_ACTIVE_PAGES',  'Active Pages');
define('_AM_XPAGES_STAT_FIELDS_COUNT',  'Custom Fields');
define('_AM_XPAGES_STAT_GALLERY_COUNT', 'Gallery Images');

// System info labels
define('_AM_XPAGES_SYSINFO_XOOPS',      'XOOPS Version:');
define('_AM_XPAGES_SYSINFO_MODULE',     'xPages Version:');
define('_AM_XPAGES_SYSINFO_PHP',        'PHP Version:');
define('_AM_XPAGES_SYSINFO_UPDATED',    'Last updated:');

// Statistics
define('_AM_XPAGES_STAT_PAGES',  'Total pages: <strong>%d</strong>');
define('_AM_XPAGES_STAT_FIELDS', 'Total field definitions: <strong>%d</strong>');

// Gallery
define('_AM_XPAGES_GALLERY_TITLE',          '🖼️ Gallery Management');
define('_AM_XPAGES_GALLERY_ADD',            '➕ Add New Image');
define('_AM_XPAGES_GALLERY_EDIT',           '✏️ Edit Image');
define('_AM_XPAGES_GALLERY_NEW',            '➕ Add New Image');
define('_AM_XPAGES_GALLERY_MANAGE',         '📸 Manage Image Gallery');
define('_AM_XPAGES_GALLERY_MANAGE_HELP',    'Manage the image gallery for this page');
define('_AM_XPAGES_GALLERY_SAVE_FIRST',     'Save the page first');
define('_AM_XPAGES_GALLERY_SAVE_FIRST_HELP','You must save the page before adding a gallery');
define('_AM_XPAGES_GALLERY_ALL_PAGES',      'All Pages');
define('_AM_XPAGES_GALLERY_DELETE_CONFIRM', 'Are you sure you want to delete the image "%s"?');
define('_AM_XPAGES_GALLERY_DELETED',        'Image deleted successfully');
define('_AM_XPAGES_GALLERY_SAVED',          'Image saved successfully');
define('_AM_XPAGES_GALLERY_SAVE_ERROR',     'An error occurred while saving!');
define('_AM_XPAGES_GALLERY_EMPTY',          'No images added yet');
define('_AM_XPAGES_GALLERY_ADD_FIRST',      '+ Add First Image');
define('_AM_XPAGES_GALLERY_IMG_TITLE',      'Title');
define('_AM_XPAGES_GALLERY_IMG_DESC',       'Description');
define('_AM_XPAGES_GALLERY_IMG_FILE',       'Image');
define('_AM_XPAGES_GALLERY_IMG_FILE_HELP',  'JPG, PNG, GIF, WEBP (max 5MB)');
define('_AM_XPAGES_GALLERY_IMG_URL',        'or External URL');
define('_AM_XPAGES_GALLERY_IMG_URL_HELP',   'Fill in if you want to use an external image URL');
define('_AM_XPAGES_GALLERY_IMG_ORDER',      'Order');
define('_AM_XPAGES_GALLERY_IMG_STATUS',     'Status');
define('_AM_XPAGES_GALLERY_CURRENT_IMG',    'Current image. If you select a new one, the old will be deleted.');
define('_AM_XPAGES_GALLERY_ORDER_STATUS',   'Order: %d | ');

// Field form - file field
define('_AM_XPAGES_FILE_FIELD_HELP',        '📎 Select image or file (jpg, png, gif, pdf, doc, zip)');
define('_AM_XPAGES_FILE_CURRENT',           'Current file:');
define('_AM_XPAGES_FILE_REPLACE_HINT',      'Select above to upload a new file (old one will be deleted).');
define('_AM_XPAGES_FILE_VIEW',              '📎 View file');
define('_AM_XPAGES_OPTIONS_HINT_TITLE',     '💡 How to write options?');
define('_AM_XPAGES_OPTIONS_HINT_BODY',      'Write each option on a new line.');
define('_AM_XPAGES_OPTIONS_HINT_EXAMPLE',   'Example:');

// include/functions.php inner texts
define('_AM_XPAGES_SELECT_PLACEHOLDER',     '-- Select --');
define('_AM_XPAGES_FILE_CURRENT_LABEL',     'Current file:');
define('_AM_XPAGES_FILE_REPLACE_NOTE',      'Select above to upload a new file (old file will be deleted).');
define('_AM_XPAGES_FILE_NONE',              'No file selected yet.');

// Block edit form
define('_AM_XPAGES_BLOCK_LIMIT_LABEL',      'Number of pages to show:');
define('_AM_XPAGES_BLOCK_SHOW_DESC',        'Show short description');

// index.php (admin) hardcoded texts
define('_AM_XPAGES_DASHBOARD_SUBTITLE',     'Page Management Module — Overview');
define('_AM_XPAGES_RECENT_PAGES_WIDGET',    '🆕 Recently Added Pages');
define('_AM_XPAGES_POPULAR_PAGES_WIDGET',   '🔥 Most Read');
define('_AM_XPAGES_MONTHLY_STATS',          '📈 Monthly Page Statistics');
define('_AM_XPAGES_QUICK_ACTIONS_WIDGET',   '⚡ Quick Actions');
define('_AM_XPAGES_SYS_INFO_WIDGET',        'ℹ️ System Information');
define('_AM_XPAGES_STAT_TOTAL_PAGES_LBL',   '📄 Total Pages');
define('_AM_XPAGES_STAT_ACTIVE_PAGES_LBL',  '✅ Active Pages');
define('_AM_XPAGES_STAT_FIELDS_LBL',        '⚙️ Custom Fields');
define('_AM_XPAGES_STAT_GALLERY_LBL',       '🖼️ Gallery Images');
define('_AM_XPAGES_BTN_NEW_PAGE',           '📄 + New Page');
define('_AM_XPAGES_BTN_NEW_FIELD',          '⚙️ + New Field');
define('_AM_XPAGES_BTN_GALLERY',            '🖼️ Gallery Management');
define('_AM_XPAGES_BTN_LIST_PAGES',         '📋 List Pages');
define('_AM_XPAGES_TOGGLE_STATUS_TITLE',    'Toggle status');
define('_AM_XPAGES_STATUS_ACTIVE',          '✅ Active');
define('_AM_XPAGES_STATUS_INACTIVE',        '❌ Inactive');

// Page editor and field form helpers
define('_AM_XPAGES_ALIAS_PLACEHOLDER',             'Auto-generated');
define('_AM_XPAGES_FIELD_OPTIONS_SAMPLE_PLACEHOLDER', 'Red&#10;Blue&#10;Green');
define('_AM_XPAGES_FIELD_OPTIONS_SAMPLE_CODE',        'Red<br>Blue<br>Green');

// About page
define('_AM_XPAGES_ABOUT_TITLE',               'About — xPages Module');
define('_AM_XPAGES_ABOUT_MODULE_INFO_TITLE',   'Module Information');
define('_AM_XPAGES_ABOUT_FEATURES_TITLE',      'Features');
define('_AM_XPAGES_ABOUT_TEMPLATE_TITLE',      'Smarty Template Variables');
define('_AM_XPAGES_ABOUT_SUPPORT_TITLE',       'Support & Contact');
define('_AM_XPAGES_ABOUT_FOOTER',              'xPages is distributed under the GPL 2.0 license.');
define('_AM_XPAGES_ABOUT_LABEL_MODULE_NAME',   'Module Name');
define('_AM_XPAGES_ABOUT_LABEL_VERSION',       'Version');
define('_AM_XPAGES_ABOUT_LABEL_AUTHOR',        'Author');
define('_AM_XPAGES_ABOUT_LABEL_WEBSITE',       'Website');
define('_AM_XPAGES_ABOUT_LABEL_LICENSE',       'License');
define('_AM_XPAGES_ABOUT_LABEL_COMPATIBILITY', 'Compatibility');
define('_AM_XPAGES_ABOUT_LABEL_ENCODING',      'Encoding');
define('_AM_XPAGES_ABOUT_FEATURE_1',           '📄 <strong>Static Pages</strong> — SEO-friendly alias URLs');
define('_AM_XPAGES_ABOUT_FEATURE_2',           '🔧 <strong>Dynamic Field System</strong> — 14 field types, global or page-specific');
define('_AM_XPAGES_ABOUT_FEATURE_3',           '📁 <strong>Hierarchical Structure</strong> — Parent/child page relationships');
define('_AM_XPAGES_ABOUT_FEATURE_4',           '🎨 <strong>Menu Integration</strong> — Automatic menu and navigation block');
define('_AM_XPAGES_ABOUT_FEATURE_5',           '🔍 <strong>SEO Optimization</strong> — Meta title/description, noindex/nofollow');
define('_AM_XPAGES_ABOUT_FEATURE_6',           '🔗 <strong>URL Redirects</strong> — Page-level URL redirects');
define('_AM_XPAGES_ABOUT_FEATURE_7',           '📊 <strong>Statistics</strong> — Hit counter and XOOPS comment support');
define('_AM_XPAGES_ABOUT_FEATURE_8',           '🔎 <strong>Search Integration</strong> — XOOPS site search');
define('_AM_XPAGES_ABOUT_FEATURE_9',           '⚡ <strong>Header/Footer Code</strong> — Page-specific JS/CSS injection');
define('_AM_XPAGES_ABOUT_SUPPORT_WEB',         'Web');
define('_AM_XPAGES_ABOUT_SUPPORT_EMAIL',       'Email');
define('_AM_XPAGES_ABOUT_SUPPORT_GITHUB',      'GitHub');
define('_AM_XPAGES_ABOUT_SMARTY_EXAMPLE', <<<'EOT'
{* Basic fields *}
{$xpages_page.title}
{$xpages_page.body nofilter}
{$xpages_page.short_desc}
{$xpages_page.alias}
{$xpages_page.hits}
{$xpages_page.create_date|date_format:"%d.%m.%Y"}
{$xpages_page.update_date|date_format:"%d.%m.%Y %H:%M"}

{* SEO *}
{$xpages_page.meta_title}
{$xpages_page.meta_desc}
{$xpages_page.robots}

{* Direct access to a custom field *}
{$xpages_page.extra_fields.brochure_url.value}
{$xpages_page.extra_fields.cover_image.value}

{* List all custom fields in a loop *}
{foreach key=fid item=f from=$xpages_page.extra_fields_by_id}
    {if $f.show_in_tpl && $f.value != ""}
    <div class="field field-{$f.field_type}" id="field-{$fid}">
        <strong>{$f.field_label}:</strong>
        {if $f.field_type == "checkbox"}
            {if $f.value}✓{else}✗{/if}
        {elseif $f.field_type == "url"}
            <a href="{$f.value}" target="_blank">{$f.value}</a>
        {elseif $f.field_type == "image"}
            <img src="{$f.value}" alt="{$f.field_label}">
        {else}
            {$f.value}
        {/if}
    </div>
    {/if}
{/foreach}
EOT
);
