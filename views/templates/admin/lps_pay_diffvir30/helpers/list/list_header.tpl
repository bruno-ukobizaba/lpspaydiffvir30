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
{if $ajax}
	<script type="text/javascript">
		$(function () {
			$(".ajax_table_link").click(function () {
				var link = $(this);
				$.post($(this).attr('href'), function (data) {
					if (data.success == 1) {
						showSuccessMessage(data.text);
						if (link.hasClass('action-disabled')){
							link.removeClass('action-disabled').addClass('action-enabled');
						} else {
							link.removeClass('action-enabled').addClass('action-disabled');
						}
						link.children().each(function () {
							if ($(this).hasClass('hidden')) {
								$(this).removeClass('hidden');
							} else {
								$(this).addClass('hidden');
							}
						});
					} else {
						showErrorMessage(data.text);
					}
				}, 'json');
				return false;
			});
		});
	</script>
{/if}
{* Display column names and arrows for ordering (ASC, DESC) *}
{if $is_order_position}
	<script type="text/javascript" src="../js/jquery/plugins/jquery.tablednd.js"></script>
	<script type="text/javascript">
		var come_from = '{$list_id|addslashes}';
		var alternate = {if $order_way == 'DESC'}'1'{else}'0'{/if};
	</script>
	<script type="text/javascript" src="../js/admin/dnd.js"></script>
{/if}
{if !$simple_header}
	<script type="text/javascript">
		$(function() {
			$('table.{$list_id|escape:'htmlall':'UTF-8'} .filter').keypress(function(e){
				var key = (e.keyCode ? e.keyCode : e.which);
				if (key == 13)
				{
					e.preventDefault();
					formSubmit(e, 'submitFilterButton{$list_id|escape:'htmlall':'UTF-8'}');
				}
			})
			$('#submitFilterButton{$list_id|escape:'htmlall':'UTF-8'}').click(function() {
				$('#submitFilter{$list_id|escape:'htmlall':'UTF-8'}').val(1);
			});

			if ($("table .datepicker").length > 0) {
				$("table .datepicker").datepicker({
					prevText: '',
					nextText: '',
					altFormat: 'yy-mm-dd'
				});
			}
		});
	</script>
{/if}

{if !$simple_header}
	<div class="leadin">
		{block name="leadin"}{/block}
	</div>
{/if}

{block name="override_header"}{/block}

{hook h='displayAdminListBefore'}

