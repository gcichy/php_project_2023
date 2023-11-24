<x-app-layout>
    @php
        $viewName = 'Szczegóły schematu';
    @endphp
    <x-information-panel :viewName="$viewName"></x-information-panel>
    @if(isset($prod_schema_tasks) and isset($instruction) and isset($prod_schema))
        <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6 flex justify-center">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col w-full">
                <div class="w-full lg:w-[90%] mt-4 md:mt-8 bg-white border border-gray-200 rounded-md shadow dark:bg-gray-800 dark:border-gray-700">
                    <ul class="flex text-sm md:text-lg lg:text-xl font-medium text-center text-gray-500 divide-x divide-gray-200 rounded-lg  dark:divide-gray-600 dark:text-gray-400" id="fullWidthTab" data-tabs-toggle="#fullWidthTabContent" role="tablist">
                        <li class="w-full">
                            <button id="production-tab" data-tabs-target="#production" type="button" role="tab" aria-controls="production" aria-selected="false" class="aria-selected:text-blue-450 inline-block w-full p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">
                                Schemat Produkcji
                            </button>
                        </li>
                        <li class="w-full">
                            <button id="manual-tab" data-tabs-target="#manual" type="button" role="tab" aria-controls="manual" aria-selected="false" class="aria-selected:text-blue-450 inline-block w-full p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">
                                Instrukcje
                            </button>
                        </li>
                    </ul>
                    <div id="fullWidthTabContent" class="border-t border-gray-200 dark:border-gray-600">
                        <div class="hidden p-4 bg-white rounded-lg lg:p-8 dark:bg-gray-800" id="production" role="tabpanel" aria-labelledby="production-tab">
                            @if(count($prod_schema_tasks) > 0)
                                <ol class="relative text-gray-700 dark:border-gray-700 dark:text-gray-400">
                                    @php
                                        $curr_schema_id = 0;
                                        $i = 0;
                                    @endphp
                                    <div class="relative text-gray-600 dark:border-gray-700 dark:text-gray-400">
                                        @foreach($prod_schema_tasks as $row)
                                            @if($row->prod_schema_id != $curr_schema_id)
                                                @php $curr_schema_id = $row->prod_schema_id; @endphp
                                                @if($i > 0)
                                    </div>
                                    @endif
                                    <div class="flex flex-col xl:flex-row align-middle justify-between  mb-5 w-full @if($i > 0) mt-8 xl:mt-12 @endif">
                                        <div class="flex items-center flex-col w-full justify-center xl:w-[48%] mb-3 xl:mb-0 text-gray-700">
                                            <p class="w-full text-md lg:text-xl font-medium pl-2 lg:pl-4 lg:pb-2 text-gray-950 border-l-4 border-blue-450">
                                                {{$row->prod_schema}}
                                            </p>
                                            <p class="w-full text-sm xl:text-md pl-2 lg:pl-4 pb-2 border-l-4 border-blue-450">
                                                {{$row->prod_schema_desc}}
                                            </p>
                                        </div>
                                        @if(!empty($row->prod_std_id))
                                            <div class="w-full xl:w-[48%] relative overflow-x-auto shadow-md flex items-center justify-center">
                                                <table class="w-full text-md xl:text-lg xl:h-[90%] text-left text-gray-700 dark:text-gray-400">
                                                    <thead class="text-gray-950 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-medium">
                                                    <tr>
                                                        <td class="px-6">
                                                            Norma Produkcji
                                                        </td>
                                                        <td class="px-6">
                                                            Czas[h]
                                                        </td>
                                                        <td class="px-6">
                                                            Ilość
                                                        </td>
                                                        <td class="px-6">
                                                            Jednostka
                                                        </td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr class="text-sm xl:text-md bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                        <td class="px-6">
                                                            {{$row->prod_std_name}}
                                                        </td>
                                                        <td class="px-6">
                                                            {{$row->prod_std_duration}}
                                                        </td>
                                                        <td class="px-6">
                                                            {{$row->prod_std_amount}}
                                                        </td>
                                                        <td class="px-6">
                                                            {{$row->prod_std_unit}}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                    <h2 class="text-gray-800 font-medium text-md xl:text-lg my-2">Lista zadań:</h2>
                                    <div class="flex flex-col items-left justify-center text-gray-700 border-blue-450 border-2 mb-3 md:w-[100%]">
                                        @endif
                                        <li class="my-5 ml-6">
                                            <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                                                {{$i+1}}
                                            </span>
                                            <h3 class="font-medium leading-tight">{{$row->task_name}}</h3>
                                            <p class="text-sm">{{$row->task_desc}}</p>
                                        </li>
                                        @php $i++; @endphp
                                        @endforeach
                                    </div>
                                </ol>
                            @else
                                <p class="w-full text-center text-red-700 text-lg mt-6">Brak danych.</p>
                            @endif
                        </div>
                        <div class="hidden p-4 bg-white rounded-lg lg:p-8 dark:bg-gray-800 flex flex-col justify-center items-center" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                            @if(!empty($instruction))
                                @php
                                    $inputPlaceholder = "Wpisz nazwę zadania...";
                                    $xListElem = "prodschema";
                                @endphp
                                <div id="search-schema" class="prodschema-toggle w-full lg:w-[60%] mb-3">
                                    <x-search-input class="w-full" :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElem"></x-search-input>
                                </div>
                                @foreach($instruction as $instr)
                                    @if(!is_null($instr->instr_id))
                                        <div class="list-element-{{$xListElem}} w-full">
                                            <div class="w-full flex flex-col justify-center items-center mb-4 mt-4">
                                                <p class="list-element-name w-full lg:w-[80%] mb-8 text-md xl:text-lg font-medium pl-2 lg:pl-4 lg:pb-2 text-gray-950 border-l-4 border-blue-450">
                                                    {{$instr->sequence_no}}. {{$instr->instruction_name}}
                                                </p>
                                                @php $path = isset($storage_path_instructions) ? $storage_path_instructions.'/' : ''; @endphp
                                                @if(!is_null($instr->video))
                                                    <video class="w-full lg:w-[90%]" width="320" height="240" controls>
                                                        <source src="{{asset('storage/'.$path.$instr->video)}}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @else
                                                    <p class="w-full text-center text-red-700 text-sm xl:text-md">Brak filmu instruktażowego.</p>
                                                @endif
                                            </div>
                                            @if(!is_null($instr->instruction_pdf))
                                                <div class="w-full flex flex-col justify-center items-center mt-8">
                                                    <embed class="w-full lg:w-[80%] h-[400px] lg:h-[600px] xl:h-[800px]" src="{{asset('storage/'.$path.$instruction->instruction_pdf)}}" width="800px" height="800px"/>
                                                </div>
                                            @else
                                                <p class="w-full text-center text-red-700 text-sm xl:text-md">Brak instrukcji tekstowej.</p>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <p class="w-full text-center text-red-700 text-sm xl:text-md">Brak instrukcji.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(isset($error_msg))
        <p class="w-full text-center text-red-700 text-lg mt-6">{{$error_msg}}</p>
    @endif
</x-app-layout>
