<?php
/**
 * xPages — Admin hakkında sayfası
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

include_once '../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';
xpages_admin_boot();

xoops_cp_header();

if (class_exists('Xmf\\Module\\Admin')) {
    \Xmf\Module\Admin::getInstance()->displayNavigation('about.php');
}
?>

<div style="margin:16px 0 24px">
    <h2 style="margin:0 0 4px;font-size:22px">ℹ️ Hakkında — xPages Modülü</h2>
    <p style="margin:0;color:#6b7280">Gelişmiş sabit sayfa yönetim modülü</p>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:20px;margin-bottom:24px">

    <!-- Modül Bilgileri -->
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07)">
        <h3 style="margin:0 0 14px;color:#4472c4;font-size:16px">📦 Modül Bilgileri</h3>
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <?php
            $rows = [
                ['Modül Adı',   'xPages (Sayfalar)'],
                ['Sürüm',       '1.0.0'],
                ['Yazar',       'Eren Yumak — <a href="https://aymak.net" target="_blank" style="color:#007bff;text-decoration:none">Aymak</a>'],
                ['Web Sitesi',  '<a href="https://aymak.net" target="_blank" style="color:#007bff;text-decoration:none">https://aymak.net</a>'],
                ['Lisans',      'GNU General Public License v2'],
                ['Uyumluluk',   'XOOPS 2.7.0+, PHP 7.0+, MySQL/MariaDB 5.6+'],
                ['Kodlama',     'UTF-8'],
            ];
            foreach ($rows as $r):
            ?>
            <tr style="border-bottom:1px solid #f0f0f0">
                <td style="padding:8px 0;width:38%;font-weight:600;color:#374151"><?= $r[0] ?></td>
                <td style="padding:8px 0"><?= $r[1] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Özellikler -->
    <div style="background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07)">
        <h3 style="margin:0 0 14px;color:#4472c4;font-size:16px">✨ Özellikler</h3>
        <ul style="margin:0;padding-left:18px;line-height:1.8;font-size:13px">
            <li>📄 <strong>Sabit Sayfalar</strong> — SEO dostu alias URL'ler</li>
            <li>🔧 <strong>Dinamik Alan Sistemi</strong> — 14 farklı alan tipi, global veya sayfa özel</li>
            <li>📁 <strong>Hiyerarşik Yapı</strong> — Parent/child sayfa ilişkisi</li>
            <li>🎨 <strong>Menü Entegrasyonu</strong> — Otomatik menü ve navigasyon bloğu</li>
            <li>🔍 <strong>SEO Optimizasyonu</strong> — Meta başlık/açıklama, noindex/nofollow</li>
            <li>🔗 <strong>301 Yönlendirme</strong> — Sayfa bazlı URL yönlendirme</li>
            <li>📊 <strong>İstatistikler</strong> — Hit sayacı ve XOOPS yorum desteği</li>
            <li>🔎 <strong>Arama Entegrasyonu</strong> — XOOPS site araması</li>
            <li>⚡ <strong>Sayfa Başı/Sonu Kod</strong> — Sayfa özel JS/CSS enjeksiyonu</li>
        </ul>
    </div>
</div>

<!-- Şablon Kullanımı -->
<div style="background:#fff;padding:22px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.07);margin-bottom:24px">
    <h3 style="margin:0 0 14px;color:#4472c4;font-size:16px">🎨 Şablon Değişkenleri (Smarty)</h3>
    <pre style="background:#1e1e1e;color:#d4d4d4;padding:16px;border-radius:8px;overflow-x:auto;font-size:12px;line-height:1.6;margin:0"><?= htmlspecialchars(
'{* Temel alanlar *}
{$xpages_page.title}
{$xpages_page.body nofilter}
{$xpages_page.short_desc}
{$xpages_page.alias}
{$xpages_page.hits}
{$xpages_page.create_date|date_format:"%d.%m.%Y"}
{$xpages_page.update_date|date_format:"%d.%m.%Y %H:%M"}

{* SEO *}
{$xpages_page.meta_title}
{$xpages_page.meta_desc}
{$xpages_page.robots}

{* İlave alana doğrudan erişim (alan adıyla) *}
{$xpages_page.extra_fields.brosur_url.value}
{$xpages_page.extra_fields.kapak_resim.value}

{* Tüm ilave alanları döngüyle listeleme *}
{foreach key=fname item=f from=$xpages_page.extra_fields}
    {if $f.show_in_tpl && $f.value != ""}
    <div class="field field-{$f.field_type}">
        <strong>{$f.field_label}:</strong>
        {if $f.field_type == "checkbox"}
            {if $f.value}✓{else}✗{/if}
        {elseif $f.field_type == "url"}
            <a href="{$f.value}" target="_blank">{$f.value}</a>
        {elseif $f.field_type == "image"}
            <img src="{$f.value}" alt="{$f.field_label}">
        {else}
            {$f.value}
        {/if}
    </div>
    {/if}
{/foreach}', ENT_QUOTES) ?></pre>
</div>

<!-- Destek -->
<div style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:22px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);margin-bottom:24px">
    <h3 style="margin:0 0 12px;font-size:16px">🆘 Destek &amp; İletişim</h3>
    <ul style="margin:0;padding-left:18px;line-height:1.8;font-size:13px">
        <li>🌐 Web: <a href="https://aymak.net" target="_blank" style="color:#ffd700;text-decoration:none">https://aymak.net</a></li>
        <li>📧 E-posta: <a href="mailto:info@aymak.net" style="color:#ffd700;text-decoration:none">info@aymak.net</a></li>
        <li>🐛 GitHub: <a href="https://github.com/aymak/xpages" target="_blank" style="color:#ffd700;text-decoration:none">github.com/aymak/xpages</a></li>
    </ul>
</div>

<div style="text-align:center;padding:16px;color:#9ca3af;font-size:12px;border-top:1px solid #e5e7eb">
    ❤️ xPages — GPL 2.0 Lisansı ile dağıtılmaktadır.
</div>

<?php
xoops_cp_footer();
