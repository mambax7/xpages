<?php

declare(strict_types=1);

/**
 * Minimal XOOPS core stubs for unit tests.
 *
 * Just enough surface to load the module's value-object + handler
 * classes without a running XOOPS instance. Tests subclass / partial-
 * mock these to control the getObjects() / getCount() return values
 * that handler methods delegate to.
 *
 * @package xpages
 */

// XoopsObject data-type constants (from XOOPS kernel).
if (!defined('XOBJ_DTYPE_INT')) {
    define('XOBJ_DTYPE_INT',     1);
    define('XOBJ_DTYPE_TXTBOX',  2);
    define('XOBJ_DTYPE_TXTAREA', 3);
}

if (!class_exists('XoopsObject')) {
    class XoopsObject
    {
        /** @var array<string,array{type:int,value:mixed}> */
        protected array $vars = [];

        public function initVar(string $name, int $type, mixed $default = null, bool $required = false, ?int $maxlength = null): void
        {
            $this->vars[$name] = ['type' => $type, 'value' => $default];
        }

        public function setVar(string $name, mixed $value, bool $notGPC = false): void
        {
            $this->vars[$name] = ($this->vars[$name] ?? ['type' => 0]) + [];
            $this->vars[$name]['value'] = $value;
        }

        public function getVar(string $name, string $format = 's'): mixed
        {
            return $this->vars[$name]['value'] ?? null;
        }
    }
}

if (!class_exists('XoopsObjectHandler')) {
    class XoopsObjectHandler
    {
        protected mixed $db;

        public function __construct(mixed $db = null)
        {
            $this->db = $db;
        }
    }
}

if (!class_exists('XoopsPersistableObjectHandler')) {
    class XoopsPersistableObjectHandler extends XoopsObjectHandler
    {
        protected string $table;
        protected string $className;
        protected string $keyName;
        protected string $identifierName;

        public function __construct(mixed $db = null, string $table = '', string $className = '', string $keyName = '', string $identifierName = '')
        {
            parent::__construct($db);
            $this->table          = $table;
            $this->className      = $className;
            $this->keyName        = $keyName;
            $this->identifierName = $identifierName;
        }

        public function create(bool $isNew = true): XoopsObject
        {
            $className = $this->className;
            return new $className();
        }

        public function get(mixed $id, bool $as_object = true): ?XoopsObject
        {
            return null;
        }

        /**
         * Return type matches real XOOPS 2.7 core: array or false on
         * error. Handlers guard with `?: []` to honour their own
         * `array`-typed return, and tests rely on the false path.
         *
         * @return list<XoopsObject>|false
         */
        public function getObjects(mixed $criteria = null, bool $id_as_key = false, bool $as_object = true): array|false
        {
            return [];
        }

        public function getCount(mixed $criteria = null): int
        {
            return 0;
        }

        public function insert(XoopsObject $object, bool $force = false): bool
        {
            return true;
        }

        public function delete(XoopsObject $object, bool $force = false): bool
        {
            return true;
        }

        public function deleteAll(mixed $criteria = null): bool
        {
            return true;
        }
    }
}

if (!class_exists('Criteria')) {
    class Criteria
    {
        public string $column;
        public mixed $value;
        public string $op;
        public string $sort = '';
        public string $order = '';
        public int $limit = 0;
        public int $start = 0;

        public function __construct(string $column, mixed $value = '', string $op = '=')
        {
            $this->column = $column;
            $this->value  = $value;
            $this->op     = $op;
        }

        public function setSort(string $sort): void   { $this->sort  = $sort; }
        public function setOrder(string $order): void { $this->order = $order; }
        public function setLimit(int $limit): void    { $this->limit = $limit; }
        public function setStart(int $start): void    { $this->start = $start; }
    }
}

if (!class_exists('CriteriaCompo')) {
    class CriteriaCompo extends Criteria
    {
        /** @var list<array{criterion:Criteria,condition:string}> */
        public array $criterias = [];

        public function __construct()
        {
            $this->column = '';
            $this->value  = '';
            $this->op     = '';
        }

        public function add(Criteria $criterion, string $condition = 'AND'): self
        {
            $this->criterias[] = ['criterion' => $criterion, 'condition' => $condition];
            return $this;
        }
    }
}

if (!class_exists('XoopsDatabase')) {
    class XoopsDatabase
    {
        /** @var list<string> SQL calls recorded by exec(), for assertions */
        public array $execCalls = [];

        public function exec(string $sql): int
        {
            $this->execCalls[] = $sql;
            return 1;
        }
    }
}
