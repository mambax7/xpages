<{* xPages — Gallery image grid + empty state.
    Rendered by admin/gallery.php. Expects:
      $page_id
      $items             — list of gallery card descriptors (see controller)
      $label_add         — "Add image" button text
      $label_empty_text  — empty-state headline
      $label_add_first   — empty-state CTA text *}>

<p><a href="gallery.php?op=add&page_id=<{$page_id}>" class="xp-btn xp-btn--add"><{$label_add}></a></p>

<{if $items|@count == 0}>
    <div class="xp-empty">
        <div class="xp-empty-icon">🖼️</div>
        <div class="xp-empty-text"><{$label_empty_text}></div>
        <a href="gallery.php?op=add&page_id=<{$page_id}>" class="xp-empty-cta xp-empty-cta--blue"><{$label_add_first}></a>
    </div>
<{else}>
    <div class="xpages-gallery-grid">
        <{foreach item=item from=$items}>
            <div class="xpages-gallery-card">
                <div class="xpages-gallery-card-img">
                    <{if $item.image_url}>
                        <img src="<{$item.image_url|escape}>" alt="">
                    <{else}>
                        <div class="xpages-gallery-card-img-placeholder">🖼️</div>
                    <{/if}>
                </div>
                <div class="xpages-gallery-card-body">
                    <h4><{$item.title|escape}></h4>
                    <p class="xpages-gallery-card-desc"><{$item.description|escape}></p>
                    <div class="xpages-gallery-card-footer">
                        <small><{$item.status_label nofilter}></small>
                        <div class="xp-actions">
                            <a href="gallery.php?op=edit&gallery_id=<{$item.id}>&page_id=<{$page_id}>" class="xp-action--edit">✏️</a>
                            <a href="gallery.php?op=delete&gallery_id=<{$item.id}>&page_id=<{$page_id}>" class="xp-action--delete">🗑️</a>
                        </div>
                    </div>
                </div>
            </div>
        <{/foreach}>
    </div>
<{/if}>
