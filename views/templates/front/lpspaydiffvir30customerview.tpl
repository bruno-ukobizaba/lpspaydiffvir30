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
{extends file='customer/page.tpl'}
{block name='page_title'}
  {l s='Deferred payments' mod='lpspaydiffvir30'}
{/block}

{block name='page_content'}
    <h6>{l s='List of pending deferred payments' mod='lpspaydiffvir30'}</h6>
    <p class="info-title">{l s='Here you will find the list of your pending deferred payments' mod='lpspaydiffvir30'}</p>
    {if $ordersWaitingPaiement}
         <table class="table table-striped table-bordered table-labeled hidden-sm-down">
            <thead class="thead-default">
                <tr>
                    <th>{l s='Reference' mod='lpspaydiffvir30'}</th>
                    <th>{l s='Order date' mod='lpspaydiffvir30'}</th>
                    <th>{l s='Date Limit' mod='lpspaydiffvir30'}</th>
                    <th>{l s='Total Order' mod='lpspaydiffvir30'}</th>
                    <th>{l s='State' mod='lpspaydiffvir30'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$ordersWaitingPaiement item=order name=myLoop}
                    <tr>
                        <td scope="row">{$order.reference|escape:'htmlall':'UTF-8'}</td>
                        <td>{dateFormat date=$order.date_order|escape:'htmlall':'UTF-8' full=0}</td>
                        <td>{dateFormat date=$order.date_limit|escape:'htmlall':'UTF-8' full=0}</td>
                        <td>
                            <span class="price">
                                {Context::getContext()->getCurrentLocale()->formatPrice($order.total_paid|escape:'htmlall':'UTF-8', Context::getContext()->currency->iso_code)}
                            </span>
                        </td>
                        <td>
                            <span class="label label-pill {if Tools::getBrightness($order.color) > 128} dark{/if}" style="background-color:{$order.color|escape:'htmlall':'UTF-8'}; border-color:{$order.color|escape:'htmlall':'UTF-8'};">
                                {$order.name|escape:'htmlall':'UTF-8'}
                            </span>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        <div class="orders hidden-md-up">
            {foreach from=$ordersWaitingPaiement item=order name=myLoop}
                <div class="order">
                    <div class="row">
                        <div class="col-xs-10">
                            <h3>{$order.reference|escape:'htmlall':'UTF-8'}</h3>
                            <div class="date">{dateFormat date=$order.date_order|escape:'htmlall':'UTF-8' full=0}</div>
                            <div class="date">{dateFormat date=$order.date_limit|escape:'htmlall':'UTF-8' full=0}</div>
                            <div class="total">
                                {Context::getContext()->getCurrentLocale()->formatPrice($order.total_paid|escape:'htmlall':'UTF-8', Context::getContext()->currency->iso_code)}
                            </div>
                            <div class="status">
                                <span class="label label-pill {if Tools::getBrightness($order.color) > 128} dark{/if}" style="background-color:{$order.color|escape:'htmlall':'UTF-8'}; border-color:{$order.color|escape:'htmlall':'UTF-8'};">
                                    {$order.name|escape:'htmlall':'UTF-8'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    {else}
        <p class="alert alert-warning">{l s='No deferred payment pending.' mod='lpspaydiffvir30'}</p>
    {/if}
{/block}
