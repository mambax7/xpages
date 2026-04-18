<{* xPages — Field add/edit form + the xpfToggleOpts JS that hides/shows
    rows based on the selected field_type. Rendered by admin/fields.php
    for op=add|edit.

    Expects (controller-supplied):
      $form_title, $label_save, $label_cancel
      $page_id, $field_id
      $field                — flat descriptor {name, label, type, options,
                              default, desc, order, status, required,
                              show_in_tpl, is_file}
      $type_options         — list of {value, label, selected} dicts
      $current_file         — null OR {url, filename, is_image}
      $label_*              — translated label text (NAME, LABEL, TYPE,
                              OPTIONS, DEFAULT, DESC, ORDER, STATUS,
                              REQUIRED, SHOW_IN_TPL, FILE_CURRENT,
                              FILE_REPLACE_HINT, ACTIVE, INACTIVE)
      $help_*               — translated help text
      $options_hint_*       — translated options-area hint lines
      $sample_placeholder, $sample_code
      $file_help_js, $default_help_js  — JSON-encoded strings for the
                              toggle() script (already json_encode'd by
                              the controller)
      $xoops_token_html     — auto-injected *}>

<div class="xp-form-card">
    <h3><{$form_title}></h3>
    <form method="post" action="fields.php" enctype="multipart/form-data">
        <input type="hidden" name="op"       value="save">
        <input type="hidden" name="page_id"  value="<{$page_id}>">
        <input type="hidden" name="field_id" value="<{$field_id}>">
        <{$xoops_token_html nofilter}>

        <table class="xpf-form-table">
            <tr>
                <td>
                    <label><{$label_name}> *</label>
                    <span class="xpf-desc"><{$help_name}></span>
                </td>
                <td><input type="text" name="field_name" value="<{$field.name|escape}>" pattern="[a-z0-9_]+" required></td>
            </tr>
            <tr>
                <td><label><{$label_label}> *</label></td>
                <td><input type="text" name="field_label" value="<{$field.label|escape}>" required></td>
            </tr>
            <tr>
                <td><label><{$label_type}></label></td>
                <td>
                    <select name="field_type" id="xpfTypeSel">
                        <{foreach item=opt from=$type_options}>
                            <option value="<{$opt.value|escape}>" <{if $opt.selected}>selected="selected"<{/if}>><{$opt.label|escape}></option>
                        <{/foreach}>
                    </select>
                </td>
            </tr>
            <tr id="xpfOptsRow">
                <td>
                    <label><{$label_options}></label>
                    <span class="xpf-desc"><{$help_options}></span>
                </td>
                <td>
                    <textarea name="field_options" id="xpfOptionsInput" rows="5" placeholder="<{$sample_placeholder|escape}>"><{$field.options|escape}></textarea>
                    <div class="xpf-options-help">
                        <{$options_hint_title}><br>
                        <{$options_hint_body}><br>
                        <{$options_hint_example}><br>
                        <code><{$sample_code|escape}></code>
                    </div>
                </td>
            </tr>
            <tr id="xpfDefaultRow">
                <td>
                    <label><{$label_default}></label>
                    <span class="xpf-desc" id="xpfDefaultDesc"><{$help_default}></span>
                </td>
                <td id="xpfDefaultCell">
                    <input type="text" name="field_default" id="xpfDefaultInput" value="<{$field.default|escape}>"<{if $field.is_file}> class="xp-hidden"<{/if}>>
                    <div id="xpfFileArea"<{if !$field.is_file}> class="xp-hidden"<{/if}>>
                        <input type="file" name="field_file" accept="image/*,application/pdf,.doc,.docx,.zip">
                        <{if $current_file}>
                            <div class="xp-margin-top-8">
                                <strong><{$label_file_current}></strong>
                                <{if $current_file.is_image}>
                                    <img src="<{$current_file.url|escape}>" class="xpf-preview" alt="Preview">
                                <{else}>
                                    <a href="<{$current_file.url|escape}>" target="_blank" rel="noopener"><{$current_file.filename|escape}></a>
                                <{/if}>
                                <br><small class="xp-text-muted"><{$help_file_replace}></small>
                            </div>
                        <{/if}>
                    </div>
                </td>
            </tr>
            <tr>
                <td><label><{$label_desc}></label></td>
                <td><input type="text" name="field_desc" value="<{$field.desc|escape}>"></td>
            </tr>
            <tr>
                <td><label><{$label_order}></label></td>
                <td><input type="number" name="field_order" value="<{$field.order}>" min="0" class="xp-input-small"></td>
            </tr>
            <tr>
                <td><label><{$label_status}></label></td>
                <td>
                    <select name="field_status">
                        <option value="1" <{if $field.status}>selected="selected"<{/if}>><{$label_active}></option>
                        <option value="0" <{if !$field.status}>selected="selected"<{/if}>><{$label_inactive}></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label><{$label_required}></label></td>
                <td><input type="checkbox" name="field_required" value="1" <{if $field.required}>checked="checked"<{/if}>></td>
            </tr>
            <tr>
                <td><label><{$label_show_in_tpl}></label></td>
                <td><input type="checkbox" name="show_in_tpl" value="1" <{if $field.show_in_tpl}>checked="checked"<{/if}>></td>
            </tr>
        </table>

        <br>
        <button type="submit" class="xp-btn xp-btn--add"><{$label_save}></button>
        <a href="fields.php?page_id=<{$page_id}>" class="xp-cancel-link"><{$label_cancel}></a>
    </form>
</div>

<script>
(function () {
    var typeSel      = document.getElementById('xpfTypeSel');
    var optsRow      = document.getElementById('xpfOptsRow');
    var defaultInput = document.getElementById('xpfDefaultInput');
    var fileArea     = document.getElementById('xpfFileArea');
    var defaultDesc  = document.getElementById('xpfDefaultDesc');
    var optionsInput = document.getElementById('xpfOptionsInput');

    // Language strings pre-encoded to JSON literals by the controller
    // (json_encode with JSON_HEX_* flags — safe to drop into JS as-is).
    var fileHelp    = <{$file_help_js nofilter}>;
    var defaultHelp = <{$default_help_js nofilter}>;

    function toggle() {
        var t = typeSel.value;

        if (t === 'select' || t === 'radio') {
            optsRow.classList.remove('xp-hidden');
            if (optionsInput) optionsInput.required = true;
        } else {
            optsRow.classList.add('xp-hidden');
            if (optionsInput) optionsInput.required = false;
        }

        if (t === 'file') {
            if (defaultInput) defaultInput.classList.add('xp-hidden');
            if (fileArea)     fileArea.classList.remove('xp-hidden');
            if (defaultDesc)  defaultDesc.textContent = fileHelp;
        } else {
            if (defaultInput) defaultInput.classList.remove('xp-hidden');
            if (fileArea)     fileArea.classList.add('xp-hidden');
            if (defaultDesc)  defaultDesc.textContent = defaultHelp;
        }
    }

    if (typeSel) {
        typeSel.addEventListener('change', toggle);
        toggle();
    }
})();
</script>
