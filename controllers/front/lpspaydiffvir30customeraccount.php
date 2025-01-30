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

require_once _PS_MODULE_DIR_ . 'lpspaydiffvir30/classes/lpspaydiffvir30listclass.php';

class LpsPayDiffVir30CustomerAccountModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        if (!Context::getContext()->customer->isLogged()) {
            Tools::redirect(
                'index.php?controller=authentication&redirect=module&module=lpspaydiffvir30&action=LpsPayDiffVir30CustomerAccount'
            );
        }
        $ordersWaitingPaiement = LpsPayDiffVir30ListClass::getOrdersWaitingPaiement(
            (int) $this->context->customer->id,
            (int) $this->context->shop->id,
            (int) $this->context->language->id
        );
        $state = new OrderState((int) Configuration::get('LPS_OS_LPSPAYDIFFVIR30'));
        if (is_object($state)) {
            $state_name = $state->name[(int) $this->context->language->id];
        } else {
            $state_name = $state->name;
        }
        $this->context->smarty->assign(
            [
                'ordersWaitingPaiement' => $ordersWaitingPaiement,
                'state_name' => $state_name,
            ]
        );
        $this->setTemplate('module:lpspaydiffvir30/views/templates/front/lpspaydiffvir30customerview.tpl');
    }
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = [
            'title' => $this->l('Deferred payments 2'),
            'url' => '',
        ];
        return $breadcrumb;
    }
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS([_PS_MODULE_DIR_ . 'lpspaydiffvir30/views/css/lpspaydiffvir30.css']);
        return true;
    }
    public function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('lpspaydiffvir30', $string, 'lpspaydiffvir30customeraccount');
    }
}
