<{* xPages — Admin dashboard (index.php).
    Controller-supplied data:
      $dashboard_title, $dashboard_subtitle
      $stat_cards       — list of {value, label, modifier}
      $recent_widget    — {title, items, empty_text, see_all_label, see_all_url}
      $popular_widget   — {title, items, empty_text}  (items have badge_html)
      $monthly_widget   — {title, bars}  (bars have month, count, percent)
      $quick_widget     — {title, actions: [{href, label, modifier}]}
      $sysinfo_widget   — {title, rows: [{label, value}]} *}>

<div class="xpages-page-header">
    <h2>📄 <{$dashboard_title}></h2>
    <p><{$dashboard_subtitle}></p>
</div>

<div class="xpages-stat-grid">
    <{foreach item=card from=$stat_cards}>
        <div class="xpages-stat-card xpages-stat-card--padded <{$card.modifier}>">
            <div class="xpages-stat-label"><{$card.label}></div>
            <div class="xpages-stat-value"><{$card.value}></div>
        </div>
    <{/foreach}>
</div>

<div class="xpages-widget-grid--400">
    <div class="xpages-widget">
        <div class="xpages-widget-header xpages-widget-header--purple"><{$recent_widget.title}></div>
        <div class="xpages-widget-content">
            <{if $recent_widget.items|@count == 0}>
                <p class="xpages-empty-muted"><{$recent_widget.empty_text}></p>
            <{else}>
                <ul class="xpages-list">
                    <{foreach item=row from=$recent_widget.items}>
                        <li>
                            <a href="page_edit.php?page_id=<{$row.id}>"><{$row.title|escape}></a>
                            <span class="xpages-badge"><{$row.date}></span>
                        </li>
                    <{/foreach}>
                </ul>
                <div class="xpages-see-all">
                    <a href="<{$recent_widget.see_all_url}>"><{$recent_widget.see_all_label}></a>
                </div>
            <{/if}>
        </div>
    </div>

    <div class="xpages-widget">
        <div class="xpages-widget-header xpages-widget-header--orange"><{$popular_widget.title}></div>
        <div class="xpages-widget-content">
            <{if $popular_widget.items|@count == 0}>
                <p class="xpages-empty-muted"><{$popular_widget.empty_text}></p>
            <{else}>
                <ul class="xpages-list">
                    <{foreach item=row from=$popular_widget.items}>
                        <li>
                            <a href="page_edit.php?page_id=<{$row.id}>"><{$row.title|escape}></a>
                            <span class="xpages-badge">👁️ <{$row.hits_formatted}></span>
                        </li>
                    <{/foreach}>
                </ul>
            <{/if}>
        </div>
    </div>
</div>

<div class="xpages-widget xpages-info-card--spaced">
    <div class="xpages-widget-header xpages-widget-header--blue"><{$monthly_widget.title}></div>
    <div class="xpages-widget-content--wide">
        <{foreach item=bar from=$monthly_widget.bars}>
            <div class="xpages-chart-bar">
                <div class="xpages-chart-bar-label xpages-chart-bar-label--wide"><{$bar.month}></div>
                <div class="xpages-chart-bar-track">
                    <div class="xpages-chart-bar-fill xpages-chart-bar-fill--blue" style="width:<{$bar.percent}>%"><{$bar.count}></div>
                </div>
            </div>
        <{/foreach}>
    </div>
</div>

<div class="xpages-widget xpages-info-card--spaced">
    <div class="xpages-widget-header xpages-widget-header--green"><{$quick_widget.title}></div>
    <div class="xpages-quick-actions">
        <{foreach item=action from=$quick_widget.actions}>
            <a href="<{$action.href}>" class="xp-btn xp-btn--wide <{$action.modifier}>"><{$action.label}></a>
        <{/foreach}>
    </div>
</div>

<div class="xpages-widget">
    <div class="xpages-widget-header xpages-widget-header--gray"><{$sysinfo_widget.title}></div>
    <div class="xpages-widget-content--wide">
        <ul class="xpages-sysinfo-list">
            <{foreach item=row from=$sysinfo_widget.rows}>
                <li><strong><{$row.label}></strong> <{$row.value|escape}></li>
            <{/foreach}>
        </ul>
    </div>
</div>
