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

defined('MOODLE_INTERNAL') || die;

require_once("{$CFG->dirroot}/course/moodleform_mod.php");

/**
 * Activity settings form.
 *
 * @package mod_finance
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_finance_mod_form extends moodleform_mod {
    /**
     * Defines the form.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement("header", "general", get_string("general", "form"));
        $mform->addElement("text", "name", get_string("financename", "mod_finance"), ["size" => "64"]);
        $mform->setType("name", PARAM_TEXT);
        $mform->addRule("name", null, "required", null, "client");

        $this->standard_intro_elements();

        $mform->addElement("html", html_writer::tag("h3", get_string("calculations", "mod_finance")));
        $mform->addElement("static", "calculationsnote", "", get_string("calculations_help", "mod_finance"));

        $fields = [
            "enable_simple" => "simpleinterest",
            "enable_compound" => "compoundinterest",
            "enable_presentvalue" => "presentvalue",
            "enable_futurevalue" => "futurevalue",
            "enable_payment" => "payment",
        ];

        foreach ($fields as $field => $stringkey) {
            $mform->addElement("advcheckbox", $field, get_string($stringkey, "mod_finance"));
            $mform->setDefault($field, 1);
        }

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Validates the form.
     *
     * @param array $data Submitted data.
     * @param array $files Submitted files.
     * @return array Validation errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $enabled = false;

        foreach (\mod_finance\instance_manager::calculation_fields() as $field) {
            if (!empty($data[$field])) {
                $enabled = true;
                break;
            }
        }

        if (!$enabled) {
            $errors["enable_simple"] = get_string("error_enableone", "mod_finance");
        }

        return $errors;
    }
}
