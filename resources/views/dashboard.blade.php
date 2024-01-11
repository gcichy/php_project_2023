<x-app-layout>
    <div class="py-4 flex justify-center items-center">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                {{ __("Witaj w DipMar-produkcja!") }}
            </div>
        </div>
    </div>
    @if(isset($works))
        @php
            $name = "Twoja praca";
        @endphp
        <x-information-panel :viewName="$name">
        </x-information-panel>
        <div class="w-full flex justify-center items-center my-4">
            <div class="w-[95%]">
                <div class="shadow-md rounded-xl mb-4 border">
                    <div class="relative overflow-x-auto">
                        <table class="block max-h-[400px] overflow-y-scroll w-full text-sm bg-gray-100 rounded-xl text-left rtl:text-right pb-2 text-gray-500 dark:text-gray-400 border-separate border-spacing-1 border-slate-300 ">
                            @php
                                $storage_path_components = isset($storage_path_components)? $storage_path_components : null;
                                $storage_path_products = isset($storage_path_products)? $storage_path_products : null;
                            @endphp
                            <x-work-table :work_array="$works->all()"
                                          :storage_path_components="$storage_path_components"
                                          :storage_path_products="$storage_path_products">
                            </x-work-table>
                        </table>
                    </div>
                    <div class="w-full p-2 bg-gray-50 rounded-b-xl">
                        {{ $works->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>

