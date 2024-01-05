import './bootstrap';
import 'flowbite';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import jQuery from 'jquery';
window.$ = jQuery;


import { Datatable, Input,Datepicker,Select, Stepper, Sticky, Modal, Ripple, initTE } from "tw-elements";
initTE({ Datatable, Datepicker, Select, Stepper, Input, Sticky, Modal, Ripple });

const datepickerTranslated6 = new Datepicker(
    document.querySelector("#exp-start-time-cycle-add"),
    {
        title: "Data",
        monthsFull: [
            "Styczeń",
            "Luty",
            "Marzec",
            "Kwiecień",
            "Maj",
            "Czerwiec",
            "Lipiec",
            "Sierpień",
            "Wrzesień",
            "Październik",
            "Listopad",
            "Grudzień",
        ],
        monthsShort: [
            "Sty",
            "Lut",
            "Mar",
            "Kwi",
            "Maj",
            "Cze",
            "Lip",
            "Sie",
            "Wrz",
            "Paź",
            "Lis",
            "Gru",
        ],
        weekdaysFull: [
            "Niedziela",
            "Poniedziałek",
            "Wtorek",
            "Środa",
            "Czwartek",
            "Piątek",
            "Sobota",
        ],
        weekdaysShort: ["Nd", "Pn", "Wt", "Śr", "Czw", "Pt", "Sb"],
        weekdaysNarrow: ["N", "P", "W", "Ś", "C", "P", "S"],
        okBtnText: "Ok",
        clearBtnText: "Wyczyść",
        cancelBtnText: "Anuluj",
    }
);
const datepickerTranslated3 = new Datepicker(
    document.querySelector("#exp-end-time-cycle-add"),
    {
        disablePast: true,
        title: "Data",
        monthsFull: [
            "Styczeń",
            "Luty",
            "Marzec",
            "Kwiecień",
            "Maj",
            "Czerwiec",
            "Lipiec",
            "Sierpień",
            "Wrzesień",
            "Październik",
            "Listopad",
            "Grudzień",
        ],
        monthsShort: [
            "Sty",
            "Lut",
            "Mar",
            "Kwi",
            "Maj",
            "Cze",
            "Lip",
            "Sie",
            "Wrz",
            "Paź",
            "Lis",
            "Gru",
        ],
        weekdaysFull: [
            "Niedziela",
            "Poniedziałek",
            "Wtorek",
            "Środa",
            "Czwartek",
            "Piątek",
            "Sobota",
        ],
        weekdaysShort: ["Nd", "Pn", "Wt", "Śr", "Czw", "Pt", "Sb"],
        weekdaysNarrow: ["N", "P", "W", "Ś", "C", "P", "S"],
        okBtnText: "Ok",
        clearBtnText: "Wyczyść",
        cancelBtnText: "Anuluj",
    }
);

const datepickerTranslated4 = new Datepicker(
    document.querySelector("#start-time-work"),
    {
        title: "Data",
        monthsFull: [
            "Styczeń",
            "Luty",
            "Marzec",
            "Kwiecień",
            "Maj",
            "Czerwiec",
            "Lipiec",
            "Sierpień",
            "Wrzesień",
            "Październik",
            "Listopad",
            "Grudzień",
        ],
        monthsShort: [
            "Sty",
            "Lut",
            "Mar",
            "Kwi",
            "Maj",
            "Cze",
            "Lip",
            "Sie",
            "Wrz",
            "Paź",
            "Lis",
            "Gru",
        ],
        weekdaysFull: [
            "Niedziela",
            "Poniedziałek",
            "Wtorek",
            "Środa",
            "Czwartek",
            "Piątek",
            "Sobota",
        ],
        weekdaysShort: ["Nd", "Pn", "Wt", "Śr", "Czw", "Pt", "Sb"],
        weekdaysNarrow: ["N", "P", "W", "Ś", "C", "P", "S"],
        okBtnText: "Ok",
        clearBtnText: "Wyczyść",
        cancelBtnText: "Anuluj",
    }
);

const datepickerTranslated5 = new Datepicker(
    document.querySelector("#end-time-work"),
    {
        title: "Data",
        monthsFull: [
            "Styczeń",
            "Luty",
            "Marzec",
            "Kwiecień",
            "Maj",
            "Czerwiec",
            "Lipiec",
            "Sierpień",
            "Wrzesień",
            "Październik",
            "Listopad",
            "Grudzień",
        ],
        monthsShort: [
            "Sty",
            "Lut",
            "Mar",
            "Kwi",
            "Maj",
            "Cze",
            "Lip",
            "Sie",
            "Wrz",
            "Paź",
            "Lis",
            "Gru",
        ],
        weekdaysFull: [
            "Niedziela",
            "Poniedziałek",
            "Wtorek",
            "Środa",
            "Czwartek",
            "Piątek",
            "Sobota",
        ],
        weekdaysShort: ["Nd", "Pn", "Wt", "Śr", "Czw", "Pt", "Sb"],
        weekdaysNarrow: ["N", "P", "W", "Ś", "C", "P", "S"],
        okBtnText: "Ok",
        clearBtnText: "Wyczyść",
        cancelBtnText: "Anuluj",
    }
);

const datepickerTranslated = new Datepicker(
    document.querySelector("#exp-start-time"),
    {
        title: "Data",
        monthsFull: [
            "Styczeń",
            "Luty",
            "Marzec",
            "Kwiecień",
            "Maj",
            "Czerwiec",
            "Lipiec",
            "Sierpień",
            "Wrzesień",
            "Październik",
            "Listopad",
            "Grudzień",
        ],
        monthsShort: [
            "Sty",
            "Lut",
            "Mar",
            "Kwi",
            "Maj",
            "Cze",
            "Lip",
            "Sie",
            "Wrz",
            "Paź",
            "Lis",
            "Gru",
        ],
        weekdaysFull: [
            "Niedziela",
            "Poniedziałek",
            "Wtorek",
            "Środa",
            "Czwartek",
            "Piątek",
            "Sobota",
        ],
        weekdaysShort: ["Nd", "Pn", "Wt", "Śr", "Czw", "Pt", "Sb"],
        weekdaysNarrow: ["N", "P", "W", "Ś", "C", "P", "S"],
        okBtnText: "Ok",
        clearBtnText: "Wyczyść",
        cancelBtnText: "Anuluj",
    }
);

const datepickerTranslated2 = new Datepicker(
    document.querySelector("#exp-end-time"),
    {
        title: "Data",
        monthsFull: [
            "Styczeń",
            "Luty",
            "Marzec",
            "Kwiecień",
            "Maj",
            "Czerwiec",
            "Lipiec",
            "Sierpień",
            "Wrzesień",
            "Październik",
            "Listopad",
            "Grudzień",
        ],
        monthsShort: [
            "Sty",
            "Lut",
            "Mar",
            "Kwi",
            "Maj",
            "Cze",
            "Lip",
            "Sie",
            "Wrz",
            "Paź",
            "Lis",
            "Gru",
        ],
        weekdaysFull: [
            "Niedziela",
            "Poniedziałek",
            "Wtorek",
            "Środa",
            "Czwartek",
            "Piątek",
            "Sobota",
        ],
        weekdaysShort: ["Nd", "Pn", "Wt", "Śr", "Czw", "Pt", "Sb"],
        weekdaysNarrow: ["N", "P", "W", "Ś", "C", "P", "S"],
        okBtnText: "Ok",
        clearBtnText: "Wyczyść",
        cancelBtnText: "Anuluj",
    }
);
