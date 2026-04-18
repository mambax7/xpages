<{* LightGallery CSS ve JS *}>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.0/css/lightgallery.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.0/css/lg-zoom.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.0/css/lg-thumbnail.min.css">
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.0/lightgallery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.0/plugins/zoom/lg-zoom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.0/plugins/thumbnail/lg-thumbnail.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lightGallery(document.getElementById('xpages-lightgallery'), {
        selector: '.gallery-item',
        plugins: [lgZoom, lgThumbnail],
        speed: 500,
        showThumbByDefault: true,
        zoom: true,
        actualSize: true,
        hash: false,
        download: true,
        counter: true,
        controls: true,
        loop: true,
        mousewheel: true,
        escKey: true,
        thumbnail: true,
        thumbWidth: 80,
        thumbHeight: 80,
        thumbMargin: 5
    });
});
</script>

<{* xPages — Tekil Sayfa Şablonu (DÜZELTİLMİŞ)
    LightGallery / gallery-card CSS is served from assets/css/style.css —
    registered by xpages_register_public_css() in page.php. *}>

<{* Sayfa başı özel kod enjeksiyonu *}>
<{if $xpages_header_code|default:''}>
<{$xpages_header_code nofilter}>
<{/if}>

<div id="xpages-page" class="xpages-page-wrap">

    <{* Breadcrumb *}>
    <{if $xpages_show_breadcrumb|default:false}>
    <nav class="xpages-breadcrumb" aria-label="breadcrumb">
        <a href="<{$xoops_url}>"><{$smarty.const._MD_XPAGES_HOME}></a>
        &rsaquo;
        <a href="<{$xpages_module_url}>"><{$smarty.const._MD_XPAGES_PAGES}></a>
        &rsaquo;
        <span><{$xpages_page.title}></span>
    </nav>
    <{/if}>

    <article class="xpages-article">
        <header class="xpages-article-header">
            <h1 class="xpages-title"><{$xpages_page.title}></h1>
            <{if ($xpages_show_lastmod|default:false) && $xpages_page.update_date}>
            <div class="xpages-meta">
                <{$smarty.const._MD_XPAGES_LAST_UPDATED}>
                <time datetime="<{$xpages_page.update_date|date_format:"%Y-%m-%d"}>">
                    <{$xpages_page.update_date|date_format:"%d.%m.%Y"}>
                </time>
                &nbsp;·&nbsp;
                <{$smarty.const._MD_XPAGES_HITS}> <{$xpages_page.hits}>
            </div>
            <{/if}>
        </header>

        <{* Kısa açıklama (varsa) *}>
        <{if $xpages_page.short_desc|default:''}>
        <div class="xpages-short-desc">
            <{$xpages_page.short_desc}>
        </div>
        <{/if}>

        <{* Ana içerik *}>
        <div class="xpages-body">
            <{$xpages_page.body nofilter}>
        </div>

		<{* ── İlave Alanlar ──────────────────────────────────────────────────── *}>
		<{if $xpages_page.extra_fields|default:[]}>
		<section class="xpages-extra-fields">
			<h3><{$smarty.const._MD_XPAGES_EXTRA_FIELDS}></h3>
			<{foreach key=fid item=f from=$xpages_page.extra_fields_by_id}>
				<{if $f.show_in_tpl && $f.value != ''}>
					<{include file=$xpages_field_value_partial f=$f fid=$fid}>
				<{/if}>
			<{/foreach}>
		</section>
		<{/if}>
		
		
<{* ── Galeri Bölümü (LightGallery ile) ──────────────────────────────────────── *}>
<{if $xpages_gallery|default:[]}>
<section class="xpages-gallery">
    <div id="xpages-lightgallery" class="xpages-public-gallery">
        <{foreach item=g from=$xpages_gallery key=index}>
        <a href="<{$g.image_url}>" class="gallery-item" data-src="<{$g.image_url}>" data-sub-html="<h4><{$g.title|escape:'html'}></h4><p><{$g.description|escape:'html'}></p>">
            <div class="xpages-public-gallery-card">
                <img src="<{$g.image_url}>" alt="<{$g.title|escape:'html'}>" loading="lazy" class="xpages-public-gallery-img">
                <div class="xpages-public-gallery-body">
                    <h4><{$g.title}></h4>
                    <p><{$g.description}></p>
                </div>
            </div>
        </a>
        <{/foreach}>
    </div>
</section>
<{/if}>

    </article>

    <{* XOOPS yorumlar (etkinleştirildiyse) *}>
    <{if $xpages_show_comments|default:false}>
    <section class="xpages-comments">
        <{include file="db:system_comments.tpl"}>
    </section>
    <{/if}>

</div><!-- #xpages-page -->

<{* Sayfa sonu özel kod enjeksiyonu *}>
<{if $xpages_footer_code|default:''}>
<{$xpages_footer_code nofilter}>
<{/if}>
