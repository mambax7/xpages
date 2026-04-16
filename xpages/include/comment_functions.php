<?php
/**
 * xPages — Yorum onay callback
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

function xpages_comments_approve(int $itemId, int $commentCount): void
{
    require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';

    $pageHandler = xpages_get_handler('page');
    $page = $pageHandler->get($itemId);
    if ($page) {
        $page->setVar('comments', $commentCount);
        $pageHandler->insert($page);
    }
}
