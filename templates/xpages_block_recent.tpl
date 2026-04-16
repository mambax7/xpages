<{* xPages — Son Sayfalar Bloğu *}>
<{if $block.pages}>
<ul class="xpages-block-recent">
<{foreach item=p from=$block.pages}>
    <li>
        <a href="<{$p.page_url}>"><{$p.title}></a>
        <{if $block.show_desc && $p.short_desc}>
        <span class="xpb-desc"><{$p.short_desc|truncate:80}></span>
        <{/if}>
    </li>
<{/foreach}>
</ul>
<{/if}>
