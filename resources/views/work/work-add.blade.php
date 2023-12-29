<x-app-layout>
    <script type="module">

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

        function cloneSelectedElements(comp, componentId, prodSchema,  productionSchemaId) {
            let clonedProdSchema = prodSchema.clone();
            clonedProdSchema.attr('id', 'selected-'+prodSchema.attr('id'));
            let prodSchemaContainer = $('#selected-prod-schema-container');
            prodSchemaContainer.append(clonedProdSchema);
            $('#selected-prod-schema-id').val(productionSchemaId);
            let schemaModalElem = adjustSelectedElement('open-modal',prodSchemaContainer);
            if(schemaModalElem.length > 0) {
                schemaModalElem.on('click', function () {
                    modalOnClick($(this));
                });
            }
            let prodSchemaListBtn = prodSchemaContainer.find('.expand-btn');
            if(prodSchemaListBtn.length > 0) {
                prodSchemaListBtn.addClass('hidden');
            }
            let prodSchemaList = adjustSelectedElement('dropdown',prodSchemaContainer);
            prodSchemaList.removeClass('hidden');
            let hiddenInputs = prodSchemaList.find('.hidden-input');
            if(hiddenInputs.length > 0) {
                hiddenInputs.removeClass('hidden');
            }
            if(comp.length > 0) {
                let clonedComp = comp.clone();
                clonedComp.attr('id', 'selected-'+comp.attr('id'));
                let compContainer = $('#selected-comp-container');
                compContainer.append(clonedComp);
                $('#selected-comp-id').val(componentId);

                let compModalElem =adjustSelectedElement('open-modal',compContainer);
                if(compModalElem.length > 0) {
                    compModalElem.on('click', function () {
                        modalOnClick($(this));
                    });
                }
                let compListBtn = adjustSelectedElement('expand-btn',compContainer);
                if(compListBtn.length > 0) {
                    compListBtn.on('click', function () {
                        expandButtonOnClick($(this),'#selected-list-');
                    });
                }
                adjustSelectedElement('dropdown',compContainer);
            }
        }

        function removeSelectedElements() {
            $('#selected-comp-id').val(null);
            $('#selected-prod-schema-id').val(null);
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
            if(colName === 'status') {
                if(sourceText === 'Po terminie') {
                    elem.addClass('bg-red-500');
                } else if(sourceText === 'Zakończony') {
                    elem.addClass('bg-red-500');
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
                        cycleTagBg = 'bg-red-500 hover:bg-red-800';
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
                        if(width === '100') {
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

        $(document).ready(function() {
            addCycleStyles();

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
                if(!($(event.target).is("a.open-modal") || $(event.target).closest("a.open-modal").length > 0
                    || $(event.target).is("div.expand-btn") || $(event.target).closest("div.expand-btn").length > 0)) {
                    removeSelectedElements();
                    let isActive = ($(this).hasClass('active-list-elem') ? true : false);
                    $('.list-element').removeClass('active-list-elem');
                    $(this).addClass('active-list-elem');

                    $('.list-element-2').removeClass('active-list-elem');


                    let compId = $(this).attr('id');
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
            });

            $('.list-element-2').on('click', function () {
                if(!($(event.target).is("a.open-modal") || $(event.target).closest("a.open-modal").length > 0
                    || $(event.target).is("div.expand-btn") || $(event.target).closest("div.expand-btn").length > 0)) {
                    var isActive = ($(this).hasClass('active-list-elem') ? true : false);
                    removeSelectedElements();
                    $('.list-element-2').removeClass('active-list-elem');
                    $(this).addClass('active-list-elem');

                    if (isActive) {
                        $('.list-element-2').removeClass('active-list-elem');
                    }
                    else {
                        let id = $(this).attr('id');
                        var schemaId = null;
                        if(id.includes('comp')) {
                            let arrayId = id.split('-');
                            let compId = arrayId[arrayId.length - 1];
                            if(arrayId.length > 3) {
                                schemaId = arrayId[arrayId.length - 3];
                            }

                            let comp = $('#comp-'+compId);
                            cloneSelectedElements(comp, compId, $(this), schemaId);
                        }
                        else {
                            let arrayId = id.split('-');
                            if(arrayId.length > 1) {
                                schemaId = arrayId[arrayId.length - 1];
                            }
                            cloneSelectedElements(null, null, $(this), schemaId);
                        }
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

            $('#selected-prod-schema-container').on('click', function () {
                // console.log($(this).find('.open-modal'));
            });

            $('.expand-btn').on('click', function () {
                expandButtonOnClick($(this),'#list-');
            });
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
                                <div class="rounded-lg w-1/2 border h-[32px]  relative bg-white">
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
                        <div class="col-span-4 xl:col-span-8 w-full bg-gray-300 py-6 flex flex-row justify-end">
                            <button type="button" id="cycle-details-{{$p_cycle->cycle_id}}" class="cycle-details mr-4 text-gray-800 bg-white uppercase hover:bg-gray-100 focus:outline-none font-medium rounded-md text-xs lg:text-sm px-2 py-1 shadow-md">
                                Statystyki
                            </button>
                        </div>
                        @if($p_cycle->category == 1)
                            <div class="col-span-4 xl:col-span-8 flex justify-start items-center flex-col bg-gray-200/50">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Wybrany materiał</dt>
                                <div id="selected-comp-container" class="w-4/5 h-full min-h-[80px] flex justify-center items-center">
                                    <input id="selected-comp-id" type="number" name="selected_component_id" class="hidden" value="">
                                </div>
                            </div>
                        @endif
                        @if($p_cycle->category != 3)
                            <div class="col-span-4 xl:col-span-8 flex justify-start items-center flex-col bg-gray-200/50">
                                <dt class="order-first text-xs lg:text-sm font-semibold leading-6 bg-gray-800 text-white w-full pl-5 py-2">Wybrane zadanie</dt>
                                <div id="selected-prod-schema-container" class="w-[95%] h-full min-h-[80px] flex justify-center items-center">
                                    <input id="selected-prod-schema-id" type="number" name="selected_prod_schema_id" class="hidden" value="">
                                </div>
                            </div>
                        @endif
                        <div class="col-span-4 xl:col-span-8 w-full text-center">
                            <a  id="add-work-button"
                                class="inline-block px-6 py-2 md:py-4 text-xs font-medium uppercase w-full text-md md:text-lg xl:text-xl leading-normal text-white focus:outline-none shadow-[0_4px_9px_-4px_rgba(0,0,0,0.2)] transition duration-150 ease-in-out hover:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] focus:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] active:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)]"
                                data-te-ripple-init
                                data-te-ripple-color="light"
                                href="">
                                {{ __('Raportuj pracę') }}
                            </a>
                        </div>
                    </dl>
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
                                                <x-list-element id="{{'comp-'.$comp->component_id}}" class="my-6 list-element flex-col w-full lg:py-0 py-0">
                                                    <div class="w-full flex flex-row justify-center">
                                                        <div class="w-[85%] flex flex-col justify-between items-center">
                                                            <div class="w-full flex justify-left items-center">
                                                                <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                                    {{$comp->name}}
                                                                </p>
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
                                                    <div id="list-{{$comp->child_id}}" class="dropdown hidden mt-6 mb-4 w-full w-[95%] mr-1">
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
                                    <div class="{{$p_cycle->category == 1? 'w-[90%]' : 'w-[70%]'}} p-2 ">
                                        @if(is_array($child_prod_schemas))
                                            @foreach($child_prod_schemas as $comp_id => $prod_schemas)
                                                @php $current_id = 0; @endphp
                                                @foreach($prod_schemas as $prod_schema)
                                                    @if($prod_schema->child_id != $current_id)
                                                        <x-list-element id="schema-{{$prod_schema->child_id}}-comp-{{$comp_id}}" class="{{'comp-'.$comp_id}} my-6 list-element-2 flex-col w-full lg:py-0 py-0 hidden">
                                                            <div class="flex flex-row justify-between w-full">
                                                                <input type="number" class="schema-list-element-id hidden" value="{{$prod_schema->child_id}}">
                                                                <div class="w-[85%] flex flex-col justify-between items-center">
                                                                    <div class="w-full flex justify-left items-center">
                                                                        <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                                            {{$prod_schema->name}}
                                                                        </p>
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
                                                                        Raportuj
                                                                    </div>
                                                                </div>
                                                                <div class="shadow-md rounded-b-xl mb-4">
                                                                    <div class="relative overflow-x-auto">
                                                                        <table class="w-full text-sm text-left rtl:text-right pb-2 bg-gray-100 text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border-slate-300">
                                                                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                                                            <tr>
                                                                                <th scope="col" class="px-2 py-3">
                                                                                    Kol.
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3">
                                                                                    Podzadanie
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden ">
                                                                                    Ilość (szt)
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden ">
                                                                                    Start pracy
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden ">
                                                                                    Koniec pracy
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden ">
                                                                                    Czas pracy (h)
                                                                                </th>
                                                                                <th scope="col" class="px-2 py-3 hidden-input hidden ">
                                                                                    Pracownik
                                                                                </th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            @php $current_schema_id = $prod_schema->prod_schema_id; @endphp
                                                                            @foreach($prod_schemas as $schema_task)
                                                                                @if($current_schema_id == $schema_task->prod_schema_id)
                                                                                    <tr id="row-{{$current_schema_id.'-'.$schema_task->task_id}}" class="font-medium text-gray-600 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-slate-300 ">
                                                                                        <td class="col-value sequence-no px-2 py-3 whitespace-nowrap text-center rounded-md">
                                                                                            {{$schema_task->task_sequence_no}}
                                                                                        </td>
                                                                                        <td class="col-value name px-2 py-3 whitespace-nowrap rounded-md">
                                                                                            {{$schema_task->task_name}}
                                                                                        </td>
                                                                                        <td class="hidden-input amount px-2 text-center hidden max-w-[50px]">
                                                                                            <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                                                <div class="w-full p-1 flex justify-center items-center h-full">
                                                                                                    @if($schema_task->task_amount_required)
                                                                                                        <input id="amount-{{$current_schema_id.'-'.$schema_task->task_id}}" type="number" min="0" value="{{old('amount')}}"
                                                                                                               class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
                                                                                                               name="amount_{{$current_schema_id.'_'.$schema_task->task_id}}" placeholder="0">
                                                                                                    @else
                                                                                                        <div class="block w-full bg-white py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                                                            -
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="hidden-input start-time px-2 py-3 text-center hidden">
                                                                                            <input type="datetime-local" id="meetingDateTime" name="meetingDateTime"
                                                                                                   min="2023-01-01T00:00" max="2023-12-31T23:59" step="60"
                                                                                                   class="block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                        </td>
                                                                                        <td class="hidden-input end-time px-2 py-3 text-center hidden">
                                                                                            <input type="datetime-local" id="meetingDateTime" name="meetingDateTime"
                                                                                                   min="2023-01-01T00:00" max="2023-12-31T23:59" step="60"
                                                                                                   class="block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                        </td>
                                                                                        <td class="hidden-input work-time-in-hours px-2 py-3 text-center hidden">
                                                                                            <div class="block w-full bg-white py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                0:00
                                                                                            </div>
                                                                                        </td>
                                                                                        <td class="hidden-input work-time-in-hours px-2 py-3 text-center hidden">
                                                                                            <div class="block w-full bg-white py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                                {{$user->employeeNo}}
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
                                                    <x-list-element id="schema-{{$prod_schema->child_id}}" class="comp my-6 list-element-2 flex-col w-full lg:py-0 py-0">
                                                        <div class="flex flex-row justify-between w-full">
                                                            <input type="number" class="schema-list-element-id hidden" value="{{$prod_schema->child_id}}">
                                                            <div class="w-[85%] flex flex-col justify-between items-center">
                                                                <div class="w-full flex justify-left items-center">
                                                                    <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                                        {{$prod_schema->name}}
                                                                    </p>
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
                                                                    Raportuj
                                                                </div>
                                                            </div>
                                                            <div class="shadow-md rounded-b-xl mb-4">
                                                                <div class="relative overflow-x-auto">
                                                                    <table class="w-full text-sm text-left rtl:text-right pb-2 bg-gray-100 text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border-slate-300">
                                                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                                                        <tr>
                                                                            <th scope="col" class="px-2 py-3">
                                                                                Kol.
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3">
                                                                                Podzadanie
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden ">
                                                                                Ilość (szt)
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden ">
                                                                                Start pracy
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden ">
                                                                                Koniec pracy
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden ">
                                                                                Czas pracy (h)
                                                                            </th>
                                                                            <th scope="col" class="px-2 py-3 hidden-input hidden ">
                                                                                Pracownik
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @php $current_schema_id = $prod_schema->prod_schema_id; @endphp
                                                                        @foreach($child_prod_schemas as $schema_task)
                                                                            @if($current_schema_id == $schema_task->prod_schema_id)
                                                                                <tr id="row-{{$current_schema_id.'-'.$schema_task->task_id}}" class="font-medium text-gray-600 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 border border-slate-300 ">
                                                                                    <td class="col-value sequence-no px-2 py-3 whitespace-nowrap text-center rounded-md">
                                                                                        {{$schema_task->task_sequence_no}}
                                                                                    </td>
                                                                                    <td class="col-value name px-2 py-3 whitespace-nowrap rounded-md">
                                                                                        {{$schema_task->task_name}}
                                                                                    </td>
                                                                                    <td class="hidden-input amount px-2 text-center hidden max-w-[50px]">
                                                                                        <div class="p-1 flex justify-center flex-row items-center h-full">
                                                                                            <div class="w-full p-1 flex justify-center items-center h-full">
                                                                                                @if($schema_task->task_amount_required)
                                                                                                    <input id="amount-{{$current_schema_id.'-'.$schema_task->task_id}}" type="number" min="0" value="{{old('amount')}}"
                                                                                                           class="xl:p-2.5 block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center"
                                                                                                           name="amount_{{$current_schema_id.'_'.$schema_task->task_id}}" placeholder="0">
                                                                                                @else
                                                                                                    <div class="block w-full bg-white py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded text-center">
                                                                                                        -
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="hidden-input start-time px-2 py-3 text-center hidden">
                                                                                        <input type="datetime-local" id="meetingDateTime" name="meetingDateTime"
                                                                                               min="2023-01-01T00:00" max="2023-12-31T23:59" step="60"
                                                                                               class="block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                    </td>
                                                                                    <td class="hidden-input end-time px-2 py-3 text-center hidden">
                                                                                        <input type="datetime-local" id="meetingDateTime" name="meetingDateTime"
                                                                                               min="2023-01-01T00:00" max="2023-12-31T23:59" step="60"
                                                                                               class="block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                    </td>
                                                                                    <td class="hidden-input work-time-in-hours px-2 py-3 text-center hidden">
                                                                                        <div class="block w-full bg-white py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                            0:00
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="hidden-input work-time-in-hours px-2 py-3 text-center hidden">
                                                                                        <div class="block w-full bg-white py-2 border text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded">
                                                                                            {{$user->employeeNo}}
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
{{--            <div class="w-full flex justify-center items-center mb-4">--}}
{{--                <div class="w-[95%]">--}}
{{--                    <div class="w-full text-lg lg:text-xl font-semibold bg-gray-800 text-white rounded-t-xl pl-5 py-2 flex flex-row justify-between">--}}
{{--                        <div class="p-3">--}}
{{--                            {{($p_cycle->category == 1)? 'Materiały i zadania' : 'Zadania'}}--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="shadow-md rounded-b-xl mb-4">--}}
{{--                        <div class="relative overflow-x-auto">--}}
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
                                @if(isset($child_schemas_modal))
                                    @php $current_id = 0; @endphp
                                    @foreach($child_schemas_modal as $c_cycle)
                                        @if($c_cycle->child_id != $current_id)
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
                                        @endif
                                        @php $current_id = $c_cycle->child_id; @endphp
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
