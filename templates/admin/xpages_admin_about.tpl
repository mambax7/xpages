<{* xPages — Admin "About" page layout.
    Data assembled by admin/about.php and passed as $info_rows, $features,
    $code_example, $support_links, $footer_text, plus a handful of label
    constants. All styling lives in assets/css/admin.css (.xpages-* classes). *}>

<div class="xpages-page-header">
    <h2><{$about_title}></h2>
    <p><{$about_desc}></p>
</div>

<div class="xpages-info-grid">

    <div class="xpages-info-card">
        <h3><{$module_info_title}></h3>
        <table class="xpages-info-table">
            <{foreach item=row from=$info_rows}>
                <tr>
                    <td><{$row.label}></td>
                    <td><{$row.value nofilter}></td>
                </tr>
            <{/foreach}>
        </table>
    </div>

    <div class="xpages-info-card">
        <h3><{$features_title}></h3>
        <ul class="xpages-info-list">
            <{foreach item=feature from=$features}>
                <li><{$feature}></li>
            <{/foreach}>
        </ul>
    </div>
</div>

<div class="xpages-info-card xpages-info-card--wide xpages-info-card--spaced">
    <h3><{$template_title}></h3>
    <pre class="xpages-code-example"><{$code_example|escape}></pre>
</div>

<div class="xpages-support-callout">
    <h3><{$support_title}></h3>
    <ul class="xpages-info-list">
        <{foreach item=link from=$support_links}>
            <li><{$link.icon}> <{$link.label}>:
                <a href="<{$link.href|escape}>" <{if $link.new_window}>target="_blank" rel="noopener"<{/if}>>
                    <{$link.text|escape}>
                </a>
            </li>
        <{/foreach}>
    </ul>
</div>

<div class="xpages-page-footer">
    <{$footer_text nofilter}>
</div>
