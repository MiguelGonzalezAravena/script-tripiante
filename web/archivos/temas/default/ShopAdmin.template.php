<?php
/**********************************************************************************
* ShopAdmin.template.php                                                          *
* Template file for SMFShop Administration page                                   *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SMFShop 3.0 (Build 12)                              *
* $Date:: 2007-01-18 19:26:55 +1100 (Thu, 18 Jan 2007)                          $ *
* $Id:: ShopAdmin.template.php 79 2007-01-18 08:26:55Z daniel15                 $ *
* Software by:                DanSoft Australia (http://www.dansoftaustralia.net/)*
* Copyright 2005-2007 by:     DanSoft Australia (http://www.dansoftaustralia.net/)*
* Support, News, Updates at:  http://www.dansoftaustralia.net/                    *
*                                                                                 *
* Forum software by:          Simple Machines (http://www.simplemachines.org)     *
* Copyright 2006-2007 by:     Simple Machines LLC (http://www.simplemachines.org) *
*           2001-2006 by:     Lewis Media (http://www.lewismedia.com)             *
***********************************************************************************
* This program is free software; you may redistribute it and/or modify it under   *
* the terms of the provided license as published by Simple Machines LLC.          *
*                                                                                 *
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
*                                                                                 *
* See the "license.txt" file for details of the Simple Machines license.          *
* The latest version of the license can always be found at                        *
* http://www.simplemachines.org.                                                  *
**********************************************************************************/

// Feel free to edit this template however you want, but be careful not to break anything.
// Make sure you have a backup handy.

