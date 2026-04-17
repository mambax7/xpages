<{* xPages — Sayfa Menüsü Bloğu *}>
<{if $block.pages}>
<ul class="xpages-block-menu">
<{foreach item=p from=$block.pages}>
    <li class="xpbm-item<{if $p.children}> xpbm-has-children<{/if}>">
        <a href="<{$p.page_url}>"><{$p.title}></a>
        <{if $p.children}>
        <ul class="xpbm-sub">
            <{foreach item=c from=$p.children}>
            <li><a href="<{$c.page_url}>"><{$c.title}></a></li>
            <{/foreach}>
        </ul>
        <{/if}>
    </li>
<{/foreach}>
</ul>
<{/if}>
