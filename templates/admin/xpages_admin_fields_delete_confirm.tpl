<{* xPages — Field delete confirmation.
    Rendered by admin/fields.php on GET of op=delete before the
    confirmation form is POST-submitted. Expects:
      $confirm_message  — already-escaped prose with the field label
      $field_id, $page_id
      $label_yes, $label_no
      $xoops_token_html — auto-injected by xpages_admin_render() *}>

<div class="xp-alert xp-alert--warning">
    <p>⚠️ <{$confirm_message nofilter}></p>
    <form method="post" action="fields.php?op=delete&field_id=<{$field_id}>&page_id=<{$page_id}>" class="xp-confirm-actions">
        <input type="hidden" name="op"       value="delete">
        <input type="hidden" name="field_id" value="<{$field_id}>">
        <input type="hidden" name="page_id"  value="<{$page_id}>">
        <input type="hidden" name="confirm"  value="1">
        <{$xoops_token_html nofilter}>
        <button type="submit" class="xp-btn xp-btn--danger"><{$label_yes}></button>
        <a href="fields.php?page_id=<{$page_id}>" class="xp-btn xp-btn--cancel"><{$label_no}></a>
    </form>
</div>
