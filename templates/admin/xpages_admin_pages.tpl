<{* xPages — Admin "Pages" listing (toolbar + table + empty state).
    Data assembled by admin/pages.php. Expects:
      $menu_title         — translated page-list heading
      $add_label          — "Add page" button copy
      $col_*              — translated column headers
      $pages              — list of row descriptors (see controller)
      $stat_text          — formatted "N pages" footer
      $no_pages_text      — empty-state headline
      $create_first_label — empty-state CTA text
      $toggle_title       — title attr for the status toggle button
      $label_edit / view / delete — translated action link text
      $xoops_token_html   — auto-injected by xpages_admin_render() *}>

<div class="xp-toolbar">
    <h2>📄 <{$menu_title}></h2>
    <a href="page_edit.php" class="xp-btn xp-btn--add">➕ <{$add_label}></a>
</div>

<{if $pages|@count > 0}>
    <div class="xp-table-wrap">
        <table class="xp-table">
            <thead><tr>
                <th>ID</th>
                <th><{$col_title}></th>
                <th>URL Alias</th>
                <th class="xp-cell-center"><{$col_status}></th>
                <th class="xp-cell-center">Sort Order</th>
                <th class="xp-cell-center"><{$col_actions}></th>
            </tr></thead>
            <tbody>
                <{foreach item=row from=$pages}>
                    <tr>
                        <td><{$row.id}></td>
                        <td><strong><{$row.title|escape}></strong></td>
                        <td><code class="xp-alias-code"><{$row.alias|escape}></code></td>
                        <td class="xp-cell-center">
                            <form method="post" action="pages.php">
                                <input type="hidden" name="op"      value="toggle">
                                <input type="hidden" name="page_id" value="<{$row.id}>">
                                <{$xoops_token_html nofilter}>
                                <button type="submit" title="<{$toggle_title|escape}>" class="xp-btn--unstyled">
                                    <{if $row.status}>✅ Aktif<{else}>❌ Pasif<{/if}>
                                </button>
                            </form>
                        </td>
                        <td class="xp-cell-center"><{$row.menu_order}></td>
                        <td class="xp-cell-center">
                            <div class="xp-actions">
                                <a href="page_edit.php?page_id=<{$row.id}>" class="xp-action--edit" title="<{$label_edit|escape}>">✏️ <{$label_edit}></a>
                                <a href="<{$row.page_url|escape}>" target="_blank" class="xp-action--view" title="<{$label_view|escape}>">👁️ <{$label_view}></a>
                                <a href="pages.php?op=delete&page_id=<{$row.id}>" class="xp-action--delete" title="<{$label_delete|escape}>">🗑️ <{$label_delete}></a>
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
<{else}>
    <div class="xp-empty">
        <div class="xp-empty-icon">📭</div>
        <div class="xp-empty-text"><{$no_pages_text}></div>
        <a href="page_edit.php" class="xp-empty-cta"><{$create_first_label}></a>
    </div>
<{/if}>
