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
 * Restore structure step for mod_finance.
 *
 * @package mod_finance
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Restore structure step for mod_finance.
 *
 * @package mod_finance
 */
class restore_finance_activity_structure_step extends restore_activity_structure_step {
    /**
     * Defines restore paths.
     *
     * @return restore_path_element[]
     */
    protected function define_structure() {
        $paths = [
            new restore_path_element("finance", "/activity/finance"),
        ];

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restores the finance instance.
     *
     * @param array $data Restored data.
     * @return void
     */
    protected function process_finance($data) {
        global $DB;

        $data = (object) $data;
        $data->course = $this->get_courseid();
        $newitemid = $DB->insert_record("finance", $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Restores files after the activity is created.
     *
     * @return void
     */
    protected function after_execute() {
        $this->add_related_files("mod_finance", "intro", null);
    }
}
