<{* xPages — Public display partial for one extra-field value.
    Counterpart to templates/admin/xpages_field_input.tpl (edit mode).

    Where the admin partial renders <input>/<select>/<textarea> FORM
    controls for a field, this partial renders the stored VALUE for
    public viewing — image <img>, download <a>, mailto, checkbox
    yes/no, textarea with line-breaks, etc.

    Receives (via <{include f=$f fid=$fid}>):
      $f   — descriptor from xpages_assign_page(): {field_id,
             field_name, field_label, field_type, value, show_in_tpl,
             file_ext}. `value` is already normalised per-type
             (safe-filename-rendered URL for file, filter_var'd email,
             xpages_normalize_url'd url).
      $fid — original field_id (for the wrapper div's DOM id).

    Caller is expected to have already guarded on $f.show_in_tpl and
    $f.value != ''.  *}>

<div class="xpf xpf-<{$f.field_type}>" id="xpf-<{$fid}>">
    <div class="xpf-label"><strong><{$f.field_label}>:</strong></div>
    <div class="xpf-value">
        <{if $f.field_type == 'file'}>
            <{assign var="fileExt" value=$f.file_ext|default:''|lower}>
            <{if $fileExt == 'jpg' || $fileExt == 'jpeg' || $fileExt == 'png' || $fileExt == 'gif' || $fileExt == 'webp'}>
                <img src="<{$f.value|escape:'html'}>" alt="<{$f.field_label}>" class="xpf-file-image">
            <{else}>
                <a href="<{$f.value|escape:'html'}>" target="_blank" rel="noopener">📎 <{$smarty.const._MD_XPAGES_DOWNLOAD_FILE}></a>
            <{/if}>

        <{elseif $f.field_type == 'url'}>
            <a href="<{$f.value|escape:'html'}>" target="_blank" rel="noopener"><{$f.value|escape:'html'}></a>

        <{elseif $f.field_type == 'email'}>
            <a href="mailto:<{$f.value|escape:'html'}>"><{$f.value|escape:'html'}></a>

        <{elseif $f.field_type == 'checkbox'}>
            <{if $f.value == 1 || $f.value == '1' || $f.value == 'on' || $f.value == 'yes'}>
                ✓ <{$smarty.const._MD_XPAGES_YES}>
            <{else}>
                ✗ <{$smarty.const._MD_XPAGES_NO}>
            <{/if}>

        <{elseif $f.field_type == 'textarea'}>
            <div class="xpf-textarea-val"><{$f.value|nl2br nofilter}></div>

        <{else}>
            <{* radio / select / text / number / tel fall through here *}>
            <span><{$f.value}></span>
        <{/if}>
    </div>
</div>
