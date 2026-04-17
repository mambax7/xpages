<?php
/**
 * xPages — Türkçe dil dosyası (admin)
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

// Sayfa formu
define('_AM_XPAGES_ADD_PAGE',      'Yeni Sayfa Ekle');
define('_AM_XPAGES_EDIT_PAGE',     'Sayfayı Düzenle');
define('_AM_XPAGES_PAGE_TITLE',    'Başlık');
define('_AM_XPAGES_PAGE_ALIAS',    'URL Alias (Kısa Ad)');
define('_AM_XPAGES_ALIAS_HELP',    'Boş bırakılırsa başlıktan otomatik oluşturulur. Örnek: hakkimizda');
define('_AM_XPAGES_SHORT_DESC',    'Kısa Açıklama');
define('_AM_XPAGES_BODY',          'İçerik');
define('_AM_XPAGES_STATUS',        'Durum');
define('_AM_XPAGES_ACTIVE',        'Aktif');
define('_AM_XPAGES_INACTIVE',      'Pasif');
define('_AM_XPAGES_PAGE_ORDER',    'Sıralama');
define('_AM_XPAGES_SHOW_IN_MENU',  'Menüde Göster');
define('_AM_XPAGES_SHOW_IN_NAV',   'Navigasyonda Göster');
define('_AM_XPAGES_PARENT_PAGE',   'Üst Sayfa');
define('_AM_XPAGES_NO_PARENT',     '— Üst Sayfa Yok —');
define('_AM_XPAGES_PAGE_STATUS',   'Durum');

// SEO
define('_AM_XPAGES_TAB_MAIN',          'Genel');
define('_AM_XPAGES_TAB_SEO',           'SEO');
define('_AM_XPAGES_TAB_ADVANCED',      'Gelişmiş');
define('_AM_XPAGES_TAB_EXTRA',         'İlave Alanlar');
define('_AM_XPAGES_META_TITLE',        'Meta Başlık');
define('_AM_XPAGES_META_TITLE_HELP',   'Boş bırakılırsa sayfa başlığı kullanılır.');
define('_AM_XPAGES_META_KEYWORDS',     'Meta Anahtar Kelimeler');
define('_AM_XPAGES_META_DESC',         'Meta Açıklama');
define('_AM_XPAGES_META_DESC_HELP',    'Arama motorlarında görünen kısa açıklama. Önerilen: 150-160 karakter.');
define('_AM_XPAGES_NOINDEX',           'Bu sayfayı indexleme (noindex)');
define('_AM_XPAGES_NOFOLLOW',          'Bu sayfadaki linkleri takip etme (nofollow)');
define('_AM_XPAGES_REDIRECT_URL',      'Yönlendirme URL');
define('_AM_XPAGES_REDIRECT_HELP',     'Doldurulursa sayfa bu adrese yönlendirilir.');
define('_AM_XPAGES_PARENT_INVALID',    'Bir sayfa kendi alt sayfalarından biri veya kendisi üst sayfa olarak seçilemez.');

// Gelişmiş
define('_AM_XPAGES_HEADER_CODE',       'Sayfa Başı Kod (<head>)');
define('_AM_XPAGES_HEADER_CODE_HELP',  'Yalnızca bu sayfanın <head> etiketine eklenecek kod (CSS, meta vb.)');
define('_AM_XPAGES_FOOTER_CODE',       'Sayfa Sonu Kod (</body>)');
define('_AM_XPAGES_FOOTER_CODE_HELP',  'Yalnızca bu sayfanın </body> etiketine eklenecek kod (JS vb.)');
define('_AM_XPAGES_MANAGE_FIELDS_FOR_PAGE', 'Bu Sayfaya Özel Alan Yönetimi');
define('_AM_XPAGES_ADVANCED_CODE_RESTRICTED', 'Sayfa başı/sonu kodunu yalnızca web yöneticileri düzenleyebilir.');

// İlave alan formu
define('_AM_XPAGES_ADD_FIELD',             'Alan Ekle');
define('_AM_XPAGES_EDIT_FIELD',            'Alanı Düzenle');
define('_AM_XPAGES_FIELD_SETTINGS',        'Alan Ayarları');
define('_AM_XPAGES_FIELD_NAME',            'Alan Adı (sistem)');
define('_AM_XPAGES_FIELD_NAME_HELP',       'Küçük harf, rakam ve alt çizgi. Şablonda {$xpages.extra_fields.alan_adi} şeklinde kullanılır.');
define('_AM_XPAGES_FIELD_LABEL',           'Etiket (görünen ad)');
define('_AM_XPAGES_FIELD_TYPE',            'Alan Tipi');
define('_AM_XPAGES_FIELD_OPTIONS',         'Seçenekler (select/radio için)');
define('_AM_XPAGES_FIELD_OPTIONS_HELP',    'Her satıra bir seçenek veya JSON: {"deger":"Etiket"}');
define('_AM_XPAGES_FIELD_DEFAULT',         'Varsayılan Değer');
define('_AM_XPAGES_FIELD_DEFAULT_HELP',    'Alan boş bırakıldığında kullanılacak varsayılan değer.');
define('_AM_XPAGES_FIELD_DESC',            'Açıklama / Yardım Metni');
define('_AM_XPAGES_FIELD_ORDER',           'Sıralama');
define('_AM_XPAGES_FIELD_STATUS',          'Alan Durumu');
define('_AM_XPAGES_FIELD_REQUIRED',        'Zorunlu Alan');
define('_AM_XPAGES_FIELD_SHOW_IN_TPL',     'Şablonda Göster');
define('_AM_XPAGES_GLOBAL_FIELDS',         'Global Alanlar (Tüm Sayfalar)');
define('_AM_XPAGES_NO_FIELDS',             'Henüz alan eklenmemiş.');
define('_AM_XPAGES_FIELDS',                'Alanlar');
define('_AM_XPAGES_BACK_TO_PAGE',          '← Sayfaya Dön');

// Alan tipleri
define('_AM_XPAGES_FIELD_TYPE_TEXT',     'Metin (tek satır)');
define('_AM_XPAGES_FIELD_TYPE_TEXTAREA', 'Metin (çok satır)');
define('_AM_XPAGES_FIELD_TYPE_EDITOR',   'Zengin Metin Editörü');
define('_AM_XPAGES_FIELD_TYPE_IMAGE',    'Resim');
define('_AM_XPAGES_FIELD_TYPE_FILE',     'Dosya');
define('_AM_XPAGES_FIELD_TYPE_URL',      'Bağlantı (URL)');
define('_AM_XPAGES_FIELD_TYPE_EMAIL',    'E-posta');
define('_AM_XPAGES_FIELD_TYPE_TEL',      'Telefon');
define('_AM_XPAGES_FIELD_TYPE_DATE',     'Tarih');
define('_AM_XPAGES_FIELD_TYPE_NUMBER',   'Sayı');
define('_AM_XPAGES_FIELD_TYPE_CHECKBOX', 'Onay Kutusu');
define('_AM_XPAGES_FIELD_TYPE_SELECT',   'Açılır Liste');
define('_AM_XPAGES_FIELD_TYPE_RADIO',    'Seçenek Butonu');
define('_AM_XPAGES_FIELD_TYPE_COLOR',    'Renk');
define('_AM_XPAGES_FIELD_TYPE_CODE',     'Kod / HTML');
define('_AM_XPAGES_FIELD_TYPE_FILE_IMG', '📎 Dosya/Resim');

// Butonlar ve işlemler
define('_AM_XPAGES_SAVE',              'Kaydet');
define('_AM_XPAGES_CANCEL',            'İptal');
define('_AM_XPAGES_EDIT',              'Düzenle');
define('_AM_XPAGES_DELETE',            'Sil');
define('_AM_XPAGES_YES',               'Evet');
define('_AM_XPAGES_NO',                'Hayır');
define('_AM_XPAGES_ACTIONS',           'İşlemler');
define('_AM_XPAGES_BROWSE',            'Gözat');
define('_AM_XPAGES_PAGETO',            'Sayfaya Git');

// Mesajlar
define('_AM_XPAGES_PAGE_SAVED',          'Sayfa başarıyla kaydedildi.');
define('_AM_XPAGES_PAGE_DELETED',        'Sayfa silindi.');
define('_AM_XPAGES_FIELD_SAVED',         'Alan başarıyla kaydedildi.');
define('_AM_XPAGES_FIELD_DELETED',       'Alan silindi.');
define('_AM_XPAGES_SAVE_ERROR',          'Kayıt sırasında hata oluştu.');
define('_AM_XPAGES_PAGE_NOT_FOUND',      'Sayfa bulunamadı.');
define('_AM_XPAGES_FIELD_NAME_EXISTS',   'Bu alan adı zaten kullanılıyor. Farklı bir ad seçin.');
define('_AM_XPAGES_DELETE_CONFIRM',      '"%s" sayfasını silmek istediğinizden emin misiniz?');
define('_AM_XPAGES_FIELD_DELETE_CONFIRM', '"%s" alanını silmek istediğinizden emin misiniz?');
define('_AM_XPAGES_INVALID_FILE_TYPE',   'Geçersiz dosya türü. İzin verilenler: jpg, png, gif, webp, pdf, doc, docx, zip');

// Dashboard
define('_AM_XPAGES_DASHBOARD',          'xPages Kontrol Paneli');
define('_AM_XPAGES_QUICK_ACTIONS',      'Hızlı İşlemler');
define('_AM_XPAGES_RECENT_PAGES',       'Son Sayfalar');
define('_AM_XPAGES_NO_PAGES',           'Henüz sayfa yok');
define('_AM_XPAGES_CREATE_FIRST',       '+ İlk Sayfayı Oluştur');
define('_AM_XPAGES_TITLE',              'Başlık');
define('_AM_XPAGES_CREATED',            'Oluşturulma');
define('_AM_XPAGES_HITS',               'Görüntülenme');
define('_AM_XPAGES_VIEW',               'Görüntüle');
define('_AM_XPAGES_ALL_PAGES',          'Tüm Sayfaları Görüntüle');
define('_AM_XPAGES_VIEW_ALL',           'Tümünü Görüntüle');
define('_AM_XPAGES_SEE_ALL_PAGES',      'Tüm sayfaları gör →');
define('_AM_XPAGES_NO_STATS',           'Henüz istatistik yok');
define('_AM_XPAGES_NO_PAGES_YET',       'Henüz sayfa eklenmemiş');

// Dashboard widget etiketleri
define('_AM_XPAGES_WIDGET_STATS',       '📊 Hızlı İstatistikler');
define('_AM_XPAGES_WIDGET_RECENT',      '🆕 Son Eklenen Sayfalar');
define('_AM_XPAGES_WIDGET_POPULAR',     '🔥 En Çok Okunanlar');
define('_AM_XPAGES_WIDGET_MONTHLY',     '📈 Aylık Sayfa İstatistikleri');
define('_AM_XPAGES_WIDGET_QUICK',       '⚡ Hızlı İşlemler');
define('_AM_XPAGES_WIDGET_SYSINFO',     'ℹ️ Sistem Bilgileri');

// Stat kartları
define('_AM_XPAGES_STAT_TOTAL_PAGES',   'Toplam Sayfa');
define('_AM_XPAGES_STAT_ACTIVE_PAGES',  'Aktif Sayfa');
define('_AM_XPAGES_STAT_FIELDS_COUNT',  'Özel Alan');
define('_AM_XPAGES_STAT_GALLERY_COUNT', 'Galeri Görseli');

// Sistem bilgileri etiketleri
define('_AM_XPAGES_SYSINFO_XOOPS',      'XOOPS Sürümü:');
define('_AM_XPAGES_SYSINFO_MODULE',     'xPages Sürümü:');
define('_AM_XPAGES_SYSINFO_PHP',        'PHP Sürümü:');
define('_AM_XPAGES_SYSINFO_UPDATED',    'Son güncelleme:');

// İstatistik
define('_AM_XPAGES_STAT_PAGES',  'Toplam sayfa: <strong>%d</strong>');
define('_AM_XPAGES_STAT_FIELDS', 'Toplam alan tanımı: <strong>%d</strong>');

// Galeri
define('_AM_XPAGES_GALLERY_TITLE',          '🖼️ Galeri Yönetimi');
define('_AM_XPAGES_GALLERY_ADD',            '➕ Yeni Görsel Ekle');
define('_AM_XPAGES_GALLERY_EDIT',           '✏️ Görsel Düzenle');
define('_AM_XPAGES_GALLERY_NEW',            '➕ Yeni Görsel Ekle');
define('_AM_XPAGES_GALLERY_MANAGE',         '📸 Görsel Galerisini Yönet');
define('_AM_XPAGES_GALLERY_MANAGE_HELP',    'Bu sayfaya ait görsel galerisini yönetin');
define('_AM_XPAGES_GALLERY_SAVE_FIRST',     'Önce sayfayı kaydedin');
define('_AM_XPAGES_GALLERY_SAVE_FIRST_HELP','Galeri eklemek için önce sayfayı kaydetmelisiniz');
define('_AM_XPAGES_GALLERY_ALL_PAGES',      'Tüm Sayfalar');
define('_AM_XPAGES_GALLERY_DELETE_CONFIRM', '"%s" görselini silmek istediğinize emin misiniz?');
define('_AM_XPAGES_GALLERY_DELETED',        'Görsel başarıyla silindi');
define('_AM_XPAGES_GALLERY_SAVED',          'Görsel başarıyla kaydedildi');
define('_AM_XPAGES_GALLERY_SAVE_ERROR',     'Kaydedilirken hata oluştu!');
define('_AM_XPAGES_GALLERY_EMPTY',          'Henüz görsel eklenmemiş');
define('_AM_XPAGES_GALLERY_ADD_FIRST',      '+ İlk Görseli Ekle');
define('_AM_XPAGES_GALLERY_IMG_TITLE',      'Başlık');
define('_AM_XPAGES_GALLERY_IMG_DESC',       'Açıklama');
define('_AM_XPAGES_GALLERY_IMG_FILE',       'Görsel');
define('_AM_XPAGES_GALLERY_IMG_FILE_HELP',  'JPG, PNG, GIF, WEBP (max 5MB)');
define('_AM_XPAGES_GALLERY_IMG_URL',        'veya Harici URL');
define('_AM_XPAGES_GALLERY_IMG_URL_HELP',   'Harici bir görsel URL\'si kullanmak isterseniz doldurun');
define('_AM_XPAGES_GALLERY_IMG_ORDER',      'Sıra');
define('_AM_XPAGES_GALLERY_IMG_STATUS',     'Durum');
define('_AM_XPAGES_GALLERY_CURRENT_IMG',    'Mevcut görsel. Yeni seçerseniz eski silinir.');
define('_AM_XPAGES_GALLERY_ORDER_STATUS',   'Sıra: %d | ');

// Alan formu - dosya alanı
define('_AM_XPAGES_FIELD_DEFAULT_HELP',     'Alan boş bırakıldığında kullanılacak varsayılan değer.');
define('_AM_XPAGES_FILE_FIELD_HELP',        '📎 Resim veya dosya seçin (jpg, png, gif, pdf, doc, zip)');
define('_AM_XPAGES_FILE_CURRENT',           'Mevcut dosya:');
define('_AM_XPAGES_FILE_REPLACE_HINT',      'Yeni dosya yüklemek için yukarıdan seçin (eskisi silinir).');
define('_AM_XPAGES_FILE_VIEW',              '📎 Dosyayı gör');
define('_AM_XPAGES_OPTIONS_HINT_TITLE',     '💡 Seçenekler nasıl yazılır?');
define('_AM_XPAGES_OPTIONS_HINT_BODY',      'Her seçeneği yeni bir satıra yazın.');
define('_AM_XPAGES_OPTIONS_HINT_EXAMPLE',   'Örnek:');

// include/functions.php içi metinler
define('_AM_XPAGES_SELECT_PLACEHOLDER',     '-- Seçiniz --');
define('_AM_XPAGES_FILE_CURRENT_LABEL',     'Mevcut dosya:');
define('_AM_XPAGES_FILE_REPLACE_NOTE',      'Yeni dosya yüklemek için yukarıdan seçin (eski dosya silinir).');
define('_AM_XPAGES_FILE_NONE',              'Henüz dosya seçilmedi.');

// Blok düzenleme formu
define('_AM_XPAGES_BLOCK_LIMIT_LABEL',      'Gösterilecek sayfa sayısı:');
define('_AM_XPAGES_BLOCK_SHOW_DESC',        'Kısa açıklamayı göster');

// index.php (admin) hardcoded metinler
define('_AM_XPAGES_DASHBOARD_SUBTITLE',     'Sayfa Yönetim Modülü — Genel Bakış');
define('_AM_XPAGES_RECENT_PAGES_WIDGET',    '🆕 Son Eklenen Sayfalar');
define('_AM_XPAGES_POPULAR_PAGES_WIDGET',   '🔥 En Çok Okunanlar');
define('_AM_XPAGES_MONTHLY_STATS',          '📈 Aylık Sayfa İstatistikleri');
define('_AM_XPAGES_QUICK_ACTIONS_WIDGET',   '⚡ Hızlı İşlemler');
define('_AM_XPAGES_SYS_INFO_WIDGET',        'ℹ️ Sistem Bilgileri');
define('_AM_XPAGES_STAT_TOTAL_PAGES_LBL',   '📄 Toplam Sayfa');
define('_AM_XPAGES_STAT_ACTIVE_PAGES_LBL',  '✅ Aktif Sayfa');
define('_AM_XPAGES_STAT_FIELDS_LBL',        '⚙️ Özel Alan');
define('_AM_XPAGES_STAT_GALLERY_LBL',       '🖼️ Galeri Görseli');
define('_AM_XPAGES_BTN_NEW_PAGE',           '📄 + Yeni Sayfa');
define('_AM_XPAGES_BTN_NEW_FIELD',          '⚙️ + Yeni Alan');
define('_AM_XPAGES_BTN_GALLERY',            '🖼️ Galeri Yönetimi');
define('_AM_XPAGES_BTN_LIST_PAGES',         '📋 Sayfaları Listele');
define('_AM_XPAGES_TOGGLE_STATUS_TITLE',    'Durumu değiştir');
define('_AM_XPAGES_STATUS_ACTIVE',          '✅ Aktif');
define('_AM_XPAGES_STATUS_INACTIVE',        '❌ Pasif');

// Sayfa düzenleyici ve alan formu yardımcıları
define('_AM_XPAGES_ALIAS_PLACEHOLDER',             'Otomatik oluşturulacak');
define('_AM_XPAGES_FIELD_OPTIONS_SAMPLE_PLACEHOLDER', 'Kırmızı&#10;Mavi&#10;Yeşil');
define('_AM_XPAGES_FIELD_OPTIONS_SAMPLE_CODE',        'Kırmızı<br>Mavi<br>Yeşil');

// Hakkında sayfası
define('_AM_XPAGES_ABOUT_TITLE',               'Hakkında — xPages Modülü');
define('_AM_XPAGES_ABOUT_MODULE_INFO_TITLE',   'Modül Bilgileri');
define('_AM_XPAGES_ABOUT_FEATURES_TITLE',      'Özellikler');
define('_AM_XPAGES_ABOUT_TEMPLATE_TITLE',      'Smarty Şablon Değişkenleri');
define('_AM_XPAGES_ABOUT_SUPPORT_TITLE',       'Destek & İletişim');
define('_AM_XPAGES_ABOUT_FOOTER',              'xPages — GPL 2.0 Lisansı ile dağıtılmaktadır.');
define('_AM_XPAGES_ABOUT_LABEL_MODULE_NAME',   'Modül Adı');
define('_AM_XPAGES_ABOUT_LABEL_VERSION',       'Sürüm');
define('_AM_XPAGES_ABOUT_LABEL_AUTHOR',        'Yazar');
define('_AM_XPAGES_ABOUT_LABEL_WEBSITE',       'Web Sitesi');
define('_AM_XPAGES_ABOUT_LABEL_LICENSE',       'Lisans');
define('_AM_XPAGES_ABOUT_LABEL_COMPATIBILITY', 'Uyumluluk');
define('_AM_XPAGES_ABOUT_LABEL_ENCODING',      'Kodlama');
define('_AM_XPAGES_ABOUT_FEATURE_1',           '📄 <strong>Sabit Sayfalar</strong> — SEO dostu alias URL\'ler');
define('_AM_XPAGES_ABOUT_FEATURE_2',           '🔧 <strong>Dinamik Alan Sistemi</strong> — 14 farklı alan tipi, global veya sayfa özel');
define('_AM_XPAGES_ABOUT_FEATURE_3',           '📁 <strong>Hiyerarşik Yapı</strong> — Parent/child sayfa ilişkisi');
define('_AM_XPAGES_ABOUT_FEATURE_4',           '🎨 <strong>Menü Entegrasyonu</strong> — Otomatik menü ve navigasyon bloğu');
define('_AM_XPAGES_ABOUT_FEATURE_5',           '🔍 <strong>SEO Optimizasyonu</strong> — Meta başlık/açıklama, noindex/nofollow');
define('_AM_XPAGES_ABOUT_FEATURE_6',           '🔗 <strong>URL Yönlendirme</strong> — Sayfa bazlı URL yönlendirme');
define('_AM_XPAGES_ABOUT_FEATURE_7',           '📊 <strong>İstatistikler</strong> — Hit sayacı ve XOOPS yorum desteği');
define('_AM_XPAGES_ABOUT_FEATURE_8',           '🔎 <strong>Arama Entegrasyonu</strong> — XOOPS site araması');
define('_AM_XPAGES_ABOUT_FEATURE_9',           '⚡ <strong>Sayfa Başı/Sonu Kod</strong> — Sayfa özel JS/CSS enjeksiyonu');
define('_AM_XPAGES_ABOUT_SUPPORT_WEB',         'Web');
define('_AM_XPAGES_ABOUT_SUPPORT_EMAIL',       'E-posta');
define('_AM_XPAGES_ABOUT_SUPPORT_GITHUB',      'GitHub');
define('_AM_XPAGES_ABOUT_SMARTY_EXAMPLE', <<<'EOT'
{* Temel alanlar *}
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
{foreach key=fid item=f from=$xpages_page.extra_fields_by_id}
    {if $f.show_in_tpl && $f.value != ""}
    <div class="field field-{$f.field_type}" id="field-{$fid}">
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
{/foreach}
EOT
);
