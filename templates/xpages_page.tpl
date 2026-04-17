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

<style>
.lg-outer .lg-thumb-item {
    border-radius: 4px;
    border: 2px solid transparent;
}
.lg-outer .lg-thumb-item.active,
.lg-outer .lg-thumb-item:hover {
    border-color: #007bff;
}
.lg-toolbar .lg-icon {
    color: #fff;
}
.lg-sub-html {
    background: rgba(0,0,0,0.7);
    font-size: 14px;
}
</style>


<{* xPages — Tekil Sayfa Şablonu (DÜZELTİLMİŞ) *}>

<{* Sayfa başı özel kod enjeksiyonu *}>
<{if $xpages_header_code}>
<{$xpages_header_code nofilter}>
<{/if}>

<div id="xpages-page" class="xpages-page-wrap">

    <{* Breadcrumb *}>
    <{if $xpages_show_breadcrumb}>
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
            <{if $xpages_show_lastmod && $xpages_page.update_date}>
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
        <{if $xpages_page.short_desc}>
        <div class="xpages-short-desc">
            <{$xpages_page.short_desc}>
        </div>
        <{/if}>

        <{* Ana içerik *}>
        <div class="xpages-body">
            <{$xpages_page.body nofilter}>
        </div>

		<{* ── İlave Alanlar (TAMAMEN YENİLENDİ) ─────────────────────────────────── *}>
		<{if $xpages_page.extra_fields}>
		<section class="xpages-extra-fields">
			<h3><{$smarty.const._MD_XPAGES_EXTRA_FIELDS}></h3>
			<{foreach key=fid item=f from=$xpages_page.extra_fields_by_id}>
				<{if $f.show_in_tpl && $f.value != ''}>
				<div class="xpf xpf-<{$f.field_type}>" id="xpf-<{$fid}>">
					<div class="xpf-label"><strong><{$f.field_label}>:</strong></div>
					<div class="xpf-value">
						<{if $f.field_type == 'file'}>
							<{* Dosya/Resim gösterimi *}>
							<{assign var="fileExt" value=$f.file_ext|lower}>
							<{if $fileExt == 'jpg' || $fileExt == 'jpeg' || $fileExt == 'png' || $fileExt == 'gif' || $fileExt == 'webp'}>
								<img src="<{$f.value|escape:'html'}>" alt="<{$f.field_label}>" style="max-width:100%;max-height:300px;border-radius:5px">
							<{else}>
								<a href="<{$f.value|escape:'html'}>" target="_blank" rel="noopener">📎 <{$smarty.const._MD_XPAGES_DOWNLOAD_FILE}></a>
							<{/if}>
						<{elseif $f.field_type == 'url'}>
							<a href="<{$f.value|escape:'html'}>" target="_blank" rel="noopener"><{$f.value|escape:'html'}></a>
						<{elseif $f.field_type == 'email'}>
							<a href="mailto:<{$f.value|escape:'html'}>"><{$f.value|escape:'html'}></a>
						<{elseif $f.field_type == 'checkbox'}>
							<{if $f.value == 1 || $f.value == '1' || $f.value == 'on' || $f.value == 'yes'}>✓ <{$smarty.const._MD_XPAGES_YES}><{else}>✗ <{$smarty.const._MD_XPAGES_NO}><{/if}>
						<{elseif $f.field_type == 'radio'}>
							<span><{$f.value}></span>
						<{elseif $f.field_type == 'select'}>
							<span><{$f.value}></span>
						<{elseif $f.field_type == 'textarea'}>
							<div class="xpf-textarea-val"><{$f.value|nl2br nofilter}></div>
						<{else}>
							<span><{$f.value}></span>
						<{/if}>
					</div>
				</div>
				<{/if}>
			<{/foreach}>
		</section>
		<{/if}>
		
		
<{* ── Galeri Bölümü (LightGallery ile) ──────────────────────────────────────── *}>
<{if $xpages_gallery}>
<section class="xpages-gallery">
    <div id="xpages-lightgallery" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:20px">
        <{foreach item=g from=$xpages_gallery key=index}>
        <a href="<{$g.image_url}>" class="gallery-item" data-src="<{$g.image_url}>" data-sub-html="<h4><{$g.title|escape:'html'}></h4><p><{$g.description|escape:'html'}></p>">
            <div style="border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.1);transition:all 0.3s;background:#fff;height:100%">
                <img src="<{$g.image_url}>" alt="<{$g.title|escape:'html'}>" loading="lazy" style="width:100%;height:220px;object-fit:cover;transition:transform 0.5s">
                <div style="padding:12px">
                    <h4 style="margin:0 0 6px;font-size:15px;color:#333"><{$g.title}></h4>
                    <p style="margin:0;font-size:12px;color:#888;line-height:1.4"><{$g.description}></p>
                </div>
            </div>
        </a>
        <{/foreach}>
    </div>
</section>

<style>
.gallery-item {
    text-decoration: none;
    display: block;
}
.gallery-item > div:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.gallery-item > div:hover img {
    transform: scale(1.03);
}
</style>
<{/if}>

<hr>
<{$xpages_page.extra_fields.metin_tek.value}>
<hr>

    </article>

    <{* XOOPS yorumlar (etkinleştirildiyse) *}>
    <{if $xpages_show_comments}>
    <section class="xpages-comments">
        <{include file="db:system_comments.tpl"}>
    </section>
    <{/if}>

</div><!-- #xpages-page -->

<{* Sayfa sonu özel kod enjeksiyonu *}>
<{if $xpages_footer_code}>
<{$xpages_footer_code nofilter}>
<{/if}>
