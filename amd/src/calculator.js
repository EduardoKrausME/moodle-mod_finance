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
 * calculator.js
 *
 * @package   mod_finance
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "core/str"], function ($, Str) {
    let notation = {};

    const notationKeys = [
        "formula_compound",
        "formula_future",
        "formula_payment",
        "formula_payment_zero",
        "formula_present",
        "formula_simple",
        "formula_symbol_future",
        "formula_symbol_interest",
        "formula_symbol_payment",
        "formula_symbol_periods",
        "formula_symbol_present",
        "formula_symbol_principal",
        "formula_symbol_rate",
        "formula_symbol_total",
    ];

    const loadNotation = function () {
        const requests = notationKeys.map(function (key) {
            return {key: key, component: "mod_finance"};
        });

        return Str.get_strings(requests).then(function (values) {
            notationKeys.forEach(function (key, index) {
                notation[key] = values[index];
            });
        });
    };

    const parseNumber = function (panel, field) {
        const value = parseFloat(panel.find('[data-field="' + field + '"]').val());
        return Number.isFinite(value) ? value : null;
    };

    const getLocale = function () {
        return document.documentElement.lang || undefined;
    };

    const formatNumber = function (value) {
        return new Intl.NumberFormat(getLocale(), {
            minimumFractionDigits: 2,
            maximumFractionDigits: 6,
        }).format(value);
    };

    const formatMoney = function (root, value) {
        const symbol = root.data("currency") || "";
        const amount = new Intl.NumberFormat(getLocale(), {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(value);
        return symbol ? symbol + " " + amount : amount;
    };

    const row = function (label, value) {
        return '<div class="finance-result-row"><span>' + label + '</span><strong>' + value + '</strong></div>';
    };

    const section = function (title, content) {
        return '<div class="finance-calculation-section"><h4>' + title + '</h4>' + content + '</div>';
    };

    const invalid = function (root, panel) {
        panel.find("[data-result]").html('<div class="alert alert-danger mb-0">' + root.data("label-invalid") + "</div>");
    };

    const rateStep = function (ratePercent, rate) {
        return notation.formula_symbol_rate + " = " + formatNumber(ratePercent) + "% ÷ 100 = " + formatNumber(rate);
    };

    const factorStep = function (rate, periods, factor) {
        return "(1 + " + notation.formula_symbol_rate + ")^" + notation.formula_symbol_periods + " = (1 + " +
            formatNumber(rate) + ")^" + formatNumber(periods) + " = " + formatNumber(factor);
    };

    const calculateSimple = function (root, panel) {
        const principal = parseNumber(panel, "principal");
        const ratePercent = parseNumber(panel, "rate");
        const periods = parseNumber(panel, "periods");
        if (principal === null || ratePercent === null || periods === null || principal < 0 || periods < 0) {
            invalid(root, panel);
            return;
        }

        const rate = ratePercent / 100;
        const interest = principal * rate * periods;
        const total = principal + interest;
        const principalSymbol = notation.formula_symbol_principal;
        const interestSymbol = notation.formula_symbol_interest;
        const totalSymbol = notation.formula_symbol_total;
        const steps = [
            rateStep(ratePercent, rate),
            interestSymbol + " = " + formatNumber(principal) + " × " + formatNumber(rate) + " × " +
                formatNumber(periods) + " = " + formatNumber(interest),
            totalSymbol + " = " + principalSymbol + " + " + interestSymbol + " = " + formatNumber(principal) +
                " + " + formatNumber(interest) + " = " + formatNumber(total),
        ];

        panel.find("[data-result]").html(
            row(root.data("label-interest"), formatMoney(root, interest)) +
            row(root.data("label-total"), formatMoney(root, total)) +
            section(root.data("label-formula"), "<code>" + notation.formula_simple + "</code>") +
            section(root.data("label-steps"), "<ol><li>" + steps.join("</li><li>") + "</li></ol>")
        );
    };

    const calculateCompound = function (root, panel) {
        const principal = parseNumber(panel, "principal");
        const ratePercent = parseNumber(panel, "rate");
        const periods = parseNumber(panel, "periods");
        if (principal === null || ratePercent === null || periods === null || principal < 0 || periods < 0 || ratePercent <= -100) {
            invalid(root, panel);
            return;
        }

        const rate = ratePercent / 100;
        const factor = Math.pow(1 + rate, periods);
        const total = principal * factor;
        const interest = total - principal;
        const principalSymbol = notation.formula_symbol_principal;
        const interestSymbol = notation.formula_symbol_interest;
        const totalSymbol = notation.formula_symbol_total;
        const steps = [
            rateStep(ratePercent, rate),
            factorStep(rate, periods, factor),
            totalSymbol + " = " + formatNumber(principal) + " × " + formatNumber(factor) + " = " + formatNumber(total),
            interestSymbol + " = " + totalSymbol + " - " + principalSymbol + " = " + formatNumber(total) + " - " +
                formatNumber(principal) + " = " + formatNumber(interest),
        ];

        panel.find("[data-result]").html(
            row(root.data("label-total"), formatMoney(root, total)) +
            row(root.data("label-interest"), formatMoney(root, interest)) +
            section(root.data("label-formula"), "<code>" + notation.formula_compound + "</code>") +
            section(root.data("label-steps"), "<ol><li>" + steps.join("</li><li>") + "</li></ol>")
        );
    };

    const calculatePresent = function (root, panel) {
        const future = parseNumber(panel, "future");
        const ratePercent = parseNumber(panel, "rate");
        const periods = parseNumber(panel, "periods");
        if (future === null || ratePercent === null || periods === null || future < 0 || periods < 0 || ratePercent <= -100) {
            invalid(root, panel);
            return;
        }

        const rate = ratePercent / 100;
        const factor = Math.pow(1 + rate, periods);
        const present = future / factor;
        const presentSymbol = notation.formula_symbol_present;
        const steps = [
            rateStep(ratePercent, rate),
            factorStep(rate, periods, factor),
            presentSymbol + " = " + formatNumber(future) + " ÷ " + formatNumber(factor) + " = " + formatNumber(present),
        ];

        panel.find("[data-result]").html(
            row(root.data("label-present"), formatMoney(root, present)) +
            section(root.data("label-formula"), "<code>" + notation.formula_present + "</code>") +
            section(root.data("label-steps"), "<ol><li>" + steps.join("</li><li>") + "</li></ol>")
        );
    };

    const calculateFuture = function (root, panel) {
        const present = parseNumber(panel, "present");
        const ratePercent = parseNumber(panel, "rate");
        const periods = parseNumber(panel, "periods");
        if (present === null || ratePercent === null || periods === null || present < 0 || periods < 0 || ratePercent <= -100) {
            invalid(root, panel);
            return;
        }

        const rate = ratePercent / 100;
        const factor = Math.pow(1 + rate, periods);
        const future = present * factor;
        const futureSymbol = notation.formula_symbol_future;
        const steps = [
            rateStep(ratePercent, rate),
            factorStep(rate, periods, factor),
            futureSymbol + " = " + formatNumber(present) + " × " + formatNumber(factor) + " = " + formatNumber(future),
        ];

        panel.find("[data-result]").html(
            row(root.data("label-future"), formatMoney(root, future)) +
            section(root.data("label-formula"), "<code>" + notation.formula_future + "</code>") +
            section(root.data("label-steps"), "<ol><li>" + steps.join("</li><li>") + "</li></ol>")
        );
    };

    const calculatePayment = function (root, panel) {
        const principal = parseNumber(panel, "principal");
        const ratePercent = parseNumber(panel, "rate");
        const periods = parseNumber(panel, "periods");
        if (principal === null || ratePercent === null || periods === null || principal < 0 || ratePercent < 0 || periods <= 0 || !Number.isInteger(periods)) {
            invalid(root, panel);
            return;
        }

        const rate = ratePercent / 100;
        let payment;
        let denominator = 1;

        if (rate === 0) {
            payment = principal / periods;
        } else {
            denominator = 1 - Math.pow(1 + rate, -periods);
            payment = principal * rate / denominator;
        }

        const totalPaid = payment * periods;
        const interest = totalPaid - principal;
        const paymentSymbol = notation.formula_symbol_payment;
        const steps = [rateStep(ratePercent, rate)];

        if (rate === 0) {
            steps.push(paymentSymbol + " = " + formatNumber(principal) + " ÷ " + formatNumber(periods) + " = " + formatNumber(payment));
        } else {
            steps.push("1 - (1 + " + notation.formula_symbol_rate + ")^(-" + notation.formula_symbol_periods + ") = " +
                formatNumber(denominator));
            steps.push(paymentSymbol + " = " + formatNumber(principal) + " × " + formatNumber(rate) + " ÷ " +
                formatNumber(denominator) + " = " + formatNumber(payment));
        }
        steps.push(root.data("label-totalpaid") + " = " + formatNumber(payment) + " × " + formatNumber(periods) + " = " + formatNumber(totalPaid));
        steps.push(root.data("label-interest") + " = " + formatNumber(totalPaid) + " - " + formatNumber(principal) + " = " + formatNumber(interest));

        panel.find("[data-result]").html(
            row(root.data("label-installment"), formatMoney(root, payment)) +
            row(root.data("label-totalpaid"), formatMoney(root, totalPaid)) +
            row(root.data("label-interest"), formatMoney(root, interest)) +
            section(root.data("label-formula"), "<code>" + (rate === 0 ? notation.formula_payment_zero : notation.formula_payment) + "</code>") +
            section(root.data("label-steps"), "<ol><li>" + steps.join("</li><li>") + "</li></ol>")
        );
    };

    const calculate = function (root, panel, mode) {
        const calculators = {
            simple: calculateSimple,
            compound: calculateCompound,
            present: calculatePresent,
            future: calculateFuture,
            payment: calculatePayment,
        };
        if (calculators[mode]) {
            calculators[mode](root, panel);
        }
    };

    const buildPanels = function (root) {
        const templates = $("[data-finance-field-templates]");
        root.find("[data-mode-panel]").each(function () {
            const panel = $(this);
            const mode = panel.data("mode-panel");
            const source = templates.find('template[data-template-mode="' + mode + '"]');
            if (source.length) {
                panel.find("[data-fields]").replaceWith(source.html());
                calculate(root, panel, mode);
            }
        });
    };

    const bindEvents = function (root) {
        root.on("click", "[data-mode-button]", function () {
            const button = $(this);
            const mode = button.data("mode-button");

            root.find("[data-mode-button]")
                .removeClass("btn-primary")
                .addClass("btn-outline-secondary")
                .attr("aria-selected", "false");
            button.removeClass("btn-outline-secondary").addClass("btn-primary").attr("aria-selected", "true");
            root.find("[data-mode-panel]").addClass("d-none");
            root.find('[data-mode-panel="' + mode + '"]').removeClass("d-none");
        });

        root.on("click", "[data-calculate]", function () {
            const panel = $(this).closest("[data-mode-panel]");
            calculate(root, panel, panel.data("mode-panel"));
        });

        root.on("input", "input[data-field]", function () {
            const panel = $(this).closest("[data-mode-panel]");
            calculate(root, panel, panel.data("mode-panel"));
        });
    };

    const init = function () {
        return loadNotation().then(function () {
            $("[data-region=finance-calculator]").each(function () {
                const root = $(this);
                buildPanels(root);
                bindEvents(root);
            });
        });
    };

    return {init: init};
});