{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListBefore{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListBefore{/capture}
	{hook h=$hookName}
{/if}

<div class="alert alert-warning" id="{$list_id|escape:'htmlall':'UTF-8'}-empty-filters-alert" style="display:none;">{l s='Please fill at least one field to perform a search in this list.' mod='lpspaydiffvir30'}</div>
{if isset($sql) && $sql}
	<form id="sql_form_{$list_id|escape:'htmlall':'UTF-8'}" action="{$link->getAdminLink('AdminRequestSql')|escape:'htmlall':'UTF-8'}&amp;addrequest_sql" method="post" class="hide">
		<input type="hidden" id="sql_query_{$list_id|escape:'htmlall':'UTF-8'}" name="sql" value="{$sql|escape:'htmlall':'UTF-8'}"/>
		<input type="hidden" id="sql_name_{$list_id|escape:'htmlall':'UTF-8'}" name="name" value=""/>
	</form>
{/if}

{block name="startForm"}
	<form method="post" action="{$action|escape:'htmlall':'UTF-8'}" class="form-horizontal clearfix" id="form-{$list_id|escape:'htmlall':'UTF-8'}">
{/block}

{if !$simple_header}
	<input type="hidden" id="submitFilter{$list_id|escape:'htmlall':'UTF-8'}" name="submitFilter{$list_id|escape:'htmlall':'UTF-8'}" value="0"/>
	<input type="hidden" name="page" value="{$page|intval}"/>
	<input type="hidden" name="selected_pagination" value="{$selected_pagination|intval}"/>
	{block name="override_form_extra"}{/block}
	<div class="panel col-lg-12">
		<div class="panel-heading">
			{if isset($icon)}<i class="{$icon|escape:'htmlall':'UTF-8'}"></i> {/if}{if is_array($title)}{$title|end}{else}{$title|escape:'htmlall':'UTF-8'}{/if}
			{if isset($list_total)}<span class="badge">{$list_total|escape:'htmlall':'UTF-8'}</span>{/if}
			{if isset($toolbar_btn) && count($toolbar_btn) >0 && $show_toolbar}
				<span class="panel-heading-action">
				{foreach from=$toolbar_btn item=btn key=k}
					{if $k != 'modules-list' && $k != 'back'}
						<a id="desc-{$table|escape:'htmlall':'UTF-8'}-{if isset($btn.imgclass)}{$btn.imgclass|escape:'htmlall':'UTF-8'}{else}{$k|escape:'htmlall':'UTF-8'}{/if}" class="list-toolbar-btn{if isset($btn.target) && $btn.target} _blank{/if}"{if isset($btn.href)} href="{$btn.href|escape:'htmlall':'UTF-8'}"{/if}{if isset($btn.js) && $btn.js} onclick="{$btn.js|escape:'htmlall':'UTF-8'}"{/if}>
							<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s=$btn.desc mod='lpspaydiffvir30'}" data-html="true" data-placement="top">
								<i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass|escape:'htmlall':'UTF-8'}{else}{$k|escape:'htmlall':'UTF-8'}{/if}{if isset($btn.class)} {$btn.class|escape:'htmlall':'UTF-8'}{/if}"></i>
							</span>
						</a>
					{/if}
				{/foreach}
					<a class="list-toolbar-btn" href="javascript:location.reload();">
						<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Refresh list' mod='lpspaydiffvir30'}" data-html="true" data-placement="top">
							<i class="process-icon-refresh"></i>
						</span>
					</a>
				{if isset($sql) && $sql}
					{assign var=sql_manager value=Profile::getProfileAccess(Context::getContext()->employee->id_profile, Tab::getIdFromClassName('AdminRequestSql'))}

					{if $sql_manager.view == 1}
						<a class="list-toolbar-btn" href="javascript:void(0);" onclick="$('.leadin').first().append('<div class=\'alert alert-info\'>' + $('#sql_query_{$list_id|escape:'htmlall':'UTF-8'}').val() + '</div>'); $(this).attr('onclick', '');">
							<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Show SQL query' mod='lpspaydiffvir30'}" data-html="true" data-placement="top" >
								<i class="process-icon-terminal"></i>
							</span>
						</a>
						<a class="list-toolbar-btn" href="javascript:void(0);" onclick="$('#sql_name_{$list_id|escape:'htmlall':'UTF-8'}').val(createSqlQueryName()); $('#sql_query_{$list_id|escape:'htmlall':'UTF-8'}').val($('#sql_query_{$list_id|escape:'htmlall':'UTF-8'}').val().replace(/\s+limit\s+[0-9,\s]+$/ig, '').trim()); $('#sql_form_{$list_id|escape:'htmlall':'UTF-8'}').submit();">
							<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Export to SQL Manager' mod='lpspaydiffvir30'}" data-html="true" data-placement="top" >
								<i class="process-icon-database"></i>
							</span>
						</a>
					{/if}
				{/if}
				</span>
			{/if}
		</div>
		{if $show_toolbar}
			<script type="text/javascript">
				//<![CDATA[
				var submited = false;
				$(function() {
					//get reference on save link
					btn_save = $('i[class~="process-icon-save"]').parent();
					//get reference on form submit button
					btn_submit = $('#{$table|escape:'htmlall':'UTF-8'}_form_submit_btn');
					if (btn_save.length > 0 && btn_submit.length > 0) {
						//get reference on save and stay link
						btn_save_and_stay = $('i[class~="process-icon-save-and-stay"]').parent();
						//get reference on current save link label
						lbl_save = $('#desc-{$table|escape:'htmlall':'UTF-8'}-save div');
						//override save link label with submit button value
						if (btn_submit.val().length > 0) {
							lbl_save.html(btn_submit.attr("value"));
						}
						if (btn_save_and_stay.length > 0) {
							//get reference on current save link label
							lbl_save_and_stay = $('#desc-{$table|escape:'htmlall':'UTF-8'}-save-and-stay div');
							//override save and stay link label with submit button value
							if (btn_submit.val().length > 0 && lbl_save_and_stay && !lbl_save_and_stay.hasClass('locked')) {
								lbl_save_and_stay.html(btn_submit.val() + " {l s='and stay' mod='lpspaydiffvir30'} ");
							}
						}
						//hide standard submit button
						btn_submit.hide();
						//bind enter key press to validate form
						$('#{$table}_form').keypress(function (e) {
							if (e.which == 13 && e.target.localName != 'textarea') {
								$('#desc-{$table|escape:'htmlall':'UTF-8'}-save').click();
							}
						});
						//submit the form
						{block name=formSubmit}
							btn_save.click(function() {
								// Avoid double click
								if (submited) {
									return false;
								}
								submited = true;
								//add hidden input to emulate submit button click when posting the form -> field name posted
								btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'" value="1" />');
								$('#{$table|escape:'htmlall':'UTF-8'}_form').submit();
								return false;
							});
							if (btn_save_and_stay) {
								btn_save_and_stay.click(function() {
									//add hidden input to emulate submit button click when posting the form -> field name posted
									btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'AndStay" value="1" />');
									$('#{$table|escape:'htmlall':'UTF-8'}_form').submit();
									return false;
								});
							}
						{/block}
					}
				});
				//]]>
			</script>
		{/if}
{elseif $simple_header}
	<div class="panel col-lg-12">
		{if isset($title)}<h3>{if isset($icon)}<i class="{$icon|escape:'htmlall':'UTF-8'}"></i> {/if}{if is_array($title)}{$title|end}{else}{$title|escape:'htmlall':'UTF-8'}{/if}</h3>{/if}
{/if}


	{if $bulk_actions && $has_bulk_actions}
		{assign var=y value=2}
	{else}
		{assign var=y value=1}
	{/if}
	<style>
	@media (max-width: 992px) {
		{foreach from=$fields_display item=param name=params}
			.table-responsive-row td:nth-of-type({math equation="x+y" x=$smarty.foreach.params.index y=$y}):before {
				content: "{$param.title|escape:'htmlall':'UTF-8'}";
			}
		{/foreach}
	}
	</style>

	{block name="preTable"}{/block}
	<div class="table-responsive-row clearfix{if isset($use_overflow) && $use_overflow} overflow-y{/if}">
		<table{if $table_id} id="table-{$table_id|escape:'htmlall':'UTF-8'}"{/if} class="table{if $table_dnd} tableDnD{/if} {$table|escape:'htmlall':'UTF-8'}" >
			<thead>
				<tr class="nodrag nodrop">
					{if $bulk_actions && $has_bulk_actions}
						<th class="center fixed-width-xs"></th>
					{/if}
					{foreach $fields_display AS $key => $params}
					<th class="{if isset($params.class)}{$params.class|escape:'htmlall':'UTF-8'}{/if}{if isset($params.align)} {$params.align|escape:'htmlall':'UTF-8'}{/if}">
						<span class="title_box{if isset($order_by) && ($key == $order_by)} active{/if}">
							{if isset($params.hint)}
								<span class="label-tooltip" data-toggle="tooltip"
									title="
										{if is_array($params.hint)}
											{foreach $params.hint as $hint}
												{if is_array($hint)}
													{$hint.text|escape:'htmlall':'UTF-8'}
												{else}
													{$hint|escape:'htmlall':'UTF-8'}
												{/if}
											{/foreach}
										{else}
											{$params.hint|escape:'htmlall':'UTF-8'}
										{/if}
									">
									{$params.title|escape:'htmlall':'UTF-8'}
								</span>
							{else}
								{$params.title|escape:'htmlall':'UTF-8'}
							{/if}
							{if (!isset($params.orderby) || $params.orderby) && !$simple_header && $show_filters}
								<a {if isset($order_by) && ($key == $order_by) && ($order_way == 'DESC')}class="active"{/if} href="{$currentIndex|escape:'htmlall':'UTF-8'}&amp;{$list_id|escape:'htmlall':'UTF-8'}Orderby={$key|urlencode}&amp;{$list_id|escape:'htmlall':'UTF-8'}Orderway=desc&amp;token={$token|escape:'htmlall':'UTF-8'}{if isset($smarty.get.$identifier)}&amp;{$identifier|escape:'htmlall':'UTF-8'}={$smarty.get.$identifier|intval}{/if}">
									<i class="icon-caret-down"></i>
								</a>
								<a {if isset($order_by) && ($key == $order_by) && ($order_way == 'ASC')}class="active"{/if} href="{$currentIndex|escape:'htmlall':'UTF-8'}&amp;{$list_id|escape:'htmlall':'UTF-8'}Orderby={$key|urlencode}&amp;{$list_id|escape:'htmlall':'UTF-8'}Orderway=asc&amp;token={$token|escape:'htmlall':'UTF-8'}{if isset($smarty.get.$identifier)}&amp;{$identifier|escape:'htmlall':'UTF-8'}={$smarty.get.$identifier|intval}{/if}">
									<i class="icon-caret-up"></i>
								</a>
							{/if}
						</span>
					</th>
					{/foreach}
					{if $shop_link_type}
						<th>
							<span class="title_box">
							{if $shop_link_type == 'shop'}
								{l s='Shop' mod='lpspaydiffvir30'}
							{else}
								{l s='Shop group' mod='lpspaydiffvir30'}
							{/if}
							</span>
						</th>
					{/if}
					{if $has_actions || $show_filters}
						<th>{if !$simple_header}{/if}</th>
					{/if}
				</tr>
			{if !$simple_header && $show_filters}
				<tr class="nodrag nodrop filter {if $row_hover}row_hover{/if}">
					{if $has_bulk_actions}
						<th class="text-center">
							--
						</th>
					{/if}
					{* Filters (input, select, date or bool) *}
					{foreach $fields_display AS $key => $params}
						<th {if isset($params.align)} class="{$params.align|escape:'htmlall':'UTF-8'}" {/if}>
							{if isset($params.search) && !$params.search}
								--
							{else}
								{if $params.type == 'bool'}
									<select class="filter fixed-width-sm center" name="{$list_id|escape:'htmlall':'UTF-8'}Filter_{if isset($params.filter_key)}{$params.filter_key|escape:'htmlall':'UTF-8'}{else}{$key|escape:'htmlall':'UTF-8'}{/if}">
										<option value="">-</option>
										<option value="1" {if $params.value == 1} selected="selected" {/if}>{l s='Yes' mod='lpspaydiffvir30'}</option>
										<option value="0" {if $params.value == 0 && $params.value != ''} selected="selected" {/if}>{l s='No' mod='lpspaydiffvir30'}</option>
									</select>
								{elseif $params.type == 'date' || $params.type == 'datetime'}
									<div class="date_range row">
 										<div class="input-group fixed-width-md center">
											<input type="text" class="filter datepicker date-input form-control" id="local_{$params.id_date|escape:'htmlall':'UTF-8'}_0" name="local_{$params.name_date|escape:'htmlall':'UTF-8'}[0]"  placeholder="{l s='From' mod='lpspaydiffvir30'}" />
											<input type="hidden" id="{$params.id_date|escape:'htmlall':'UTF-8'}_0" name="{$params.name_date|escape:'htmlall':'UTF-8'}[0]" value="{if isset($params.value.0)}{$params.value.0|escape:'htmlall':'UTF-8'}{/if}">
											<span class="input-group-addon">
												<i class="icon-calendar"></i>
											</span>
										</div>
 										<div class="input-group fixed-width-md center">
											<input type="text" class="filter datepicker date-input form-control" id="local_{$params.id_date|escape:'htmlall':'UTF-8'}_1" name="local_{$params.name_date|escape:'htmlall':'UTF-8'}[1]"  placeholder="{l s='To' mod='lpspaydiffvir30'}" />
											<input type="hidden" id="{$params.id_date|escape:'htmlall':'UTF-8'}_1" name="{$params.name_date|escape:'htmlall':'UTF-8'}[1]" value="{if isset($params.value.1)}{$params.value.1|escape:'htmlall':'UTF-8'}{/if}">
											<span class="input-group-addon">
												<i class="icon-calendar"></i>
											</span>
										</div>
										<script>
											$(function() {
												var dateStart = parseDate($("#{$params.id_date|escape:'htmlall':'UTF-8'}_0").val());
												var dateEnd = parseDate($("#{$params.id_date|escape:'htmlall':'UTF-8'}_1").val());
												$("#local_{$params.id_date|escape:'htmlall':'UTF-8'}_0").datepicker("option", "altField", "#{$params.id_date|escape:'htmlall':'UTF-8'}_0");
												$("#local_{$params.id_date|escape:'htmlall':'UTF-8'}_1").datepicker("option", "altField", "#{$params.id_date|escape:'htmlall':'UTF-8'}_1");
												if (dateStart !== null){
													$("#local_{$params.id_date|escape:'htmlall':'UTF-8'}_0").datepicker("setDate", dateStart);
												}
												if (dateEnd !== null){
													$("#local_{$params.id_date|escape:'htmlall':'UTF-8'}_1").datepicker("setDate", dateEnd);
												}
											});
										</script>
									</div>
								{elseif $params.type == 'select'}
									{if isset($params.filter_key)}
										<select class="filter{if isset($params.align) && $params.align == 'center'}center{/if}" onchange="$('#submitFilterButton{$list_id|escape:'htmlall':'UTF-8'}').focus();$('#submitFilterButton{$list_id|escape:'htmlall':'UTF-8'}').click();" name="{$list_id|escape:'htmlall':'UTF-8'}Filter_{$params.filter_key|escape:'htmlall':'UTF-8'}" {if isset($params.width)} style="width:{$params.width|intval}px"{/if}>
											<option value="" {if $params.value == ''} selected="selected" {/if}>-</option>
											{if isset($params.list) && is_array($params.list)}
												{foreach $params.list AS $option_value => $option_display}
													<option value="{$option_value|escape:'htmlall':'UTF-8'}" {if (string)$option_display === (string)$params.value ||  (string)$option_value === (string)$params.value} selected="selected"{/if}>{$option_display|escape:'htmlall':'UTF-8'}</option>
												{/foreach}
											{/if}
										</select>
									{/if}
								{else}
									<input type="text" class="filter" name="{$list_id|escape:'htmlall':'UTF-8'}Filter_{if isset($params.filter_key)}{$params.filter_key|escape:'htmlall':'UTF-8'}{else}{$key|escape:'htmlall':'UTF-8'}{/if}" value="{$params.value|escape:'htmlall':'UTF-8'}" {if isset($params.width) && $params.width != 'auto'} style="width:{$params.width|intval}px"{/if} />
								{/if}
							{/if}
						</th>
					{/foreach}
					{if $shop_link_type}
						<th>--</th>
					{/if}
					{if $has_actions || $show_filters}
						<th class="actions">
							{if $show_filters}
							<span class="pull-right">
								{*Search must be before reset for default form submit*}
								<button type="submit" id="submitFilterButton{$list_id|escape:'htmlall':'UTF-8'}" name="submitFilter" class="btn btn-default" data-list-id="{$list_id|escape:'htmlall':'UTF-8'}">
									<i class="icon-search"></i> {l s='Search' mod='lpspaydiffvir30'}
								</button>
								{if $filters_has_value}
									<button type="submit" name="submitReset{$list_id|escape:'htmlall':'UTF-8'}" class="btn btn-warning">
										<i class="icon-eraser"></i> {l s='Reset' mod='lpspaydiffvir30'}
									</button>
								{/if}
							</span>
							{/if}
						</th>
					{/if}
				</tr>
			{/if}
			</thead>
