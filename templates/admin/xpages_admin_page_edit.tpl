<{* xPages — Admin page add / edit form.
    Single template that covers all four tabs (Main / SEO / Advanced /
    Extra Fields) because they share one <form> wrapper. Rendered by
    admin/page_edit.php after the save branch either redirects or echoes
    a save-error alert.

    Controller-supplied data:
      $form_title                — "Add page" / "Edit page"
      $page_id                   — int (0 for new pages)
      $page                      — flat descriptor {title, alias, short_desc,
                                   body, status, menu_order, show_in_menu,
                                   show_in_nav, parent_id, meta_title,
                                   meta_keywords, meta_desc, noindex,
                                   nofollow, redirect_url, header_code,
                                   footer_code}
      $parent_options            — list of {id, title, selected} dicts for
                                   the parent-page <select>. Controller
                                   pre-filters out $pageId + descendants.
      $can_use_advanced_code     — bool; gates the header/footer_code
                                   textareas vs. a "restricted" notice
      $has_extra_fields          — bool; shows the Extra tab
      $extra_fields_html         — pre-rendered HTML (from xpages_render_
                                   field_input), rendered via {nofilter}
      $label_*                   — translated copy (see keys below)
      $xoops_token_html          — auto-injected by xpages_admin_render() *}>

<h3><{$form_title}></h3>

