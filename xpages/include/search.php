<?php
/**
 * xPages — XOOPS arama entegrasyonu
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

function xpages_search(array $queryArray, int $andor, int $limit, int $offset, int $uid): array
{
    require_once XOOPS_ROOT_PATH . '/modules/xpages/include/functions.php';

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $conditions = [];
    foreach ($queryArray as $q) {
        $q = $db->escape($q);
        $conditions[] = "(title LIKE '%{$q}%' OR body LIKE '%{$q}%' OR short_desc LIKE '%{$q}%')";
    }

    $glue  = ($andor === 0) ? ' AND ' : ' OR ';
    $where = implode($glue, $conditions);

    $sql = 'SELECT page_id, title, short_desc, update_date, alias'
         . ' FROM ' . $db->prefix('xpages_pages')
         . ' WHERE page_status = 1'
         . ($where ? ' AND (' . $where . ')' : '')
         . ' ORDER BY update_date DESC'
         . ' LIMIT ' . (int)$offset . ', ' . (int)$limit;

    $result = $db->query($sql);
    $ret    = [];
    while ($row = $db->fetchArray($result)) {
        $url = $row['alias']
            ? XOOPS_URL . '/modules/xpages/page.php?alias=' . rawurlencode($row['alias'])
            : XOOPS_URL . '/modules/xpages/page.php?page_id=' . (int)$row['page_id'];

        $ret[] = [
            'image' => XOOPS_URL . '/modules/xpages/images/logo.png',
            'link'  => $url,
            'title' => htmlspecialchars($row['title'], ENT_QUOTES),
            'time'  => (int)$row['update_date'],
            'uid'   => 0,
        ];
    }
    return $ret;
}
