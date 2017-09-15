<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2017 SalesAgility Ltd.
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

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once("modules/AOW_WorkFlow/aow_utils.php");

class AOW_WorkFlowController extends SugarController
{

    protected function action_getModuleFields()
    {
        if (!empty($_REQUEST['aow_module']) && $_REQUEST['aow_module'] != '') {
            if (isset($_REQUEST['rel_field']) && $_REQUEST['rel_field'] != '') {
                $module = getRelatedModule($_REQUEST['aow_module'], $_REQUEST['rel_field']);
            } else {
                $module = $_REQUEST['aow_module'];
            }
            echo getModuleFields($module, $_REQUEST['view'], $_REQUEST['aow_value']);
        }
        die;

    }

    protected function action_getRelatedModule()
    {
        if (!empty($_REQUEST['aow_module']) && $_REQUEST['aow_module'] != '') {
            if (isset($_REQUEST['rel_field']) && $_REQUEST['rel_field'] != '') {
                $module = getRelatedModule($_REQUEST['aow_module'], $_REQUEST['rel_field']);
            } else {
                $module = $_REQUEST['aow_module'];
            }
            echo htmlspecialchars($module);
        }
        die;

    }

    protected function action_getModuleRelationships()
    {
        if (!empty($_REQUEST['aow_module']) && $_REQUEST['aow_module'] != '') {
            if (isset($_REQUEST['rel_field']) && $_REQUEST['rel_field'] != '') {
                $module = getRelatedModule($_REQUEST['aow_module'], $_REQUEST['rel_field']);
            } else {
                $module = $_REQUEST['aow_module'];
            }
            echo getModuleRelationships($module);
        }
        die;

    }


    protected function action_getModuleOperatorField()
    {

        global $app_list_strings, $beanFiles, $beanList;

        if (isset($_REQUEST['rel_field']) && $_REQUEST['rel_field'] != '') {
            $module = getRelatedModule($_REQUEST['aow_module'], $_REQUEST['rel_field']);
        } else {
            $module = $_REQUEST['aow_module'];
        }
        $fieldname = $_REQUEST['aow_fieldname'];
        $aow_field = $_REQUEST['aow_newfieldname'];

        if (isset($_REQUEST['view'])) {
            $view = $_REQUEST['view'];
        } else {
            $view = 'EditView';
        }

        if (isset($_REQUEST['aow_value'])) {
            $value = $_REQUEST['aow_value'];
        } else {
            $value = '';
        }


        require_once($beanFiles[$beanList[$module]]);
        $focus = new $beanList[$module];
        $vardef = $focus->getFieldDefinition($fieldname);

        if ($vardef) {

            switch ($vardef['type']) {
                case 'double':
                case 'decimal':
                case 'float':
                case 'currency':
                    $valid_opp = array(
                        'Equal_To',
                        'Not_Equal_To',
                        'Greater_Than',
                        'Less_Than',
                        'Greater_Than_or_Equal_To',
                        'Less_Than_or_Equal_To',
                        'is_null'
                    );
                    break;
                case 'uint':
                case 'ulong':
                case 'long':
                case 'short':
                case 'tinyint':
                case 'int':
                    $valid_opp = array(
                        'Equal_To',
                        'Not_Equal_To',
                        'Greater_Than',
                        'Less_Than',
                        'Greater_Than_or_Equal_To',
                        'Less_Than_or_Equal_To',
                        'is_null'
                    );
                    break;
                case 'date':
                case 'datetime':
                case 'datetimecombo':
                    $valid_opp = array(
                        'Equal_To',
                        'Not_Equal_To',
                        'Greater_Than',
                        'Less_Than',
                        'Greater_Than_or_Equal_To',
                        'Less_Than_or_Equal_To',
                        'is_null'
                    );
                    break;
                case 'enum':
                case 'multienum':
                    $valid_opp = array('Equal_To', 'Not_Equal_To', 'is_null');
                    break;
                default:
                    $valid_opp = array('Equal_To', 'Not_Equal_To', 'Contains', 'Starts_With', 'Ends_With', 'is_null');
                    break;
            }

            foreach ($app_list_strings['aow_operator_list'] as $key => $keyValue) {
                if (!in_array($key, $valid_opp)) {
                    unset($app_list_strings['aow_operator_list'][$key]);
                }
            }


            $app_list_strings['aow_operator_list'];
            if ($view == 'EditView') {
                echo "<select type='text' name='$aow_field' id='$aow_field ' title='' tabindex='116'>" . get_select_options_with_id($app_list_strings['aow_operator_list'],
                        $value) . "</select>";
            } else {
                echo $app_list_strings['aow_operator_list'][$value];
            }
        }
        die;

    }

