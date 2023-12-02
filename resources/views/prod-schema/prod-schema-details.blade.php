<x-app-layout>
    @php
        $viewName = 'Szczegóły zadania';
    @endphp
    <x-information-panel :viewName="$viewName"></x-information-panel>
    @if(isset($prod_schema_tasks) and isset($prod_schema))
        <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6 flex justify-center">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col w-full">
                <div class="w-full lg:w-[90%] mt-4 md:mt-8 bg-white border border-gray-200 rounded-md shadow dark:bg-gray-800 dark:border-gray-700">
                    <ul class="flex text-sm md:text-lg lg:text-xl font-medium text-center text-gray-500 divide-x divide-gray-200 rounded-lg  dark:divide-gray-600 dark:text-gray-400" id="fullWidthTab" data-tabs-toggle="#fullWidthTabContent" role="tablist">
                        <li class="w-full">
                            <button id="production-tab" data-tabs-target="#production" type="button" role="tab" aria-controls="production" aria-selected="false" class="aria-selected:text-blue-450 inline-block w-full p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">
                                Zadanie
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
                                    <div class="my-4">
                                        <label for="countable" class="block text-sm lg:text-md xl:text-lg font-medium text-gray-900 dark:text-white">Niemierzalne</label>
                                        <input
                                            class="countable ml-2 mr-2 mt-[0.3rem] h-3.5 w-8 appearance-none rounded-[0.4375rem] bg-neutral-300 before:pointer-events-none before:absolute before:h-3.5 before:w-3.5 before:rounded-full before:bg-transparent before:content-[''] after:absolute after:z-[2] after:-mt-[0.1875rem] after:h-5 after:w-5 after:rounded-full after:border-none after:bg-neutral-100 after:shadow-[0_0px_3px_0_rgb(0_0_0_/_7%),_0_2px_2px_0_rgb(0_0_0_/_4%)] after:transition-[background-color_0.2s,transform_0.2s] after:content-[''] checked:bg-primary checked:after:absolute checked:after:z-[2] checked:after:-mt-[3px] checked:after:ml-[1.0625rem] checked:after:h-5 checked:after:w-5 checked:after:rounded-full checked:after:border-none checked:after:bg-primary checked:after:shadow-[0_3px_1px_-2px_rgba(0,0,0,0.2),_0_2px_2px_0_rgba(0,0,0,0.14),_0_1px_5px_0_rgba(0,0,0,0.12)] checked:after:transition-[background-color_0.2s,transform_0.2s] checked:after:content-[''] hover:cursor-pointer focus:outline-none focus:ring-0 focus:before:scale-100 focus:before:opacity-[0.12] focus:before:shadow-[3px_-1px_0px_13px_rgba(0,0,0,0.6)] focus:before:transition-[box-shadow_0.2s,transform_0.2s] focus:after:absolute focus:after:z-[1] focus:after:block focus:after:h-5 focus:after:w-5 focus:after:rounded-full focus:after:content-[''] checked:focus:border-primary checked:focus:bg-primary checked:focus:before:ml-[1.0625rem] checked:focus:before:scale-100 checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca] checked:focus:before:transition-[box-shadow_0.2s,transform_0.2s] dark:bg-neutral-600 dark:after:bg-neutral-400 dark:checked:bg-primary dark:checked:after:bg-primary dark:focus:before:shadow-[3px_-1px_0px_13px_rgba(255,255,255,0.4)] dark:checked:focus:before:shadow-[3px_-1px_0px_13px_#3b71ca]"
                                            type="checkbox" role="switch" id="countable" name="countable"
                                            {{ $prod_schema_tasks[0]->non_countable ? 'checked' : '' }} disabled/>
                                    </div>
                                    <h2 class="text-gray-800 font-medium text-md xl:text-lg my-2">Lista podzadań:</h2>
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
                        <div class="p-4 bg-white rounded-lg lg:p-8 dark:bg-gray-800 flex flex-col justify-center items-center" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                            @if(isset($instruction) and $instruction instanceof \App\Models\Instruction)
                                    @if(!is_null($instruction->id))
                                        <div class="w-full flex flex-col justify-center items-center mb-4 mt-4">
                                            <p class="list-element-name w-full lg:w-[80%] mb-8 text-md xl:text-lg font-medium pl-2 lg:pl-4 lg:pb-2 text-gray-950 border-l-4 border-blue-450">
                                                {{$instruction->name}}
                                            </p>
                                            @php $path = isset($storage_path_instructions) ? $storage_path_instructions.'/' : ''; @endphp
                                            @if(!is_null($instruction->video))
                                                <video class="w-full lg:w-[90%]" width="320" height="240" controls>
                                                    <source src="{{asset('storage/'.$path.$instruction->video)}}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @else
                                                <p class="w-full text-center text-red-700 text-sm xl:text-md">Brak filmu instruktażowego.</p>
                                            @endif
                                        </div>
                                        @if(!is_null($instruction->instruction_pdf))
                                            <div class="w-full flex flex-col justify-center items-center mt-8">
                                                <embed class="w-full lg:w-[80%] h-[400px] lg:h-[600px] xl:h-[800px]" src="{{asset('storage/'.$path.$instruction->instruction_pdf)}}" width="800px" height="800px"/>
                                            </div>
                                        @else
                                            <p class="w-full text-center text-red-700 text-sm xl:text-md">Brak instrukcji tekstowej.</p>
                                        @endif
                                    @endif
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
