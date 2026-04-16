<?php
/**
 * xPages — Bloklar
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';
xpages_load_language('admin');

// ── Son Sayfalar Bloğu ────────────────────────────────────────────────────────

function xpages_block_recent(array $options): array
{
    $limit     = isset($options[0]) ? (int)$options[0] : 5;
    $showDesc  = isset($options[1]) ? (int)$options[1] : 1;

    $pageHandler = xpages_get_handler('page');

    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('page_status', 1));
    $criteria->setSort('update_date');
    $criteria->setOrder('DESC');
    $criteria->setLimit($limit);

    $pages = $pageHandler->getObjects($criteria);
    $block = [];

    foreach ($pages as $p) {
        $block['pages'][] = [
            'page_id'    => $p->getVar('page_id'),
            'title'      => $p->getVar('title'),
            'short_desc' => $showDesc ? $p->getVar('short_desc') : '',
            'page_url'   => $p->getPageUrl(),
            'update_date'=> $p->getVar('update_date'),
        ];
    }

    $block['show_desc'] = $showDesc;
    return $block;
}

function xpages_block_recent_edit(array $options): string
{
    $limit    = isset($options[0]) ? (int)$options[0] : 5;
    $showDesc = isset($options[1]) ? (int)$options[1] : 1;

    $form  = '<label>' . _AM_XPAGES_BLOCK_LIMIT_LABEL . ' ';
    $form .= '<input type="number" name="options[0]" value="' . $limit . '" min="1" max="50" size="3"></label><br>';
    $form .= '<label><input type="checkbox" name="options[1]" value="1"' . ($showDesc ? ' checked' : '') . '> ' . _AM_XPAGES_BLOCK_SHOW_DESC . '</label>';
    return $form;
}

// ── Sayfa Menüsü Bloğu ────────────────────────────────────────────────────────

function xpages_block_menu(): array
{
    $pageHandler = xpages_get_handler('page');
    $rootPages   = $pageHandler->getMenuPages(0);

    $block = [];
    foreach ($rootPages as $p) {
        $pid      = (int)$p->getVar('page_id');
        $children = $pageHandler->getMenuPages($pid);
        $childArr = [];
        foreach ($children as $c) {
            $childArr[] = [
                'page_id' => $c->getVar('page_id'),
                'title'   => $c->getVar('title'),
                'page_url'=> $c->getPageUrl(),
            ];
        }
        $block['pages'][] = [
            'page_id'  => $pid,
            'title'    => $p->getVar('title'),
            'page_url' => $p->getPageUrl(),
            'children' => $childArr,
        ];
    }
    return $block;
}