    protected function action_getFieldTypeOptions()
    {

        global $app_list_strings, $beanFiles, $beanList;

        if (isset($_REQUEST['rel_field']) && $_REQUEST['rel_field'] != '') {
            $module = getRelatedModule($_REQUEST['aow_module'], $_REQUEST['rel_field']);
        } else {
            $module = $_REQUEST['aow_module'];
        }
        $fieldname = $_REQUEST['aow_fieldname'];
        $aow_field = $_REQUEST['aow_newfieldname'];

        if (isset($_REQUEST['view'])) {
            $view = $_REQUEST['view'];
        } else {
            $view = 'EditView';
        }

        if (isset($_REQUEST['aow_value'])) {
            $value = $_REQUEST['aow_value'];
        } else {
            $value = '';
        }


        require_once($beanFiles[$beanList[$module]]);
        $focus = new $beanList[$module];
        $vardef = $focus->getFieldDefinition($fieldname);

        switch ($vardef['type']) {
            case 'double':
            case 'decimal':
            case 'float':
            case 'currency':
                $valid_opp = array('Value', 'Field', 'Any_Change');
                break;
            case 'uint':
            case 'ulong':
            case 'long':
            case 'short':
            case 'tinyint':
            case 'int':
                $valid_opp = array('Value', 'Field', 'Any_Change');
                break;
            case 'date':
            case 'datetime':
            case 'datetimecombo':
                $valid_opp = array('Value', 'Field', 'Any_Change', 'Date');
                break;
            case 'enum':
            case 'dynamicenum':
            case 'multienum':
                $valid_opp = array('Value', 'Field', 'Any_Change', 'Multi');
                break;
            case 'relate':
            case 'id':
                $valid_opp = array('Value', 'Field', 'Any_Change', 'SecurityGroup');
                break;
            default:
                $valid_opp = array('Value', 'Field', 'Any_Change');
                break;
        }

        if (!file_exists('modules/SecurityGroups/SecurityGroup.php')) {
            unset($app_list_strings['aow_condition_type_list']['SecurityGroup']);
        }
        foreach ($app_list_strings['aow_condition_type_list'] as $key => $keyValue) {
            if (!in_array($key, $valid_opp)) {
                unset($app_list_strings['aow_condition_type_list'][$key]);
            }
        }

        if ($view == 'EditView') {
            echo "<select type='text'  name='$aow_field' id='$aow_field' title='' tabindex='116'>" . get_select_options_with_id($app_list_strings['aow_condition_type_list'],
                    $value) . "</select>";
        } else {
            echo $app_list_strings['aow_condition_type_list'][$value];
        }
        die;

    }

    protected function action_getActionFieldTypeOptions()
    {

        global $app_list_strings, $beanFiles, $beanList;

        $module = $_REQUEST['aow_module'];
        $fieldname = $_REQUEST['aow_fieldname'];
        $aow_field = $_REQUEST['aow_newfieldname'];

        if (isset($_REQUEST['view'])) {
            $view = $_REQUEST['view'];
        } else {
            $view = 'EditView';
        }

        if (isset($_REQUEST['aow_value'])) {
            $value = $_REQUEST['aow_value'];
        } else {
            $value = '';
        }


        require_once($beanFiles[$beanList[$module]]);
        $focus = new $beanList[$module];
        $vardef = $focus->getFieldDefinition($fieldname);

        switch ($vardef['type']) {
            case 'double':
            case 'decimal':
            case 'float':
            case 'currency':
                $valid_opp = array('Value', 'Field');
                break;
            case 'uint':
            case 'ulong':
            case 'long':
            case 'short':
            case 'tinyint':
            case 'int':
                $valid_opp = array('Value', 'Field');
                break;
            case 'date':
            case 'datetime':
            case 'datetimecombo':
                $valid_opp = array('Value', 'Field', 'Date');
                break;
            case 'enum':
            case 'multienum':
                $valid_opp = array('Value', 'Field');
                break;
            case 'relate':
                $valid_opp = array('Value', 'Field');
                if ($vardef['module'] == 'Users') {
                    $valid_opp = array('Value', 'Field', 'Round_Robin', 'Least_Busy', 'Random');
                }
                break;
            default:
                $valid_opp = array('Value', 'Field');
                break;
        }

        foreach ($app_list_strings['aow_action_type_list'] as $key => $keyValue) {
            if (!in_array($key, $valid_opp)) {
                unset($app_list_strings['aow_action_type_list'][$key]);
            }
        }

        if ($view == 'EditView') {
            echo "<select type='text'  name='$aow_field' id='$aow_field' title='' tabindex='116'>" . get_select_options_with_id($app_list_strings['aow_action_type_list'],
                    $value) . "</select>";
        } else {
            echo $app_list_strings['aow_action_type_list'][$value];
        }
        die;

    }

