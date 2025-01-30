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

class AdminLpsPayDiffVir30Controller extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->lang = false;
        $this->allow_export = false;
        $this->deleted = false;
        $this->table = 'lps_defered_payments2';
        $this->id = 'id_lps_defered_payments';
        $this->className = 'LpsPayDiffVir30ListClass';
        $this->context = Context::getContext();
        $this->explicitSelect = true;
        $this->simple_header = false;
        $this->no_link = true;
        if (Shop::isFeatureActive()
            && (Shop::getContext() != Shop::CONTEXT_SHOP || Shop::getContext() != Shop::CONTEXT_ALL)
        ) {
            $this->shopLinkType = 'shop';
        } else {
            $this->shopLinkType = '';
        }
        $this->show_toolbar = false;
        $this->toolbar_title = $this->l('Deferred payments 2');
        $this->addRowAction('viewOrder');
        $this->_select .= 'CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`, c.`company`,
            os.*, osl.`name` AS lpsstate, a.`paid` AS lpspaid, a.`id_lps_defered_payments`';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (a.`order_state` = os.`id_order_state`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl
            ON (osl.`id_order_state` = os.`id_order_state` AND osl.`id_lang` =' . $this->context->language->id . ')';
        $this->_orderBy = $this->id;
        $this->_orderWay = 'DESC';
        $this->list_no_link = true;
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }
        $this->fields_list = [
            'id_order' => [
                'title' => $this->l('ID Order'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'search' => true,
            ],
            'reference' => [
                'title' => $this->l('Reference'),
                'search' => true,
            ],
            'customer' => [
                'title' => $this->l('Customer'),
                'havingFilter' => true,
                'search' => true,
            ],
            'company' => [
                'title' => $this->l('Company'),
                'search' => true,
            ],
            'total_paid' => [
                'title' => $this->l('Total Paid'),
                'type' => 'price',
                'search' => true,
                'align' => 'text-right',
            ],
            'lpsstate' => [
                'title' => $this->l('Status'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'lpsstate',
            ],
            'date_order' => [
                'title' => $this->l('Date Order'),
                'type' => 'date',
                'search' => true,
            ],
            'date_limit' => [
                'title' => $this->l('Date Limit'),
                'type' => 'date',
                'search' => true,
            ],
            'mail_send' => [
                'title' => $this->l('Reminder mail'),
                'type' => 'bool',
                'lpsactive' => 'true',
                'tmpTableFilter' => true,
                'orderby' => false,
            ],
            'lpspaid' => [
                'title' => $this->l('Paid'),
                'type' => 'bool',
                'lpsactive' => 'true',
                'tmpTableFilter' => true,
                'orderby' => false,
            ],
        ];
        parent::__construct();
    }
    public function displayViewOrderLink($token = null, $id, $name = null)
    {
        $sql = 'SELECT lpsdp.`id_order`
        FROM `' . _DB_PREFIX_ . 'lps_defered_payments2` lpsdp
        WHERE lpsdp.`id_lps_defered_payments` = ' . (int) $id;
        $id_order = Db::getInstance()->getValue($sql);
        $tpl = $this->context->smarty->createTemplate(
            _PS_ROOT_DIR_ . '/modules/lpspaydiffvir30/views/templates/admin/list_action_vieworders.tpl'
        );
        $tpl->assign(
            [
                'href' => $this->context->link->getAdminLink(
                    'AdminOrders',
                    true,
                    ['vieworder' => 1, 'id_order' => $id_order]
                ),
                'action' => $this->l('View'),
            ]
        );
        return $tpl->fetch();
    }
    public function postProcess()
    {
        parent::postProcess();
    }
    public function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('lpspaydiffvir30', $string, 'AdminLpsPayDiffVir30');
    }
}
