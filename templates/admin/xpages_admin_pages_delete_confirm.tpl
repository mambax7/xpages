<{* xPages — Delete confirmation for a single page.
    Rendered by admin/pages.php when the admin clicks a delete link
    but hasn't yet POST-submitted the confirmation. Expects:
      $confirm_message  — prose, may include HTML (the page title is
                          pre-escaped by the controller via sprintf)
      $page_id          — int
      $label_yes        — translated "Yes" button text
      $label_no         — translated "No" button text
      $xoops_token_html — auto-injected by xpages_admin_render() *}>

<div class="xp-alert xp-alert--warning">
    <p>⚠️ <{$confirm_message nofilter}></p>
    <form method="post" action="pages.php?op=delete&page_id=<{$page_id}>" class="xp-confirm-actions">
        <input type="hidden" name="op"       value="delete">
        <input type="hidden" name="page_id"  value="<{$page_id}>">
        <input type="hidden" name="confirm"  value="1">
        <{$xoops_token_html nofilter}>
        <button type="submit" class="xp-btn xp-btn--danger"><{$label_yes}></button>
        <a href="pages.php" class="xp-btn xp-btn--cancel"><{$label_no}></a>
    </form>
</div>
