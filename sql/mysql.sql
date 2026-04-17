# Table structure for table `xpages_pages`
CREATE TABLE `xpages_pages` (
    `page_id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`        VARCHAR(255) NOT NULL DEFAULT '',
    `alias`        VARCHAR(255) NOT NULL DEFAULT '',
    `body`         LONGTEXT NOT NULL,
    `short_desc`   TEXT NOT NULL,
    `page_status`  TINYINT(1) NOT NULL DEFAULT 1,
    `menu_order`   SMALLINT(5) NOT NULL DEFAULT 0,
    `show_in_menu` TINYINT(1) NOT NULL DEFAULT 1,
    `show_in_nav`  TINYINT(1) NOT NULL DEFAULT 1,
    `parent_id`    INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `uid`          INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `create_date`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `update_date`  INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `meta_title`   VARCHAR(255) NOT NULL DEFAULT '',
    `meta_keywords`TEXT NOT NULL,
    `meta_desc`    TEXT NOT NULL,
    `noindex`      TINYINT(1) NOT NULL DEFAULT 0,
    `nofollow`     TINYINT(1) NOT NULL DEFAULT 0,
    `redirect_url` VARCHAR(500) NOT NULL DEFAULT '',
    `header_code`  TEXT NOT NULL,
    `footer_code`  TEXT NOT NULL,
    `hits`         INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `comments`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`page_id`),
    UNIQUE KEY `alias` (`alias`),
    KEY `parent_id` (`parent_id`),
    KEY `page_status` (`page_status`),
    KEY `menu_order` (`menu_order`)
) ENGINE=InnoDB;

# Table structure for table `xpages_fields`
CREATE TABLE `xpages_fields` (
    `field_id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `page_id`       INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `field_name`    VARCHAR(100) NOT NULL DEFAULT '',
    `field_label`   VARCHAR(255) NOT NULL DEFAULT '',
    `field_type`    VARCHAR(50) NOT NULL DEFAULT 'text',
    `field_options` TEXT NOT NULL,
    `field_required`TINYINT(1) NOT NULL DEFAULT 0,
    `field_order`   SMALLINT(5) NOT NULL DEFAULT 0,
    `field_status`  TINYINT(1) NOT NULL DEFAULT 1,
    `field_desc`    VARCHAR(500) NOT NULL DEFAULT '',
    `field_default` TEXT NOT NULL,
    `show_in_tpl`   TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`field_id`),
    KEY `page_id` (`page_id`),
    KEY `field_order` (`field_order`)
) ENGINE=InnoDB;

# Table structure for table `xpages_field_values`
CREATE TABLE `xpages_field_values` (
    `value_id`   INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `page_id`    INT(10) UNSIGNED NOT NULL,
    `field_id`   INT(10) UNSIGNED NOT NULL,
    `field_value`LONGTEXT NOT NULL,
    PRIMARY KEY (`value_id`),
    UNIQUE KEY `page_field` (`page_id`, `field_id`),
    KEY `page_id` (`page_id`),
    KEY `field_id` (`field_id`)
) ENGINE=InnoDB;


CREATE TABLE `xpages_gallery` (
    `gallery_id`    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `page_id`       INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `title`         VARCHAR(255) NOT NULL DEFAULT '',
    `description`   TEXT NOT NULL,
    `image_path`    VARCHAR(255) NOT NULL DEFAULT '',
    `image_url`     VARCHAR(500) NOT NULL DEFAULT '',
    `image_order`   SMALLINT(5) NOT NULL DEFAULT 0,
    `image_status`  TINYINT(1) NOT NULL DEFAULT 1,
    `create_date`   INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `uid`           INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`gallery_id`),
    KEY `page_id` (`page_id`),
    KEY `image_order` (`image_order`)
) ENGINE=InnoDB;