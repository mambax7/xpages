<?php

declare(strict_types=1);

/**
 * xPages — Field-value value object.
 *
 * The handler lives in fieldvaluehandler.php, which this file loads at
 * the bottom. See class/page.php for the rationale.
 *
 * @package  xpages
 * @author   Eren Yumak — Aymak (aymak.net)
 */

class XpagesFieldvalue extends XoopsObject
{
    public function __construct()
    {
        $this->initVar('value_id',    XOBJ_DTYPE_INT,    null, false);
        $this->initVar('page_id',     XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('field_id',    XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('field_value', XOBJ_DTYPE_TXTAREA, '',   false);
    }
}

require_once __DIR__ . '/fieldvaluehandler.php';
