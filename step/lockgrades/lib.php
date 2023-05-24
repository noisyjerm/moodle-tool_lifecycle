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
 * Step subplugin for locking grades.
 *
 * @package    lifecyclestep_lockgrades
 * @copyright  2023 Te Wānanga o Aotearoa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lifecycle\step;

use tool_lifecycle\local\manager\settings_manager;
use tool_lifecycle\local\response\step_response;
use grade_item;
use grade_grade;
use tool_lifecycle\settings_type;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

class lockgrades extends libbase {

    /**
     * @param int $processid of the respective process.
     * @param int $instanceid of the step instance.
     * @param mixed $course to be processed.
     * @return step_response
     */
    public function process_course($processid, $instanceid, $course) {
        $ctx = \context_course::instance($course->id);
        $gradeitems = grade_item::fetch_all(['courseid' => $course->id]);
        $capabiltiy = settings_manager::get_settings($instanceid, settings_type::STEP)['setting_capability'];
        $students = $capabiltiy != '' ? get_enrolled_users($ctx, $capabiltiy) : [];
        if (grade_item::fetch_all(['courseid' => $course->id, 'needsupdate' => 1]) !== false) {
            grade_regrade_final_grades($course->id);
        }
        foreach ($gradeitems as $key => $gradeitem) {
            if (settings_manager::get_settings($instanceid, settings_type::STEP)['setting_items'] === 1) {
                $gradeitem->set_locked(1);
            }
            $usergrades = grade_grade::fetch_users_grades($gradeitem, array_keys($students));
            foreach ($usergrades as $grade) {
                if (isset($grade->id)) {
                    $grade->set_locked(1);
                } else {
                    if (!$gradeitem->needsupdate) {
                        $grade->locked = time();
                    }
                    $grade->insert('lifecyclestep/lockgrades', true);
                }
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
            new instance_setting('setting_capability', PARAM_CAPABILITY, true),
            new instance_setting('setting_items', PARAM_BOOL, true),
        );
    }

    /**
     * This method can be overriden, to add form elements to the form_step_instance.
     * It is called in definition().
     * @param \MoodleQuickForm $mform
     * @throws \coding_exception
     */
    public function extend_add_instance_form_definition($mform) {
        $elementname = 'setting_capability';
        $mform->addElement('text', $elementname,
            get_string($elementname, 'lifecyclestep_lockgrades'));
        $mform->addHelpButton($elementname, $elementname, 'lifecyclestep_lockgrades');
        $mform->setType($elementname, PARAM_CAPABILITY);
        $mform->setDefault($elementname, 'mod/assign:submit');

        $elementname = 'setting_items';
        $options = [ 0 => get_string('no'),
                     1 => get_string("yes")];
        $mform->addElement('select', $elementname,
            get_string($elementname, 'lifecyclestep_lockgrades'), $options);
        $mform->setDefault($elementname, 0);
        $mform->addHelpButton($elementname, $elementname, 'lifecyclestep_lockgrades');
    }

    /**
     * The return value should be equivalent with the name of the subplugin folder.
     * @return string technical name of the subplugin
     */
    public function get_subpluginname() {
        return 'lockgrades';
    }

}
