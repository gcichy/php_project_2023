@props(['button_id','id', 'route','remove_elem_id','remove_elem_class','name',])
@if(isset($button_id) and isset($id) and isset($route) and isset($remove_elem_id) and isset($remove_elem_class) and isset($name))
    <script type="module">
        $(document).ready(function() {

            // Open modal
            $("#{{$button_id}}").on('click', function () {
                $('.{{$remove_elem_class}}').addClass('hidden');
                $("#modal-background-{{$id}}, #modal-{{$id}}").removeClass("hidden");
                let activeElem = $('.list-element.active-list-elem');
                if(typeof activeElem.attr('id') === "string") {
                    let id = activeElem.attr('id').split('-');

                    if(id.length > 1) {
                        $('#remove-id-{{$id}}').val(id[1]);
                        $('#{{$remove_elem_id}}' + id[1]).removeClass('hidden')
                    }

                }
            });

            // Close modal
            $("#close-modal-button-{{$id}}").on('click', function () {
                $("#modal-background-{{$id}}, #modal-{{$id}}").addClass("hidden");
            });
        });
    </script>

    <button type="button" id="{{$button_id}}" disabled class="btn btn-primary on-select remove inline-flex items-center ml-1 lg:ml-3 lg:mr-5 px-2 py-1 lg:px-4 lg:py-2 bg-red-600 hover:bg-red-800 border border-transparent rounded-md font-semibold text-sm md:text-md xl:text-lg text-white uppercase tracking-widest focus:bg-gray-700  focus:ring-4 focus:outline-none focus:ring-gray-300  focus:ring-offset-2 transition ease-in-out duration-150">
        {{__('Usuń')}}
    </button>
    <!-- Modal Background -->
    <div id="modal-background-{{$id}}" class="z-[100] fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden"></div>

    <!-- Modal Container -->
    <div id="modal-{{$id}}" class="z-[100] fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[90%] md:w-2/3 xl:w-1/2 bg-white p-8 rounded shadow-md hidden">
        <!-- Modal Header -->
        <div class="mb-6">
            <h2 class="text-xl lg:text-2xl font-medium">{{__('Usuń ').$name}}</h2>
        </div>
        <div><p>{{$slot}}</p></div>
        <form action="{{ route($route) }}" method="POST">
            @method('DELETE')
            @csrf
            <div class="flex items-center flex-row justify-center w-full">
                <div class="flex items-start flex-col w-full text-gray-700">
                    <p class="w-full text-md lg:text-xl font-medium pl-2 lg:pl-4 lg:pb-2 text-gray-950">
                        {{__('Czy na pewno chcesz usunąć ').$name.__('?')}}
                    </p>
                    <p class="w-full text-sm xl:text-md pl-2 lg:pl-4 pb-2">
                        {{__('Wpisz "usuń" aby kontynuować .')}}
                    </p>
                    <input type="text" id="confirmation" name="confirmation" value="" class="ml-2 lg:ml-4 shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                    <x-input-error :messages="$errors->get('confirmation')" class="mt-2 pl-2 lg:pl-4" />
                </div>
            </div>
            <div class="my-6 w-[100%] flex flex-row justify-center items-center">
                <x-nav-button id="close-modal-button-{{$id}}" class="mr-[5%]">
                    {{__('Anuluj')}}
                </x-nav-button>
                <x-submit-button id="submit-remove-{{$id}}" type="submit" class="ml-[5%] bg-red-600 hover:bg-red-800">
                    {{__('Usuń')}}
                </x-submit-button>
            </div>
            <input type="text" id="remove-id-{{$id}}" name="remove_id" value="" class="hidden">
        </form>
    </div>
@endif

