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
$(document).ready(function () {
  var LPS_PAY_DIFFVIR30_ALLOW_BW_on = document.getElementById(
    "LPS_PAY_DIFFVIR30_ALLOW_BW_on",
  );
  var LPS_PAY_DIFFVIR30_ALLOW_BW_off = document.getElementById(
    "LPS_PAY_DIFFVIR30_ALLOW_BW_off",
  );
  if (LPS_PAY_DIFFVIR30_ALLOW_BW_off.checked) {
    $(".LPS_PAY_DIFFVIR30_BW_OWNER").hide();
    $(".LPS_PAY_DIFFVIR30_BW_DETAILS").hide();
    $(".LPS_PAY_DIFFVIR30_BW_ADDRESS").hide();
  }
  $('input[name="LPS_PAY_DIFFVIR30_ALLOW_BW"]').click(function () {
    if (LPS_PAY_DIFFVIR30_ALLOW_BW_on.checked) {
      $(".LPS_PAY_DIFFVIR30_BW_OWNER").fadeIn("fast");
      $(".LPS_PAY_DIFFVIR30_BW_DETAILS").fadeIn("fast");
      $(".LPS_PAY_DIFFVIR30_BW_ADDRESS").fadeIn("fast");
    }
    if (LPS_PAY_DIFFVIR30_ALLOW_BW_off.checked) {
      $(".LPS_PAY_DIFFVIR30_BW_OWNER").fadeOut("fast");
      $(".LPS_PAY_DIFFVIR30_BW_DETAILS").fadeOut("fast");
      $(".LPS_PAY_DIFFVIR30_BW_ADDRESS").fadeOut("fast");
    }
  });
  var LPS_PAY_DIFFVIR30_ALLOW_CH_on = document.getElementById(
    "LPS_PAY_DIFFVIR30_ALLOW_CH_on",
  );
  var LPS_PAY_DIFFVIR30_ALLOW_CH_off = document.getElementById(
    "LPS_PAY_DIFFVIR30_ALLOW_CH_off",
  );
  if (LPS_PAY_DIFFVIR30_ALLOW_CH_off.checked) {
    $(".LPS_PAY_DIFFVIR30_CH_NAME").hide();
    $(".LPS_PAY_DIFFVIR30_CH_ADDRESS").hide();
  }
  $('input[name="LPS_PAY_DIFFVIR30_ALLOW_CH"]').click(function () {
    if (LPS_PAY_DIFFVIR30_ALLOW_CH_on.checked) {
      $(".LPS_PAY_DIFFVIR30_CH_NAME").fadeIn("fast");
      $(".LPS_PAY_DIFFVIR30_CH_ADDRESS").fadeIn("fast");
    }
    if (LPS_PAY_DIFFVIR30_ALLOW_CH_off.checked) {
      $(".LPS_PAY_DIFFVIR30_CH_NAME").fadeOut("fast");
      $(".LPS_PAY_DIFFVIR30_CH_ADDRESS").fadeOut("fast");
    }
  });
});
