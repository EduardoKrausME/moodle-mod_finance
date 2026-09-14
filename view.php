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
 * view.php
 *
 * @package   mod_finance
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

$id = required_param("id", PARAM_INT);
$cm = get_coursemodule_from_id("finance", $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$finance = $DB->get_record("finance", ["id" => $cm->instance], "*", MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/finance:view", $context);

$PAGE->set_url("/mod/finance/view.php", ["id" => $cm->id]);
$PAGE->set_title(format_string($finance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->js_call_amd("mod_finance/calculator", "init");

$event = \mod_finance\event\course_module_viewed::create([
    "objectid" => $cm->id,
    "context" => $context,
]);
$event->add_record_snapshot("course_modules", $cm);
$event->add_record_snapshot("course", $course);
$event->trigger();

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$currency = get_string("currency_symbol", "mod_finance");
$ratehint = get_string("rateperiodhint", "mod_finance");

$modes = [];
$definitions = [
    ["field" => "enable_simple", "key" => "simple", "label" => "simpleinterest", "description" => "simpleinterest_desc"],
    ["field" => "enable_compound", "key" => "compound", "label" => "compoundinterest", "description" => "compoundinterest_desc"],
    ["field" => "enable_presentvalue", "key" => "present", "label" => "presentvalue", "description" => "presentvalue_desc"],
    ["field" => "enable_futurevalue", "key" => "future", "label" => "futurevalue", "description" => "futurevalue_desc"],
    ["field" => "enable_payment", "key" => "payment", "label" => "payment", "description" => "payment_desc"],
];

foreach ($definitions as $definition) {
    if (!empty($finance->{$definition["field"]})) {
        $modes[] = [
            "key" => $definition["key"],
            "label" => get_string($definition["label"], "mod_finance"),
            "description" => get_string($definition["description"], "mod_finance"),
        ];
    }
}

foreach ($modes as $index => &$mode) {
    $mode["active"] = $index === 0;
}
unset($mode);

$templatecontext = [
    "modes" => $modes,
    "currency" => $currency,
    "ratehint" => $ratehint,
    "labels" => [
        "principal" => get_string("principal", "mod_finance"),
        "rate" => get_string("rate", "mod_finance"),
        "periods" => get_string("periods", "mod_finance"),
        "futureamount" => get_string("futureamount", "mod_finance"),
        "presentamount" => get_string("presentamount", "mod_finance"),
        "financedamount" => get_string("financedamount", "mod_finance"),
        "calculate" => get_string("calculate", "mod_finance"),
        "result" => get_string("result", "mod_finance"),
        "interest" => get_string("interest", "mod_finance"),
        "totalamount" => get_string("totalamount", "mod_finance"),
        "presentvalue" => get_string("presentvalue", "mod_finance"),
        "futurevalue" => get_string("futurevalue", "mod_finance"),
        "installment" => get_string("installment", "mod_finance"),
        "totalpaid" => get_string("totalpaid", "mod_finance"),
        "formula" => get_string("formula", "mod_finance"),
        "steps" => get_string("steps", "mod_finance"),
        "invalidvalues" => get_string("invalidvalues", "mod_finance"),
    ],
];

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($finance->name));

if (!empty($finance->intro)) {
    echo $OUTPUT->box(format_module_intro("finance", $finance, $cm->id), "generalbox mod_introbox");
}

echo $OUTPUT->render_from_template("mod_finance/calculator", $templatecontext);
echo $OUTPUT->footer();
