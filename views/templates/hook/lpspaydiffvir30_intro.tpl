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
<section>
    {if isset($lpsdp_type_payments)}
        <h5 class="black">
            <strong>
                {if $lpsdp_type_payments == 1}
                    {l s='Your payment type is %s days deferred payment.' sprintf=[$lpsdp_number_days] mod='lpspaydiffvir30'}
                {else if $lpsdp_type_payments == 2}
                    {l s='Your payment type is deferred payment end month + %s days.' sprintf=[$lpsdp_number_days] mod='lpspaydiffvir30'}
                {else if $lpsdp_type_payments == 3}
                    {l s='Your payment type is deferred payment end month to %s days.' sprintf=[$lpsdp_number_days] mod='lpspaydiffvir30'}
                {/if}
            </strong>
        </h5>
    {/if}
    {if (isset($lpsdp_allow_bw) && $lpsdp_allow_bw) || (isset($lpsdp_allow_ch) && $lpsdp_allow_ch)}
        {include file='module:lpspaydiffvir30/views/templates/hook/_partials/payment_infos.tpl'}
    {else}
        <p>
            {l s='Please transfer the invoice amount to our bank account. You will receive our order confirmation by email.' mod='lpspaydiffvir30'}
            <br />
            {l s='The total amount of your order comes to:' mod='lpspaydiffvir30'} {$lpsdp_total|escape:'htmlall':'UTF-8'}.
        </p>
    {/if}
</section>
