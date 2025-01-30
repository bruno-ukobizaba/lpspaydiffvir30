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

class Lpspaydiffvir30RemindermailModuleFrontController extends ModuleFrontController
{
    public $auth = false;

    public $guestAllowed = false;

    public $display_column_left = false;

    public $display_column_right = false;

    public function display()
    {
        $LPS_PAY_DIFFVIR30_TOKEN = Configuration::get('LPS_PAY_DIFFVIR30_TOKEN');
        $id_shop = Tools::getValue('id_shop');
        if ((!Tools::getValue('LPS_PAY_DIFFVIR30_TOKEN') && $LPS_PAY_DIFFVIR30_TOKEN != Tools::getValue('LPS_PAY_DIFFVIR30_TOKEN'))
            || !$this->module->active
            || !$id_shop
        ) {
            echo 'Bad token';
        } else {
            $this->module->sendReminderMail($id_shop);
            echo 'end';
        }
    }
}
