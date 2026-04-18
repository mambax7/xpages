<?php

declare(strict_types=1);

namespace XoopsModules\Xpages;

/**
 * xPages — Module helper.
 *
 * Thin subclass of Xmf\Module\Helper that exposes an application-level
 * singleton. The parent's getHandler() implementation already loads
 * class/{name}.php and instantiates Xpages{Name}Handler, so no override
 * is needed while the handler classes remain in the global namespace.
 *
 * Current callers:
 *   - include/functions.php::xpages_get_handler() delegates here.
 *   - include/functions.php::xpages_load_language() delegates here.
 *
 * @package xpages
 */
class Helper extends \Xmf\Module\Helper
{
    public function __construct()
    {
        parent::__construct(basename(dirname(__DIR__)));
    }

    /**
     * Process-wide singleton.
     */
    public static function getInstance(): self
    {
        static $instance;
        if (null === $instance) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Return the dirname string.
     */
    public function getDirname(): string
    {
        return (string)$this->dirname;
    }
}
