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
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'lpspaydiffvir30/classes/lpspaydiffvir30listclass.php';

class LpsPayDiffVir30 extends PaymentModule
{
    public $errors = [];

    public $html;

    public $extra_mail_vars;

    public $template_mail;

    public $templates;

    public function __construct()
    {
        $this->name = 'lpspaydiffvir30';
        $this->tab = 'payments_gateways';
        $this->version = '8.0.5';
        $this->author = 'Loulou66';
        $this->need_instance = 0;
        $this->module_key = 'a5bb4205946526c07d0b6126809045f1';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Paiement différé virement à 30 jours nets');
        $this->description = $this->l('This module allows you to accept payment by Deferred payment.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = ['min' => '1.7.0', 'max' => _PS_VERSION_];
        $this->context = Context::getContext();
        $this->canonicalPath = Tools::getHttpHost(true) . $this->context->shop->physical_uri;
        $this->controllers = ['validation'];
        $this->is_eu_compatible = 1;
        $this->configs = [
            'LPS_PAY_DIFFVIR30_TYPE_PAYMENT' => 0,
            'LPS_PAY_DIFFVIR30_DAYS' => 30,
            'LPS_PAY_DIFFVIR30_REMINDER_DAYS' => 7,
            'LPS_PAY_DIFFVIR30_MERCHANT_MAIL' => Configuration::get('PS_SHOP_EMAIL'),
            'LPS_PAY_DIFFVIR30_CUSTMER_GROUP' => json_encode([3]),
            'LPS_PAY_DIFFVIR30_STATUT_ORDER' => 2,
            'LPS_PAY_DIFFVIR30_ALLOW_BW' => 0,
            'LPS_PAY_DIFFVIR30_BW_OWNER' => null,
            'LPS_PAY_DIFFVIR30_BW_DETAILS' => null,
            'LPS_PAY_DIFFVIR30_BW_ADDRESS' => null,
            'LPS_PAY_DIFFVIR30_ALLOW_CH' => 0,
            'LPS_PAY_DIFFVIR30_CH_NAME' => null,
            'LPS_PAY_DIFFVIR30_CH_ADDRESS' => null,
        ];
        $this->templates = [$this->name, 'lpspaydiffvir30_bw_ch', 'lpspaydiffvir30_bw', 'lpspaydiffvir30_ch'];
        $datas_mail = LpsPayDiffVir30ListClass::getDatasMail();
        $this->template_mail = $datas_mail['template_mail'];
        $this->extra_mail_vars = $datas_mail['extra_mail_vars'];
    }
    public function install()
    {
        $install = true;
        $install &= include dirname(__FILE__) . '/sql/install.php';
        $shops = Shop::getShops(true);
        foreach ($shops as $shop) {
            foreach ($this->configs as $key => $val) {
                $install &= Configuration::updateValue($key, $val, false, $shop['id_shop_group'], $shop['id_shop']);
            }
        }
        return $install
            && parent::install()
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->registerHook('paymentOptions')
            && $this->registerHook('paymentReturn')
            && $this->registerHook('displayCustomerAccount')
            && $this->registerHook('actionOrderStatusUpdate')
            && $this->registerHook('actionValidateOrder')
            && $this->addOrderStates()
            && $this->addTabs()
            && $this->addMeta();
    }
    public function uninstall()
    {
        $uninstall = true;
        $uninstall &= include dirname(__FILE__) . '/sql/uninstall.php';
        foreach ($this->configs as $key => $val) {
            $val = $key;
            $uninstall &= Configuration::deleteByName($val);
        }
        return $uninstall
            && parent::uninstall()
            && $this->delTabs()
            && $this->delMeta();
    }
    public function addOrderStates()
    {
        $addOrderStates = true;
        if (!Configuration::hasKey('LPS_OS_LPSPAYDIFFVIR30')) {
            $orderStates = new OrderState();
            $orderStates->module_name = $this->name;
            $orderStates->send_email = true;
            $orderStates->color = '#4169E1';
            foreach (Language::getLanguages(true) as $lang) {
                if ($lang['iso_code'] == 'fr') {
                    $orderStates->name[$lang['id_lang']] = 'Paiement différé en attente';
                } else {
                    $orderStates->name[$lang['id_lang']] = 'Deferred payment pending';
                }
                $orderStates->template[$lang['id_lang']] = $this->name;
            }
            $addOrderStates &= $orderStates->save();
            $addOrderStates &= Configuration::updateValue('LPS_OS_LPSPAYDIFFVIR30', $orderStates->id);
            $source = _PS_MODULE_DIR_ . $this->name . '/views/img/os/pending.gif';
            $destination = _PS_ROOT_DIR_ . '/img/os/' . $orderStates->id . '.gif';
            $addOrderStates &= Tools::copy($source, $destination);
        }
        return $addOrderStates;
    }
    public function addMeta()
    {
        $addMeta = true;
        $meta_pages = [
            [
                'page' => 'module-lpspaydiffvir30-remindermail',
                'title' => [
                    'en' => 'Cronjob reminder mail',
                    'fr' => 'Cronjob reminder mail',
                ],
                'url_rewrite' => [
                    'en' => 'remindermail',
                    'fr' => 'remindermail',
                ],
            ],
            [
                'page' => 'module-lpspaydiffvir30-lpspaydiffvir30customeraccount',
                'title' => [
                    'en' => 'Deferred payment',
                    'fr' => 'Paiement différé',
                ],
                'url_rewrite' => [
                    'en' => 'deferred-payment',
                    'fr' => 'paiement-differe',
                ],
            ],
        ];
        foreach ($meta_pages as $page) {
            $existMeta = Meta::getMetaByPage($page['page'], (int) $this->context->language->id);
            if (!$existMeta) {
                $meta = new Meta();
                $meta->page = $page['page'];
                foreach (Language::getLanguages(false) as $lang) {
                    if ($lang['iso_code'] == 'fr') {
                        $meta->title[(int) $lang['id_lang']] = $page['title']['fr'];
                        $meta->url_rewrite[(int) $lang['id_lang']] = $page['url_rewrite']['fr'];
                    } else {
                        $meta->title[(int) $lang['id_lang']] = $page['title']['en'];
                        $meta->url_rewrite[(int) $lang['id_lang']] = $page['url_rewrite']['en'];
                    }
                }
                $addMeta &= $meta->save();
            }
        }
        return $addMeta;
    }
    private function delMeta()
    {
        $delMeta = true;
        $metas = Meta::getMetas();
        $meta_pages = [
            'module-lpspaydiffvir30-remindermail',
            'module-lpspaydiffvir30-lpspaydiffvir30customeraccount',
        ];
        foreach ($metas as $meta) {
            foreach ($meta_pages as $page) {
                if ($meta['page'] === $page) {
                    $m = new Meta((int) $meta['id_meta']);
                    $delMeta &= $m->delete();
                }
            }
        }
        return $delMeta;
    }
    public function addTabs()
    {
        $addTabs = true;
        $tab = new Tab();
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentOrders');
        $tab->class_name = 'AdminLpsPayDiffVir30';
        $tab->module = $this->name;
        foreach (Language::getLanguages(false) as $lang) {
            if ($lang['iso_code'] == 'fr') {
                $tab->name[(int) $lang['id_lang']] = 'Paiements différés';
            } else {
                $tab->name[(int) $lang['id_lang']] = 'Deferred payments';
            }
        }
        $addTabs &= $tab->save();
        return $addTabs;
    }
    public function delTabs()
    {
        $class_name = 'AdminLpsPayDiffVir30';
        $id_tab = Tab::getIdFromClassName($class_name);
        $delTabs = true;
        if ($id_tab) {
            $tabTemp = new Tab((int) $id_tab);
            $delTabs &= $tabTemp->delete();
        }
        return $delTabs;
    }
    public function hookActionAdminControllerSetMedia($params)
    {
        if (($this->context->controller->controller_name == 'AdminModules')
            && Tools::getValue('configure') == $this->name
        ) {
            $this->context->controller->addCSS($this->_path . 'views/css/' . $this->name . 'admin.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/js/admin/' . $this->name . 'admin.js');
        }
    }
    public function getWarningMultishopHtml()
    {
        return $this->displayWarning(
            sprintf(
                $this->l('You cannot manage %s from a "All Shops" or a "Group Shop" context.'),
                $this->displayName
            ) .
            ' ' .
            $this->l('Select directly the shop you want to edit.')
        );
    }
    public function getContent()
    {
        $this->html = '';
        if (Shop::isFeatureActive()) {
            if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
                return $this->getWarningMultishopHtml();
            }
        }
        if (!Configuration::get('LPS_PAY_DIFFVIR30_TOKEN') || Tools::isSubmit('regenerate' . $this->name . 'Token')) {
            $LPS_PAY_DIFFVIR30_TOKEN = Tools::passwdGen(20);
            Configuration::updateValue('LPS_PAY_DIFFVIR30_TOKEN', $LPS_PAY_DIFFVIR30_TOKEN);
        }
        if (Tools::isSubmit('save' . $this->name)) {
            $this->postProcess();
            if (!count($this->errors)) {
                foreach ($this->configs as $key => $val) {
                    if ($key !== 'LPS_PAY_DIFFVIR30_TOKEN') {
                        $val = Tools::getValue($key);
                        if ($key == 'LPS_PAY_DIFFVIR30_CUSTMER_GROUP') {
                            $groups = Tools::getValue('groupBox');
                            if (!Configuration::updateValue($key, json_encode($groups))) {
                                $this->l('An error occurred while attempting to save.');
                            }
                        } else {
                            if (!Configuration::updateValue($key, $val)) {
                                $this->errors['last'] = $this->l('An error occurred while attempting to save.');
                            }
                        }
                    }
                }
                if (!Tools::getValue('LPS_PAY_DIFFVIR30_ALLOW_BW')) {
                    Configuration::updateValue('LPS_PAY_DIFFVIR30_BW_OWNER', null);
                    Configuration::updateValue('LPS_PAY_DIFFVIR30_BW_DETAILS', null);
                    Configuration::updateValue('LPS_PAY_DIFFVIR30_BW_ADDRESS', null);
                } elseif (!Tools::getValue('LPS_PAY_DIFFVIR30_ALLOW_CH')) {
                    Configuration::updateValue('LPS_PAY_DIFFVIR30_CH_NAME', null);
                    Configuration::updateValue('LPS_PAY_DIFFVIR30_CH_ADDRESS', null);
                }
            }
            if (count($this->errors)) {
                $this->html .= $this->displayError($this->errors);
            } else {
                $this->html .= $this->displayConfirmation($this->l('Configuration saving'));
            }
        }
        $this->html .= $this->dispalyCronjob();
        $this->html .= $this->configform();
        return $this->html;
    }
    public function postProcess()
    {
        if (!is_array(Tools::getValue('groupBox'))) {
            $this->errors[] = $this->l('You must select at least one customer group.');
        }
        foreach ($this->configs as $key => $val) {
            $val = Tools::getValue($key);
            if ($key == 'LPS_PAY_DIFFVIR30_DAYS'
                && (!Validate::isInt((int) Tools::getValue('LPS_PAY_DIFFVIR30_DAYS'))
                    || (int) Tools::getValue('LPS_PAY_DIFFVIR30_DAYS') <= 0)
            ) {
                $this->errors[] = $this->l(
                    'The « Number of days (deferred payment) » field is invalid. Please enter a positive number.'
                );
            }
            if ($key == 'LPS_PAY_DIFFVIR30_REMINDER_DAYS'
                && (!Validate::isInt((int) Tools::getValue('LPS_PAY_DIFFVIR30_REMINDER_DAYS'))
                    || (int) Tools::getValue('LPS_PAY_DIFFVIR30_REMINDER_DAYS') <= 0)
            ) {
                $this->errors[] = $this->l(
                    'The « Number of days (reminder mail) » field is invalid. Please enter a positive number.'
                );
            }
            if ($key == 'LPS_PAY_DIFFVIR30_MERCHANT_MAIL' && !$val) {
                $this->errors[] = $this->l('You must select at least one merchant mail.');
            }
            if (Tools::getValue('LPS_PAY_DIFFVIR30_ALLOW_BW')) {
                if ($key == 'LPS_PAY_DIFFVIR30_BW_OWNER' && !$val) {
                    $this->errors[] = $this->l('Account owner is required.');
                }
                if ($key == 'LPS_PAY_DIFFVIR30_BW_DETAILS' && !$val) {
                    $this->errors[] = $this->l('Account details are required.');
                }
            }
            if (Tools::getValue('LPS_PAY_DIFFVIR30_ALLOW_CH')) {
                if ($key == 'LPS_PAY_DIFFVIR30_CH_NAME' && !$val) {
                    $this->errors[] = $this->l('The "Pay to the order of" field is required.');
                }
                if ($key == 'LPS_PAY_DIFFVIR30_CH_ADDRESS' && !$val) {
                    $this->errors[] = $this->l('The "Address" field is required.');
                }
            }
        }
    }
    public function dispalyCronjob()
    {
        $tokenAdmin = Tools::getAdminTokenLite('AdminModules');
        $LPS_PAY_DIFFVIR30_TOKEN = Configuration::get('LPS_PAY_DIFFVIR30_TOKEN');
        $urlCronjobphp = 'php ' .
             _PS_ROOT_DIR_ .
            '/remindermail?LPS_PAY_DIFFVIR30_TOKEN=' . $LPS_PAY_DIFFVIR30_TOKEN .
            '&id_shop=' . $this->context->shop->id;
        $urlCronjobwget = $this->canonicalPath .
            'remindermail?LPS_PAY_DIFFVIR30_TOKEN=' . $LPS_PAY_DIFFVIR30_TOKEN .
            '&id_shop=' . $this->context->shop->id;
        $module_url = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name .
            '&tab_module=' . $this->tab .
            '&tab_module=' . $this->tab .
            '&module_name=' . $this->name .
            '&token=' . Tools::getAdminTokenLite('AdminModules');
        $modulecronIsEnable = Module::isEnabled('cronjobs');
        $pathModuleCronjob = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=cronjobs&tab_module=Administration&module_name=cronjobs&token=' . $tokenAdmin;
        $this->context->smarty->assign(
            [
                'urlCronjobphp' => $urlCronjobphp,
                'regenerateTokenModule' => 'regenerate' . $this->name . 'Token',
                'urlCronjobwget' => $urlCronjobwget,
                'module_url' => $module_url,
                'modulecronIsEnable' => $modulecronIsEnable,
                'pathModuleCronjob' => $pathModuleCronjob,
            ]
        );
        return $this->display(__FILE__, 'views/templates/admin/displaycronjob.tpl');
    }
    public function configform()
    {
        $type_payments = [
            ['id' => 0, 'name' => $this->l('Days +')],
            ['id' => 1, 'name' => $this->l('End Month +')],
            ['id' => 2, 'name' => $this->l('End Month to')],
        ];
        $list_groups = Group::getGroups($this->context->language->id, $this->context->shop->id);
        foreach ($list_groups as $key => $group) {
            if ($group['id_group'] === Configuration::get('PS_GUEST_GROUP')
                || $group['id_group'] === Configuration::get('PS_UNIDENTIFIED_GROUP')) {
                unset($list_groups[$key]);
            }
        }
        $list_statuts = OrderState::getOrderStates($this->context->language->id);
        foreach ($list_statuts as $key => $list_statut) {
            if ((int) $list_statut['id_order_state'] == (int) Configuration::get('LPS_OS_LPSPAYDIFFVIR30')) {
                unset($list_statuts[$key]);
            }
        }
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->l('Type of payment'),
                        'name' => 'LPS_PAY_DIFFVIR30_TYPE_PAYMENT',
                        'desc' => $this->l('Select the type of payment'),
                        'hint' => $this->l('Select the type of payment'),
                        'options' => [
                            'query' => $type_payments,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Number of days (deferred payment)'),
                        'name' => 'LPS_PAY_DIFFVIR30_DAYS',
                        'suffix' => $this->l('days'),
                        'hint' => $this->l('Enter number of days (deferred payment)'),
                        'desc' => $this->l('Enter number of days (deferred payment)'),
                        'class' => 'fixed-width-xxl',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Number of days (reminder mail)'),
                        'name' => 'LPS_PAY_DIFFVIR30_REMINDER_DAYS',
                        'suffix' => $this->l('days'),
                        'hint' => $this->l('Enter number of days (reminder mail)'),
                        'desc' => $this->l('Enter number of days (reminder mail)'),
                        'class' => 'fixed-width-xxl',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Merchant mail(s)'),
                        'name' => 'LPS_PAY_DIFFVIR30_MERCHANT_MAIL',
                        'desc' => $this->l('Enter Marchant mail(s) separate by coma'),
                        'hint' => $this->l('Enter Marchant mail(s) separate by coma'),
                    ],
                    [
                        'type' => 'group',
                        'label' => $this->l('Customer group'),
                        'name' => 'groupBox',
                        'hint' => $this->l('Select all the groups professional accounts'),
                        'desc' => $this->l('Select all the groups professional accounts'),
                        'values' => $list_groups,
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Statut order paid'),
                        'name' => 'LPS_PAY_DIFFVIR30_STATUT_ORDER',
                        'desc' => $this->l('Select status for paid orders'),
                        'hint' => $this->l('Select status for paid orders'),
                        'options' => [
                            'query' => $list_statuts,
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Allow bank wire'),
                        'name' => 'LPS_PAY_DIFFVIR30_ALLOW_BW',
                        'desc' => $this->l('Allow bank wire for order paid'),
                        'hint' => $this->l('Allow bank wire for order paid'),
                        'form_group_class' => 'LPS_PAY_DIFFVIR30_ALLOW_BW',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Account owner'),
                        'name' => 'LPS_PAY_DIFFVIR30_BW_OWNER',
                        'desc' => $this->l('Enter account owner'),
                        'hint' => $this->l('Enter account owner'),
                        'form_group_class' => 'LPS_PAY_DIFFVIR30_BW_OWNER',
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Details'),
                        'name' => 'LPS_PAY_DIFFVIR30_BW_DETAILS',
                        'desc' => $this->l('Such as bank branch, IBAN number, BIC, etc.'),
                        'hint' => $this->l('Such as bank branch, IBAN number, BIC, etc.'),
                        'form_group_class' => 'LPS_PAY_DIFFVIR30_BW_DETAILS',
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Bank address'),
                        'name' => 'LPS_PAY_DIFFVIR30_BW_ADDRESS',
                        'desc' => $this->l('Enter bank address'),
                        'hint' => $this->l('Enter bank address'),
                        'form_group_class' => 'LPS_PAY_DIFFVIR30_BW_ADDRESS',
                    ],
                    [
                        'type' => 'switch',
                        'is_bool' => true,
                        'label' => $this->l('Allow cheque'),
                        'name' => 'LPS_PAY_DIFFVIR30_ALLOW_CH',
                        'desc' => $this->l('Allow bank wire for order paid'),
                        'hint' => $this->l('Allow bank wire for order paid'),
                        'form_group_class' => 'LPS_PAY_DIFFVIR30_ALLOW_CH',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Pay to the order of (name)'),
                        'name' => 'LPS_PAY_DIFFVIR30_CH_NAME',
                        'desc' => $this->l('Enter pay to the order of (name)'),
                        'hint' => $this->l('Enter pay to the order of (name)'),
                        'form_group_class' => 'LPS_PAY_DIFFVIR30_CH_NAME',
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Address'),
                        'name' => 'LPS_PAY_DIFFVIR30_CH_ADDRESS',
                        'desc' => $this->l('Address where the check should be sent to.'),
                        'hint' => $this->l('Address where the check should be sent to.'),
                        'form_group_class' => 'LPS_PAY_DIFFVIR30_CH_ADDRESS',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save Settings'),
                    'name' => 'save' . $this->name,
                    'class' => 'button btn btn-default',
                    'icon' => 'process-icon-save',
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->textarea_autosize = true;
        $helper->submit_action = 'save' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex .
            '&configure=' . $this->name .
            '&tab_module=front_office_features&module_name=' . $this->name;
        $helper->show_toolbar = false;
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];
        return $helper->generateForm([$fields_form]);
    }
    protected function getConfigFormValues()
    {
        $fields_value = [];
        $groups_list = Group::getGroups($this->context->language->id, $this->context->shop->id);
        foreach ($groups_list as $key => $group) {
            if ($group['id_group'] === Configuration::get('PS_GUEST_GROUP')
                || $group['id_group'] === Configuration::get('PS_UNIDENTIFIED_GROUP')
            ) {
                unset($groups_list[$key]);
            }
        }
        foreach ($this->configs as $key => $val) {
            $val = Configuration::get($key);
            $fields_value[$key] = $val;
        }
        $LPS_PAY_DIFFVIR30_CUSTMER_GROUP = json_decode(Configuration::get('LPS_PAY_DIFFVIR30_CUSTMER_GROUP'));
        if (empty($LPS_PAY_DIFFVIR30_CUSTMER_GROUP)) {
            $LPS_PAY_DIFFVIR30_CUSTMER_GROUP = [];
        }
        foreach ($groups_list as $group) {
            if (in_array($group['id_group'], $LPS_PAY_DIFFVIR30_CUSTMER_GROUP)) {
                $displaygroup = 1;
            } else {
                $displaygroup = 0;
            }
            $fields_value['groupBox_' . $group['id_group']] = $displaygroup;
        }
        return $fields_value;
    }
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        $display = false;
        $customerGroups = Customer::getGroupsStatic($this->context->customer->id);
        $LPS_PAY_DIFFVIR30_CUSTMER_GROUP = json_decode(Configuration::get('LPS_PAY_DIFFVIR30_CUSTMER_GROUP'));
        foreach ($customerGroups as $group) {
            foreach ($LPS_PAY_DIFFVIR30_CUSTMER_GROUP as $customer_group) {
                if ((int) $group == (int) $customer_group) {
                    $display = true;
                    break;
                }
            }
        }
        if ($display) {
            $cart = $this->context->cart;
            $total = sprintf('%s', Tools::displayPrice($cart->getOrderTotal(true, Cart::BOTH)));
            $all_datas = array_merge(['lpsdp_total' => $total], $this->getVarInfos());
            $this->context->smarty->assign($all_datas);
            $setAdditionalInformation = $this->fetch('module:lpspaydiffvir30/views/templates/hook/lpspaydiffvir30_intro.tpl');
            $setAction = $this->context->link->getModuleLink($this->name, 'validation', [], true);
            $lpsdp_Option = new PaymentOption();
            $lpsdp_Option->setModuleName($this->displayName)
                ->setCallToActionText($this->l('Pay by deferred payment'))
                ->setAction($setAction)
                ->setAdditionalInformation($setAdditionalInformation);
            return [$lpsdp_Option];
        }
    }
    public function getVarInfos()
    {
        $LPS_PAY_DIFFVIR30_ALLOW_BW = (int) Configuration::get('LPS_PAY_DIFFVIR30_ALLOW_BW');
        $LPS_PAY_DIFFVIR30_ALLOW_CH = (int) Configuration::get('LPS_PAY_DIFFVIR30_ALLOW_CH');
        $datas_bw = [];
        $datas_ch = [];
        $datas = [
            'lpsdp_number_days' => (int) Configuration::get('LPS_PAY_DIFFVIR30_DAYS'),
            'lpsdp_type_payments' => ((int) Configuration::get('LPS_PAY_DIFFVIR30_TYPE_PAYMENT') + 1),
            'lpsdp_allow_bw' => (int) Configuration::get('LPS_PAY_DIFFVIR30_ALLOW_BW'),
            'lpsdp_allow_ch' => (int) Configuration::get('LPS_PAY_DIFFVIR30_ALLOW_CH'),
        ];
        if ($LPS_PAY_DIFFVIR30_ALLOW_BW) {
            $datas_bw = [
            'lpsdp_bw_owner' => Configuration::get('LPS_PAY_DIFFVIR30_BW_OWNER'),
            'lpsdp_bw_details' => Tools::nl2br(Configuration::get('LPS_PAY_DIFFVIR30_BW_DETAILS')),
            'lpsdp_bw_address' => Tools::nl2br(Configuration::get('LPS_PAY_DIFFVIR30_BW_ADDRESS')),
            ];
        }
        if ($LPS_PAY_DIFFVIR30_ALLOW_CH) {
            $datas_ch = [
            'lpsdp_ch_name' => Configuration::get('LPS_PAY_DIFFVIR30_CH_NAME'),
            'lpsdp_ch_address' => Tools::nl2br(Configuration::get('LPS_PAY_DIFFVIR30_CH_ADDRESS')),
            ];
        }
        $full_datas = array_merge($datas, $datas_bw, $datas_ch);
        return $full_datas;
    }
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }
        $state = $params['order']->getCurrentState();
        if (in_array(
            $state,
            [
                Configuration::get('LPS_OS_LPSPAYDIFFVIR30'),
                Configuration::get('PS_OS_OUTOFSTOCK'),
                Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'),
            ]
        )) {
            $lpsdp_date_limit = LpsPayDiffVir30ListClass::getDeferedPaymentByIDOrder(
                $params['order']->id,
                $this->context->shop->id
            );
            $total = sprintf(
                '%s',
                Tools::displayPrice(
                    $params['order']->getOrdersTotalPaid(),
                    new Currency($params['order']->id_currency),
                    false
                )
            );
            $datas = [
                'lpsdp_status' => 'ok',
                'lpsdp_id_order' => $params['order']->id,
                'lpsdp_date_limit' => Tools::displayDate($lpsdp_date_limit->date_limit),
                'lpsdp_reference' => $params['order']->reference,
                'lpsdp_total' => $total,
                'shop_name' => $this->context->shop->name,
            ];
            $all_datas = array_merge($datas, $this->getVarInfos());
            $this->context->smarty->assign($all_datas);
        } else {
            $this->smarty->assign('lpsdp_status', 'failed');
        }
        return $this->fetch('module:lpspaydiffvir30/views/templates/hook/payment_return.tpl');
    }
    public function checkCurrency($cart)
    {
        $currency_order = new Currency((int) $cart->id_currency);
        $currencies_module = $this->getCurrency((int) $cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }
    public function hookDisplayCustomerAccount()
    {
        $customerGroups = Customer::getGroupsStatic($this->context->customer->id);
        $LPS_PAY_DIFFVIR30_CUSTMER_GROUP = json_decode(Configuration::get('LPS_PAY_DIFFVIR30_CUSTMER_GROUP'));
        $display = false;
        foreach ($customerGroups as $group) {
            foreach ($LPS_PAY_DIFFVIR30_CUSTMER_GROUP as $customer_group) {
                if ((int) $group == (int) $customer_group) {
                    $display = true;
                    break;
                }
            }
        }
        if ($display) {
            return $this->display(dirname(__FILE__), 'lpspaydiffvir30customeraccount.tpl');
        }
    }
    public function hookActionOrderStatusUpdate($params)
    {
        $DeferedPayment = LpsPayDiffVir30ListClass::getDeferedPaymentByIDOrder(
            $params['id_order'],
            $this->context->shop->id
        );
        if (Validate::isLoadedObject($DeferedPayment)) {
            $DeferedPayment->state = $params['newOrderStatus']->id;
            if ($params['newOrderStatus']->id == (int) Configuration::get('LPS_PAY_DIFFVIR30_STATUT_ORDER')) {
                $DeferedPayment->paid = true;
                $DeferedPayment->order_state = (int) Configuration::get('LPS_PAY_DIFFVIR30_STATUT_ORDER');
            } else {
                $DeferedPayment->paid = false;
                $DeferedPayment->order_state = $params['newOrderStatus']->id;
            }
            $DeferedPayment->save();
        }
    }
    public function sendReminderMail($id_shop)
    {
        $allOrdersWaitingPaiement = LpsPayDiffVir30ListClass::getAllOrdersWaitingPaiement((int) $id_shop);
        $res = true;
        $reinder_date = Configuration::get('LPS_PAY_DIFFVIR30_REMINDER_DAYS');
        $shop_lang = Configuration::get('PS_LANG_DEFAULT');
        foreach ($allOrdersWaitingPaiement as $orderWP) {
            $check_date = date('Y-m-d', strtotime($orderWP['date_limit'] . '-' . $reinder_date . ' days'));
            if (date('Y-m-d') == $check_date) {
                $order = new Order((int) $orderWP['id_order']);
                $currency = new Currency((int) $order->id_currency);
                $merchant_mails = Configuration::get('LPS_PAY_DIFFVIR30_MERCHANT_MAIL');
                $merchant_mails = explode(',', $merchant_mails);
                $customer = new Customer((int) $orderWP['id_customer']);
                array_push($merchant_mails, $customer->email);
                foreach ($merchant_mails as $email) {
                    if ($email == $customer->email) {
                        $id_lang = (int) $order->id_lang;
                        $template = 'lpspaydiffvir30_reminder_customer';
                    } else {
                        $id_lang = (int) $shop_lang;
                        $template = 'lpspaydiffvir30_reminder_merchant';
                    }
                    $subject = sprintf(
                        $this->ltrans('Payment Reminder Order : %s', $id_lang),
                        $order->reference
                    );
                    $datas = [
                        '{firstname}' => $customer->firstname,
                        '{lastname}' => $customer->lastname,
                        '{reference}' => $order->reference,
                        '{date_order}' => Tools::displayDate($orderWP['date_order']),
                        '{total_paid}' => Tools::displayPrice($orderWP['total_paid'], $currency),
                        '{date_limit}' => Tools::displayDate($orderWP['date_limit']),
                        '{company}' => $customer->company,
                    ];
                    if (!Mail::Send(
                        (int) $id_lang,
                        $template,
                        $subject,
                        $datas,
                        $email,
                        $customer->firstname . ' ' . $customer->lastname,
                        null,
                        null,
                        null,
                        null,
                        dirname(__FILE__) . '/mails/',
                        false,
                        (int) $id_shop
                    )) {
                        $res &= false;
                    }
                }
                if ($res) {
                    $LpsPayDiffVir30ListClass = new LpsPayDiffVir30ListClass($orderWP['id_lps_defered_payments']);
                    $LpsPayDiffVir30ListClass->mail_send = true;
                    $LpsPayDiffVir30ListClass->update();
                }
            }
        }
    }
    public function hookActionValidateOrder($params)
    {
        $orderStatus = $params['orderStatus'];
        if ((int) $orderStatus->id == (int) Configuration::get('LPS_OS_LPSPAYDIFFVIR30')) {
            $context = Context::getContext();
            $order = $params['order'];
            $cart = $params['cart'];
            $orderStatus = $params['orderStatus'];
            $id_shop = (int) $context->shop->id;
            $LpsPayDiffVir30ListClass = new LpsPayDiffVir30ListClass();
            $LpsPayDiffVir30ListClass->id_order = $order->id;
            $LpsPayDiffVir30ListClass->reference = $order->reference;
            $LpsPayDiffVir30ListClass->id_customer = $order->id_customer;
            $LpsPayDiffVir30ListClass->id_currency = $order->id_currency;
            $LpsPayDiffVir30ListClass->total_paid = (float) $cart->getOrderTotal(true, Cart::BOTH);
            $LpsPayDiffVir30ListClass->order_state = (int) $orderStatus->id;
            $LpsPayDiffVir30ListClass->color_state = $orderStatus->color;
            $LpsPayDiffVir30ListClass->date_order = $order->date_add;
            $LpsPayDiffVir30ListClass->date_limit = LpsPayDiffVir30ListClass::getDateLimit($order->date_add);
            $LpsPayDiffVir30ListClass->paid = false;
            $LpsPayDiffVir30ListClass->id_shop = (int) $id_shop;
            $LpsPayDiffVir30ListClass->add();
        }
    }
    public function ltrans($string, $id_lang = null)
    {
        $_MODULE = [];
        $source = $this->name;
        if (null !== $id_lang) {
            $iso = Language::getIsoById((int) $id_lang);
        } else {
            $iso = Language::getIsoById((int) $this->context->language->id);
        }
        $name = $this->name;
        $filesByPriority = [
            _PS_MODULE_DIR_ . $name . '/translations/' . $iso . '.php',
            _PS_THEME_DIR_ . 'modules/' . $name . '/' . $iso . '.php',
            _PS_THEME_DIR_ . 'modules/' . $name . '/translations/' . $iso . '.php',
        ];
        foreach ($filesByPriority as $file) {
            if (file_exists($file)) {
                include $file;
            }
        }
        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);
        $currentKey = Tools::strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $source) . '_' . $key;
        $defaultKey = Tools::strtolower('<{' . $name . '}prestashop>' . $source) . '_' . $key;
        if (isset($_MODULE[$currentKey])) {
            $ret = Tools::stripslashes($_MODULE[$currentKey]);
        } elseif (isset($_MODULE[$defaultKey])) {
            $ret = Tools::stripslashes($_MODULE[$defaultKey]);
        } else {
            $ret = $string;
        }
        return $ret;
    }
    public function transStringMail()
    {
        $this->l('Payment Reminder Order : %s');
    }
    public function isUsingNewTranslationSystem()
    {
        return false;
    }
}
