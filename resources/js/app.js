import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import jQuery from 'jquery';
window.$ = jQuery;

import {Datepicker, Select, Input, Stepper, Sticky, Modal, Ripple, initTE } from "tw-elements";
initTE({ Datepicker, Select, Stepper, Input, Sticky, Modal, Ripple });
