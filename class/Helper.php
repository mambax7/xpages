<?php

declare(strict_types=1);

namespace XoopsModules\Xpages;

/**
 * xPages — Module helper.
 *
 * Thin subclass of Xmf\Module\Helper with singleton access + a handful
 * of typed accessors that wrap the XOOPS request-scoped globals. The
 * accessors let library functions avoid `global $xoopsUser;`-style
 * declarations (H3) and make the dependency explicit + mockable.
 *
 * Accessors available:
 *   Helper::getInstance()->user()      → ?\XoopsUser      (alias: $xoopsUser)
 *   Helper::getInstance()->module()    → ?\XoopsModule    (alias: $xoopsModule — parent also exposes getModule())
 *   Helper::getInstance()->security()  → ?object          (alias: $xoopsSecurity)
 *   Helper::getInstance()->logger()    → ?object          (alias: $xoopsLogger)
 *   Helper::getInstance()->db()        → \XoopsDatabase   (XoopsDatabaseFactory::getDatabaseConnection())
 *   Helper::getInstance()->getConfig() — inherited from Xmf\Module\Helper (alias: $xoopsModuleConfig[$name])
 *   Helper::getInstance()->getHandler()— inherited.
 *
 * All accessors fall back to $GLOBALS[...] so tests that don't run a
 * full XOOPS bootstrap can seed the globals in tests/bootstrap.php.
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

    /**
     * Current logged-in user, or null if anonymous / unknown.
     */
    public function user(): ?\XoopsUser
    {
        $u = $GLOBALS['xoopsUser'] ?? null;
        return ($u instanceof \XoopsUser) ? $u : null;
    }

    /**
     * Current module object (request-scoped). Returns the live
     * $xoopsModule from $GLOBALS; parent::getModule() re-fetches from
     * the module handler, which is heavier.
     */
    public function module(): ?\XoopsModule
    {
        $m = $GLOBALS['xoopsModule'] ?? null;
        return ($m instanceof \XoopsModule) ? $m : null;
    }

    /**
     * XOOPS security helper (CSRF token + validation). Returns null
     * before cp_header.php has run.
     */
    public function security(): ?object
    {
        $s = $GLOBALS['xoopsSecurity'] ?? null;
        return is_object($s) ? $s : null;
    }

    /**
     * XOOPS logger, for addExtra() / addMessage(). Returns null on
     * install / early-boot paths where the logger isn't wired up.
     */
    public function logger(): ?object
    {
        $l = $GLOBALS['xoopsLogger'] ?? null;
        return is_object($l) ? $l : null;
    }

    /**
     * Shared XOOPS database connection. Uses XoopsDatabaseFactory so
     * install / upgrade hooks can reach the DB before $xoopsDB is
     * populated in the request.
     */
    public function db(): \XoopsDatabase
    {
        return \XoopsDatabaseFactory::getDatabaseConnection();
    }
}
