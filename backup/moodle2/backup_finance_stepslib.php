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
 * Backup structure step for mod_finance.
 *
 * @package mod_finance
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_finance_activity_structure_step extends backup_activity_structure_step {
    /**
     * Defines the backup structure.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        $finance = new backup_nested_element("finance", ["id"], [
            "name",
            "intro",
            "introformat",
            "enable_simple",
            "enable_compound",
            "enable_presentvalue",
            "enable_futurevalue",
            "enable_payment",
            "timecreated",
            "timemodified",
        ]);

        $finance->set_source_table("finance", ["id" => backup::VAR_ACTIVITYID]);
        $finance->annotate_files("mod_finance", "intro", null);

        return $this->prepare_activity_structure($finance);
    }
}
