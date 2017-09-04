{*
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
*}
{sugar_include type="smarty" file="modules/Project/tpls/PopupHeader.tpl"}

<html>
<!--Create task pop-up-->
<div style="display: none;">
    <div id="dialog"  title="{$mod.LBL_ADD_NEW_TASK}">
        <p>
            {$mod.LBL_EDIT_TASK_PROPERTIES}
        </p>
        <form id="popup_form" class="projects-gantt-chart-popup">
            <fieldset>
                <table width="100%">
                    <tr><td>

                            <input type="hidden" name="project_id" id="project_id" value="{$projectID}">
                            <input type="hidden" name="consider_business_hours" id="consider_business_hours" value="{$projectBusinessHours}">
                            <input type="hidden" name="task_id" style="display: none; visibility: collapse;" id="task_id" value="">

                            <input type="radio" name="Milestone" value="Subtask" checked="checked" id="Subtask" />
                            <label id="Subtask_label" for="Subtask"><{$mod.LBL_SUBTASK}></label>
                            <input type="radio" name="Milestone" value="Milestone" id="Milestone" />

                            <label id="Milestone_label" for="Milestone"><{$mod.LBL_MILESTONE_FLAG}></label>
                            <label id="parent_task_id" for="parent_task" style="display: none; visibility: collapse;"><{$mod.LBL_PARENT_TASK_ID}></label>
                            <input id="parent_task" class="text ui-widget-content ui-corner-all" style="display: none; visibility: collapse;" type="text" name="parent_task" value="" />

                            <label for="task_name"><{$mod.LBL_TASK_NAME}></label>
                            <input type="text" name="task_name" id="task_name" class="text ui-widget-content ui-corner-all" />

                            <label for="Predecessor"><{$mod.LBL_PREDECESSORS}></label>
                            <select id="Predecessor" name="Predecessor" class="text ui-widget-content ui-corner-all" />

                            <label for="relation_type"><{$mod.LBL_RELATIONSHIP_TYPE}></label>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
    <!--Delete task pop-up-->
    <div id="delete_dialog" title="">
        <p>
            Are you sure you want to delete this task?
        </p>
    </div>
</div>
<!-- Pop-up End -->

<div id="wrapper" >

    <input id="project_id" type="hidden" name="project_id" value="{$projectTasks}" />
    <div id="project_wrapper">


    </div>
</div>
<!--Main body end-->


</html>