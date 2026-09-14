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

namespace mod_finance;

/**
 * Handles activity instance persistence.
 *
 * @package mod_finance
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class instance_manager {
    /**
     * Adds an activity instance.
     *
     * @param \stdClass $data Activity data.
     * @return int
     */
    public static function add(\stdClass $data): int {
        global $DB;

        $data->timecreated = time();
        $data->timemodified = $data->timecreated;
        self::normalise_flags($data);

        return $DB->insert_record("finance", $data);
    }

    /**
     * Updates an activity instance.
     *
     * @param \stdClass $data Activity data.
     * @return bool
     */
    public static function update(\stdClass $data): bool {
        global $DB;

        $data->id = $data->instance;
        $data->timemodified = time();
        self::normalise_flags($data);

        return $DB->update_record("finance", $data);
    }

    /**
     * Deletes an activity instance.
     *
     * @param int $id Instance ID.
     * @return bool
     */
    public static function delete(int $id): bool {
        global $DB;

        if (!$DB->record_exists("finance", ["id" => $id])) {
            return false;
        }

        $DB->delete_records("finance", ["id" => $id]);
        return true;
    }

    /**
     * Normalises checkbox values before saving.
     *
     * @param \stdClass $data Activity data.
     * @return void
     */
    private static function normalise_flags(\stdClass $data): void {
        foreach (self::calculation_fields() as $field) {
            $data->{$field} = empty($data->{$field}) ? 0 : 1;
        }
    }

    /**
     * Returns the configuration field names.
     *
     * @return string[]
     */
    public static function calculation_fields(): array {
        return [
            "enable_simple",
            "enable_compound",
            "enable_presentvalue",
            "enable_futurevalue",
            "enable_payment",
        ];
    }
}
