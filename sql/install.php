<?php
/**
 * Loulou66
 * LpsPayDiffVir30 module for Prestashop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Loulou66.fr <contact@loulou66.fr>
 *  @copyright loulou66.fr
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lps_defered_payments_vir30` (
    `id_lps_defered_payments` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `id_order` int(10) unsigned NOT NULL,
    `reference` varchar(200) NOT NULL,
    `id_customer` int(10) unsigned NOT NULL,
    `id_currency` int(10) unsigned NOT NULL,
    `total_paid` decimal(20,6) NOT NULL,
    `outstanding_amount` decimal(20,6) NOT NULL,
    `order_state` int(10) unsigned NOT NULL,
    `date_order`  date NOT NULL,
    `date_limit`  date NOT NULL,
    `paid`  tinyint(1) unsigned NOT NULL,
    `mail_send`  tinyint(1) unsigned NOT NULL,
    `id_shop` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id_lps_defered_payments`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;';
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
return true;
