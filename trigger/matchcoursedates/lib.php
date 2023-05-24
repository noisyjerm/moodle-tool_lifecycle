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
 * Subplugin for matching course dates.
 *
 * @package lifecycletrigger_matchcoursedates
 * @copyright  2023 Te Wānanga o Aotearoa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lifecycle\trigger;

use tool_lifecycle\local\manager\settings_manager;
use tool_lifecycle\local\response\trigger_response;
use tool_lifecycle\settings_type;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../../lib.php');

/**
 * Class which implements the basic methods necessary for a cleanyp courses trigger subplugin
 * @package lifecycletrigger_matchcoursedates
 * @copyright  2023 Te Wānanga o Aotearoa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matchcoursedates extends base_automatic {

    /**
     * Checks the course and returns a repsonse, which tells if the course should be further processed.
     * @param object $course Course to be processed.
     * @param int $triggerid Id of the trigger instance.
     * @return trigger_response
     */
    public function check_course($course, $triggerid) {
        // Everything is already in the sql statement.
        return trigger_response::trigger();
    }

    /**
     * Add sql comparing the course start and end dates with the dates specified in the trigger settings.
     * @param int $triggerid Id of the trigger.
     * @return array A list containing the constructed sql fragment and an array of parameters.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_course_recordset_where($triggerid) {
        $where = "{course}.startdate > :earlieststart AND {course}.startdate < :lateststart";
        $checkenddate = settings_manager::get_settings($triggerid, settings_type::TRIGGER)['checkenddates'] && true;
        if ($checkenddate) {
            $where .= " AND {course}.enddate > :earliestend AND {course}.enddate < :latestend";
        }
        $params = array(
            "earlieststart" => settings_manager::get_settings($triggerid, settings_type::TRIGGER)['earlieststart'],
            "lateststart" => settings_manager::get_settings($triggerid, settings_type::TRIGGER)['lateststart'] + 86400,
            "earliestend" => settings_manager::get_settings($triggerid, settings_type::TRIGGER)['earliestend'],
            "latestend" => settings_manager::get_settings($triggerid, settings_type::TRIGGER)['latestend'] + 86400,
        );
        return array($where, $params);
    }

    /**
     * The return value should be equivalent with the name of the subplugin folder.
     * @return string technical name of the subplugin
     */
    public function get_subpluginname() {
        return 'matchcoursedates';
    }

    /**
     * Defines which settings each instance of the subplugin offers for the user to define.
     * @return instance_setting[] containing settings keys and PARAM_TYPES
     */
    public function instance_settings() {
        return array(
            new instance_setting('earlieststart', PARAM_INT, true),
            new instance_setting('lateststart', PARAM_INT, true),
            new instance_setting('checkenddates', PARAM_BOOL, true),
            new instance_setting('earliestend', PARAM_INT, true),
            new instance_setting('latestend', PARAM_INT, true),
        );
    }

    /**
     * Adding date selectors to the form so we can set our settings.
     * @param \MoodleQuickForm $mform
     * @throws \coding_exception
     */
    public function extend_add_instance_form_definition($mform) {
        $element = 'earlieststart';
        $mform->addElement('date_selector', $element, get_string($element, 'lifecycletrigger_matchcoursedates'));
        $mform->addHelpButton('earlieststart', 'earlieststart', 'lifecycletrigger_matchcoursedates');
        $element = 'lateststart';
        $mform->addElement('date_selector', $element, get_string($element, 'lifecycletrigger_matchcoursedates'));

        $element = 'checkenddates';
        $mform->addElement('advcheckbox', $element, get_string($element, 'lifecycletrigger_matchcoursedates'));
        $mform->setType($element, PARAM_BOOL);
        $mform->setDefault($element, false);

        $element = 'earliestend';
        $mform->addElement('date_selector', $element, get_string($element, 'lifecycletrigger_matchcoursedates'));
        $mform->addHelpButton($element, $element, 'lifecycletrigger_matchcoursedates');
        $mform->hideIf($element, 'checkenddates');
        $element = 'latestend';
        $mform->addElement('date_selector', $element, get_string($element, 'lifecycletrigger_matchcoursedates'));
        $mform->hideIf($element, 'checkenddates');
    }

}