// The main admin page
// TODO: Fix this code! Tables are ugly, and there's way too many :P
function template_main() {
  global $modSettings, $scripturl, $context, $txt, $shopVersion;
  
  echo '
        <form action="', $scripturl, '?action=shop_general;save" method="post">
          <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 1.5ex;">
            <tr>
              <td valign="top"  colspan="3">
                <table width="100%" cellpadding="5" cellspacing="1" border="0" class="bordercolor">
                  <tr>
                    <td class="catbg">', $txt['shop_version_info_header'], '</td>
                  </tr><tr>
                    <td class="windowbg2" valign="top" style="height: 18ex;" align="center">
                      <b>', $txt['shop_version_info_header'], ':</b><br />
                      ', $txt['shop_version_number'], ': <span id="yourShopVersion">', $shopVersion['version'], '</span> (Build <span id="yourShopBuild">', $shopVersion['build'], ')</span><br />
                      ', $txt['shop_database_version'], ': ', $modSettings['shopVersion'], ' (Build ', $modSettings['shopBuild'], ')<br />
                      ', $txt['shop_version_reldate'], ': ', $shopVersion['date'], '<br /><br />';

  if ($shopVersion['develVersion'] == true)
    echo '
                      <b>This is a development version of SMFShop!</b><br />
                      SVN ID: ', $shopVersion['SVNid'], '<br />
                      SVN Date: ', $shopVersion['SVNdate'], '<br />';
  
  echo '
                      <br />
                      <span id="currShopVersion">', $txt['shop_unable_connect'], '</span>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr><td style="width: 1ex;">&nbsp;</td></tr>
            <tr>
              <td valign="top" style="width: 50%;">
                <table width="100%" cellpadding="5" cellspacing="1" border="0" class="bordercolor">
                  <tr>
                    <td class="catbg">', $txt['shop_settings_general'], '</td>
                  </tr><tr>
                    <td class="windowbg2" valign="top" style="height: 18ex;">
                      ', $txt['shop_itemsperpage'], ': <input type="text" name="itemspage" value="', $modSettings['shopItemsPerPage'], '" size="5" /><br />
                      <input type="checkbox" name="tradeenabled" id="tradeenabled"', ($modSettings['shopTradeEnabled'] == '1' ? ' checked="checked"' : ''), ' /><label for="tradeenabled">', $txt['shop_trade_enable'], '</label><br /><hr size="1" width="100%" class="hrcolor" />
                      <input type="checkbox" name="bankenabled" id="bankenabled"', ($modSettings['shopBankEnabled'] == '1' ? ' checked="checked"' : ''), ' /><label for="bankenabled">', $txt['shop_bank'], ' Enabled</label><br />
                      <table>
                        <tr>
                          <td align="right"><label for="interest">', $txt['shop_bank_interest'], ':</label></td>
                          <td><input type="text" name="interest" id="interest" value="', $modSettings['shopInterest'], '" size="5" />% per day</td>
                        </tr><tr>
                          <td align="right"><label for="feeDeposit">', $txt['shop_bank_fee_deposit'], ':</label></td>
                          <td>', $modSettings['shopCurrencyPrefix'], '<input type="text" name="feeDeposit" id="feeDeposit" value="', $modSettings['shopFeeDeposit'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                        </tr><tr>
                          <td align="right"><label for="feeWithdraw">', $txt['shop_bank_fee_withdraw'], ':</label></td>
                          <td>', $modSettings['shopCurrencyPrefix'], '<input type="text" name="feeWithdraw" id="feeWithdraw" value="', $modSettings['shopFeeWithdraw'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                        </tr><tr>
                          <td align="right"><label for="minDeposit">', $txt['shop_bank_minDeposit'], ':</label></td>
                          <td>', $modSettings['shopCurrencyPrefix'], '<input type="text" name="minDeposit" id="minDeposit" value="', $modSettings['shopMinDeposit'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                        </tr><tr>
                          <td align="right"><label for="minWithdraw">', $txt['shop_bank_minWithdraw'], ':</label></td>
                          <td>', $modSettings['shopCurrencyPrefix'], '<input type="text" name="minWithdraw" id="minWithdraw" value="', $modSettings['shopMinWithdraw'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                        </tr>
                      </table>
                      <div class="smalltext">', $txt['shop_bank_disableMin'], '</div>
                      <hr size="1" width="100%" class="hrcolor" />
                      <table>
                        <tr>
                          <td align="right"><label for="image_width">', $txt['shop_image_width'], ':</label></td>
                          <td><input type="text" name="image_width" id="image_width" value="', $modSettings['shopImageWidth'], '" size="5" /></td>
                        </tr><tr>
                          <td align="right"><label for="image_height">', $txt['shop_image_height'], ':</label></td>
                          <td><input type="text" name="image_height" id="image_height" value="', $modSettings['shopImageHeight'], '" size="5" /></td>
                        </tr>
                      </table>
                      <input type="submit" value="', $txt['shop_save_changes'], '" /><br />
                      ', ($context['shop_saved'] == true ? '<b>' . $txt['shop_saved'] . '</b>' : ''), '
                    </td>
                  </tr>
                </table>
              </td>

              <td style="width: 1ex;">&nbsp;</td>
              <td valign="top" style="width: 50%;">
                <table width="100%" cellpadding="5" cellspacing="1" border="0" class="bordercolor" id="supportVersionsTable">
                  <tr>
                    <td class="catbg">', $txt['shop_settings_currency'], '</td>
                  </tr><tr>
                    <td class="windowbg2" valign="top" style="height: 18ex;">
                      <label for="prefix">', $txt['shop_currency_prefix'], ':</label> <input type="text" name="prefix" id="prefix" value="', $modSettings['shopCurrencyPrefix'], '" size="5" /><br />
                      <label for="suffix">', $txt['shop_currency_suffix'], ':</label> <input type="text" name="suffix" id="suffix" value="', $modSettings['shopCurrencySuffix'], '" size="5" /><br />

                      <div class="smalltext">', $txt['shop_pre-suf_confuse'], '</div><br />
                      <table>
                        <tr>
                          <td align="right"><label for="pertopic">', $txt['shop_per_new_topic'], ':</label></td>
                          <td>', $modSettings['shopCurrencyPrefix'], '<input type="text" name="pertopic" id="pertopic" value="', $modSettings['shopPointsPerTopic'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                        </tr><tr>
                          <td align="right"><label for="perpost">', $txt['shop_per_new_post'], ':</label></td>
                          <td>', $modSettings['shopCurrencyPrefix'], '<input type="text" name="perpost" id="perpost" value="', $modSettings['shopPointsPerPost'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                        </tr><tr>
                          <td align="right"><label for="regamount">', $txt['shop_reg_bonus'], ':</label></td>
                          <td>', $modSettings['shopCurrencyPrefix'], '<input type="text" name="regamount" id="regamount" value="', $modSettings['shopRegAmount'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                        </tr>
                      </table>
                      
                      <input type="submit" value="', $txt['shop_save_changes'], '" /><br />
                      ', ($context['shop_saved'] == true ? '<b>' . $txt['shop_saved'] . '</b>' : ''), '
                    </td>
                  </tr>
                </table>
                <!-- &nbsp; -->
                <table width="100%" cellpadding="5" cellspacing="1" border="0" class="bordercolor">
                  <tr>
                    <td class="catbg">', $txt['shop_bonuses'], '</td>
                  </tr><tr>
                    <td class="windowbg2" valign="top" style="height: 18ex;">
                      <table>
                        <tr>
                          <td align="right"><label for="perword">', $txt['shop_per_word'], ':</label></td>
                          <td>', $modSettings['shopCurrencyPrefix'], '<input type="text" name="perword" id="perword" value="', $modSettings['shopPointsPerWord'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                        </tr><tr>
                          <td align="right"><label for="perchar">', $txt['shop_per_char'], ':</label></td>
                          <td>', $modSettings['shopCurrencyPrefix'], '<input type="text" name="perchar" id="perchar" value="', $modSettings['shopPointsPerChar'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                        </tr><tr>
                          <td colspan="2" class="smalltext">', $txt['shop_bonus_zero'], '</td>
                        </tr><tr>
                          <td align="right"><label for="limit">', $txt['shop_per_post_limit'], ':</label></td>
                          <td>', $modSettings['shopCurrencyPrefix'], '<input type="text" name="limit" id="limit" value="', $modSettings['shopPointsLimit'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                        </tr><tr>
                          <td colspan="2" class="smalltext">', $txt['shop_post_limit_zero'], '</td>
                        </tr>
                      </table><br />
                      
                      ', $txt['shop_bonus_info'], '<br /><br />
                      
                      <input type="submit" value="', $txt['shop_save_changes'], '" /><br />
                      ', ($context['shop_saved'] == true ? '<b>' . $txt['shop_saved'] . '</b>' : ''), '
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </form>
        <br />
        New releases can always be found at the <a href="http://www.smfhacks.com/smfshop/">http://www.smfhacks.com/smfshop/</a><br />
  <b>Has the SMF Shop helped you?</b> Then support the developers:<br />
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
  <input type="hidden" name="cmd" value="_xclick">
  <input type="hidden" name="business" value="sales@visualbasiczone.com">
  <input type="hidden" name="item_name" value="SMF Shop">
  <input type="hidden" name="no_shipping" value="1">
  <input type="hidden" name="no_note" value="1">
  <input type="hidden" name="currency_code" value="USD">
  <input type="hidden" name="tax" value="0">
  <input type="hidden" name="bn" value="PP-DonationsBF">
  <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" border="0" name="submit" alt="Make payments with PayPal - it is fast, free and secure!" />
  <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
      <br />
      <table>
        <tr>
        <td>
        <a href="http://www.adbrite.com/mb/?spid=11444&afb=120x60-1-blue">
<img src="http://files.adbrite.com/mb/images/120x60-1-blue.gif" border="0"></a>
        </td>
        <td>
                <a href="http://www.kqzyfj.com/click-3289266-10408495" target="_top">
<img src="http://www.tqlkg.com/image-3289266-10408495" width="120" height="60" alt="" border="0"/></a>

        </td>
        </table>';
        
  if ($shopVersion['develVersion'] == false)
    echo '
        <script language="JavaScript" type="text/javascript" src="http://www.smfhacks.com/versions/shop-version.js?build=' . $shopVersion['build'] . '"></script>';
  else
    echo '
        <script language="JavaScript" type="text/javascript">
          var currShopVerStr = document.getElementById(\'currShopVersion\');
          setInnerHTML(currShopVerStr, \'Hey, why are you running a development version? Check the SMFHacks website for the latest version!\');
        </script>';
}

// Member's Inventory
function template_inventory() {
  global $modSettings, $scripturl, $context, $txt, $settings;

echo '
        <table width="100%" cellpadding="5" cellspacing="0" border="0" class="tborder" style="margin-top: 1.5ex;">
          <tr class="titlebg"><td align="center">', $txt['shop_edit_inventory'], '</td></tr>
          <tr valign="top" class="windowbg2">
            <td style="padding-bottom: 2ex;" width="100%">';

  // The 'Please Type a Members Name' page
  if (empty($_GET['do']) || $_GET['do'] == '')
    echo '
              ', $txt['shop_edit_member_inventory'], '<br />
              <form action="', $scripturl, '?action=shop_inventory;do=viewmember" method="post">
                <input name="searchfor" type="text" size="70" />
                <a href="', $scripturl, '?action=findmember;input=searchfor;quote=1;sesc=', $context['session_id'], '" onclick="return reqWin(this.href, 350, 400);"><img src="', $settings['images_url'], '/icons/assist.gif" border="0" alt="', $txt['find_members'], '" /> Find Members</a><br />
                <input type="submit" value="', $txt['shop_next'], '" />
              </form>';

  // The Inventory list
  else
  {
    // If we need to show a message
    if (isset($context['shop_message']))
      echo '
              <div style="color: red; font-weight: bold;">', $context['shop_message'], '</div>'; 

    echo '
              <i>', sprintf($txt['shop_edit_member'], $context['shop_inv']['member'], $context['shop_inv']['realName']), '</i><br /><br />', '
              ';
    foreach ($context['shop_inv']['list'] as $row)
      echo '
              ', $txt['shop_inventory'], ' #', $row['id'], ' - ', $row['name'], ' - ', sprintf($txt['shop_bought_for'], $row['amtpaid']), ' - <a href="', $scripturl, '?action=shop_inventory;do=delete&id=', $row['id'], ';userid=', $context['shop_inv']['member'], '">', $txt['shop_delete'], '</a><br />';
    
    echo '
              <br />
              <form action="', $scripturl, '?action=shop_inventory;do=editmoney" method="post">
                <input type="hidden" name="userid" value="', $context['shop_inv']['member'], '" />
                <table>
                  <tr>
                    <td align="right"><label for="money_pocket">', $txt['shop_money_in_pocket'], ':</label></td>
                    <td><input type="text" value="', $context['shop_inv']['money_pocket'], '" name="money_pocket" id="money_pocket" /></td>
                  </tr><tr>
                    <td align="right"><label for="money_bank">', $txt['shop_money_in_bank'], ':</label></td>
                    <td><input type="text" value="', $context['shop_inv']['money_bank'], '" name="money_bank" id="money_bank" /></td>
                  </tr>
                </table>
                <input type="submit" value="', $txt['shop_save_changes'], '" />
              </form>
    ';
  }
  // Close the table
  echo '
            </td>
          </tr>
        </table>';
}

// Add an item to the shop
function template_items_add()
{
  global $context, $scripturl, $txt, $boardurl, $modSettings;

  echo '
        <table width="100%" cellpadding="5" cellspacing="0" border="0" class="tborder" style="margin-top: 1.5ex;">
          <tr class="titlebg"><td align="center">', $txt['shop_add_item'], '</td></tr>
          <tr valign="top" class="windowbg2">
            <td style="padding-bottom: 2ex;" width="100%">';

  // Step 1 - Choose Item
  if (empty($_GET['step']) || $_GET['step'] == 0)
    // How did you get here? You _should_ be at the 'Edit Items' page
    // Oh well, tell them where this has moved
    echo '
              This has moved to the Edit Items section. Please go there instead :-)';

  // Step 2 - Settings for the item
  else if ($_GET['step'] == 1)
  {
    // The 'adding an item' notice
    echo '
              ', sprintf($txt['shop_add_item_message2'], $context['shop_item']['name'], $context['shop_item']['authorName'], $context['shop_item']['authorEmail'],  $context['shop_item']['authorWeb']), '

              <br /><br />', $txt['shop_item_configure'], '<br />
              <form action="', $scripturl, '?action=shop_items_add;step=2" method="post" name="theAdminForm">
                <input type="hidden" name="item" value="', $context['shop_item']['name'], '" />
                <input type="hidden" name="require_input" value="', $context['shop_item']['require_input'], '" />
                <input type="hidden" name="can_use_item" value="', $context['shop_item']['can_use_item'], '" />
                <table>
                  <tr>
                    <td align="right"><label for="itemname">', $txt['shop_name'], ':</label></td>
                    <td><input name="itemname" id="itemname" type="text" value="', $context['shop_item']['friendlyname'], '" size="80"  style="width: 100%" /></td>
                  </tr><tr>
                    <td align="right" valign="top"><label for="itemdesc">', $txt['shop_description'], ':</label></td>
                    <td><textarea name="itemdesc" id="itemdesc" cols="40" rows="6" style="width: 100%">', $context['shop_item']['desc'], '</textarea></td>
                  </tr><tr>
                    <td align="right"><label for="itemprice">', $txt['shop_price'], ':</label></td>
                    <td>', $modSettings['shopCurrencyPrefix'], '<input name="itemprice" id="itemprice" type="text" value="', $context['shop_item']['price'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                  </tr><tr>
                    <td align="right"><label for="itemstock">', $txt['shop_stock'], ':</label></td>
                    <td><input name="itemstock" id="itemstock" type="text" value="', $context['shop_item']['stock'], '" size="5" /></td>
                  </tr><tr>
                    <td align="right"><label for="cat">', $txt['shop_category'], ':</label></td>
                    <td>
                      <select name="cat" id="cat">
                        <option value="0">', $txt['shop_cat_no'], '</option>';
    foreach ($context['shop_categories'] as $category)
      echo '
                        <option value="', $category['id'], '">', $category['name'], '</option>';
    echo '
                      </select>
                    </td>
                  </tr><tr>
                    <td align="right"><label for="icon">', $txt['shop_image'], ':</label></td>
                    <td>
                      <!-- TODO: Should JavaScript detect Sources URL? -->
                      <script type="text/javascript" language="javascript">
                      <!--
                      function show_image()
                      {
                        if (document.theAdminForm.icon.value !== "none")
                        {
                          // TODO: Should this detect the sources URL, rather than just assume?
                          var image_url = "', $boardurl, '/Sources/shop/item_images/" + document.theAdminForm.icon.value;
                          document.images["icon"].src = image_url;
                        }
                        else
                        {
                          document.images["icon"].src = "', $boardurl, '/Sources/shop/item_images/blank.gif";
                        }
                      }
                      //-->
                      </script>
                      
                      <select name="icon" id="icon" onchange="show_image()">
                        <option value="blank.gif" selected="selected">[NONE]</option>';
    // Dropdown box of images
    foreach ($context['shop_images'] as $image)
      echo '
                        <option value="', $image, '">', $image, '</option>';
    echo '
                      </select>
                      <img name="icon" src="', $boardurl, '/Sources/shop/item_images/blank.gif" border="1" width="', $modSettings['shopImageWidth'], '" height="', $modSettings['shopImageHeight'], '" alt="Item Image" /><br />
                      ', $txt['shop_item_notice'], '
                      
                    </td>
                  </tr><tr>
                    <!-- <td><label for="itemdelete">', $txt['shop_delete_after_use'], ':</label></td>
                    <td>[Put the input field here... Checkbox?]</td> -->
                  </tr>
                </table>
                <br />
                <label><input type="checkbox" name="itemdelete" id="itemdelete" ', ($context['shop_item']['delete_after_use'] ? ' checked="checked"' : ''), '/> ', $txt['shop_delete_after_use'], '</label>
                
                <br />', (isset($context['shop_item']['addInput']) && $context['shop_item']['addInput'] != '' && $context['shop_item']['addInput'] != false ? '<br />
                <b>' . $txt['shop_name_desc_match'] . '</b><br />
                ' . $context['shop_item']['addInput'] . '<br />
                ' : '') , '<br />
                <input type="submit" value="', $txt['shop_add_item'], '" />
              </form>';
  }

  // Close the table
  echo '
            </td>
          </tr>
        </table>';
}

// The 'Edit an Item' template
// TODO: Some code is similar to that on the add items page. Should they be combined?
function template_items_edit()
{ 
  global $context, $scripturl, $txt, $modSettings, $boardurl;

  echo '
        <table width="100%" cellpadding="5" cellspacing="0" border="0" class="tborder" style="margin-top: 1.5ex;">
          <tr class="titlebg"><td align="center">', $txt['shop_admin_items_addedit'], '</td></tr>
          <tr valign="top" class="windowbg2">
            <td style="padding-bottom: 2ex;" width="100%">';

  // Editing an item
  if (isset($_GET['do']) && $_GET['do'] == 'edit')
  {
    echo '
              ', $txt['shop_editing_item'], ' ', $context['shop_edit']['id'], ':
              <form action="', $scripturl, '?action=shop_items_edit;do=edit2" method="post" name="theAdminForm">
                <input type="hidden" name="id" value="', $context['shop_edit']['id'], '" />
                
                
                <table>
                  <tr>
                    <td align="right"><label for="itemname">', $txt['shop_name'], ':</label></td>
                    <td><input name="itemname" id="itemname" type="text" value="', $context['shop_edit']['name'], '" size="80"  style="width: 100%" /></td>
                  </tr><tr>
                    <td align="right" valign="top"><label for="itemdesc">', $txt['shop_description'], ':</label></td>
                    <td><textarea name="itemdesc" id="itemdesc" cols="40" rows="6" style="width: 100%">', $context['shop_edit']['desc'], '</textarea></td>
                  </tr><tr>
                    <td align="right"><label for="itemprice">', $txt['shop_price'], ':</label></td>
                    <td>', $modSettings['shopCurrencyPrefix'], '<input name="itemprice" id="itemprice" type="text" value="', $context['shop_edit']['price'], '" size="5" />', $modSettings['shopCurrencySuffix'], '</td>
                  </tr><tr>
                    <td align="right"><label for="itemstock">', $txt['shop_stock'], ':</label></td>
                    <td><input name="itemstock" id="itemstock" type="text" value="', $context['shop_edit']['stock'], '" size="5" /></td>
                  </tr><tr>
                    <td align="right"><label for="cat">', $txt['shop_category'], ':</label></td>
                    <td>
                      <select name="cat" id="cat">
                        <option value="0"', ($context['shop_edit']['category'] == 0 ? ' selected="selected"' : ''), '>', $txt['shop_cat_no'], '</option>';
    foreach ($context['shop_categories'] as $category)
      echo '
                        <option value="', $category['id'], '"', ($context['shop_edit']['category'] == $category['id'] ? ' selected="selected"' : ''), '>', $category['name'], '</option>';
    echo '
                      </select>
                    </td>
                  </tr><tr>
                    <td align="right"><label for="icon">', $txt['shop_image'], ':</label></td>
                    <td>
                      <!-- TODO: Should JavaScript detect Sources URL? -->
                      <script type="text/javascript" language="javascript">
                      <!--
                      function show_image()
                      {
                        if (document.theAdminForm.icon.value !== "none")
                        {
                          // TODO: Should this detect the sources URL, rather than just assume?
                          var image_url = "', $boardurl, '/Sources/shop/item_images/" + document.theAdminForm.icon.value;
                          document.images["icon"].src = image_url;
                        }
                        else
                        {
                          document.images["icon"].src = "', $boardurl, '/Sources/shop/item_images/blank.gif";
                        }
                      }
                      //-->
                      </script>
                      
                      <select name="icon" id="icon" onchange="show_image()">
                        <option value="blank.gif"', ($context['shop_edit']['image'] == 'blank.gif' ? ' selected="selected"' : ''), '>[NONE]</option>';
    // Get all images for the dropdown list
    foreach ($context['shop_images'] as $image)
      echo '
                        <option value="', $image, '"', ($context['shop_edit']['image'] == $image ? ' selected="selected"' : ''), '>', $image, '</option>';
    echo '
                      </select>
                      <img name="icon" src="', $boardurl, '/Sources/shop/item_images/', $context['shop_edit']['image'], '" border="1" width="', $modSettings['shopImageWidth'], '" height="', $modSettings['shopImageHeight'], '" alt="Item Image" /><br />
                      ', $txt['shop_item_notice'], '
                      
                    </td>
                  </tr>
                </table>
                <br />
                <label><input type="checkbox" name="itemdelete" id="itemdelete" ', ($context['shop_edit']['delete_after_use'] ? ' checked="checked"' : ''), '/> ', $txt['shop_delete_after_use'], '</label>
                
                <br />', ($context['shop_edit']['addInputEditable'] == true && isset($context['shop_edit']['addInput']) && $context['shop_edit']['addInput'] != '' && $context['shop_edit']['addInput'] != false ? '<br />
                <b>' . $txt['shop_name_desc_match'] . '</b><br />
                ' . $context['shop_edit']['addInput'] . '<br />
                ' : '') , '<br />
                
                <br /><input type="submit" value="', $txt['shop_edit'], '"/>
              </form>';
    
  
  }
  // Deleting an item (or multiple items) - Ask if they're sure
  else if (isset($_GET['do']) && $_GET['do'] == 'del')
  {
    echo '
              <form action="', $scripturl, '?action=shop_items_edit;do=del2" method="post">
                ', $txt['shop_sure_delete'], '<br />
                <ul>';
    
    // Loop through each item chosen to delete...
    foreach ($context['shop_delete'] as $row)
      // and output them to the page, along with a hidden input field (so we know what id's to delete)
      echo '
                  <li><input type="hidden" name="delete[]" value="', $row['id'], '" /> ', $row['name'], '</li>';
              
    echo ' 
                
                </ul>
                <input type="submit" value="', $txt['shop_delete'], '" />
                <input type="button" value="', $txt['shop_noway'], '" onclick="window.location=\'', $scripturl, '?action=shop_items_edit\'" />
              </form>
      ';

  }
  // Otherwise, if they're on the main page, or they've returned from somewhere else
  // Choose item to edit or Delete
  else
  {
    // If we have a message to display, display it
    // This is when they've edited or deleted an item, or something bad happened
    if (isset($context['shop_edit_message'])) 
      echo '
              <span style="color: red; font-weight: bold;">', $context['shop_edit_message'], '</span><br /><br />';

    // The 'Add Item' box
    echo '
              ', $txt['shop_add_item_message'], '<br />
              <form action="', $scripturl, '?action=shop_items_add;step=1" method="post">
                <select name="item">';
    // For every item that's possible to add...
    foreach ($context['shop_add'] as $row)
      //... add it as an option
      echo '
                  <option value="', $row['name'], '">', $row['friendlyname'], ' by ', $row['authorName'], ' &lt;', $row['authorEmail'], '&gt;</option>';

    // The submit button, and the list of current items
    echo '
                </select>
                <input type="submit" value="', $txt['shop_next'], '" />
              </form>
              ', $txt['shop_edit_message'], '<br /><br />
              
              <form action="', $scripturl, '?action=shop_items_edit;do=del" method="post">';
    // Loop through each current item...
    foreach ($context['shop_edit'] as $row)
      // ... and output something for it
      echo '
                <input type="checkbox" name="delete[]" id="delete_', $row['id'], '" value="', $row['id'], '" /> <label for="delete_', $row['id'], '"><b>', $row['name'], '</b></label> - <a href="', $scripturl, '?action=shop_items_edit;do=edit&id=', $row['id'], '">', $txt['shop_edit'], '</a><br />';
    // The submit button, and that's all :)
    echo '
                <input type="submit" value="', $txt['shop_delete'], '" />
              </form>';
  }
  // Close the table
  echo '
            </td>
          </tr>
        </table>';
}

// The restock page
function template_restock()
{
  global $txt, $scripturl;
  // The first bit of the page
  echo '
        <table width="100%" cellpadding="5" cellspacing="0" border="0" class="tborder" style="margin-top: 1.5ex;">
          <tr class="titlebg"><td align="center">', $txt['shop_admin_restock'], '</td></tr>
          <tr valign="top" class="windowbg2">
            <td style="padding-bottom: 2ex;" width="100%">';
  // Step 1 - Ask the user some questions
  if (!isset($_GET['step']) || $_GET['step'] == 1)
    echo '
              <form action="', $scripturl, '?action=shop_restock;step=2" method="post">
                <table>
                  <tr>
                    <td align="right"><label for="lessthan">', $txt['shop_restock_lessthan'], ':</label></td>
                    <td><input type="text" name="lessthan" id="lessthan" value="5" /></td>
                  </tr><tr>
                    <td align="right"><label for="amount">', $txt['shop_restock_amount'], ':</label></td>
                    <td><input type="text" name="amount" id="amount" value="50" /></td>
                  </tr>
                </table>
                <input type="submit" value="', $txt['shop_next'], '" />
              </form>';
  // Step 2 - Actually do it!
  // TODO: Show errors here
  else if ($_GET['step'] == 2)
    echo '
              Updated stock!';
  
  // The bottom of the page
  echo '
            </td>
          </tr>
        </table>';
}

// Usergroup functions
function template_usergroup()
{
  global $txt, $scripturl,$context, $modSettings;
  
  // First bit of the page
  echo '
        <table width="100%" cellpadding="5" cellspacing="0" border="0" class="tborder" style="margin-top: 1.5ex;">
          <tr class="titlebg"><td align="center">', $txt['shop_admin_usergroup'], '</td></tr>
          <tr valign="top" class="windowbg2">
            <td style="padding-bottom: 2ex;" width="100%">';

  // Step 1: Ask the user what to do
  if (!isset($_GET['step']) || $_GET['step'] == 1)
  {
    echo '
              <form action="', $scripturl, '?action=shop_usergroup;step=2" method="post">
                ', $txt['shop_membergroup_desc'], '<br /><br />
                <table>
                  <tr>
                    <td align="right"><label for="usergroup">', $txt['shop_membergroup'], ':</label></td>
                    <td>
                      <select name="usergroup" id="usergroup">';
    // Loop through all available membergroups
    foreach ($context['shop_usergroups'] as $row)
      echo '
                        <option value="', $row['id'], '">', $row['groupName'], '</option>';
    echo '
                      </select>
                    </td>
                  </tr><tr>
                    <td>', $txt['shop_action'], ':</td>
                    <td><label><input type="radio" name="m_action" value="add" checked="checked" />', $txt['shop_add'], '</label> <label><input type="radio" name="m_action" value="sub" />', $txt['shop_subtract'], '</label></td>
                  </tr><tr>
                    <td><label for="value">', $txt['shop_amount'], ':</label></td>
                    <td>'.$modSettings['shopCurrencyPrefix'], '<input type="text" name="value" id="value" value="0" size="10" />'.$modSettings['shopCurrencySuffix'], '</td>
                  </tr>
                </table>
                <input type="submit" value="', $txt['shop_next'], '">
              </form>';
  }
  // Step 2: Tell them everything was done well
  // TODO: Show errors here
  else if ($_GET['step'] == 2)
    echo '
        Action completed!';
  
  // Bottom of the page
  echo '
        </td>
      </tr>
    </table>';
}

// The category modification page
function template_categories()
{
  global $txt, $context, $scripturl;
  // The first bit of the page
  echo '
        <table width="100%" cellpadding="5" cellspacing="0" border="0" class="tborder" style="margin-top: 1.5ex;">
          <tr class="titlebg"><td align="center">', $txt['shop_admin_cat'], '</td></tr>';
  /*      <tr valign="top" class="windowbg2">
            <td style="padding-bottom: 2ex;" width="100%">';*/
  if (isset($context['shop_cat_message'])) 
    echo '
          <tr><td><span style="color: red; font-weight: bold;">', $context['shop_cat_message'], '</span><br /><br /></tr></td>';
  echo '
          <tr class="catbg"><td>', $txt['shop_categories'], '</td></tr>
          <tr>
            <td>
              <table width="100%">';
  
  $alternating = 'windowbg';
  // Loop through each category
  foreach ($context['shop_cats'] as $category)
  {
    echo '
                <tr class="', $alternating, '">
                  <td>', $category['name'], '</td>
                  <td width="80px">', $category['count'], ' ', ($category['count'] == 1 ? $txt['shop_item'] : $txt['shop_items']), '</td>
                  <td width="100px" align="right"><a href="', $scripturl, '?action=shop_cat;do=del&id=', $category['id'], '" onclick="return confirm(\'', $txt['shop_sure_delete_cat'], '\');">', $txt['shop_delete'], '</a></td>
                </tr>';
    $alternating = ($alternating == 'windowbg' ? 'windowbg2' : 'windowbg');
  }

  echo '
              </table>
            </td>
          </tr>
          <tr class="catbg"><td>', $txt['shop_new_cat'], '</td></tr>
          <tr>
            <td>
              <form action="', $scripturl, '?action=shop_cat;do=add" method="post">
                <label>', $txt['shop_name'], ': <input type="text" name="cat_name" id="cat_name" size="50" /></label><br />
                <input type="submit" value="', $txt['shop_create_cat'], '" />
              </form>
            </td>
          </tr>
        </table>';
}
?>
