<{* xPages — Sayfa Listesi Şablonu *}>
<{* @package  xpages *}>
<{* @author   Eren Yumak — Aymak (aymak.net) *}>

<div id="xpages-index">

    <h2><{$smarty.const._MD_XPAGES_INDEX_TITLE}></h2>

    <{if $xpages_list}>
        <ul class="xpages-list">
        <{foreach item=p from=$xpages_list}>
            <li class="xpages-item">
                <h3><a href="<{$p.page_url}>"><{$p.title}></a></h3>
                <{if $p.short_desc}>
                    <p class="xpages-short-desc"><{$p.short_desc}></p>
                <{/if}>
                <div class="xpages-meta">
                    <{if $p.update_date}>
                        <span class="xpages-updated">
                            <{$smarty.const._MD_XPAGES_LAST_UPDATED}>
                            <{$p.update_date|date_format:"%d.%m.%Y"}>
                        </span>
                    <{/if}>
                    <span class="xpages-hits"><{$smarty.const._MD_XPAGES_HITS}> <{$p.hits}></span>
                </div>
                <a href="<{$p.page_url}>" class="xpages-readmore"><{$smarty.const._MD_XPAGES_READ_MORE}> →</a>
            </li>
        <{/foreach}>
        </ul>

        <{* Sayfalama *}>
        <{if $xpages_total > $xpages_limit}>
        <div class="xpages-pagination">
            <{if $xpages_start > 0}>
                <a href="<{$xpages_module_url}>?start=<{$xpages_start - $xpages_limit}>">
                    <{$smarty.const._MD_XPAGES_PREV}>
                </a>
            <{/if}>
            <{if ($xpages_start + $xpages_limit) < $xpages_total}>
                <a href="<{$xpages_module_url}>?start=<{$xpages_start + $xpages_limit}>">
                    <{$smarty.const._MD_XPAGES_NEXT}>
                </a>
            <{/if}>
        </div>
        <{/if}>

    <{else}>
        <p class="xpages-empty"><{$smarty.const._MD_XPAGES_NO_PAGES}></p>
    <{/if}>

</div>
