<x-app-layout>
    @php
        $viewName = 'Szczegóły komponentu';
    @endphp
    <x-information-panel :viewName="$viewName">
    </x-information-panel>
    @if(isset($comp) and isset($prod_standards) and isset($data) and isset($instruction))
        <div class="w-full md:w-[90%] md:ml-[5%] mt-4 md:mt-8 bg-white border border-gray-200 rounded-md shadow dark:bg-gray-800 dark:border-gray-700">
            <ul class="flex text-sm md:text-lg lg:text-xl font-medium text-center text-gray-500 divide-x divide-gray-200 rounded-lg  dark:divide-gray-600 dark:text-gray-400" id="fullWidthTab" data-tabs-toggle="#fullWidthTabContent" role="tablist">
                <li class="w-full">
                    <button id="info-tab" data-tabs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true" class="aria-selected:text-blue-450 inline-block w-full p-4 rounded-tl-lg bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">
                        Informacje
                    </button>
                </li>
                <li class="w-full">
                    <button id="production-tab" data-tabs-target="#production" type="button" role="tab" aria-controls="production" aria-selected="false" class="aria-selected:text-blue-450 inline-block w-full p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">
                        Etapy produkcji
                    </button>
                </li>
                <li class="w-full">
                    <button id="manual-tab" data-tabs-target="#manual" type="button" role="tab" aria-controls="manual" aria-selected="false" class="aria-selected:text-blue-450 inline-block w-full p-4 bg-gray-50 hover:bg-gray-100 focus:outline-none dark:bg-gray-700 dark:hover:bg-gray-600">
                        Instrukcja
                    </button>
                </li>
            </ul>
            <div id="fullWidthTabContent" class="border-t border-gray-200 dark:border-gray-600">
                <div class="hidden p-4 bg-white rounded-lg md:p-8 dark:bg-gray-800" id="info" role="tabpanel" aria-labelledby="info-tab">
                    <dl class="grid grid-cols-1 gap-8 xl:gap-2 p-4 mx-auto text-gray-900 xl:grid-cols-2 dark:text-white sm:p-8">
                        <div class="flex flex-col items-center justify-center xl:w-[95%]">
                            @if(!empty($comp->image))
                                <div class="max-w-[350px] lg:max-w-[450px]">
                                    @php $path = isset($storage_path) ? $storage_path.'/' : ''; @endphp
                                    <img src="{{asset('storage/'.$path.$comp->image)}}">
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-center justify-center md:w-[100%]">
                            <div class="comp-list-{{$comp->id}} w-full">
                                <div class="relative overflow-x-auto shadow-md">
                                    <table class="w-full text-sm md:text-lg text-left text-gray-500 dark:text-gray-400">
                                        <thead class="text-sm md:text-lg text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                {{$comp->name}}
                                            </th>
                                            <th scope="col" class="px-6 py-3"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                Materiał
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
                                                    $name .= ' [cm]';
                                                @endphp
                                                {{$name}}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{$dim}}
                                            </td>
                                        </tr>
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                Produkowane niezależnie
                                            </th>
                                            <td class="px-6 py-4">
                                                @if($comp->independent == 1)
                                                    tak
                                                @else
                                                    nie
                                                @endif
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
                                    @if(count($prod_standards) > 0)
                                        <table class="w-full text-sm md:text-lg text-left text-gray-500 dark:text-gray-400">
                                            <thead class="text-sm md:text-lg text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">
                                                    Normy Produkcji
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Czas [h]
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Ilość
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Jednostka
                                                </th>
                                            </tr>
                                            </thead>
                                            @foreach($prod_standards as $std)
                                                <tbody>
                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{$std->name}}
                                                    </th>
                                                    <td class="px-6 py-4">
                                                        {{$std->duration_hours}}
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        {{$std->amount}}
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        {{$std->unit}}
                                                    </td>
                                                </tr>
                                                </tbody>
                                            @endforeach
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </dl>
                </div>
                <div class="hidden p-4 bg-white rounded-lg lg:p-8 dark:bg-gray-800" id="production" role="tabpanel" aria-labelledby="production-tab">
                    @if(count($data) > 0)
                        <ol class="relative text-gray-700 dark:border-gray-700 dark:text-gray-400">
                        @php
                            $curr_schema_id = 0;
                            $i = 0;
                        @endphp
                        <div class="relative text-gray-600 dark:border-gray-700 dark:text-gray-400">
                            @foreach($data as $row)
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
                                        <div class="w-full xl:w-[48%] relative overflow-x-auto shadow-md flex items-center justify-center">
                                            <table class="w-full text-md xl:text-lg xl:h-[90%] text-left text-gray-700 dark:text-gray-400">
                                                <thead class="text-gray-950 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-medium">
                                                <tr>
                                                    <td class="px-6">
                                                        Norma Produkcji
                                                    </td>
                                                    <td class="px-6">
                                                        Czas [h]
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
                                    </div>
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
                    @if($instruction instanceof \App\Models\Instruction)
                        <div class="w-full flex flex-col justify-center items-center mb-12 mt-4">
                            <p class="w-full lg:w-[80%] mb-8 text-md md:text-xl font-medium pl-2 lg:pl-4 lg:pb-2 text-gray-950 border-l-4 border-blue-450">
                                {{$instruction->name}}
                            </p>
                            @if(!is_null($instruction->video))
                                <video class="w-full lg:w-[80%]" width="320" height="240" controls>
                                    <source src="{{asset('storage/lights_go.mp4')}}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                        @if(!is_null($instruction->instruction_pdf))
                            <div class="w-full flex flex-col justify-center items-center">
                                <embed class="w-full lg:w-[80%] h-[400px] lg:h-[600px] xl:h-[800px]" src="{{asset('storage/DATA_MODEL_prototyp.pdf')}}" width="800px" height="800px"/>
                            </div>
                        @endif
                    @else
                        <p class="w-full text-center text-red-700 text-lg mt-6">Brak instrukcji.</p>
                    @endif
                </div>
            </div>
        </div>
    @elseif(isset($error_msg))
        <p class="w-full text-center text-red-700 text-lg mt-6">{{$error_msg}}</p>
    @endif
</x-app-layout>
