<{* xPages — Gallery image add / edit form.
    Controller-supplied data:
      $form_title       — "Add image" / "Edit image"
      $page_id, $gallery_id
      $gallery          — flat descriptor {title, description, image_url,
                          image_order, image_status}
      $current_image    — null OR {url} (preview of existing uploaded file)
      $label_*          — translated form labels
      $help_*           — translated help text
      $xoops_token_html — auto-injected by xpages_admin_render() *}>

<div class="xp-form-card">
    <h3><{$form_title}></h3>
    <form method="post" action="gallery.php" enctype="multipart/form-data">
        <input type="hidden" name="op"         value="save">
        <input type="hidden" name="page_id"    value="<{$page_id}>">
        <input type="hidden" name="gallery_id" value="<{$gallery_id}>">
        <{$xoops_token_html nofilter}>

        <table class="xpf-form-table">
            <tr>
                <td><label><{$label_title}> *</label></td>
                <td><input type="text" name="title" value="<{$gallery.title|escape}>" required></td>
            </tr>
            <tr>
                <td><label><{$label_desc}></label></td>
                <td><textarea name="description" rows="3"><{$gallery.description|escape}></textarea></td>
            </tr>
            <tr>
                <td><label><{$label_file}></label></td>
                <td>
                    <input type="file" name="image_file" accept="image/*">
                    <small class="xpf-desc"><{$help_file}></small>
                    <{if $current_image}>
                        <br><img src="<{$current_image.url|escape}>" class="image-preview" alt="">
                        <br><small><{$label_current_img}></small>
                    <{/if}>
                </td>
            </tr>
            <tr>
                <td><label><{$label_url}></label></td>
                <td>
                    <input type="url" name="image_url" value="<{$gallery.image_url|escape}>" placeholder="https://...">
                    <small class="xpf-desc"><{$help_url}></small>
                </td>
            </tr>
            <tr>
                <td><label><{$label_order}></label></td>
                <td><input type="number" name="image_order" value="<{$gallery.image_order}>" min="0" class="xp-input-small"></td>
            </tr>
            <tr>
                <td><label><{$label_status}></label></td>
                <td>
                    <select name="image_status">
                        <option value="1" <{if $gallery.image_status}>selected="selected"<{/if}>><{$label_active}></option>
                        <option value="0" <{if !$gallery.image_status}>selected="selected"<{/if}>><{$label_inactive}></option>
                    </select>
                </td>
            </tr>
        </table>
        <br>
        <button type="submit" class="xp-btn xp-btn--add"><{$label_save}></button>
        <a href="gallery.php?page_id=<{$page_id}>" class="xp-cancel-link"><{$label_cancel}></a>
    </form>
</div>
