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

class OrderHistoryVir30 extends OrderHistoryCore
{
    public function sendEmail($order, $template_vars = false)
    {
        $result = Db::getInstance()->getRow('
            SELECT osl.`template`, c.`lastname`, c.`firstname`, osl.`name` AS osname, c.`email`, os.`module_name`, os.`id_order_state`, os.`pdf_invoice`, os.`pdf_delivery`
            FROM `' . _DB_PREFIX_ . 'order_history` oh
                LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON oh.`id_order` = o.`id_order`
                LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON o.`id_customer` = c.`id_customer`
                LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON oh.`id_order_state` = os.`id_order_state`
                LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = o.`id_lang`)
            WHERE oh.`id_order_history` = ' . (int) $this->id . ' AND os.`send_email` = 1');
        if (isset($result['template']) && Validate::isEmail($result['email'])) {
            ShopUrl::cacheMainDomainForShop($order->id_shop);
            $topic = $result['osname'];
            $carrierUrl = '';
            if (Validate::isLoadedObject($carrier = new Carrier((int) $order->id_carrier, $order->id_lang))) {
                $carrierUrl = $carrier->url;
            }
            $data = [
                '{lastname}' => $result['lastname'],
                '{firstname}' => $result['firstname'],
                '{id_order}' => (int) $this->id_order,
                '{order_name}' => $order->getUniqReference(),
                '{followup}' => str_replace('@', $order->getWsShippingNumber() ?? '', $carrierUrl),
                '{shipping_number}' => $order->getWsShippingNumber(),
            ];
            $path_mails = _PS_MAIL_DIR_;
            $template_mail = $result['template'];
            if ($result['module_name']) {
                $module = Module::getInstanceByName($result['module_name']);
                if (Validate::isLoadedObject($module)
                    && isset($module->extra_mail_vars)
                    && is_array($module->extra_mail_vars)
                ) {
                    $data = array_merge($data, $module->extra_mail_vars);
                }
                $template_mail = (isset($module->template_mail) && $module->template_mail)
                    ? $module->template_mail
                    : $result['template'];
                if ($module->name == 'lpspaydiffvir30') {
                    $path_mails = _PS_MODULE_DIR_ . 'lpspaydiffvir30/mails/';
                }
            }
            if (is_array($template_vars)) {
                $data = array_merge($data, $template_vars);
            }
            $context = Context::getContext();
            $data['{total_paid}'] = Tools::getContextLocale($context)->formatPrice((float) $order->total_paid, Currency::getIsoCodeById((int) $order->id_currency));
            if (Validate::isLoadedObject($order)) {
                if ($result['pdf_invoice'] || $result['pdf_delivery']) {
                    $currentLanguage = $context->language;
                    $orderLanguage = new Language((int) $order->id_lang);
                    $context->language = $orderLanguage;
                    $context->getTranslator()->setLocale($orderLanguage->locale);
                    $invoice = $order->getInvoicesCollection();
                    $file_attachement = [];
                    if ($result['pdf_invoice'] && (int) Configuration::get('PS_INVOICE') && $order->invoice_number) {
                        Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => $invoice]);
                        $pdf = new PDF($invoice, PDF::TEMPLATE_INVOICE, $context->smarty);
                        $file_attachement['invoice']['content'] = $pdf->render(false);
                        $file_attachement['invoice']['name'] = Configuration::get('PS_INVOICE_PREFIX', (int) $order->id_lang, null, $order->id_shop) . sprintf('%06d', $order->invoice_number) . '.pdf';
                        $file_attachement['invoice']['mime'] = 'application/pdf';
                    }
                    if ($result['pdf_delivery'] && $order->delivery_number) {
                        $pdf = new PDF($invoice, PDF::TEMPLATE_DELIVERY_SLIP, $context->smarty);
                        $file_attachement['delivery']['content'] = $pdf->render(false);
                        $file_attachement['delivery']['name'] = Configuration::get('PS_DELIVERY_PREFIX', (int) $order->id_lang, null, $order->id_shop) . sprintf('%06d', $order->delivery_number) . '.pdf';
                        $file_attachement['delivery']['mime'] = 'application/pdf';
                    }
                    $context->language = $currentLanguage;
                    $context->getTranslator()->setLocale($currentLanguage->locale);
                } else {
                    $file_attachement = null;
                }
                if (!Mail::Send(
                    (int) $order->id_lang,
                    $template_mail,
                    $topic,
                    $data,
                    $result['email'],
                    $result['firstname'] . ' ' . $result['lastname'],
                    null,
                    null,
                    $file_attachement,
                    null,
                    $path_mails,
                    false,
                    (int) $order->id_shop
                )) {
                    return false;
                }
            }
            ShopUrl::resetMainDomainCache();
        }
        return true;
    }
}
