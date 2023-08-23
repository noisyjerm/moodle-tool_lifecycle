<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Step subplugin for changing role in cohort enrol method.
 *
 * @package lifecyclestep_updatecohortenrol
 * @copyright  2023 Te Wānanga o Aotearoa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lifecycle\step;

use tool_lifecycle\local\manager\settings_manager;
use tool_lifecycle\local\response\step_response;
use tool_lifecycle\settings_type;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

class updatecohortenrol extends libbase {

    /**
     * @param int $processid of the respective process.
     * @param int $instanceid of the step instance.
     * @param mixed $course to be processed.
     * @return step_response
     */
    public function process_course($processid, $instanceid, $course) {
        $enrolmethods = enrol_get_instances($course->id, false);
        $oldrole = settings_manager::get_settings($instanceid, settings_type::STEP)['setting_oldrole'];
        $newrole = settings_manager::get_settings($instanceid, settings_type::STEP)['setting_newrole'];
        foreach ($enrolmethods as $enrol) {
            if ($enrol->enrol === 'cohort' && $enrol->roleid == $oldrole) {
                $update = new \stdClass();
                $update->roleid = $newrole;
                $update->customint2 = $enrol->customint2;
                $instance = new \enrol_cohort_plugin();
                $instance->update_instance($enrol, $update);
            }
        }

        return step_response::proceed();
    }

    /**
     * Defines which settings each instance of the subplugin offers for the user to define.
     * @return instance_setting[] containing settings keys and PARAM_TYPES
     */
    public function instance_settings() {
        return array(
            new instance_setting('setting_oldrole', PARAM_INT, true),
            new instance_setting('setting_newrole', PARAM_INT, true),
        );
    }

    /**
     * This method can be overriden, to add form elements to the form_step_instance.
     * It is called in definition().
     * @param \MoodleQuickForm $mform
     * @throws \coding_exception
     */
    public function extend_add_instance_form_definition($mform) {
        global $COURSE;
        $ctx = \context_course::instance($COURSE->id);
        $roles = [0 => get_string('none')];
        $roles = $roles + get_default_enrol_roles($ctx);

        $elementname = 'setting_oldrole';
        $mform->addElement('select', $elementname,
            get_string($elementname, 'lifecyclestep_updatecohortenrol'), $roles);
        $mform->setType($elementname, PARAM_INT);
        $mform->setDefault($elementname, 5);

        $elementname = 'setting_newrole';
        $mform->addElement('select', $elementname,
            get_string($elementname, 'lifecyclestep_updatecohortenrol'), $roles);
        $mform->setType($elementname, PARAM_INT);

    }

    /**
     * The return value should be equivalent with the name of the subplugin folder.
     * @return string technical name of the subplugin
     */
    public function get_subpluginname() {
        return 'updatecohortenrol';
    }

}
