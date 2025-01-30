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
{if (isset($lpsdp_allow_bw) && $lpsdp_allow_bw)}
    {l s='Please send us your bank wire following these rules:' mod='lpspaydiffvir30'}
    <dl>
        <dt>{l s='Amount' mod='lpspaydiffvir30'}</dt>
        <dd>{$lpsdp_total|escape:'htmlall':'UTF-8'}</dd>
        <dt>{l s='Account owner' mod='lpspaydiffvir30'}</dt>
        <dd>{if $lpsdp_bw_owner}{$lpsdp_bw_owner|escape:'htmlall':'UTF-8'}{else}___________{/if}</dd>
        <dt>{l s='Account details' mod='lpspaydiffvir30'}</dt>
        <dd>{if $lpsdp_bw_details}{$lpsdp_bw_details nofilter}{else}___________{/if}</dd>
        {if $lpsdp_bw_address}
            <dt>{l s='Bank address' mod='lpspaydiffvir30'}</dt>
            <dd>{$lpsdp_bw_address nofilter}</dd>
        {/if}
    </dl>
{/if}
{if (isset($lpsdp_allow_bw) && $lpsdp_allow_bw) && (isset($lpsdp_allow_ch) && $lpsdp_allow_ch)}
    {l s='OR' mod='lpspaydiffvir30'}<br /><br />
{/if}
{if (isset($lpsdp_allow_ch) && $lpsdp_allow_ch)}
    {l s='Please send us your check following these rules:' mod='lpspaydiffvir30'}
    <dl>
        <dt>{l s='Amount' mod='lpspaydiffvir30'}</dt>
        <dd>{$lpsdp_total|escape:'htmlall':'UTF-8'}</dd>
        <dt>{l s='Payee' mod='lpspaydiffvir30'}</dt>
        <dd>{if $lpsdp_ch_name}{$lpsdp_ch_name|escape:'htmlall':'UTF-8'}{else}___________{/if}</dd>
        <dt>{l s='Send your check to this address' mod='lpspaydiffvir30'}</dt>
        <dd>{if $lpsdp_ch_address}{$lpsdp_ch_address nofilter}{else}___________{/if}</dd>
    </dl>
{/if}