<form method="post" action="page_edit.php" enctype="multipart/form-data" id="xpages-edit-form">
    <input type="hidden" name="op"      value="save">
    <input type="hidden" name="page_id" value="<{$page_id}>">
    <{$xoops_token_html nofilter}>

    <ul class="xp-tabs" id="xpTabList">
        <li class="active"><a href="#tab-main" onclick="xpShowTab(this,'tab-main');return false"><{$label_tab_main}></a></li>
        <li><a href="#tab-seo"  onclick="xpShowTab(this,'tab-seo');return false"><{$label_tab_seo}></a></li>
        <li><a href="#tab-adv"  onclick="xpShowTab(this,'tab-adv');return false"><{$label_tab_advanced}></a></li>
        <{if $has_extra_fields}>
            <li><a href="#tab-extra" onclick="xpShowTab(this,'tab-extra');return false"><{$label_tab_extra}></a></li>
        <{/if}>
    </ul>

    <{* ── TAB: Genel Bilgiler ── *}>
    <div id="tab-main" class="xp-tab-pane active">
        <div class="xpages-field">
            <label><{$label_page_title}> <span class="req">*</span></label>
            <input type="text" name="title" value="<{$page.title|escape}>" required>
        </div>
        <div class="xpages-field">
            <label><{$label_page_alias}></label>
            <input type="text" name="alias" value="<{$page.alias|escape}>" placeholder="<{$alias_placeholder|escape}>">
            <small class="xpf-desc"><{$alias_help}></small>
        </div>
        <div class="xpages-field">
            <label><{$label_short_desc}></label>
            <textarea name="short_desc" rows="3"><{$page.short_desc|escape}></textarea>
        </div>
        <div class="xpages-field">
            <label><{$label_body}></label>
            <textarea name="body" rows="16"><{$page.body|escape}></textarea>
        </div>
        <div class="xp-row">
            <div class="xpages-field">
                <label><{$label_status}></label>
                <select name="page_status">
                    <option value="1" <{if $page.status == 1}>selected="selected"<{/if}>><{$label_active}></option>
                    <option value="0" <{if $page.status == 0}>selected="selected"<{/if}>><{$label_inactive}></option>
                </select>
            </div>
            <div class="xpages-field">
                <label><{$label_page_order}></label>
                <input type="number" name="menu_order" value="<{$page.menu_order}>" min="0">
            </div>
        </div>
        <div class="xp-row">
            <div class="xpages-field">
                <label><input type="checkbox" name="show_in_menu" value="1" <{if $page.show_in_menu}>checked="checked"<{/if}>> <{$label_show_in_menu}></label>
            </div>
            <div class="xpages-field">
                <label><input type="checkbox" name="show_in_nav" value="1" <{if $page.show_in_nav}>checked="checked"<{/if}>> <{$label_show_in_nav}></label>
            </div>
        </div>
        <div class="xpages-field">
            <label><{$label_parent_page}></label>
            <select name="parent_id">
                <option value="0"><{$label_no_parent}></option>
                <{foreach item=opt from=$parent_options}>
                    <option value="<{$opt.id}>" <{if $opt.selected}>selected="selected"<{/if}>><{$opt.title|escape}></option>
                <{/foreach}>
            </select>
        </div>
    </div>

    <{* ── TAB: SEO ── *}>
    <div id="tab-seo" class="xp-tab-pane">
        <div class="xpages-field">
            <label><{$label_meta_title}></label>
            <input type="text" name="meta_title" value="<{$page.meta_title|escape}>" maxlength="255">
            <small class="xpf-desc"><{$meta_title_help}></small>
        </div>
        <div class="xpages-field">
            <label><{$label_meta_keywords}></label>
            <textarea name="meta_keywords" rows="2"><{$page.meta_keywords|escape}></textarea>
        </div>
        <div class="xpages-field">
            <label><{$label_meta_desc}></label>
            <textarea name="meta_desc" rows="3"><{$page.meta_desc|escape}></textarea>
            <small class="xpf-desc"><{$meta_desc_help}></small>
        </div>
        <div class="xp-row">
            <div class="xpages-field">
                <label><input type="checkbox" name="noindex" value="1" <{if $page.noindex}>checked="checked"<{/if}>> <{$label_noindex}></label>
            </div>
            <div class="xpages-field">
                <label><input type="checkbox" name="nofollow" value="1" <{if $page.nofollow}>checked="checked"<{/if}>> <{$label_nofollow}></label>
            </div>
        </div>
        <div class="xpages-field">
            <label><{$label_redirect_url}></label>
            <input type="url" name="redirect_url" value="<{$page.redirect_url|escape}>" placeholder="https://">
            <small class="xpf-desc"><{$redirect_help}></small>
        </div>
    </div>

    <{* ── TAB: Gelişmiş ── *}>
    <div id="tab-adv" class="xp-tab-pane">
        <{if $can_use_advanced_code}>
            <div role="alert" class="xp-adv-warning">
                <strong>⚠ </strong><{$label_advanced_code_warning nofilter}>
            </div>
            <div class="xpages-field">
                <label><{$label_header_code}></label>
                <textarea name="header_code" rows="5" class="xp-code-textarea"><{$page.header_code|escape}></textarea>
                <small class="xpf-desc"><{$header_code_help}></small>
            </div>
            <div class="xpages-field">
                <label><{$label_footer_code}></label>
                <textarea name="footer_code" rows="5" class="xp-code-textarea"><{$page.footer_code|escape}></textarea>
                <small class="xpf-desc"><{$footer_code_help}></small>
            </div>
        <{else}>
            <div class="xpages-field">
                <label><{$label_header_code}></label>
                <div class="xp-alert xp-alert--muted">
                    <{$label_advanced_code_restricted}>
                </div>
            </div>
        <{/if}>
        <{if $page_id}>
            <div class="xpages-field">
                <label><{$label_manage_fields}></label>
                <a href="fields.php?page_id=<{$page_id}>" class="xp-btn xp-btn--primary"><{$label_menu_fields}></a>
            </div>
            <div class="xpages-field">
                <label><{$label_gallery}></label>
                <a href="gallery.php?page_id=<{$page_id}>" class="xp-btn xp-btn--warning"><{$label_gallery_manage}></a>
                <small class="xpf-desc"><{$gallery_manage_help}></small>
            </div>
        <{else}>
            <div class="xpages-field">
                <label><{$label_gallery}></label>
                <button type="button" class="xp-btn" disabled><{$label_gallery_save_first}></button>
                <small class="xpf-desc"><{$gallery_save_first_help}></small>
            </div>
        <{/if}>
    </div>

    <{* ── TAB: İlave Alanlar ── *}>
    <{if $has_extra_fields}>
        <div id="tab-extra" class="xp-tab-pane">
            <{$extra_fields_html nofilter}>
        </div>
    <{/if}>

    <br>
    <input type="submit" value="<{$label_save|escape}>" class="formButton">
    <a href="pages.php" class="xp-cancel-link"><{$label_cancel}></a>
</form>

<script>
function xpShowTab(el, id) {
    document.querySelectorAll('.xp-tab-pane').forEach(function (p) { p.classList.remove('active'); });
    document.querySelectorAll('#xpTabList li').forEach(function (l) { l.classList.remove('active'); });
    document.getElementById(id).classList.add('active');
    el.parentElement.classList.add('active');
}
</script>