    protected function action_getModuleFieldType()
    {
        if (isset($_REQUEST['rel_field']) && $_REQUEST['rel_field'] != '') {
            $rel_module = getRelatedModule($_REQUEST['aow_module'], $_REQUEST['rel_field']);
        } else {
            $rel_module = $_REQUEST['aow_module'];
        }
        $module = $_REQUEST['aow_module'];
        $fieldname = $_REQUEST['aow_fieldname'];
        $aow_field = $_REQUEST['aow_newfieldname'];

        if (isset($_REQUEST['view'])) {
            $view = $_REQUEST['view'];
        } else {
            $view = 'EditView';
        }

        if (isset($_REQUEST['aow_value'])) {
            $value = $_REQUEST['aow_value'];
        } else {
            $value = '';
        }

        switch ($_REQUEST['aow_type']) {
            case 'Field':
                if (isset($_REQUEST['alt_module']) && $_REQUEST['alt_module'] != '') {
                    $module = $_REQUEST['alt_module'];
                }
                if ($view == 'EditView') {
                    echo "<select type='text'  name='$aow_field' id='$aow_field ' title='' tabindex='116'>" . getModuleFields($module,
                            $view, $value, getValidFieldsTypes($module, $fieldname)) . "</select>";
                } else {
                    echo getModuleFields($module, $view, $value);
                }
                break;
            case 'Any_Change';
                echo '';
                break;
            case 'Date':
                echo getDateField($module, $aow_field, $view, $value, false);
                break;
            case 'Multi':
                echo getModuleField($rel_module, $fieldname, $aow_field, $view, $value, 'multienum');
                break;
            case 'SecurityGroup':
                $module = 'Accounts';
                $fieldname = 'SecurityGroups';
            case 'Value':
            default:
                echo getModuleField($rel_module, $fieldname, $aow_field, $view, $value);
                break;
        }
        die;

    }

    protected function action_getModuleFieldTypeSet()
    {
        $module = $_REQUEST['aow_module'];
        $fieldname = $_REQUEST['aow_fieldname'];
        $aow_field = $_REQUEST['aow_newfieldname'];

        if (isset($_REQUEST['view'])) {
            $view = $_REQUEST['view'];
        } else {
            $view = 'EditView';
        }

        if (isset($_REQUEST['aow_value'])) {
            $value = $_REQUEST['aow_value'];
        } else {
            $value = '';
        }

        switch ($_REQUEST['aow_type']) {
            case 'Field':
                $valid_fields = getValidFieldsTypes($module, $fieldname);
                if (isset($_REQUEST['alt_module']) && $_REQUEST['alt_module'] != '') {
                    $module = $_REQUEST['alt_module'];
                }
                if ($view == 'EditView') {
                    echo "<select type='text'  name='$aow_field' id='$aow_field ' title='' tabindex='116'>" . getModuleFields($module,
                            $view, $value, $valid_fields) . "</select>";
                } else {
                    echo getModuleFields($module, $view, $value);
                }
                break;
            case 'Date':
                if (isset($_REQUEST['alt_module']) && $_REQUEST['alt_module'] != '') {
                    $module = $_REQUEST['alt_module'];
                }
                echo getDateField($module, $aow_field, $view, $value);
                break;
            Case 'Round_Robin';
            Case 'Least_Busy';
            Case 'Random';
                echo getAssignField($aow_field, $view, $value);
                break;
            case 'Value':
            default:
                echo getModuleField($module, $fieldname, $aow_field, $view, $value);
                break;
        }
        die;

    }

    protected function action_getModuleField()
    {
        if (isset($_REQUEST['view'])) {
            $view = $_REQUEST['view'];
        } else {
            $view = 'EditView';
        }

        if (isset($_REQUEST['aow_value'])) {
            $value = $_REQUEST['aow_value'];
        } else {
            $value = '';
        }

        echo getModuleField($_REQUEST['aow_module'], $_REQUEST['aow_fieldname'], $_REQUEST['aow_newfieldname'], $view,
            $value);
        die;
    }

    protected function action_getRelFieldTypeSet()
    {
        $module = $_REQUEST['aow_module'];
        $fieldname = $_REQUEST['aow_fieldname'];
        $aow_field = $_REQUEST['aow_newfieldname'];

        if (isset($_REQUEST['view'])) {
            $view = $_REQUEST['view'];
        } else {
            $view = 'EditView';
        }

        if (isset($_REQUEST['aow_value'])) {
            $value = $_REQUEST['aow_value'];
        } else {
            $value = '';
        }

        switch ($_REQUEST['aow_type']) {
            case 'Field':
                if (isset($_REQUEST['alt_module']) && $_REQUEST['alt_module'] != '') {
                    $module = $_REQUEST['alt_module'];
                }
                if ($view == 'EditView') {
                    echo "<select type='text'  name='$aow_field' id='$aow_field ' title='' tabindex='116'>" . getModuleFields($module,
                            $view, $value) . "</select>";
                } else {
                    echo getModuleFields($module, $view, $value);
                }
                break;
            case 'Value':
            default:
                echo getModuleField($module, $fieldname, $aow_field, $view, $value);
                break;
        }
        die;

    }

