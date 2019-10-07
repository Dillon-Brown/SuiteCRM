{*
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2019 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */
 *}
<form name='delete{$ZIP_FILE}' action='{$FORM_ACTION}' method='POST'>
    <input type='hidden' name='current_step' value='{$NEXT_STEP}'>
    <input type='hidden' name='languagePackAction' value='{$TYPE}'>
    <input type='hidden' name='manifest' value='\".urlencode({$MANIFEST}).\"'>
    <input type='hidden' name='zipFile' value='\".urlencode({$ZIP_FILE}).\"'>
    <table><tr>
            <td align=\"left\" valign=\"top\" colspan=2>
                <b><font color='red' >{$MOD.LBL_MODULE_LICENSE}</font></b>
            </td>
            <td>
                <span><a class=\"listViewTdToolsS1\" id='href_animate' onClick=\"PackageManager.toggleLowerDiv('span_animate_div_{$DIV_ID}', 'span_license_div_{$DIV_ID}', 350, 0);\"><span id='span_animate_div_{$DIV_ID}'<img src='".SugarThemeRegistry::current()->getImageURL('advanced_search.gif')."' width='8' height='8' alt='Advanced' border='0'>&nbsp;Expand</span></a></span></td>
            </td>
        </tr>
    </table>
    <div id='span_license_div_{$DIV_ID}' style=\"display: none;">
    <table>
        <tr>
            <td align=\"left\" valign=\"top\" colspan=2>
                <textarea cols=\"100\" rows=\"8\">{$LICENSE_CONTENT}</textarea>
            </td>
        </tr>
        <tr>
            <td align=\"left\" valign=\"top\" colspan=2>
                <input type='radio' id='radio_license_agreement_accept' name='radio_license_agreement' value='accept' onClick=\"document.getElementById('{$$modify_field}').value = 'yes';\">{$MOD.LBL_ACCEPT}&nbsp;
                <input type='radio' id='radio_license_agreement_reject' name='radio_license_agreement' value='reject' checked onClick=\"document.getElementById('{$modify_field}').value = 'no';\">{$MOD.LBL_DENY}
            </td>
        </tr>
    </table>
    </div>
</form>
