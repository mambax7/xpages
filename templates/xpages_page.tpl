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
		
		
		<{* ── Galeri Bölümü ──────────────────────────────────────────────────────── *}>
<{if $xpages_gallery}>
<section class="xpages-gallery">
    <h3><{$smarty.const._MD_XPAGES_GALLERY_TITLE}></h3>
    <div class="xpages-gallery-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:15px;margin-top:15px">
        <{foreach item=g from=$xpages_gallery}>
        <div class="xpages-gallery-item" style="border-radius:8px;overflow:hidden;box-shadow:0 2px 5px rgba(0,0,0,0.1)">
            <a href="<{$g.image_url|escape:'html'}>" data-lightbox="xpages-gallery" data-title="<{$g.title}>">
                <img src="<{$g.image_url|escape:'html'}>" alt="<{$g.title}>" style="width:100%;height:150px;object-fit:cover">
            </a>
            <div style="padding:10px">
                <h4 style="margin:0 0 5px;font-size:14px"><{$g.title}></h4>
                <p style="margin:0;font-size:12px;color:#6c757d"><{$g.description}></p>
            </div>
        </div>
        <{/foreach}>
    </div>
</section>
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
