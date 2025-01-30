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

class LpsPayDiffVir30ListClass extends ObjectModel
{
    public $id_order;

    public $reference;

    public $id_customer;

    public $id_currency;

    public $total_paid;

    public $outstanding_amount;

    public $order_state;

    public $date_order;

    public $date_limit;

    public $paid;

    public $mail_send;

    public $id_shop;

    public static $definition = [
        'table' => 'lps_defered_payments_vir30',
        'primary' => 'id_lps_defered_payments',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'reference' => ['type' => self::TYPE_STRING],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_currency' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'total_paid' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'outstanding_amount' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
            'order_state' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'date_order' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_limit' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'paid' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'mail_send' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    public static function getOrderByIDCart($id_cart)
    {
        $sql = 'SELECT o.`id_order`
            FROM `' . _DB_PREFIX_ . 'orders` o WHERE o.`id_cart`=' . (int) $id_cart;
        $id_order = Db::getInstance()->getValue($sql);
        if ($id_order) {
            return new Order((int) $id_order);
        } else {
            return false;
        }
    }

    public static function getDeferedPaymentByIDOrder($id_order, $id_shop)
    {
        $sql = 'SELECT lpsdp.`id_lps_defered_payments`
            FROM `' . _DB_PREFIX_ . 'lps_defered_payments_vir30` lpsdp
            WHERE lpsdp.`id_order`=' . (int) $id_order . '
            AND lpsdp.`id_shop` =' . (int) $id_shop;
        $id_lps_defered_payments = Db::getInstance()->getValue($sql);
        if ($id_lps_defered_payments) {
            return new LpsPayDiffVir30ListClass($id_lps_defered_payments);
        } else {
            return false;
        }
    }

    public static function getDateLimit($date_order)
    {
        $type_payment = Configuration::get('LPS_PAY_DIFFVIR30_TYPE_PAYMENT');
        $payment_delay = Configuration::get('LPS_PAY_DIFFVIR30_DAYS');
        $date_format_lite = 'Y-m-d';
        $date_format_end_month = 'Y-m-t';
        switch ($type_payment) {
            case '0':
                // Jour +
                $new_date = date($date_format_lite, strtotime($date_order . '+' . $payment_delay . ' days'));
                return date($date_format_lite, strtotime($date_order . '+' . $payment_delay . ' days'));
            case '1':
                // fin de mois +
                $new_date = date($date_format_end_month, strtotime($date_order));
                return date($date_format_lite, strtotime($new_date . '+' . $payment_delay . ' days'));
            case '2':
                // fin de mois à
                $new_date = date($date_format_lite, strtotime($date_order . '+' . $payment_delay . ' days'));
                return date($date_format_end_month, strtotime($new_date));
            default:
                break;
        }
    }

    public static function getOrdersWaitingPaiement($id_customer, $id_shop, $id_lang)
    {
        $sql = 'SELECT lpsdp.*, os.`color`, osl.`name`
            FROM `' . _DB_PREFIX_ . 'lps_defered_payments_vir30` lpsdp
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (lpsdp.`order_state` = os.`id_order_state`)
            LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON
            (osl.`id_order_state` = os.`id_order_state` AND osl.`id_lang` =' . (int) $id_lang . ')
            WHERE lpsdp.`id_customer` =' . (int) $id_customer . '
            AND lpsdp.`order_state` =' . Configuration::get('LPS_OS_LPSPAYDIFFVIR30') . '
            AND lpsdp.`id_shop` =' . (int) $id_shop;
        return Db::getInstance()->ExecuteS($sql);
    }

    public static function getAllOrdersWaitingPaiement($id_shop)
    {
        $sql = 'SELECT lpsdp.*
            FROM `' . _DB_PREFIX_ . 'lps_defered_payments_vir30` lpsdp
            WHERE lpsdp.`mail_send` = 0
            AND lpsdp.`paid` = 0
            AND lpsdp.`id_shop` =' . (int) $id_shop;
        return Db::getInstance()->ExecuteS($sql);
    }

    public static function getDatasMail()
    {
        $datas_mail = [];
        $LPS_PAY_DIFFVIR30_ALLOW_BW = Configuration::get('LPS_PAY_DIFFVIR30_ALLOW_BW');
        $LPS_PAY_DIFFVIR30_ALLOW_CH = Configuration::get('LPS_PAY_DIFFVIR30_ALLOW_CH');
        if ($LPS_PAY_DIFFVIR30_ALLOW_BW && $LPS_PAY_DIFFVIR30_ALLOW_CH) {
            $datas_mail['template_mail'] = 'lpspaydiffvir30_bw_ch';
            $datas_mail['extra_mail_vars'] = [
                '{lpspaydiffvir30_bw_owner}' => Configuration::get('LPS_PAY_DIFFVIR30_BW_OWNER'),
                '{lpspaydiffvir30_bw_details}' => Tools::nl2br(Configuration::get('LPS_PAY_DIFFVIR30_BW_DETAILS')),
                '{lpspaydiffvir30_bw_address}' => Tools::nl2br(Configuration::get('LPS_PAY_DIFFVIR30_BW_ADDRESS')),
                '{lpspaydiffvir30_ch_name}' => Configuration::get('LPS_PAY_DIFFVIR30_CH_NAME'),
                '{lpspaydiffvir30_ch_address}' => Tools::nl2br(Configuration::get('LPS_PAY_DIFFVIR30_CH_ADDRESS')),
                '{lpspaydiffvir30_date_limit}' => Tools::displayDate(self::getDateLimit(date('Y-m-d'))),
            ];
        } elseif ($LPS_PAY_DIFFVIR30_ALLOW_BW && !$LPS_PAY_DIFFVIR30_ALLOW_CH) {
            $datas_mail['template_mail'] = 'lpspaydiffvir30_bw';
            $datas_mail['extra_mail_vars'] = [
                '{lpspaydiffvir30_bw_owner}' => Configuration::get('LPS_PAY_DIFFVIR30_BW_OWNER'),
                '{lpspaydiffvir30_bw_details}' => Tools::nl2br(Configuration::get('LPS_PAY_DIFFVIR30_BW_DETAILS')),
                '{lpspaydiffvir30_bw_address}' => Tools::nl2br(Configuration::get('LPS_PAY_DIFFVIR30_BW_ADDRESS')),
                '{lpspaydiffvir30_date_limit}' => Tools::displayDate(self::getDateLimit(date('Y-m-d'))),
            ];
        } elseif (!$LPS_PAY_DIFFVIR30_ALLOW_BW && $LPS_PAY_DIFFVIR30_ALLOW_CH) {
            $datas_mail['template_mail'] = 'lpspaydiffvir30_ch';
            $datas_mail['extra_mail_vars'] = [
                '{lpspaydiffvir30_ch_name}' => Configuration::get('LPS_PAY_DIFFVIR30_CH_NAME'),
                '{lpspaydiffvir30_ch_address}' => Tools::nl2br(Configuration::get('LPS_PAY_DIFFVIR30_CH_ADDRESS')),
                '{lpspaydiffvir30_date_limit}' => Tools::displayDate(self::getDateLimit(date('Y-m-d'))),
            ];
        } elseif (!$LPS_PAY_DIFFVIR30_ALLOW_BW && !$LPS_PAY_DIFFVIR30_ALLOW_CH) {
            $datas_mail['template_mail'] = 'lpspaydiffvir30';
            $datas_mail['extra_mail_vars'] = [
                '{lpspaydiffvir30_date_limit}' => Tools::displayDate(self::getDateLimit(date('Y-m-d'))),
            ];
        }
        return $datas_mail;
    }
}
