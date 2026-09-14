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
 * lib.php
 *
 * @package   mod_finance
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the features supported by the activity.
 *
 * @param string $feature Feature constant.
 * @return mixed
 */
function finance_supports($feature) {
    return match ($feature) {
        FEATURE_MOD_INTRO => true,
        FEATURE_SHOW_DESCRIPTION => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_OTHER,
        default => null,
    };
}

/**
 * Adds a finance activity instance.
 *
 * @param stdClass $data Activity data.
 * @param mod_finance_mod_form|null $mform Form instance.
 * @return int
 */
function finance_add_instance($data, $mform = null) {
    return \mod_finance\instance_manager::add($data);
}

/**
 * Updates a finance activity instance.
 *
 * @param stdClass $data Activity data.
 * @param mod_finance_mod_form|null $mform Form instance.
 * @return bool
 */
function finance_update_instance($data, $mform = null) {
    return \mod_finance\instance_manager::update($data);
}

/**
 * Deletes a finance activity instance.
 *
 * @param int $id Instance ID.
 * @return bool
 */
function finance_delete_instance($id) {
    return \mod_finance\instance_manager::delete($id);
}
