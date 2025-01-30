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
<div class="panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i>
        {l s='Cronjob' mod='lpspaydiffvir30'}
    </div>
    <div class="alert alert-info">
        {l s='You must install a cronjob to automatically send reminder emails.' mod='lpspaydiffvir30'}
        <br><br>
        <span class="label label-default">{l s='Cronjob' mod='lpspaydiffvir30'}</span>
        <code>{$urlCronjobphp|escape:'htmlall':'UTF-8'}</code>
        <br><br>
        <span style="margin-left:30px;" class="icon-arrows-v"></span>
        {l s='Install one of the two cronjob' mod='lpspaydiffvir30'}
        <br><br>
        <span class="label label-default">{l s='Cronjob' mod='lpspaydiffvir30'}</span>
        <code>wget {$urlCronjobwget|escape:'htmlall':'UTF-8'}</code>
        <hr>
        <form action="{$module_url|escape:'htmlall':'UTF-8'}" method="post">
            <button name="{$regenerateTokenModule|escape:'htmlall':'UTF-8'}" id="{$regenerateTokenModule|escape:'htmlall':'UTF-8'}" type="submit" class="btn btn-primary">
                {l s='Renew TOKEN' mod='lpspaydiffvir30'}
            </button>
            <a class="btn btn-primary" href="{$urlCronjobwget|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Execute cron task manualy' mod='lpspaydiffvir30'}</a>
        </form>
        {if $modulecronIsEnable}
            <hr>
            {l s='You can configure the' mod='lpspaydiffvir30'}
            <a class="label label-success" href="{$pathModuleCronjob|escape:'htmlall':'UTF-8'}"  target="_blank">{l s='CRONJOB HERE' mod='lpspaydiffvir30'}</a>
        {/if}
    </div>
</div>