<{* xPages — Extra-fields listing + empty state + stats footer.
    Rendered by admin/fields.php. Expects:
      $page_id
      $fields            — list of row descriptors (see controller)
      $stat_text
      $col_*             — column header labels
      $label_edit, $label_delete, $label_add
      $no_fields_text    — empty-state headline
      $upload_url        — module's public upload URL (/xpages/) *}>

<p><a href="fields.php?op=add&page_id=<{$page_id}>" class="xp-btn xp-btn--add">➕ <{$label_add}></a></p>

<{if $fields|@count == 0}>
    <div class="xp-empty">
        <div class="xp-empty-icon">⚙️</div>
        <div class="xp-empty-text"><{$no_fields_text}></div>
        <a href="fields.php?op=add&page_id=<{$page_id}>" class="xp-empty-cta xp-empty-cta--blue">+ <{$label_add}></a>
    </div>
<{else}>
    <div class="xp-table-wrap">
        <table class="xp-table">
            <thead><tr>
                <th>ID</th>
                <th><{$col_name}></th>
                <th><{$col_label}></th>
                <th><{$col_type}></th>
                <th><{$col_order}></th>
                <th><{$col_status}></th>
                <th><{$col_actions}></th>
            </tr></thead>
            <tbody>
                <{foreach item=row from=$fields}>
                    <tr>
                        <td><{$row.id}></td>
                        <td>
                            <code class="xp-alias-code"><{$row.name|escape}></code>
                            <{if $row.is_global}> <small class="xp-scope-label">(global)</small><{/if}>
                        </td>
                        <td>
                            <strong><{$row.label|escape}></strong>
                            <{if $row.file_thumb}>
                                <br><img src="<{$row.file_thumb|escape}>" class="xp-thumb-sm" alt="">
                            <{elseif $row.file_url}>
                                <br><small><a href="<{$row.file_url|escape}>" target="_blank" rel="noopener" class="xp-text-small"><{$label_file_view}></a></small>
                            <{/if}>
                        </td>
                        <td><{$row.type_label|escape}></td>
                        <td class="xp-cell-center"><{$row.order}></td>
                        <td class="xp-cell-center"><{if $row.status}>✅<{else}>❌<{/if}></td>
                        <td>
                            <div class="xp-actions">
                                <a href="fields.php?op=edit&field_id=<{$row.id}>&page_id=<{$page_id}>" class="xp-action--edit">✏️ <{$label_edit}></a>
                                <a href="fields.php?op=delete&field_id=<{$row.id}>&page_id=<{$page_id}>" class="xp-action--delete">🗑️ <{$label_delete}></a>
                            </div>
                        </td>
                    </tr>
                <{/foreach}>
            </tbody>
        </table>
    </div>

    <div class="xp-alert xp-alert--info">
        📊 <{$stat_text}>
    </div>
<{/if}>
