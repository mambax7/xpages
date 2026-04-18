<{* xPages — Gallery admin header (toolbar + back-to-page link).
    Rendered above list/form/delete templates by admin/gallery.php.
    Expects: $gallery_title, $page_title_display, $page_id (0 when no
    scoping page), $label_back_to_page. *}>

<div class="xp-toolbar xp-toolbar--inline">
    <h2><{$gallery_title}></h2>
    <span class="xp-text-muted">— <{$page_title_display}></span>
</div>

<{if $page_id}>
    <p><a href="page_edit.php?page_id=<{$page_id}>" class="xp-action--edit">◀ <{$label_back_to_page}></a></p>
<{/if}>
