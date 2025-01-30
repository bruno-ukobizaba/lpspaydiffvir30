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
{extends file="helpers/list/list_content.tpl"}
{block name="td_content"}
    {if isset($params.lpstatus)}
        <span class="label color_field" style="background-color: {$tr.color|escape:'htmlall':'UTF-8'}; color:{if Tools::getBrightness($tr.color) < 128}white{else}#383838{/if}">
            {$tr.$key|escape:'htmlall':'UTF-8'}
        </span>
    {elseif isset($params.lpsactive)}
        {if $tr.$key}
            <span class="list-action-enable action-enabled">
                <i class="icon-check"></i>
            </span>
        {else}
            <span class="list-action-enable action-disabled">
                <i class="icon-remove"></i>
            </span>
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}