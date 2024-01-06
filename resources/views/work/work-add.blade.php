<x-app-layout>
    <script type="module">
        function selectOldElements() {
            let category = $('#selected-cycle-category').val();
            if(category == 1) {
                let selectedCompCycleId = $('#selected-comp-cycle-id').val();
                let selectedSchemaCycleId = $('#selected-prod-schema-cycle-id').val();
                if(Number.isInteger(parseInt(selectedCompCycleId, 10)) && parseInt(selectedCompCycleId, 10) > 0
                    && Number.isInteger(parseInt(selectedSchemaCycleId, 10)) && parseInt(selectedSchemaCycleId, 10) > 0) {
                    let elementCompCycleId = '#comp-' + selectedCompCycleId;
                    $(elementCompCycleId).trigger('click');
                    let elementSchemaCycleId = '#schemacycle-' + selectedSchemaCycleId +'-compcycle-' + selectedCompCycleId;
                    $(elementSchemaCycleId).trigger('click');
                }
            }
            else if(category == 2) {
                let selectedSchemaCycleId = $('#selected-prod-schema-cycle-id').val();
                console.log($(selectedSchemaCycleId));
                if(Number.isInteger(parseInt(selectedSchemaCycleId, 10)) && parseInt(selectedSchemaCycleId, 10) > 0) {
                    let elementSchemaCycleId = '#schemacycle-' + selectedSchemaCycleId;
                    console.log($(elementSchemaCycleId));
                    $(elementSchemaCycleId).trigger("click");
                }
            }
            let checkedBoxesIdString = $('#checked-boxes-id-string').val();
            let checkedBoxesIdArray = checkedBoxesIdString.split(';');
            checkedBoxesIdArray.forEach(function(elem) {
                if(elem.length > 0) {
                    let id = $('#'+elem);
                    if(id.length > 0) {
                        id.trigger("click");
                    }
                }
            });
        }

        function adjustSelectedElement(elemClass, container) {
            let selectedElem = container.find('.'+elemClass);
            if(selectedElem.length === 1) {
                selectedElem.attr('id', 'selected-'+selectedElem.attr('id'));
                selectedElem.addClass('selected-'+elemClass).removeClass(elemClass);
                return selectedElem;
            }
            return null;
        }

        function modalOnClick(button) {
            $("#modal-details-background").removeClass("hidden");
            let idArr = button.attr('id').split('-');
            getRowData(idArr[idArr.length-1]);
        }

        function expandButtonOnClick(button, idPrefix) {
            let id = button.attr('id').split('-');
            id = id[id.length-1];
            let listId = $(idPrefix+id);

            if(button.hasClass('rotate-180')) {
                button.removeClass('rotate-180');
                button.addClass('rotate-0');
            } else {
                button.removeClass('rotate-0');
                button.addClass('rotate-180');
            }

            if(listId.hasClass('hidden')) {
                listId.removeClass('hidden');
            } else {
                listId.addClass('hidden');
                listId.addClass('just-hidden');
            }
        }

        function cloneSelectedElements(comp, componentId, prodSchema,  prodSchemaCycleId) {
            let clonedProdSchema = prodSchema.clone();
            clonedProdSchema.attr('id', 'selected-'+prodSchema.attr('id'));
            let prodSchemaContainer = $('#selected-prod-schema-container');
            prodSchemaContainer.append(clonedProdSchema);
            $('#selected-prod-schema-cycle-id').val(prodSchemaCycleId);
            let schemaModalElem = adjustSelectedElement('open-modal',prodSchemaContainer);
            if(schemaModalElem != null && schemaModalElem.length > 0) {
                schemaModalElem.on('click', function () {
                    modalOnClick($(this));
                });
            }
            let prodSchemaListBtn = prodSchemaContainer.find('.expand-btn');
            if(prodSchemaListBtn.length > 0) {
                prodSchemaListBtn.addClass('hidden');
            }

            handleInputTable(prodSchemaContainer);

            if(comp != null && comp.length > 0) {
                let clonedComp = comp.clone();
                clonedComp.attr('id', 'selected-'+comp.attr('id'));
                let compContainer = $('#selected-comp-container');
                compContainer.append(clonedComp);
                $('#selected-comp-cycle-id').val(componentId);

                let compModalElem =adjustSelectedElement('open-modal',compContainer);
                if(compModalElem != null && compModalElem.length > 0) {
                    compModalElem.on('click', function () {
                        modalOnClick($(this));
                    });
                }
                let compListBtn = adjustSelectedElement('expand-btn',compContainer);
                if(compListBtn != null && compListBtn.length > 0) {
                    compListBtn.on('click', function () {
                        expandButtonOnClick($(this),'#selected-list-');
                    });
                }
                adjustSelectedElement('dropdown',compContainer);
            }
        }

        //function gets input table from cloned prod-schema, extracts table and clones it into input container
        function handleInputTable(prodSchemaContainer) {
            let prodSchemaList = adjustSelectedElement('dropdown',prodSchemaContainer);
            if(prodSchemaList != null) {
                let inputTable = prodSchemaList.find('.input-table');
                if(inputTable.length > 0) {
                    let inputTableCloned = inputTable.clone();
                    prodSchemaList.remove();
                    let hiddenInputs = inputTableCloned.find('.hidden-input');
                    if(hiddenInputs.length > 0) {
                        hiddenInputs.removeClass('hidden');
                    }
                    let timeInputs = inputTableCloned.find('.input-time');
                    if(timeInputs.length > 0) {
                        timeInputs.each(function () {
                            $(this).attr('id','selected-'+$(this).attr('id'));
                        })
                        timeInputs.removeClass('input-time').addClass('selected-input-time');
                    }
                    let removeEmployeeBtns = inputTableCloned.find('.remove-employee');
                    if(removeEmployeeBtns.length > 0) {
                        removeEmployeeBtns.on('click', function() {
                            removeEmployee($(this));
                        });
                    }
                    let addEmployeeBtns = inputTableCloned.find('.add-employee');
                    if(addEmployeeBtns.length > 0) {
                        addEmployeeBtns.each(function () {
                            $(this).attr('id', 'selected-'+$(this).attr('id'));
                        })
                        addEmployeeBtns.addClass('selected-add-employee').removeClass('add-employee');
                        addEmployeeBtns.on('click', addEmployee);
                    }
                    let prodSchemaCheckboxes = inputTableCloned.find('.input-checkbox');
                    if(prodSchemaCheckboxes.length > 0) {
                        prodSchemaCheckboxes.each(function () {
                            $(this).attr('id', 'selected-'+$(this).attr('id'));
                        })
                        prodSchemaCheckboxes.addClass('selected-input-checkbox').removeClass('input-checkbox');
                        prodSchemaCheckboxes.change(function() {
                            toggleInputsDisability($(this));
                        });
                    }
                    console.log(inputTableCloned.find('option'));
                    $('#input-container').append(inputTableCloned);
                    $('.selected-input-time').on('change', function () {
                        calculateWorkDuration($(this));
                    });
                }

                let inputTablePrompt = $('#input-table-prompt');
                if(inputTablePrompt.length > 0) {
                    inputTablePrompt.addClass('hidden');
                }
            }
        }

        function calculateWorkDuration(timeElement) {
            let parent = timeElement.closest('.input-table-row');
            let minutesDifference = 0;
            if(timeElement.hasClass('input-start-time')) {
                let endTime = parent.find('.input-end-time');
                minutesDifference = calculateTimeDiff(timeElement.val(),endTime.val());
            } else if(timeElement.hasClass('input-end-time')) {
                let startTime = parent.find('.input-start-time');
                minutesDifference = calculateTimeDiff(startTime.val(),timeElement.val());
            }
            let workDurationElem = parent.find('.work-duration');

            if(workDurationElem.length > 0) {
                workDurationElem.val(minutesDifference);
            }
            let workDuration = formatMinutesDuration(minutesDifference);
            let workTimeElem = parent.find('.work-time-in-hours');
            if(workTimeElem.length > 0) {
                workTimeElem.text(workDuration);
            }
        }

        function calculateTimeDiff(startDate,endDate) {
            if(isValidDate(startDate) && isValidDate(endDate)) {
                const date1 = new Date(startDate);
                const date2 = new Date(endDate);

                // Calculate the difference in milliseconds
                const timeDifference = date2 - date1;
                // Convert the difference to minutes
                return Math.floor(timeDifference / (1000 * 60));;
            }
            return null;
        }

        function isValidDate(value) {
            const dateObject = new Date(value);
            return !isNaN(dateObject.getTime());
        }

        function formatMinutesDuration(minutes) {
            let minus = minutes < 0;
            minutes = Math.abs(minutes);
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            const formattedMinutes = remainingMinutes < 10 ? `0${remainingMinutes}` : remainingMinutes;

            return minus? `-${hours}:${formattedMinutes}` : `${hours}:${formattedMinutes}`;
        }
        function removeInputTable() {
            let inputTable = $('#input-container').find('.input-table');
            if(inputTable.length >0) {
                inputTable.remove();
            }
            let inputTablePrompt = $('#input-table-prompt');
            if(inputTablePrompt.length > 0) {
                inputTablePrompt.removeClass('hidden');
            }
        }

        function removeSelectedElements() {
            $('#selected-comp-cycle-id').val(null);
            $('#selected-prod-schema-cycle-id').val(null);
            $('#selected-prod-schema-container :nth-child(2)').remove();
            $('#selected-comp-container :nth-child(2)').remove();
        }

        function getRowData(id) {
            let row = $('#row-'+id).find('.col-value');
            let modalDetailsTable = $('#modal-details-table');
            let productivity = $('#row-'+id).find('.col-value.productivity');
            let productivityStyle = 'text-red-500';
            if(productivity.length === 1 && parseInt(productivity.text().trim()) > 100 ) {
                productivityStyle = 'text-green-450';
            }
            row.each(function () {
                // Get the class attribute of the current element
                let classNames = $(this).attr('class').split(' ');
                if(classNames.length >= 2) {
                    let colName = classNames[1];
                    let elem = modalDetailsTable.find('.col-value.'+colName);
                    if(elem.length === 1) {
                        modalProductivityStyles(colName, elem, productivityStyle);
                        modalStatusStyles(colName, elem, $(this).text().trim());
                        elem.text($(this).text().trim());
                    }
                }
            });
        }
        function modalProductivityStyles(colName, elem, style) {
            let productivityList = ['productivity','current-amount','expected-amount-per-spent-time']
            if(productivityList.includes(colName)) {
                elem.addClass(style);
            }
        }
        function modalStatusStyles(colName, elem, sourceText) {
            console.log(colName, elem, sourceText);
            if(colName === 'status') {
                if(sourceText === 'Po terminie') {
                    elem.addClass('bg-red-500');
                } else if(sourceText === 'Zakończony') {
                    elem.addClass('bg-green-450');
                } else if(sourceText === 'Aktywny') {
                    elem.addClass('bg-blue-450');
                } else {
                    elem.addClass('bg-yellow-300');
                }
            }
        }

        function addCycleStyles() {
            let cycles = $('.cycle');
            cycles.each(function() {
                let styles = $(this).find('.cycle_styles').text();
                styles = styles.split(';');
                if(styles.length === 2) {
                    let cycleClasses = '';
                    let cycleTagBg = '';
                    let cycleTagText = '';
                    let status = parseInt(styles[0]);

                    if(status === 0) {
                        //cycleClasses = 'ring-green-450 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-green-450 hover:bg-green-700';
                        cycleTagText = 'Zakończony';
                    } else if(status === 3) {
                        //cycleClasses = 'ring-red-500 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-red-600 hover:bg-red-800';
                        cycleTagText = 'Po terminie';
                    } else if(status === 1) {
                        //cycleClasses = 'ring-blue-450 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-blue-450 hover:bg-blue-800';
                        cycleTagText = 'Aktywny';
                    } else if(status === 2) {
                        //cycleClasses = 'ring-yellow-300 ring-4 ring-offset-4';
                        cycleTagBg = 'bg-yellow-300 hover:bg-yellow-600';
                        cycleTagText = 'Nierozpoczęty';
                    }
                    //$(this).addClass(cycleClasses);
                    let cycleTag = $(this).find('.cycle-tag');
                    if(cycleTag.length === 1) {
                        cycleTag.addClass(cycleTagBg).text(cycleTagText);
                    }
                    let addWorkButton =  $(this).find('#add-work-button');
                    if(addWorkButton.length === 1) {
                        addWorkButton.addClass(cycleTagBg)
                    }
                    let progress = $(this).find('.progress');
                    if(progress.length === 1) {
                        let width = parseInt(styles[1]);
                        if(width >= 100) {
                            $(progress).addClass('rounded-lg');
                        } else {
                            $(progress).addClass('rounded-l-lg');
                        }
                        if(width < 5) {
                            width = 'w-0';
                        } else if(width < 20) {
                            width = 'w-1/6';
                        } else if(width < 30) {
                            width = 'w-1/4';
                        } else if(width < 40) {
                            width = 'w-1/3';
                        } else if(width < 60) {
                            width = 'w-1/2';
                        } else if(width < 70) {
                            width = 'w-2/3';
                        } else if(width < 85) {
                            width = 'w-3/4';
                        } else if(width < 100) {
                            width = 'w-5/6';
                        } else {
                            width = 'w-full';
                        }
                        $(progress).addClass(width);
                    }
                }
            });
        }

        function setMaxInputTime() {
            let currentDate = new Date();
            currentDate.setHours(currentDate.getHours() + 9);
            let formattedDatetime = currentDate.toISOString().slice(0, 16);
            $(".selected-input-time").attr("max", formattedDatetime);
        }

        function toggleInputsDisability(checkboxInput) {
            let parent = checkboxInput.closest('.input-table-row');
            if(parent.length > 0) {
                let disabledInputs = parent.find('.disabled-input');
                let employeeBtns = parent.find('.disabled-input-employee-btn');
                if(disabledInputs.length > 0) {
                    if (checkboxInput.is(":checked")) {
                        disabledInputs.prop("disabled", false).removeClass('bg-gray-200').addClass('bg-white');
                        parent.addClass('bg-blue-150');
                        if(employeeBtns.length > 0) {
                            employeeBtns.prop("disabled", false)
                        }
                    }
                    else {
                        disabledInputs.prop("disabled", true).removeClass('bg-white').addClass('bg-gray-200');
                        parent.removeClass('bg-blue-150');
                        let durationHours = parent.find('.work-time-in-hours');
                        if(durationHours.length > 0) {
                            durationHours.text('0:00');
                        }
                        let nullableInputs = parent.find('.nullable-input');
                        if(nullableInputs.length > 0) {
                            nullableInputs.val(null)
                        }
                        if(employeeBtns.length > 0) {
                            employeeBtns.prop("disabled", true)
                        }
                        removeAllEmployees(checkboxInput);
                    }
                }
            }

        }

        function clickListElement(elem) {
            let isActive = (elem.hasClass('active-list-elem') ? true : false);
            $('.list-element').removeClass('active-list-elem');
            elem.addClass('active-list-elem');

            $('.list-element-2').removeClass('active-list-elem');


            let compId = elem.attr('id');
            let prodSchemas = $('.list-element-2.'+ compId);
            $('.list-element-2').addClass('hidden');

            if (isActive) {
                $('.list-element').removeClass('active-list-elem');
            }
            else {
                if(prodSchemas.hasClass('hidden')){
                    prodSchemas.removeClass('hidden');
                }
            }
        }

        function clickListElement2(elem) {
            var isActive = (elem.hasClass('active-list-elem') ? true : false);
            $('.list-element-2').removeClass('active-list-elem');
            elem.addClass('active-list-elem');

            if (isActive) {
                $('.list-element-2').removeClass('active-list-elem');
            }
            else {
                let id = elem.attr('id');
                var schemaCycleId = null;
                if(id.includes('compcycle')) {
                    let arrayId = id.split('-');
                    let compCycleId = arrayId[arrayId.length - 1];
                    if(arrayId.length > 3) {
                        schemaCycleId = arrayId[arrayId.length - 3];
                    }
                    let comp = $('#comp-'+compCycleId);
                    cloneSelectedElements(comp, compCycleId, elem, schemaCycleId);
                }
                else {
                    let arrayId = id.split('-');
                    if(arrayId.length > 1) {
                        schemaCycleId = arrayId[arrayId.length - 1];
                    }
                    cloneSelectedElements(null, null, elem, schemaCycleId);
                }
            }
        }

        function addEmployee() {
            let parent = $(this).closest('.input-table-row');
            let employeeCount = parent.find('.employee-input-count');
            let count = parseInt(employeeCount.val());
            if(employeeCount.length > 0 && count > 0) {
                count += 1;
            }
            let firstEmployee = parent.find('.employee-input');
            if(firstEmployee.length > 0 ) {
                let clonedSelect = firstEmployee.clone();

                clonedSelect.attr({
                    'id': count.toString()+'-'+firstEmployee.attr('id'),
                    'name': count.toString()+'_'+firstEmployee.attr('name')  // You may adjust the name attribute as needed
                }).removeClass('employee-input').addClass('added-employee-input');
                // let emptyOption = $('<option>', { value: '', text: '', selected: true });
                // clonedSelect.append(emptyOption);
                clonedSelect.val(null);
                if(count < 6) {
                    if(count > 2) {
                        let previousCount = count - 1;
                        let previousInput = $('#'+ previousCount.toString()+'-'+firstEmployee.attr('id'));
                        if(previousInput.length > 0) {
                            clonedSelect.insertAfter(previousInput);
                        }
                        else {
                            clonedSelect.insertAfter(firstEmployee);
                        }
                    }
                    else {
                        clonedSelect.insertAfter(firstEmployee);
                    }
                    employeeCount.val(count);
                }
            }
        }

        function removeEmployee(elem) {
            let parent = elem.closest('.input-table-row');
            let firstEmployee = parent.find('.employee-input');
            let employeeCount = parent.find('.employee-input-count');
            let count = parseInt(employeeCount.val());
            if (employeeCount.length > 0 && count > 1 && firstEmployee.length > 0) {
                let previousInput = $('#' + count.toString() + '-' + firstEmployee.attr('id'));
                if (previousInput.length > 0) {
                    previousInput.remove();
                    employeeCount.val(count - 1);
                } else {
                    $('.added-employee-input').remove();
                    employeeCount.val(1);
                }
            }
        }

        function removeAllEmployees(elem) {
            let parent = elem.closest('.input-table-row');
            let employeeCount = parent.find('.employee-input-count');
            let count = parseInt(employeeCount.val());
            while(count > 1) {
                removeEmployee(elem);
                count = parseInt(employeeCount.val());
            }
        }
        $(document).ready(function() {
            addCycleStyles();
            setMaxInputTime();

            $('.cycle-details').on('click',function (){
                let id = $(this).attr('id');
                id = id.split('-');
                id = id[id.length - 1];

                let cycle = $('#cycle-'+id);
                if(cycle.length === 1) {
                    let additionalInfo = cycle.find('.additional-info');
                    if(additionalInfo.length > 0 && additionalInfo.hasClass('hidden')) {
                        additionalInfo.removeClass('hidden');
                    } else {
                        additionalInfo.addClass('hidden');
                    }
                }
            });

            $('.list-element').on('click', function () {
                if($(event).length === 0) {
                    clickListElement($(this));
                }
                else if(!($(event.target).is("a.open-modal") || $(event.target).closest("a.open-modal").length > 0
                    || $(event.target).is("div.expand-btn") || $(event.target).closest("div.expand-btn").length > 0)) {
                    removeSelectedElements();
                    removeInputTable();
                    clickListElement($(this));
                }
            });

            $('.list-element-2').on('click', function () {
                if(!$(this).hasClass('finished')) {
                    if($(event).length === 0) {
                        clickListElement2($(this));
                    }
                    else if(!($(event.target).is("a.open-modal") || $(event.target).closest("a.open-modal").length > 0
                        || $(event.target).is("div.expand-btn") || $(event.target).closest("div.expand-btn").length > 0)) {
                        removeSelectedElements();
                        removeInputTable();
                        clickListElement2($(this));
                    }
                }


            });

            $("#close-modal-details-button").on('click', function () {
                $("#modal-details-background").addClass("hidden");
            });

            $(".open-modal").on('click', function () {
                $("#modal-details-background").removeClass("hidden");
                let idArr = $(this).attr('id').split('-');
                getRowData(idArr[idArr.length-1]);
            });


            $('.expand-btn').on('click', function () {
                expandButtonOnClick($(this),'#list-');
            });
            $('.selected-input-time').on('change', function () {
                calculateWorkDuration($(this));
            });

            $('.selected-input-checkbox ').change(function() {
                toggleInputsDisability($(this));
            });

            $(".add-employee").on("click", addEmployee);

            $('.remove-employee').on("click", function() {
                removeEmployee($(this));
            });

            selectOldElements();
        });
    </script>
    @if(isset($user) and $user instanceof \App\Models\User)
        @if(session('status'))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-green-600 space-y-1">
                    {{session('status')}}
                </p>
            </div>
        @endif
            @if(session('status_err'))
                <div class="flex justify-center items-center">
                    <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-red-700 space-y-1">
                        {{session('status_err')}}
                    </p>
                </div>
            @endif
        @if(isset($status_err))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-red-700 space-y-1">
                    {{$status_err}}
                </p>
            </div>
        @endif
        @php
            $name = "Raportuj pracę dla cyklu";
        @endphp
        <x-information-panel :viewName="$name">
        </x-information-panel>
        @if(isset($p_cycle) and isset($child_cycles))
            <div class="flex flex-col justify-center items-center w-full mt-4">
                <div id="cycle-{{$p_cycle->cycle_id}}" class="cycle w-[95%] rounded-xl bg-white my-5 shadow-md">
                    <p class="cycle_status hidden">{{$p_cycle->status}}</p>
                    <p class="cycle_styles hidden">{{$p_cycle->status}};{{$p_cycle->style_progress}}</p>
                    @if($p_cycle->finished == 0)
                        <form method="POST" action="{{ route('work.store',['id' => $p_cycle->cycle_id]) }}" enctype="multipart/form-data" class="w-full">
                    @endif
                        @csrf
                        <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-hidden text-left rounded-xl">
                            <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2">
                                <dt class="order-first text-sm lg:text-lg font-semibold bg-gray-800 text-white w-[45%] xl:w-1/2 rounded-tl-xl pl-5 py-2 flex flex-row justify-between">
                                    <div class="p-1">
                                        {{($p_cycle->category == 1)? 'Produkt' : (($p_cycle->category == 2)? 'Materiał' : 'Zadanie')}}
                                    </div>
                                    <div class="text-xs lg:text-sm flex justify-center items-center">
                                        <div class="cycle-tag p-1 mx-2 rounded-md whitespace-nowrap"></div>
                                    </div>
                                </dt>
                                <dd class=" text-lg xl:text-xl font-semibold tracking-tight text-gray-900 pl-5 py-4">{{$p_cycle->name}}</dd>
                            </div>
                            <div class="col-span-2 flex flex-col bg-gray-200/50 border-r">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Postęp</dt>
                                <div class="flex justify-center items-center w-full h-full p-2">
                                    <div class="rounded-lg w-1/2 border h-[32px]  relative bg-white shadow-md">
                                        <div class="absolute h-1/2 w-full top-[16%] lg:top-[8%] flex justify-center text-sm lg:text-lg font-semibold">
                                            {{$p_cycle->current_amount}}/{{$p_cycle->total_amount}}
                                        </div>
                                        <div class="progress h-full  tracking-tight bg-green-450"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2 flex flex-col bg-gray-200/50">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Zakładany termin</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        <svg fill="#000000" width="30px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                            <g fill-rule="evenodd">
                                                <path d="M1439.667 1226.667H1333V1440c0 14.187 5.653 27.733 15.573 37.76l123.414 123.306 75.413-75.413-107.733-107.733v-191.253Z"/>
                                                <path d="M1386.333 1813.333C1180.467 1813.333 1013 1645.867 1013 1440c0-205.867 167.467-373.333 373.333-373.333 205.867 0 373.334 167.466 373.334 373.333 0 205.867-167.467 373.333-373.334 373.333m0-853.333c-264.64 0-480 215.36-480 480s215.36 480 480 480 480-215.36 480-480-215.36-480-480-480"/>
                                                <path d="M1546.333 426.667H159.666v-160c0-29.44 24-53.334 53.334-53.334h160v53.334c0 29.44 23.894 53.333 53.333 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h746.667v53.334c0 29.44 23.894 53.333 53.334 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h160c29.333 0 53.333 23.894 53.333 53.334v160Zm-53.333-320h-160V53.333C1333 23.893 1309.107 0 1279.667 0c-29.44 0-53.334 23.893-53.334 53.333v53.334H479.666V53.333C479.666 23.893 455.773 0 426.333 0 396.894 0 373 23.893 373 53.333v53.334H213c-88.213 0-160 71.786-160 160v1546.666h746.666v-106.666h-640V533.333h1386.667v320H1653V266.667c0-88.214-71.787-160-160-160Z"/>
                                            </g>
                                        </svg>
                                        {{$p_cycle->expected_end_time}}
                                    </dd>
                                </div>
                            </div>
                            {{--                            ROW 1--}}
                            <div class="col-span-2 flex justify-start flex-col bg-gray-200/50 border-r-2">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Zdjęcie</dt>
                                <div class="flex justify-center items-center p-1">
                                    <div class="max-w-[150px]">
                                        @if(!is_null($p_cycle->image))
                                            @php $path = ''; @endphp
                                            @if($p_cycle->category == 1)
                                                @php $path = isset($storage_path_products) ? $storage_path_products.'/' : ''; @endphp
                                            @elseif($p_cycle->category == 2)
                                                @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp
                                            @endif
                                            <img src="{{asset('storage/'.$path.$p_cycle->image)}}">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Przypisani pracownicy</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        {{$p_cycle->assigned_employee_no}}
                                    </dd>
                                </div>
                            </div>
                            <div class="col-span-4 flex flex-col bg-gray-200/50">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Uwagi</dt>
                                <div class="w-full h-full flex justify-center items-center">
                                    <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        {{$p_cycle->additional_comment}}
                                    </dd>
                                </div>
                            </div>
                            <div class="col-span-4 xl:col-span-8 w-full bg-gray-300 py-2 flex flex-row justify-end">
                                <button type="button" id="open-modal-{{$p_cycle->cycle_id}}" class="open-modal mr-4 text-gray-800 bg-white uppercase hover:bg-gray-100 focus:outline-none font-medium rounded-md text-xs lg:text-sm px-2 py-1 shadow-md">
                                    Statystyki
                                </button>
                            </div>
                            <input id="checked-boxes-id-string" type="text" name="checked_boxes_id_string" class="hidden" value="{{isset($checked_boxes_id_string)? $checked_boxes_id_string : ''}}">
                            @if($p_cycle->category == 1)
                                <div class="col-span-4 xl:col-span-8 flex justify-start items-center flex-col bg-gray-200/50">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Wybrany materiał</dt>
                                    <div id="selected-comp-container" class="w-4/5 h-full min-h-[20px] flex justify-center items-center">
                                        <input id="selected-comp-cycle-id" type="number" name="selected_component_cycle_id" class="hidden" value="{{old('selected_component_cycle_id')}}">
                                    </div>
                                </div>
                            @endif
                            @if($p_cycle->category != 3)
                                <div class="col-span-4 xl:col-span-8 flex justify-start items-center flex-col bg-gray-200/50">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Wybrane zadanie</dt>
                                    <div id="selected-prod-schema-container" class="w-4/5 h-full min-h-[20px] flex justify-center items-center">
                                        <input id="selected-prod-schema-cycle-id" type="number" name="selected_prod_schema_cycle_id" class="hidden" value="{{old('selected_prod_schema_cycle_id')}}">
                                    </div>
                                </div>
                            @endif
                            <div class="col-span-4 xl:col-span-8 flex justify-start items-center flex-col bg-gray-200/50">
                                <div class="w-full text-sm lg:text-lg font-semibold bg-gray-800 text-white pl-5 py-2 flex flex-row justify-between">
                                    <div class="p-1">
                                        Raportuj
                                    </div>
                                </div>
                                @if(session('validation_err'))
                                    <x-input-error :messages="session('validation_err')" class="mt-4 mb-2 px-2" />
                                @endif
                                @if($p_cycle->category != 3)
                                    <div id="input-table-prompt" class="w-full flex justify-center items-center">
                                        <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-green-600 space-y-1">
                                            Aby zaraportować pracę wybierz {{$p_cycle->category == 1? 'materiał i zadanie' : 'zadanie'}}
                                        </p>
                                    </div>
                                @endif
                                <input id="selected-cycle-category" type="number" name="selected_cycle_category" class="hidden" value="{{old('selected_cycle_category') ? old('selected_cycle_category') : $p_cycle->category}}">
                                <div id="input-container" class="w-full">
                                    @if($p_cycle->category == 3 and isset($child_prod_schemas) )
                                        <div class="input-table shadow-md rounded-b-xl mb-4">
                                            <div class="relative overflow-scroll max-h-[400px]">
                                                <table class="w-full text-sm text-left rtl:text-right pb-2 bg-gray-100 text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border-slate-300">
                                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                                    <tr>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Wybierz
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Kol.
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Podzadanie
                                                        </th>
                                                        <th scope="col" class="px-6 py-3 text-center">
                                                            Ilość (szt)
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Start pracy
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Koniec pracy
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Czas pracy (h)
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Pracownicy
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Komentarz
                                                        </th>
                                                        <th scope="col" class="px-5 py-3 text-center">
                                                            Defekty (szt)
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Defekty - przyczyna
                                                        </th>
                                                        <th scope="col" class="px-9 py-3 text-center">
                                                            Odpady
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Odpady - jednostka
                                                        </th>
                                                        <th scope="col" class="px-2 py-3 text-center">
                                                            Odpady - przyczyna
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($child_prod_schemas as $schema_task)
                                                        <tr id="row-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" class="input-table-row font-medium text-gray-600 dark:bg-gray-800 dark:border-gray-700 hover:bg-blue-150 dark:hover:bg-gray-600 border border-slate-300 ">
                                                            <td class="">
                                                                <div class="w-full h-full flex justify-center items-center">
                                                                    <input type="checkbox" id="selected-check-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                           name="check_{{$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                           class="selected-input-checkbox rounded-sm"
                                                                           value="{{old('check_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"/>
                                                                </div>
                                                            </td>
                                                            <td class="sequence-no px-2 py-3 whitespace-nowrap text-center rounded-md">
                                                                {{$schema_task->task_sequence_no}}
                                                            </td>
                                                            <td class="name px-2 py-3 whitespace-nowrap rounded-md">
                                                                {{$schema_task->task_name}}
                                                            </td>
                                                            <td class="amount px-2 w-[70px]">
                                                                <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                    <div class="w-full p-1 flex justify-center items-center h-full">
                                                                        @if($schema_task->task_amount_required)
                                                                            <input id="{{'amount-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" type="number" min="0"
                                                                                   class="disabled-input amount-input nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center"
                                                                                   name="{{'amount_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" placeholder="0"
                                                                                   value="{{old('amount_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                   disabled>
                                                                        @else
                                                                            <div class="block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                                -
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="px-2 py-3 text-center">
                                                                <input type="datetime-local" id="{{'start-time-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                       name="{{'start_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                       min="2024-01-01T00:00" max="{{isset($max_time)? $max_time.'T23:59' : '2060-12-31T23:59'}}" step="60"
                                                                       class="disabled-input input-start-time selected-input-time nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                                       value="{{old('start_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                       disabled>
                                                            </td>
                                                            <td class="px-2 py-3 text-center">
                                                                <input type="datetime-local" id="end-time-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                       name="{{'end_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                       min="2024-01-01T00:00" max="{{isset($max_time)? $max_time.'T23:59' : '2060-12-31T23:59'}}" step="60"
                                                                       class="disabled-input input-end-time selected-input-time nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                                       value="{{old('end_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                       disabled>
                                                            </td>
                                                            <td class="px-2 py-3 text-center">
                                                                <div class="work-time-in-hours disabled-input block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                    @php
                                                                        $duration_hours = '0:00';
                                                                        $duration_minutes = old('work_duration_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id);
                                                                        if($duration_minutes) {
                                                                            $hours = floor($duration_minutes / 60);
                                                                            $minutes = $duration_minutes % 60;
                                                                            $duration_hours = sprintf('%d:%02d', $hours, $minutes);
                                                                        }
                                                                    @endphp
                                                                    {{$duration_hours}}
                                                                </div>
                                                                <input id="{{'work-duration-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                       type="number" name="{{'work_duration_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                       value="{{old('work_duration_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                       class="disabled-input work-duration nullable-input hidden" disabled>
                                                            </td>
                                                            <td class="employee-no px-2 py-3 text-center">
                                                                @if(isset($users) and in_array($user->role,['admin','manager']))
                                                                    <div class="p-1 flex justify-start items-center flex-row h-full min-w-[130px]">
{{--                                                                        @php $unique_id = 'employee_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id; @endphp--}}
{{--                                                                        <x-select-multiple :uniqueId="$unique_id" :placeholder="__('Pracownicy')"--}}
{{--                                                                                           :classes="__('disabled-input bg-gray-200')" :disabled="__(true)">--}}
{{--                                                                            <x-slot name="options">--}}
{{--                                                                                @foreach($users as $u)--}}
{{--                                                                                    <option value="{{$u->id}}"--}}
{{--                                                                                            @if(old('employee_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id))--}}
{{--                                                                                                selected--}}
{{--                                                                                            @elseif($user->id == $u->id)--}}
{{--                                                                                                selected--}}
{{--                                                                                        @endif>--}}
{{--                                                                                        {{$u->employeeNo}}--}}
{{--                                                                                    </option>--}}
{{--                                                                                @endforeach--}}
{{--                                                                            </x-slot>--}}
{{--                                                                        </x-select-multiple>--}}
                                                                        <div class="flex justify-center flex-col mr-2">
                                                                            <button type="button" id="{{'employee-add-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                    disabled class="disabled-input-employee-btn add-employee employee-btn inline-block  p-0.5 bg-gray-800 rounded-sm m-0.5">
                                                                                <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg">
                                                                                    <title>dodaj pracownika</title>
                                                                                    <path d="M4 12H20M12 4V20" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                </svg>
                                                                            </button>
                                                                            <button type="button" id="{{'employee-remove-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" disabled class="disabled-input-employee-btn remove-employee employee-btn inline-block  p-0.5 bg-gray-800 rounded-sm m-0.5">
                                                                                <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg">
                                                                                    <title>usuń pracownika</title>
                                                                                    <path d="M6 12L18 12" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                        <input id="{{'employee-count-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" type="number" value="1"
                                                                               name="{{'employee_count_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                               disabled class="disabled-input employee-input-count hidden">
                                                                        <select id="{{'employee-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                name="{{'employee_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                disabled class="disabled-input employee-input bg-gray-200 w-[100px] mr-2 block py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                            @foreach($users as $u)
                                                                                <option value="{{$u->id}}" class="bg-white"
                                                                                        @if(old('employee_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $u->id)
                                                                                            selected
                                                                                        @elseif($user->id == $u->id)
                                                                                            selected
                                                                                    @endif>
                                                                                    {{$u->employeeNo}}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                @else
                                                                    <div class="block w-full bg-white py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                        {{$user->employeeNo}}
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="px-2 py-3 text-center">
                                                                <textarea id="{{'comment-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                          name="{{'comment_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" maxlength="255"
                                                                          class="disabled-input nullable-input xl:p-2.5 bg-gray-200 block min-w-[250px] w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                                          disabled placeholder="Komentarz" rows="1">{{old('comment_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}</textarea>
                                                            </td>
                                                            <td class="defect px-2 w-[70px]">
                                                                <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                    <div class="w-full p-1 flex justify-center items-center h-full">
                                                                        @if($schema_task->task_amount_required)
                                                                            <input id="defect-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" type="number" min="0" value="{{old('defect_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                   class="disabled-input defect-input nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center"
                                                                                   name="defect_{{$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" placeholder="-"
                                                                                   disabled>
                                                                        @else
                                                                            <div class="block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                                -
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="defect-rc px-2 py-3 text-center">
                                                                <div class="p-1 flex justify-center items-center h-full">
                                                                    @if($schema_task->task_amount_required)
                                                                        <select id="{{'defect-rc-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                name="{{'defect_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                disabled class="disabled-input defect-rc-input nullable-input bg-gray-200 min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                            <option value="" class="bg-white">
                                                                            </option>
                                                                            @if(isset($reason_codes))
                                                                                @foreach($reason_codes as $code)
                                                                                    <option value="{{$code->reason_code}}" class="bg-white"
                                                                                            @if(old('defect_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $code->reason_code)
                                                                                                selected
                                                                                        @endif>
                                                                                        {{$code->reason_code_desc}}
                                                                                    </option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                    @else
                                                                        <div class="block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                            -
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="waste px-2 w-[70px]">
                                                                <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                    <div class="w-full p-1 flex justify-center items-center h-full">
                                                                        <input id="waste-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                               type="number" min="0" step="any" value="{{old('waste_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                               class="disabled-input waste-input nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center"
                                                                               name="waste_{{$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" placeholder="-"
                                                                               disabled>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="waste-unit px-2 py-3 text-center">
                                                                <div class="p-1 flex justify-center items-center h-full">
                                                                    <select id="{{'waste-unit-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                            name="{{'waste_unit_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                            disabled class="disabled-input waste-unit-input nullable-input bg-gray-200 min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                        <option value="" class="bg-white">
                                                                        </option>
                                                                        @if(isset($units))
                                                                            @foreach($units as $u)
                                                                                <option value="{{$u->unit}}" class="bg-white"
                                                                                        @if(old('waste_unit_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $u->unit)
                                                                                            selected
                                                                                        @endif>
                                                                                    {{$u->unit}}
                                                                                </option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td class="waste-rc px-2 py-3 text-center">
                                                                <div class="p-1 flex justify-center items-center h-full">
                                                                    <select id="{{'waste-rc-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                            name="{{'waste_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                            disabled class="disabled-input waste-rc-input nullable-input bg-gray-200 min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                        <option value="" class="bg-white">
                                                                        </option>
                                                                        @if(isset($reason_codes))
                                                                            @foreach($reason_codes as $code)
                                                                                <option value="{{$code->reason_code}}" class="bg-white"
                                                                                        @if(old('waste_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $code->reason_code)
                                                                                            selected
                                                                                    @endif>
                                                                                    {{$code->reason_code_desc}}
                                                                                </option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-span-4 xl:col-span-8 w-full text-center">
                                @if($p_cycle->finished == 0)
                                    <button  id="add-work-button"
                                             class="inline-block px-6 py-2 md:py-4 text-xs font-medium uppercase w-full text-md md:text-lg xl:text-xl leading-normal text-white focus:outline-none shadow-[0_4px_9px_-4px_rgba(0,0,0,0.2)] transition duration-150 ease-in-out hover:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] focus:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] active:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)]"
                                             data-te-ripple-init
                                             data-te-ripple-color="light"
                                             type="submit">
                                        {{ __('Raportuj pracę') }}
                                    </button>
                                @else
                                    <div class="inline-block px-6 py-2 md:py-4 bg-green-450 text-xs font-medium uppercase w-full text-md md:text-lg xl:text-xl leading-normal text-white focus:outline-none shadow-[0_4px_9px_-4px_rgba(0,0,0,0.2)] transition duration-150 ease-in-out hover:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] focus:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] active:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)]">
                                        {{ __('Cykl zakończony') }}
                                    </div>
                                @endif
                            </div>
                        </dl>
                    @if($p_cycle->finished == 0)
                        </form>
                    @endif
                </div>
            </div>
            @if($p_cycle->category != 3 and isset($child_prod_schemas))
                <div class="w-full flex justify-center items-center mb-4">
                    <div class="w-[95%]">
                        <div class="w-full text-lg lg:text-xl font-semibold bg-gray-800 text-white rounded-t-xl pl-5 py-2 flex flex-row justify-between">
                            <div class="p-3">
                                {{($p_cycle->category == 1)? 'Wybierz materiał i zadanie' : 'Wybierz zadanie'}}
                            </div>
                        </div>
                        <div class="shadow-md rounded-b-xl mb-4 flex justify-center flex-col xl:flex-row">
                            @if($p_cycle->category == 1 and isset($child_components))
                                <div class="w-full xl:w-1/2 flex justify-start items-center flex-col">
                                    <div class="w-full shadow-md bg-white text-lg text-gray-800 font-semibold pl-5 py-2">
                                        <div class="p-2">
                                            Materiały
                                        </div>
                                    </div>
                                    <div class="w-full h-full max-h-[400px] overflow-y-scroll flex justify-center items-start">
                                        <div class="w-[90%] px-2 my-4">
                                            @foreach($child_components as $comp)
                                                <x-list-element id="{{'comp-'.$comp->child_id}}" class="my-6 list-element flex-col w-full lg:py-0 py-0">
                                                    <div class="w-full flex flex-row justify-center">
                                                        <div class="w-[85%] flex flex-col justify-between items-center">
                                                            <div class="w-full flex justify-left items-center">
                                                                <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                                    {{$comp->name}}
                                                                </p>
                                                                @if($comp->status == 0)
                                                                    <p class="my-2 rounded-lg inline-block text-white bg-green-450 shadow-lg list-element-name py-1.5 px-2 text-xs whitespace-nowrap overflow-clip">
                                                                        Zakończone
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="w-[15%] flex justify-end items-center">
                                                            <div id="expbtn-comp-{{$comp->child_id}}" class="expand-btn inline-block  p-0.5 bg-gray-800 rounded-md rotate-0 transition-all mr-1">
                                                                <svg width="30px" height="30px" viewBox="0 0 1024 1024" class="w-5 h-5 lg:w-6 lg:h-6"  xmlns="http://www.w3.org/2000/svg">
                                                                    <title>szczegóły materiału</title>
                                                                    <path d="M903.232 256l56.768 50.432L512 768 64 306.432 120.768 256 512 659.072z" fill="#ffffff" />
                                                                </svg>
                                                            </div>
                                                            <div class="flex justify-center ml-1">
                                                                <a id="open-modal-{{$comp->child_id}}" type="button"
                                                                   class="open-modal font-medium text-blue-600 dark:text-blue-500 hover:underline p-0.5 bg-gray-800 rounded-md shadow-md">
                                                                    <svg fill="#ffffff" width="30px" height="30px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 lg:w-6 lg:h-6">
                                                                        <title>podgląd podcyklu</title>
                                                                        <path d="M15.694 13.541l2.666 2.665 5.016-5.017 2.59 2.59 0.004-7.734-7.785-0.046 2.526 2.525-5.017 5.017zM25.926 16.945l-1.92-1.947 0.035 9.007-16.015 0.009 0.016-15.973 8.958-0.040-2-2h-7c-1.104 0-2 0.896-2 2v16c0 1.104 0.896 2 2 2h16c1.104 0 2-0.896 2-2l-0.074-7.056z"></path>
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="list-{{$comp->child_id}}" class="dropdown hidden mt-6 mb-4 w-[95%] mr-1">
                                                        <div class="relative overflow-x-auto shadow-md">
                                                            <table class="w-full text-sm md:text-lg text-left text-gray-500 dark:text-gray-400">
                                                                <thead class="text-sm md:text-lg text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                                <tr>
                                                                    <th scope="col" class="px-6 py-3">
                                                                        Opis
                                                                    </th>
                                                                    <th scope="col" class="px-6 py-3"></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                        Zdjęcie
                                                                    </th>
                                                                    <td class="p-1">
                                                                        @if(!is_null($comp->image))
                                                                            @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp
                                                                            <div class="flex justify-center">
                                                                                <div class="max-w-[150px]">
                                                                                    <img src="{{asset('storage/'.$path.$comp->image)}}" alt="">
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                        Surowiec
                                                                    </th>
                                                                    <td class="px-6 py-4">
                                                                        {{is_null($comp->material) ? '' : $comp->material}}
                                                                    </td>
                                                                </tr>
                                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                        @php
                                                                            $name = '';
                                                                            $dim = '';
                                                                            if(!is_null($comp->height)) {
                                                                                $name .= 'wys ';
                                                                                $dim .= $comp->height.' ';
                                                                            }
                                                                            if(!is_null($comp->length)) {
                                                                                if(!empty($name)) {
                                                                                    $name .= 'x  ';
                                                                                    $dim .= 'x  ';
                                                                                }
                                                                                $name .= 'dług ';
                                                                                $dim .= $comp->length.' ';
                                                                            }
                                                                            if(!is_null($comp->width)) {
                                                                                if(!empty($name)) {
                                                                                    $name .= 'x  ';
                                                                                    $dim .= 'x  ';
                                                                                }
                                                                                $name .= 'szer';
                                                                                $dim .= $comp->width.' ';
                                                                            }
                                                                            $name .= empty($name) ? 'Wymiary' : ' [cm]';

                                                                        @endphp
                                                                        {{$name}}
                                                                    </th>
                                                                    <td class="px-6 py-4">
                                                                        {{$dim}}
                                                                    </td>
                                                                </tr>
                                                                @if(!empty($comp->description))
                                                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                            Szczegóły
                                                                        </th>
                                                                        <td class="px-6 py-4">
                                                                            {{$comp->description}}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </x-list-element>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="w-full {{$p_cycle->category == 1? 'xl:w-1/2' : ''}}  flex justify-start items-center flex-col">
                                @if($p_cycle->category == 1)
                                    <div class="w-full shadow-md bg-white text-lg text-gray-800 font-semibold pl-5 py-2">
                                        <div class="p-2">
                                            Zadania
                                        </div>
                                    </div>
                                @endif
                                <div class="w-full h-full max-h-[400px] overflow-y-scroll flex justify-center items-start">
                                    <div class="w-[95%] p-2 ">
                                        @if(is_array($child_prod_schemas))
                                            @foreach($child_prod_schemas as $comp_cycle_id => $prod_schemas)
                                                @php $current_id = 0; @endphp
                                                @foreach($prod_schemas as $prod_schema)
                                                    @if($prod_schema->child_id != $current_id)
                                                        <x-list-element id="schemacycle-{{$prod_schema->child_id}}-compcycle-{{$comp_cycle_id}}"
                                                                        class="{{'comp-'.$comp_cycle_id}} {{($prod_schema->status == 0)? 'finished' : ''}} my-6 list-element-2 flex-col w-full lg:py-0 py-0 hidden">
                                                            <div class="flex flex-row justify-between w-full">
                                                                <input type="number" class="schema-list-element-id hidden" value="{{$prod_schema->child_id}}">
                                                                <div class="w-[80%] flex flex-col justify-between items-center">
                                                                    <div class="w-full flex flex-row justify-left items-center">
                                                                        <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                                            {{$prod_schema->name}}
                                                                        </p>
                                                                        @if($prod_schema->status == 0)
                                                                            <p class="my-2 rounded-lg inline-block text-white bg-green-450 shadow-lg list-element-name py-1.5 px-2 text-xs whitespace-nowrap overflow-clip">
                                                                                Zakończone
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="w-[15%] flex justify-end items-center">
                                                                    <div id="expbtn-schema-{{$prod_schema->child_id}}" class="expand-btn inline-block  p-0.5 bg-gray-800 rounded-md rotate-0 transition-all mr-1">
                                                                        <svg width="30px" height="30px" viewBox="0 0 1024 1024" class="w-5 h-5 lg:w-6 lg:h-6"  xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M903.232 256l56.768 50.432L512 768 64 306.432 120.768 256 512 659.072z" fill="#ffffff" />
                                                                        </svg>
                                                                    </div>
                                                                    <div class="flex justify-center ml-1">
                                                                        <a id="open-modal-{{$prod_schema->child_id}}" type="button"
                                                                           class="open-modal font-medium text-blue-600 dark:text-blue-500 hover:underline p-0.5 bg-gray-800 rounded-md shadow-md">
                                                                            <svg fill="#ffffff" width="30px" height="30px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 lg:w-6 lg:h-6">
                                                                                <title>podgląd podcyklu</title>
                                                                                <path d="M15.694 13.541l2.666 2.665 5.016-5.017 2.59 2.59 0.004-7.734-7.785-0.046 2.526 2.525-5.017 5.017zM25.926 16.945l-1.92-1.947 0.035 9.007-16.015 0.009 0.016-15.973 8.958-0.040-2-2h-7c-1.104 0-2 0.896-2 2v16c0 1.104 0.896 2 2 2h16c1.104 0 2-0.896 2-2l-0.074-7.056z"></path>
                                                                            </svg>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="list-{{$prod_schema->child_id}}" class="dropdown w-[95%] my-3 hidden">
                                                                <div class="w-full text-sm lg:text-lg font-semibold bg-gray-800 text-white rounded-t-xl pl-5 py-2 flex flex-row justify-between">
                                                                    <div class="p-1">
                                                                        Podzadania
                                                                    </div>
                                                                </div>
                                                                <div class="input-table shadow-md rounded-b-xl mb-4">
                                                                    <div class="relative overflow-scroll max-h-[400px]">
                                                                        <table class="w-full text-sm text-left rtl:text-right pb-2 bg-gray-100 text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border-slate-300">
                                                                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                                                            <tr>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                    Wybierz
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 text-center">
                                                                                    Kol.
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 text-center">
                                                                                    Podzadanie
                                                                                </th>
                                                                                <th scope="col" class="px-6 py-3 hidden-input hidden text-center">
                                                                                    Ilość (szt)
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                    Start pracy
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                    Koniec pracy
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                    Czas pracy (h)
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                    Pracownicy
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                    Komentarz
                                                                                </th>
                                                                                <th scope="col" class="px-5 py-3 hidden-input hidden text-center">
                                                                                    Defekty (szt)
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                    Defekty - przyczyna
                                                                                </th>
                                                                                <th scope="col" class="px-9 py-3 hidden-input hidden text-center">
                                                                                    Odpady
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                    Odpady - jednostka
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                    Odpady - przyczyna
                                                                                </th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            @php $current_schema_id = $prod_schema->prod_schema_id; @endphp
                                                                            @foreach($prod_schemas as $schema_task)
                                                                                @if($current_schema_id == $schema_task->prod_schema_id)
                                                                                    <tr id="row-{{$current_schema_id.'-'.$schema_task->task_id}}" class="input-table-row font-medium text-gray-600 dark:bg-gray-800 dark:border-gray-700 hover:bg-blue-150 dark:hover:bg-gray-600 border border-slate-300 ">
                                                                                        <td class="hidden-input hidden">
                                                                                            <div class="w-full h-full flex justify-center items-center">
                                                                                                <input type="checkbox" id="check-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                       name="check_{{$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                       class="input-checkbox rounded-sm"
                                                                                                       value="{{old('check_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"/>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="sequence-no px-2 py-3 whitespace-nowrap text-center rounded-md">
                                                                                            {{$schema_task->task_sequence_no}}
                                                                                        </td>
                                                                                        <td class="name px-2 py-3 whitespace-nowrap rounded-md">
                                                                                            {{$schema_task->task_name}}
                                                                                        </td>
                                                                                        <td class="hidden-input hidden amount px-2 w-[70px]">
                                                                                            <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                                                <div class="w-full p-1 flex justify-center items-center h-full">
                                                                                                    @if($schema_task->task_amount_required)
                                                                                                        <input id="{{'amount-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" type="number" min="0"
                                                                                                               class="disabled-input amount-input nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center"
                                                                                                               name="{{'amount_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" placeholder="0"
                                                                                                               value="{{old('amount_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                                               disabled>
                                                                                                    @else
                                                                                                        <div class="block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                                                            -
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="hidden-input px-2 py-3 text-center hidden">
                                                                                            <input type="datetime-local" id="{{'start-time-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                   name="{{'start_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                   min="2024-01-01T00:00" max="{{isset($max_time)? $max_time.'T23:59' : '2060-12-31T23:59'}}" step="60"
                                                                                                   class="disabled-input input-start-time input-time nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                                                                   value="{{old('start_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                                   disabled>
                                                                                        </td>
                                                                                        <td class="hidden-input px-2 py-3 text-center hidden">
                                                                                            <input type="datetime-local" id="end-time-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                   name="{{'end_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                   min="2024-01-01T00:00" max="{{isset($max_time)? $max_time.'T23:59' : '2060-12-31T23:59'}}" step="60"
                                                                                                   class="disabled-input input-end-time input-time nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                                                                   value="{{old('end_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                                   disabled>
                                                                                        </td>
                                                                                        <td class="hidden-input px-2 py-3 text-center hidden">
                                                                                            <div class="disabled-input work-time-in-hours block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                @php
                                                                                                    $duration_hours = '0:00';
                                                                                                    $duration_minutes = old('work_duration_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id);
                                                                                                    if($duration_minutes) {
                                                                                                        $hours = floor($duration_minutes / 60);
                                                                                                        $minutes = $duration_minutes % 60;
                                                                                                        $duration_hours = sprintf('%d:%02d', $hours, $minutes);
                                                                                                    }
                                                                                                @endphp
                                                                                                {{$duration_hours}}
                                                                                            </div>
                                                                                            <input id="{{'work-duration-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                   type="number" name="{{'work_duration_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                   value="{{old('work_duration_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                                   class="disabled-input work-duration nullable-input hidden" disabled>
                                                                                        </td>
                                                                                        <td class="hidden-input employee-no px-2 py-3 text-center hidden">
                                                                                            @if(isset($users) and in_array($user->role,['admin','manager']))
                                                                                                <div class="p-1 flex justify-start items-center h-full">
                                                                                                    <div class="flex justify-center flex-col mr-2">
                                                                                                        <button type="button" id="" disabled class="disabled-input-employee-btn add-employee employee-btn inline-block  p-0.5 bg-gray-800 rounded-sm m-0.5">
                                                                                                            <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg">
                                                                                                                <title>dodaj pracownika</title>
                                                                                                                <path d="M4 12H20M12 4V20" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                                            </svg>
                                                                                                        </button>
                                                                                                        <button type="button" id="" disabled class="disabled-input-employee-btn remove-employee employee-btn inline-block  p-0.5 bg-gray-800 rounded-sm m-0.5">
                                                                                                            <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg">
                                                                                                                <title>usuń pracownika</title>
                                                                                                                <path d="M6 12L18 12" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                                            </svg>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <input id="{{'employee-count-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" type="number" value="1"
                                                                                                           name="{{'employee_count_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                           disabled class="disabled-input employee-input-count hidden">
                                                                                                    <select id="{{'employee-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                            name="{{'employee_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                            disabled class="disabled-input employee-input bg-gray-200 w-[100px] mr-2 block py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                        @foreach($users as $u)
                                                                                                            <option value="{{$u->id}}" class="bg-white"
                                                                                                                    @if(old('employee_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $u->id)
                                                                                                                        selected
                                                                                                                    @elseif($user->id == $u->id)
                                                                                                                        selected
                                                                                                                @endif>
                                                                                                                {{$u->employeeNo}}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                            @else
                                                                                                <div class="block w-full bg-white py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                    {{$user->employeeNo}}
                                                                                                </div>
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="hidden-input hidden px-2 py-3 text-center">
                                                                                            <textarea id="{{'comment-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                  name="{{'comment_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" maxlength="255"
                                                                                                  class="disabled-input nullable-input xl:p-2.5 bg-gray-200 block min-w-[250px] w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                                                                  disabled placeholder="Komentarz" rows="1">{{old('comment_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}</textarea>
                                                                                        </td>
                                                                                        <td class="hidden-input hidden defect px-2 w-[70px]">
                                                                                            <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                                                <div class="w-full p-1 flex justify-center items-center h-full">
                                                                                                    @if($schema_task->task_amount_required)
                                                                                                        <input id="defect-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" type="number" min="0" value="{{old('defect_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                                               class="disabled-input defect-input nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center"
                                                                                                               name="defect_{{$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" placeholder="-"
                                                                                                               disabled>
                                                                                                    @else
                                                                                                        <div class="block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                                                            -
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="hidden-input hidden defect-rc px-2 py-3 text-center">
                                                                                            <div class="p-1 flex justify-center items-center h-full">
                                                                                                @if($schema_task->task_amount_required)
                                                                                                    <select id="{{'defect-rc-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                            name="{{'defect_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                            disabled class="disabled-input defect-rc-input nullable-input bg-gray-200 min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                        <option value="" class="bg-white">
                                                                                                        </option>
                                                                                                        @if(isset($reason_codes))
                                                                                                            @foreach($reason_codes as $code)
                                                                                                                <option value="{{$code->reason_code}}" class="bg-white"
                                                                                                                        @if(old('defect_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $code->reason_code)
                                                                                                                            selected
                                                                                                                    @endif>
                                                                                                                    {{$code->reason_code_desc}}
                                                                                                                </option>
                                                                                                            @endforeach
                                                                                                        @endif
                                                                                                    </select>
                                                                                                @else
                                                                                                    <div class="block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                                                        -
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="hidden-input hidden waste px-2 w-[70px]">
                                                                                            <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                                                <div class="w-full p-1 flex justify-center items-center h-full">
                                                                                                    <input id="waste-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                           type="number" min="0" step="any" value="{{old('waste_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                                           class="disabled-input waste-input nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center"
                                                                                                           name="waste_{{$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" placeholder="-"
                                                                                                           disabled>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="hidden-input hidden waste-unit px-2 py-3 text-center">
                                                                                            <div class="p-1 flex justify-center items-center h-full">
                                                                                                <select id="{{'waste-unit-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                        name="{{'waste_unit_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                        disabled class="disabled-input waste-unit-input nullable-input bg-gray-200 min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                    <option value="" class="bg-white">
                                                                                                    </option>
                                                                                                    @if(isset($units))
                                                                                                        @foreach($units as $u)
                                                                                                            <option value="{{$u->unit}}" class="bg-white"
                                                                                                                    @if(old('waste_unit_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $u->unit)
                                                                                                                        selected
                                                                                                                @endif>
                                                                                                                {{$u->unit}}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    @endif
                                                                                                </select>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="hidden-input hidden waste-rc px-2 py-3 text-center">
                                                                                            <div class="p-1 flex justify-center items-center h-full">
                                                                                                <select id="{{'waste-rc-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                        name="{{'waste_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                        disabled class="disabled-input waste-rc-input bg-gray-200 min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                    <option value="" class="bg-white">
                                                                                                    </option>
                                                                                                    @if(isset($reason_codes))
                                                                                                        @foreach($reason_codes as $code)
                                                                                                            <option value="{{$code->reason_code}}" class="bg-white"
                                                                                                                    @if(old('waste_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $code->reason_code)
                                                                                                                        selected
                                                                                                                @endif>
                                                                                                                {{$code->reason_code_desc}}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    @endif
                                                                                                </select>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endif
                                                                            @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </x-list-element>
                                                    @endif
                                                    @php $current_id = $prod_schema->child_id; @endphp
                                                @endforeach
                                            @endforeach
                                        @else
                                            @php $current_id = 0; @endphp
                                            @foreach($child_prod_schemas  as $prod_schema)
                                                @if($prod_schema->child_id != $current_id)
                                                    <x-list-element id="schemacycle-{{$prod_schema->child_id}}"
                                                                    class="{{($prod_schema->status == 0)? 'finished' : ''}} comp my-6 list-element-2 flex-col w-full lg:py-0 py-0">
                                                        <div class="flex flex-row justify-between w-full">
                                                            <input type="number" class="schema-list-element-id hidden" value="{{$prod_schema->child_id}}">
                                                            <div class="w-[80%] flex flex-col justify-between items-center">
                                                                <div class="w-full flex flex-row justify-left items-center">
                                                                    <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                                        {{$prod_schema->name}}
                                                                    </p>
                                                                    @if($prod_schema->status == 0)
                                                                        <p class="my-2 rounded-lg inline-block text-white bg-green-450 shadow-lg list-element-name py-1.5 px-2 text-xs whitespace-nowrap overflow-clip">
                                                                            Zakończone
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="w-[15%] flex justify-end items-center">
                                                                <div id="expbtn-schema-{{$prod_schema->child_id}}" class="expand-btn inline-block  p-0.5 bg-gray-800 rounded-md rotate-0 transition-all mr-1">
                                                                    <svg width="30px" height="30px" viewBox="0 0 1024 1024" class="w-5 h-5 lg:w-6 lg:h-6"  xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M903.232 256l56.768 50.432L512 768 64 306.432 120.768 256 512 659.072z" fill="#ffffff" />
                                                                    </svg>
                                                                </div>
                                                                <div class="flex justify-center ml-1">
                                                                    <a id="open-modal-{{$prod_schema->child_id}}" type="button"
                                                                       class="open-modal font-medium text-blue-600 dark:text-blue-500 hover:underline p-0.5 bg-gray-800 rounded-md shadow-md">
                                                                        <svg fill="#ffffff" width="30px" height="30px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 lg:w-6 lg:h-6">
                                                                            <title>podgląd podcyklu</title>
                                                                            <path d="M15.694 13.541l2.666 2.665 5.016-5.017 2.59 2.59 0.004-7.734-7.785-0.046 2.526 2.525-5.017 5.017zM25.926 16.945l-1.92-1.947 0.035 9.007-16.015 0.009 0.016-15.973 8.958-0.040-2-2h-7c-1.104 0-2 0.896-2 2v16c0 1.104 0.896 2 2 2h16c1.104 0 2-0.896 2-2l-0.074-7.056z"></path>
                                                                        </svg>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="list-{{$prod_schema->child_id}}" class="dropdown w-[95%] my-3 hidden">
                                                            <div class="w-full text-sm lg:text-lg font-semibold bg-gray-800 text-white rounded-t-xl pl-5 py-2 flex flex-row justify-between">
                                                                <div class="p-1">
                                                                    Podzadania
                                                                </div>
                                                            </div>
                                                            <div class="input-table shadow-md rounded-b-xl mb-4">
                                                                <div class="relative overflow-scroll max-h-[400px]">
                                                                    <table class="w-full text-sm text-left rtl:text-right pb-2 bg-gray-100 text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border-slate-300">
                                                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                                                        <tr>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                Wybierz
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 text-center">
                                                                                Kol.
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 text-center">
                                                                                Podzadanie
                                                                            </th>
                                                                            <th scope="col" class="px-6 py-3 hidden-input hidden text-center">
                                                                                Ilość (szt)
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                Start pracy
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                Koniec pracy
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                Czas pracy (h)
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                Pracownicy
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                Komentarz
                                                                            </th>
                                                                            <th scope="col" class="px-5 py-3 hidden-input hidden text-center">
                                                                                Defekty (szt)
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                Defekty - przyczyna
                                                                            </th>
                                                                            <th scope="col" class="px-9 py-3 hidden-input hidden text-center">
                                                                                Odpady
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                Odpady - jednostka
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden text-center">
                                                                                Odpady - przyczyna
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @php $current_schema_id = $prod_schema->prod_schema_id; @endphp
                                                                        @foreach($child_prod_schemas as $schema_task)
                                                                            @if($current_schema_id == $schema_task->prod_schema_id)
                                                                                <tr id="row-{{$current_schema_id.'-'.$schema_task->task_id}}" class="input-table-row font-medium text-gray-600 dark:bg-gray-800 dark:border-gray-700 hover:bg-blue-150 dark:hover:bg-gray-600 border border-slate-300 ">
                                                                                    <td class="hidden-input hidden">
                                                                                        <div class="w-full h-full flex justify-center items-center">
                                                                                            <input type="checkbox" id="check-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                   name="check_{{$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                   class="input-checkbox rounded-sm"
                                                                                                   value="{{old('check_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"/>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="sequence-no px-2 py-3 whitespace-nowrap text-center rounded-md">
                                                                                        {{$schema_task->task_sequence_no}}
                                                                                    </td>
                                                                                    <td class="name px-2 py-3 whitespace-nowrap rounded-md">
                                                                                        {{$schema_task->task_name}}
                                                                                    </td>
                                                                                    <td class="hidden-input amount px-2 hidden w-[70px]">
                                                                                        <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                                            <div class="w-full p-1 flex justify-center items-center h-full">
                                                                                                @if($schema_task->task_amount_required)
                                                                                                    <input id="{{'amount-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" type="number" min="0"
                                                                                                           class="disabled-input amount-input nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center"
                                                                                                           name="{{'amount_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" placeholder="0"
                                                                                                           value="{{old('amount_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                                           disabled>
                                                                                                @else
                                                                                                    <div class="block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                                                        -
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="hidden-input px-2 py-3 text-center hidden">
                                                                                        <input type="datetime-local" id="{{'start-time-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                               name="{{'start_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                               min="2024-01-01T00:00" max="{{isset($max_time)? $max_time.'T23:59' : '2060-12-31T23:59'}}" step="60"
                                                                                               class="disabled-input input-start-time selected-input-time nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                                                               value="{{old('start_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                               disabled>
                                                                                    </td>
                                                                                    <td class="hidden-input px-2 py-3 text-center hidden">
                                                                                        <input type="datetime-local" id="end-time-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                               name="{{'end_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                               min="2024-01-01T00:00" max="{{isset($max_time)? $max_time.'T23:59' : '2060-12-31T23:59'}}" step="60"
                                                                                               class="disabled-input input-end-time input-time nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                                                               value="{{old('end_time_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                               disabled>
                                                                                    </td>
                                                                                    <td class="hidden-input px-2 py-3 text-center hidden">
                                                                                        <div class="disabled-input work-time-in-hours block bg-gray-200 w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                            @php
                                                                                                $duration_hours = '0:00';
                                                                                                $duration_minutes = old('work_duration_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id);
                                                                                                if($duration_minutes) {
                                                                                                    $hours = floor($duration_minutes / 60);
                                                                                                    $minutes = $duration_minutes % 60;
                                                                                                    $duration_hours = sprintf('%d:%02d', $hours, $minutes);
                                                                                                }
                                                                                            @endphp
                                                                                            {{$duration_hours}}
                                                                                        </div>
                                                                                        <input id="{{'work-duration-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                               type="number" name="{{'work_duration_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                               value="{{old('work_duration_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                               class="disabled-input work-duration nullable-input hidden" disabled>
                                                                                    </td>
                                                                                    <td class="hidden-input employee-no px-2 py-3 text-center hidden">
                                                                                        @if(isset($users) and in_array($user->role,['admin','manager']))
                                                                                            <div class="p-1 flex justify-start items-center h-full">
                                                                                                <div class="flex justify-center flex-col mr-2">
                                                                                                    <button type="button" id="" disabled class="disabled-input-employee-btn add-employee employee-btn inline-block  p-0.5 bg-gray-800 rounded-sm m-0.5">
                                                                                                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg">
                                                                                                            <title>dodaj pracownika</title>
                                                                                                            <path d="M4 12H20M12 4V20" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                    <button type="button" id="" disabled class="disabled-input-employee-btn remove-employee employee-btn inline-block  p-0.5 bg-gray-800 rounded-sm m-0.5">
                                                                                                        <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg">
                                                                                                            <title>usuń pracownika</title>
                                                                                                            <path d="M6 12L18 12" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                </div>
                                                                                                <input id="{{'employee-count-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" type="number" value="1"
                                                                                                       name="{{'employee_count_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                       disabled class="disabled-input employee-input-count hidden">
                                                                                                <select id="{{'employee-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                        name="{{'employee_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                        disabled class="disabled-input employee-input bg-gray-200 w-[100px] mr-2 block py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                    @foreach($users as $u)
                                                                                                        <option value="{{$u->id}}" class="bg-white"
                                                                                                                @if(old('employee_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $u->id)
                                                                                                                    selected
                                                                                                                @elseif($user->id == $u->id)
                                                                                                                    selected
                                                                                                            @endif>
                                                                                                            {{$u->employeeNo}}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </div>
                                                                                        @else
                                                                                            <div class="block w-full bg-white py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                {{$user->employeeNo}}
                                                                                            </div>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="hidden-input hidden px-2 py-3 text-center">
                                                                                        <textarea id="{{'comment-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                              name="{{'comment_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" maxlength="255"
                                                                                              class="disabled-input nullable-input xl:p-2.5 bg-gray-200 block min-w-[250px] w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                                                              disabled placeholder="Komentarz" rows="1">{{old('comment_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}</textarea>
                                                                                    </td>
                                                                                    <td class="hidden-input hidden defect px-2 max-w-[50px]">
                                                                                        <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                                            <div class="w-full p-1 flex justify-center items-center h-full">
                                                                                                @if($schema_task->task_amount_required)
                                                                                                    <input id="defect-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}" type="number" min="0" value="{{old('defect_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                                           class="disabled-input defect-input nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center"
                                                                                                           name="defect_{{$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" placeholder="-"
                                                                                                           disabled>
                                                                                                @else
                                                                                                    <div class="block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                                                        -
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="hidden-input hidden defect-rc px-2 py-3 text-center">
                                                                                        <div class="p-1 flex justify-center items-center h-full">
                                                                                            @if($schema_task->task_amount_required)
                                                                                                <select id="{{'defect-rc-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                        name="{{'defect_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                        disabled class="disabled-input defect-rc-input nullable-input bg-gray-200 min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                    <option value="" class="bg-white">
                                                                                                    </option>
                                                                                                    @if(isset($reason_codes))
                                                                                                        @foreach($reason_codes as $code)
                                                                                                            <option value="{{$code->reason_code}}" class="bg-white"
                                                                                                                    @if(old('defect_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $code->reason_code)
                                                                                                                        selected
                                                                                                                @endif>
                                                                                                                {{$code->reason_code_desc}}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    @endif
                                                                                                </select>
                                                                                            @else
                                                                                                <div class="block w-full bg-gray-200 py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                                                    -
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="hidden-input hidden waste px-2 max-w-[50px]">
                                                                                        <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                                            <div class="w-full p-1 flex justify-center items-center h-full">
                                                                                                <input id="waste-{{$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                       type="number" min="0" step="any" value="{{old('waste_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id)}}"
                                                                                                       class="disabled-input waste-input nullable-input bg-gray-200 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center"
                                                                                                       name="waste_{{$schema_task->prod_schema_id.'_'.$schema_task->task_id}}" placeholder="-"
                                                                                                       disabled>
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="hidden-input hidden waste-unit px-2 py-3 text-center">
                                                                                        <div class="p-1 flex justify-center items-center h-full">
                                                                                            <select id="{{'waste-unit-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                    name="{{'waste_unit_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                    disabled class="disabled-input waste-unit-input nullable-input bg-gray-200 min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                <option value="" class="bg-white">
                                                                                                </option>
                                                                                                @if(isset($units))
                                                                                                    @foreach($units as $u)
                                                                                                        <option value="{{$u->unit}}" class="bg-white"
                                                                                                                @if(old('waste_unit_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $u->unit)
                                                                                                                    selected
                                                                                                            @endif>
                                                                                                            {{$u->unit}}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                @endif
                                                                                            </select>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="hidden-input hidden waste-rc px-2 py-3 text-center">
                                                                                        <div class="p-1 flex justify-center items-center h-full">
                                                                                            <select id="{{'waste-rc-'.$schema_task->prod_schema_id.'-'.$schema_task->task_id}}"
                                                                                                    name="{{'waste_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id}}"
                                                                                                    disabled class="disabled-input waste-rc-input nullable-input bg-gray-200 min-w-[100px] block w-full py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                <option value="" class="bg-white">
                                                                                                </option>
                                                                                                @if(isset($reason_codes))
                                                                                                    @foreach($reason_codes as $code)
                                                                                                        <option value="{{$code->reason_code}}" class="bg-white"
                                                                                                                @if(old('waste_rc_'.$schema_task->prod_schema_id.'_'.$schema_task->task_id) == $code->reason_code)
                                                                                                                    selected
                                                                                                            @endif>
                                                                                                            {{$code->reason_code_desc}}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                @endif
                                                                                            </select>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </x-list-element>
                                                @endif
                                                @php $current_id = $prod_schema->child_id; @endphp
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

<table class="hidden">
    <tbody>
    @if(isset($child_components))
        @foreach($child_components as $c_cycle)
            <tr id="row-{{$c_cycle->child_id}}" class="">
                <td class="">
                    @php
                        switch ($c_cycle->status) {
                            case 0:
                                $status = 'Zakończony';
                                $bg = 'bg-green-450';
                                break;
                            case 1:
                                $status = 'Aktywny';
                                $bg = 'bg-blue-450';
                                break;
                            case 2:
                                $status = 'Nierozpoczęty';
                                $bg = 'bg-yellow-300';
                                break;
                            case 3:
                                $status = 'Po terminie';
                                $bg = 'bg-red-500';
                                break;
                        }
                    @endphp
                    <div class="">
                        <div class="">
                            <div class="col-value status">
                                {{$status}}
                            </div>
                        </div>
                    </div>

                </td>
                <td class="col-value category">
                    {{$c_cycle->category == 2? 'Materiał' : 'Zadanie'}}
                </td>
                <td class="">
                    @if(!is_null($c_cycle->image) and $c_cycle->category == 2)
                        @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp
                        <div class="flex justify-center">
                            <div class="max-w-[100px]">
                                <img src="{{asset('storage/'.$path.$c_cycle->image)}}" alt="">
                            </div>
                        </div>
                    @endif
                </td>
                <td class="col-value name">
                    {{$c_cycle->name}}
                </td>
                <td class="col-value productivity">
                    {{$c_cycle->productivity.'%'}}
                </td>
                <td class="col-value time-spent-in-hours">
                    {{$c_cycle->time_spent_in_hours}}
                </td>
                <td class="col-value current-amount">
                    {{$c_cycle->current_amount}}
                </td>
                <td class="col-value expected-amount-per-spent-time">
                    {{$c_cycle->expected_amount_per_spent_time}}
                </td>
                <td class="col-value total-amount">
                    {{$c_cycle->total_amount}}
                </td>
                <td class="col-value progress">
                    {{$c_cycle->progress}}
                </td>
                <td class="col-value start-time">
                    {{$c_cycle->start_time}}
                </td>
                <td class="col-value end-time">
                    {{empty($c_cycle->end_time) ? 'cykl trwa' : $c_cycle->end_time}}
                </td>
                <td class="col-value expected-amount-per-time-frame">
                    {{$c_cycle->expected_amount_per_time_frame}}
                </td>
                <td class="col-value expected-time-to-complete-in-hours">
                    {{$c_cycle->expected_time_to_complete_in_hours}}
                </td>
                <td class="col-value defect-amount">
                    {{$c_cycle->defect_amount}}
                </td>
                <td class="col-value defect-percent">
                    {{$c_cycle->defect_percent}}
                </td>
                <td class="col-value waste-amount">
                    {{is_null($c_cycle->waste_amount) ? '-' : $c_cycle->waste_amount}}
                </td>
                <td class="col-value waste-unit">
                    {{is_null($c_cycle->waste_unit) ? '-' : $c_cycle->waste_unit}}
                </td>
            </tr>
        @endforeach
    @endif
    @if(isset($modal_data))
        @foreach($modal_data as $c_cycle)
            <tr id="row-{{$c_cycle->child_id}}" class="">
                <td class="">
                    @php
                        switch ($c_cycle->status) {
                            case 0:
                                $status = 'Zakończony';
                                $bg = 'bg-green-450';
                                break;
                            case 1:
                                $status = 'Aktywny';
                                $bg = 'bg-blue-450';
                                break;
                            case 2:
                                $status = 'Nierozpoczęty';
                                $bg = 'bg-yellow-300';
                                break;
                            case 3:
                                $status = 'Po terminie';
                                $bg = 'bg-red-500';
                                break;
                        }
                    @endphp
                    <div class="">
                        <div class="">
                            <div class="col-value status">
                                {{$status}}
                            </div>
                        </div>
                    </div>

                </td>
                <td class="col-value category">
                    {{$c_cycle->category == 1? 'Produkt' : ($c_cycle->category == 2? 'Materiał' : 'Zadanie')}}
                </td>
                <td class="">
                    @if(!is_null($c_cycle->image) and $c_cycle->category == 2)
                        @php $path = isset($storage_path_components) ? $storage_path_components.'/' : ''; @endphp
                        <div class="flex justify-center">
                            <div class="max-w-[100px]">
                                <img src="{{asset('storage/'.$path.$c_cycle->image)}}" alt="">
                            </div>
                        </div>
                    @endif
                </td>
                <td class="col-value name">
                    {{$c_cycle->name}}
                </td>
                <td class="col-value productivity">
                    {{$c_cycle->productivity.'%'}}
                </td>
                <td class="col-value time-spent-in-hours">
                    {{$c_cycle->time_spent_in_hours}}
                </td>
                <td class="col-value current-amount">
                    {{$c_cycle->current_amount}}
                </td>
                <td class="col-value expected-amount-per-spent-time">
                    {{$c_cycle->expected_amount_per_spent_time}}
                </td>
                <td class="col-value total-amount">
                    {{$c_cycle->total_amount}}
                </td>
                <td class="col-value progress">
                    {{$c_cycle->progress}}
                </td>
                <td class="col-value start-time">
                    {{$c_cycle->start_time}}
                </td>
                <td class="col-value end-time">
                    {{empty($c_cycle->end_time) ? 'cykl trwa' : $c_cycle->end_time}}
                </td>
                <td class="col-value expected-amount-per-time-frame">
                    {{$c_cycle->expected_amount_per_time_frame}}
                </td>
                <td class="col-value expected-time-to-complete-in-hours">
                    {{$c_cycle->expected_time_to_complete_in_hours}}
                </td>
                <td class="col-value defect-amount">
                    {{$c_cycle->defect_amount}}
                </td>
                <td class="col-value defect-percent">
                    {{$c_cycle->defect_percent}}
                </td>
                <td class="col-value waste-amount">
                    {{is_null($c_cycle->waste_amount) ? '-' : $c_cycle->waste_amount}}
                </td>
                <td class="col-value waste-unit">
                    {{is_null($c_cycle->waste_unit) ? '-' : $c_cycle->waste_unit}}
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
{{--                        </div>--}}
{{--                        <div class="w-full p-2 bg-gray-50 rounded-b-xl">--}}
{{--                            {{ $child_cycles->links() }}--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div id="modal-details-background" class="z-[100] fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden">
                <!-- Modal Container -->
                <div id="modal-details" class="z-[100] fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[95%] md:w-[90%] xl:w-4/5 bg-white rounded-lg shadow-md">
                    <!-- Modal Header -->
                    <div class="w-full bg-gray-800 rounded-t-lg text-white p-4 flex flex-row justify-between items-center">
                        <h2 class="text-xl lg:text-2xl font-medium">Szczegóły podcyklu</h2>
                        <x-nav-button id="close-modal-details-button" class="">
                            <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M19.207 6.207a1 1 0 0 0-1.414-1.414L12 10.586 6.207 4.793a1 1 0 0 0-1.414 1.414L10.586 12l-5.793 5.793a1 1 0 1 0 1.414 1.414L12 13.414l5.793 5.793a1 1 0 0 0 1.414-1.414L13.414 12l5.793-5.793z" fill="#ffffff"/></svg>
                        </x-nav-button>
                    </div>
                    <div class="flex justify-center items-start max-h-[500px] overflow-y-scroll mt-6">
                        <div id="modal-details-table" class="cycle w-[95%] rounded-xl bg-white my-5 shadow-md">
                            <dl class="grid grid-cols-4 xl:grid-cols-8 overflow-hidden text-left rounded-xl">
                                <div class="col-span-4 flex flex-col bg-gray-200/50 xl:border-r-2">
                                    <dt class="order-first text-sm lg:text-lg font-semibold bg-gray-800 text-white w-[45%] xl:w-1/2 rounded-tl-xl pl-5 py-2 flex flex-row justify-between">
                                        <div class="col-value category p-1">
                                        </div>
                                        <div class="text-xs lg:text-sm flex justify-center items-center">
                                            <div class="col-value status cycle-tag p-1 mx-2 rounded-md whitespace-nowrap"></div>
                                        </div>
                                    </dt>
                                    <dd class="col-value name text-lg xl:text-xl font-semibold tracking-tight text-gray-900 pl-5 py-4"></dd>
                                </div>
                                <div class="col-span-2 flex flex-col bg-gray-200/50">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Początek cyklu</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            <svg fill="#000000" width="30px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                                <g fill-rule="evenodd">
                                                    <path d="M1439.667 1226.667H1333V1440c0 14.187 5.653 27.733 15.573 37.76l123.414 123.306 75.413-75.413-107.733-107.733v-191.253Z"/>
                                                    <path d="M1386.333 1813.333C1180.467 1813.333 1013 1645.867 1013 1440c0-205.867 167.467-373.333 373.333-373.333 205.867 0 373.334 167.466 373.334 373.333 0 205.867-167.467 373.333-373.334 373.333m0-853.333c-264.64 0-480 215.36-480 480s215.36 480 480 480 480-215.36 480-480-215.36-480-480-480"/>
                                                    <path d="M1546.333 426.667H159.666v-160c0-29.44 24-53.334 53.334-53.334h160v53.334c0 29.44 23.894 53.333 53.333 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h746.667v53.334c0 29.44 23.894 53.333 53.334 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h160c29.333 0 53.333 23.894 53.333 53.334v160Zm-53.333-320h-160V53.333C1333 23.893 1309.107 0 1279.667 0c-29.44 0-53.334 23.893-53.334 53.333v53.334H479.666V53.333C479.666 23.893 455.773 0 426.333 0 396.894 0 373 23.893 373 53.333v53.334H213c-88.213 0-160 71.786-160 160v1546.666h746.666v-106.666h-640V533.333h1386.667v320H1653V266.667c0-88.214-71.787-160-160-160Z"/>
                                                </g>
                                            </svg>
                                            <p class="col-value start-time"></p>
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Koniec cyklu</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="w-full text-sm font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                            <svg fill="#000000" width="30px" height="20px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                                                <g fill-rule="evenodd">
                                                    <path d="M1439.667 1226.667H1333V1440c0 14.187 5.653 27.733 15.573 37.76l123.414 123.306 75.413-75.413-107.733-107.733v-191.253Z"/>
                                                    <path d="M1386.333 1813.333C1180.467 1813.333 1013 1645.867 1013 1440c0-205.867 167.467-373.333 373.333-373.333 205.867 0 373.334 167.466 373.334 373.333 0 205.867-167.467 373.333-373.334 373.333m0-853.333c-264.64 0-480 215.36-480 480s215.36 480 480 480 480-215.36 480-480-215.36-480-480-480"/>
                                                    <path d="M1546.333 426.667H159.666v-160c0-29.44 24-53.334 53.334-53.334h160v53.334c0 29.44 23.894 53.333 53.333 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h746.667v53.334c0 29.44 23.894 53.333 53.334 53.333 29.44 0 53.333-23.893 53.333-53.333v-53.334h160c29.333 0 53.333 23.894 53.333 53.334v160Zm-53.333-320h-160V53.333C1333 23.893 1309.107 0 1279.667 0c-29.44 0-53.334 23.893-53.334 53.333v53.334H479.666V53.333C479.666 23.893 455.773 0 426.333 0 396.894 0 373 23.893 373 53.333v53.334H213c-88.213 0-160 71.786-160 160v1546.666h746.666v-106.666h-640V533.333h1386.667v320H1653V266.667c0-88.214-71.787-160-160-160Z"/>
                                                </g>
                                            </svg>
                                            <p class="col-value end-time"></p>
                                        </dd>
                                    </div>
                                </div>
                                {{--                            ROW 3--}}
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Produktywność</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value productivity w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Postęp (%)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value progress w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Wyk. ilość (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value current-amount w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1 ">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Cel (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value total-amount w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. ilość/dzień (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value expected-amount-per-time-frame w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2 xl:border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Defekty (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value defect-amount w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 xl:border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. il/Czas pracy (szt)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value expected-amount-per-spent-time w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Czas pracy (h)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value time-spent-in-hours w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="additional-info col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Oczek. czas wyk. (h)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value expected-time-to-complete-in-hours w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class=" col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Defekty (%)</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value defect-percent w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Odpady</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value waste-amount w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                                <div class="col-span-2 flex flex-col bg-gray-200/50 border-r-2">
                                    <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Odpady jednostka</dt>
                                    <div class="w-full h-full flex justify-center items-center">
                                        <dd class="col-value waste-unit w-full text-lg font-semibold tracking-tight text-gray-900 pl-5 flex flex-row py-1">
                                        </dd>
                                    </div>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    @endif

</x-app-layout>
