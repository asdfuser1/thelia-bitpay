
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- bitpay_config
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `bitpay_config`;

CREATE TABLE `bitpay_config`
(
    `name` VARCHAR(255) NOT NULL,
    `value` VARCHAR(255),
    PRIMARY KEY (`name`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
