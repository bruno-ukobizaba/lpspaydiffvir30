{**
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
 *}
{if $lpsdp_status == 'ok'}
    <p>{l s='Your order on %s is complete.' sprintf=[$shop_name] mod='lpspaydiffvir30'}</p>
    <p>
        {if !isset($lpsdp_reference)}
           {l s='Do not forget to indicate your order number' mod='lpspaydiffvir30'} <strong>{$lpsdp_id_order|escape:'htmlall':'UTF-8'}</strong>.
        {else}
            {l s='Do not forget to indicate your order reference' mod='lpspaydiffvir30'} <strong>{$lpsdp_reference|escape:'htmlall':'UTF-8'}</strong>.
        {/if}
    </p>
    <p>
        <span style="color: red;"><strong>{l s='Your payment must reach us before %s.' sprintf=[$lpsdp_date_limit] mod='lpspaydiffvir30' }</strong></span>
        <br /> <strong>{l s='Your order will be sent as soon as we receive your payment.' mod='lpspaydiffvir30'}</strong>
        <br />{l s='If you have questions, comments or concerns, please contact our' mod='lpspaydiffvir30'} <a href="{$link->getPageLink('contact', true)|escape:'htmlall':'UTF-8'}">{l s='expert customer support team.' mod='lpspaydiffvir30'}</a>
    </p>
{else}
    <p class="alert alert-warning">
        {l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' mod='lpspaydiffvir30'}
        <a href="{$link->getPageLink('contact', true)|escape:'htmlall':'UTF-8'}">{l s='customer service department.' mod='lpspaydiffvir30'}</a>
    </p>
{/if}
