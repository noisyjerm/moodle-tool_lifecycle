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
 * Lang strings for match course dates.
 *
 * @package lifecycletrigger_matchcoursedates
 * @copyright  2023 Te Wānanga o Aotearoa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']         = 'Match-Kurstermin - Trigger';
$string['privacy:metadata']   = 'Dieses Subplugin speichert keine persönlichen Daten.';

$string['earlieststart']      = 'Frühester Starttermin.';
$string['earlieststart_help'] = 'Der Trigger wird ausgelöst, wenn das Datum des Kursbeginns zwischen 0000h am
                                 frühesten dem frühesten Startdatum und 2400h dem spätesten Startdatum liegt.';
$string['lateststart']        = 'Spätester Starttermin.';
$string['checkenddates']      = 'Enddatum vergleichen.';
$string['earliestend']        = 'Frühestes Enddatum.';
$string['earliestend_help']   = 'Der Trigger wird ausgelöst, wenn das Enddatum des Kurses ebenfalls zwischen 0000h am frühesten Enddatum und 2400h
                                 für das späteste Enddatum liegt. Kurse ohne Enddatum verhalten sich so, als ob das Enddatum 0000h 1. Januar 1970 wäre.';
$string['latestend']          = 'Spätestes Enddatum.';