    protected function action_getRelActionFieldTypeOptions()
    {

        global $app_list_strings, $beanFiles, $beanList;

        $module = $_REQUEST['aow_module'];
        $alt_module = $_REQUEST['alt_module'];
        $fieldname = $_REQUEST['aow_fieldname'];
        $aow_field = $_REQUEST['aow_newfieldname'];

        if (isset($_REQUEST['view'])) {
            $view = $_REQUEST['view'];
        } else {
            $view = 'EditView';
        }

        if (isset($_REQUEST['aow_value'])) {
            $value = $_REQUEST['aow_value'];
        } else {
            $value = '';
        }


        require_once($beanFiles[$beanList[$module]]);
        $focus = new $beanList[$module];
        $vardef = $focus->getFieldDefinition($fieldname);
        $valid_opp = array('Value', 'Field');

        foreach ($app_list_strings['aow_rel_action_type_list'] as $key => $keyValue) {
            if (!in_array($key, $valid_opp)) {
                unset($app_list_strings['aow_rel_action_type_list'][$key]);
            }
        }

        if ($view == 'EditView') {
            echo "<select type='text'  name='$aow_field' id='$aow_field' title='' tabindex='116'>" . get_select_options_with_id($app_list_strings['aow_rel_action_type_list'],
                    $value) . "</select>";
        } else {
            echo $app_list_strings['aow_rel_action_type_list'][$value];
        }
        die;

    }

    protected function action_getAction()
    {
        global $beanList, $beanFiles;

        $action_name = 'action' . $_REQUEST['aow_action'];
        $line = $_REQUEST['line'];

        if ($_REQUEST['aow_module'] == '' || !isset($beanList[$_REQUEST['aow_module']])) {
            echo '';
            die;
        }

        if (file_exists('custom/modules/AOW_Actions/actions/' . $action_name . '.php')) {

            require_once('custom/modules/AOW_Actions/actions/' . $action_name . '.php');

        } else {
            if (file_exists('modules/AOW_Actions/actions/' . $action_name . '.php')) {

                require_once('modules/AOW_Actions/actions/' . $action_name . '.php');

            } else {
                echo '';
                die;
            }
        }

        $custom_action_name = "custom" . $action_name;
        if (class_exists($custom_action_name)) {
            $action_name = $custom_action_name;
        }

        $id = '';
        $params = array();
        if (isset($_REQUEST['id'])) {
            require_once('modules/AOW_Actions/AOW_Action.php');
            $aow_action = new AOW_Action();
            $aow_action->retrieve($_REQUEST['id']);
            $id = $aow_action->id;
            $params = unserialize(base64_decode($aow_action->parameters));
        }

        $action = new $action_name($id);

        require_once($beanFiles[$beanList[$_REQUEST['aow_module']]]);
        $bean = new $beanList[$_REQUEST['aow_module']];
        echo $action->edit_display($line, $bean, $params);
        die;
    }

    protected function action_getEmailField()
    {
        $module = $_REQUEST['aow_module'];
        $aow_field = $_REQUEST['aow_newfieldname'];

        if (isset($_REQUEST['view'])) {
            $view = $_REQUEST['view'];
        } else {
            $view = 'EditView';
        }

        if (isset($_REQUEST['aow_value'])) {
            $value = $_REQUEST['aow_value'];
        } else {
            $value = '';
        }

        switch ($_REQUEST['aow_type']) {
            case 'Record Email';
                echo '';
                break;
            case 'Related Field':
                $rel_field_list = getRelatedEmailableFields($module);
                if ($view == 'EditView') {
                    echo "<select type='text'  name='$aow_field' id='$aow_field' title='' tabindex='116'>" . get_select_options_with_id($rel_field_list,
                            $value) . "</select>";
                } else {
                    echo $rel_field_list[$value];
                }
                break;
            case 'Specify User':
                echo getModuleField('Accounts', 'assigned_user_name', $aow_field, $view, $value);
                break;
            case 'Users':
                echo getAssignField($aow_field, $view, $value);
                break;
            case 'Email Address':
            default:
                if ($view == 'EditView') {
                    echo "<input type='text' name='$aow_field' id='$aow_field' size='25' title='' tabindex='116' value='$value'>";
                } else {
                    echo $value;
                }
                break;
        }
        die;

    }


    protected function action_testFlow()
    {

        echo 'Started<br />';
        require_once('modules/AOW_WorkFlow/AOW_WorkFlow.php');
        $workflow = new AOW_WorkFlow();

        if ($workflow->run_flows()) {
            echo 'PASSED';
        }

    }

}
