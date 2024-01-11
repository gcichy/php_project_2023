import './bootstrap';
import 'flowbite';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import jQuery from 'jquery';
window.$ = jQuery;


import { Datatable, Input,Datepicker,Select, Stepper, Sticky, Modal, Ripple, initTE } from "tw-elements";
initTE({ Datatable, Datepicker, Select, Stepper, Input, Sticky, Modal, Ripple });

