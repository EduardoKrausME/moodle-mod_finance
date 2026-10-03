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
 * Backup task for mod_finance.
 *
 * @package mod_finance
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/mod/finance/backup/moodle2/backup_finance_stepslib.php');

/**
 * backup_finance_activity_task
 */
class backup_finance_activity_task extends backup_activity_task {
    /**
     * Defines activity-specific settings.
     *
     * @return void
     */
    protected function define_my_settings() {
    }

    /**
     * Defines activity-specific steps.
     *
     * @return void
     */
    protected function define_my_steps() {
        $this->add_step(new backup_finance_activity_structure_step("finance_structure", "finance.xml"));
    }

    /**
     * Encodes content links.
     *
     * @param string $content Content to encode.
     * @return string
     */
    public static function encode_content_links($content) {
        global $CFG;

        $baseurl = preg_quote($CFG->wwwroot . '/mod/finance/view.php?id=', '/');
        return preg_replace('/(' . $baseurl . ')([0-9]+)/', '$@FINANCEVIEWBYID*$2@$', $content);
    }
}
