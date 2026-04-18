<{* xPages — Gallery image delete confirmation.
    Rendered by admin/gallery.php when a GET-side delete link hits
    before the POST confirm. Expects:
      $confirm_message  — pre-formatted prose (image title escaped)
      $gallery_id, $page_id
      $label_yes, $label_no
      $xoops_token_html — auto-injected *}>

<div class="xp-alert xp-alert--warning">
    <p>⚠️ <{$confirm_message nofilter}></p>
    <form method="post" action="gallery.php?op=delete&gallery_id=<{$gallery_id}>&page_id=<{$page_id}>" class="xp-confirm-actions">
        <input type="hidden" name="op"         value="delete">
        <input type="hidden" name="gallery_id" value="<{$gallery_id}>">
        <input type="hidden" name="page_id"    value="<{$page_id}>">
        <input type="hidden" name="confirm"    value="1">
        <{$xoops_token_html nofilter}>
        <button type="submit" class="xp-btn xp-btn--danger"><{$label_yes}></button>
        <a href="gallery.php?page_id=<{$page_id}>" class="xp-btn xp-btn--cancel"><{$label_no}></a>
    </form>
</div>
