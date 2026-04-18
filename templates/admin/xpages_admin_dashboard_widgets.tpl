<{* xPages — Legacy dashboard widget grid (dashboard_widgets.php).
    Rendered by xpages_dashboard_widgets(). Six-widget layout:
    stats / recent / popular / monthly chart / quick actions / sysinfo.

    Controller-supplied data:
      $stats_widget     — {title, cards: [{value, label, modifier}]}
      $recent_widget    — {title, items, empty_text, see_all_label}
      $popular_widget   — {title, items, empty_text}
      $monthly_widget   — {title, bars: [{month, count, percent}]}
      $quick_widget     — {title, actions: [{href, label, modifier}]}
      $sysinfo_widget   — {title, rows: [{label, value}]} *}>

<div class="xpages-widget-grid">

    <div class="xpages-widget">
        <div class="xpages-widget-header"><{$stats_widget.title}></div>
        <div class="xpages-widget-content">
            <div class="xpages-stat-cards">
                <{foreach item=card from=$stats_widget.cards}>
                    <div class="xpages-stat-card <{$card.modifier}>">
                        <h3><{$card.value}></h3>
                        <p><{$card.label}></p>
                    </div>
                <{/foreach}>
            </div>
        </div>
    </div>

    <div class="xpages-widget">
        <div class="xpages-widget-header"><{$recent_widget.title}></div>
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
                    <a href="pages.php"><{$recent_widget.see_all_label}></a>
                </div>
            <{/if}>
        </div>
    </div>

    <div class="xpages-widget">
        <div class="xpages-widget-header"><{$popular_widget.title}></div>
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

    <div class="xpages-widget">
        <div class="xpages-widget-header"><{$monthly_widget.title}></div>
        <div class="xpages-widget-content">
            <div class="xpages-chart">
                <{foreach item=bar from=$monthly_widget.bars}>
                    <div class="xpages-chart-bar">
                        <div class="xpages-chart-bar-label"><{$bar.month}></div>
                        <div class="xpages-chart-bar-fill" style="width:<{$bar.percent}>%"><{$bar.count}></div>
                    </div>
                <{/foreach}>
            </div>
        </div>
    </div>

    <div class="xpages-widget">
        <div class="xpages-widget-header"><{$quick_widget.title}></div>
        <div class="xpages-widget-content">
            <div class="xpages-quick-column">
                <{foreach item=action from=$quick_widget.actions}>
                    <a href="<{$action.href}>" class="xp-btn xp-btn--block <{$action.modifier}>"><{$action.label}></a>
                <{/foreach}>
            </div>
        </div>
    </div>

    <div class="xpages-widget">
        <div class="xpages-widget-header"><{$sysinfo_widget.title}></div>
        <div class="xpages-widget-content">
            <ul class="xpages-sysinfo-list">
                <{foreach item=row from=$sysinfo_widget.rows}>
                    <li><strong><{$row.label}></strong> <{$row.value|escape}></li>
                <{/foreach}>
            </ul>
        </div>
    </div>

</div>
