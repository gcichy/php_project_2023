import './bootstrap';
import 'flowbite';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import jQuery from 'jquery';
window.$ = jQuery;

import { Datatable, Input,Datepicker,Select, Stepper, Sticky, Modal, Ripple, initTE } from "tw-elements";
initTE({ Datatable, Datepicker, Select, Stepper, Input, Sticky, Modal, Ripple });

const datepickerTranslated = new Datepicker(
    document.querySelector("#exp-end-time-start"),
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
    document.querySelector("#exp-end-time-end"),
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

const table = document.getElementById("datatable-clickable-rows");
const modal = document.getElementById("modal-clickable-rows");
const modalBody = document.getElementById("modal-body-clickable-rows");
const modalHeader = document.getElementById(
    "modal-header-clickable-rows"
);

const modalInstance = new Modal(modal);

const setupButtons = (action) => {
    document
        .querySelectorAll(`.${action}-email-button`)
        .forEach((button) => {
            button.addEventListener("click", (e) => {
                e.stopPropagation();

                const index = button.getAttribute("data-te-index");

                console.log(`${action} message: ${index}`, messages[index]);
            });
        });
};

const columnsClickable = [
    { label: "Actions", field: "actions", sort: false },
    { label: "From", field: "from" },
    { label: "Title", field: "title" },
    { label: "Message", field: "preview", sort: false },
    { label: "Date", field: "date" },
];

const messages = [
    {
        from: "admin@mdbootstrap.com",
        title: "TW elements spring sale",
        message:
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sed metus ultricies, sollicitudin est nec, blandit turpis. Fusce venenatis nisi volutpat, pharetra elit eu, ullamcorper metus. Vestibulum dapibus laoreet aliquam. Maecenas sed magna ut libero consequat elementum. Maecenas euismod pellentesque pulvinar. Morbi sit amet turpis eget dolor rutrum eleifend. Sed bibendum diam nec diam posuere pulvinar. Cras ac bibendum arcu.",
        date: "11/12/2019",
    },
    {
        from: "user@mdbootstrap.com",
        title: "How to use TW elements?",
        message:
            "Quisque tempor ligula eu lobortis scelerisque. Mauris tristique mi a erat egestas, quis dictum nibh iaculis. Sed gravida sodales egestas. In tempus mollis libero sit amet lacinia. Duis non augue sed leo imperdiet efficitur faucibus vitae elit. Mauris eu cursus ligula. Praesent posuere efficitur cursus.",
        date: "10/12/2019",
    },
    {
        from: "user@mdbootstrap.com",
        title: "Licence renewal",
        message:
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sed metus ultricies, sollicitudin est nec, blandit turpis. Fusce venenatis nisi volutpat, pharetra elit eu, ullamcorper metus. Vestibulum dapibus laoreet aliquam. Maecenas sed magna ut libero consequat elementum. Maecenas euismod pellentesque pulvinar. Morbi sit amet turpis eget dolor rutrum eleifend. Sed bibendum diam nec diam posuere pulvinar. Cras ac bibendum arcu.",
        date: "09/12/2019",
    },
    {
        from: "admin@mdbootstrap.com",
        title: "Black friday offer",
        message:
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sed metus ultricies, sollicitudin est nec, blandit turpis. Fusce venenatis nisi volutpat, pharetra elit eu, ullamcorper metus. Vestibulum dapibus laoreet aliquam. Maecenas sed magna ut libero consequat elementum. Maecenas euismod pellentesque pulvinar. Morbi sit amet turpis eget dolor rutrum eleifend. Sed bibendum diam nec diam posuere pulvinar. Cras ac bibendum arcu.",
        date: "08/12/2019",
    },
];

const rowsClickable = messages.map((email, i) => {
    const getPreview = (message, length) => {
        if (message.length <= length) return message;

        return `${message.slice(0, length)}...`;
    };

    return {
        ...email,
        preview: getPreview(email.message, 20),
        actions: `
    <div class="flex">
      <a role="button" class="star-email-button text-warning" data-te-index="${i}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
        </svg>
      </a>
      <a role="button" class="delete-email-button text-neutral-300 ms-2" data-te-index="${i}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
        </svg>
      </a>
    </div>
  `,
    };
});

table.addEventListener("rowClick.te.datatable", (e) => {
    const { index } = e;
    const { message, title, from } = messages[index];

    modalHeader.innerHTML = `
  <h5 class="text-xl font-medium leading-normal text-neutral-800 dark:text-neutral-200">${title}</h5>
  <button
    id="close-button"
    type="button"
    class="box-content rounded-none border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none"
    data-te-modal-dismiss
    aria-label="Close">
    <svg
      xmlns="http://www.w3.org/2000/svg"
      fill="currentColor"
      viewBox="0 0 24 24"
      stroke-width="1.5"
      stroke="currentColor"
      class="h-6 w-6">
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M6 18L18 6M6 6l12 12" />
    </svg>
  </button>`;
    modalBody.innerHTML = `
  <h6 class="mb-4">From: <strong>${from}</strong></h6>
  <p>${message}</p>
`;

    modalInstance.show();
    document
        .getElementById("close-button")
        .addEventListener("click", () => {
            modalInstance.hide();
        });
});

table.addEventListener("render.te.datatable", () => {
    setupButtons("star");
    setupButtons("delete");
});

const datatableInstance = new Datatable(table, {
    columns: columnsClickable,
    rows: rowsClickable,
});
