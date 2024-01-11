@props(['button_id','id', 'route','remove_elem_id','remove_elem_class','name','disabled','button_classes'])
@if(isset($button_id) and isset($id) and isset($route) and isset($remove_elem_id) and isset($remove_elem_class) and isset($name) and isset($disabled))
    <script type="module">
        $(document).ready(function() {

            // Open modal
            $("#{{$button_id}}").on('click', function () {
                console.log('halo');
                if(!$(this).hasClass('bg-gray-400')) {
                    $('.{{$remove_elem_class}}').addClass('hidden');
                    $("#modal-background-{{$id}}, #modal-{{$id}}").removeClass("hidden");
                    let activeElem = $('.list-element.active-list-elem');
                    if(typeof activeElem.attr('id') === "string") {
                        let id = activeElem.attr('id').split('-');
                        console.log($('#remove-id-{{$id}}'));
                        if(id.length > 1) {
                            $('#remove-id-{{$id}}').val(id[1]);
                            $('#{{$remove_elem_id}}' + id[1]).removeClass('hidden')
                        }
                    }
                }
            });

            // Close modal
            $("#close-modal-button-{{$id}}").on('click', function () {
                $("#modal-background-{{$id}}, #modal-{{$id}}").addClass("hidden");
            });
        });
    </script>

    <button type="button" id="{{$button_id}}" {{$disabled}}
        class="btn btn-primary on-select remove inline-flex items-center bg-red-600 hover:bg-red-800 shadow-md
               {{isset($button_classes)? $button_classes : 'rounded-md ml-1 lg:ml-3 lg:mr-5 my-1 px-2 py-1 lg:px-4 lg:py-2 text-xs md:text-sm xl:text-lg' }}
               border border-transparent font-semibold text-white uppercase tracking-widest [:where(&)]:focus:bg-red-800 focus:outline-none transition ease-in-out duration-150">
        {{__('Usuń')}}
    </button>
    <!-- Modal Background -->
    <div id="modal-background-{{$id}}" class="z-[100] fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden"></div>

    <!-- Modal Container -->
    <div id="modal-{{$id}}" class="z-[100] fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[90%] md:w-2/3 xl:w-1/2 bg-white rounded-lg shadow-md hidden">
        <!-- Modal Header -->
        <div class="mb-6 w-full bg-gray-800 rounded-t-lg text-white p-4 flex flex-row justify-between items-center">
            <h2 class="text-xl lg:text-2xl ml-2 font-medium">{{__('Usuń ').$name}}</h2>
            <x-nav-button id="close-modal-button-{{$id}}" class="">
                <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M19.207 6.207a1 1 0 0 0-1.414-1.414L12 10.586 6.207 4.793a1 1 0 0 0-1.414 1.414L10.586 12l-5.793 5.793a1 1 0 1 0 1.414 1.414L12 13.414l5.793 5.793a1 1 0 0 0 1.414-1.414L13.414 12l5.793-5.793z" fill="#ffffff"/></svg>
            </x-nav-button>
        </div>
        <div><p>{{$slot}}</p></div>
        <form action="{{ $route }}" method="POST">
            @method('DELETE')
            @csrf
            <div class="flex items-center flex-row justify-center ml-4 w-full">
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
            <div class="mt-4 w-[100%] flex flex-row justify-center items-center bg-gray-200 rounded-b-lg">
{{--                <x-submit-button id="submit-remove-{{$id}}" type="submit" class="ml-[5%] bg-red-600 hover:bg-red-800 text-sm">--}}
{{--                    {{__('Usuń')}}--}}
{{--                </x-submit-button>--}}
                <button
                    class="inline-block rounded-b-lg px-6 py-2 md:py-4 text-xs font-medium uppercase w-full text-md md:text-lg xl:text-xl bg-red-600 hover:bg-red-800 leading-normal text-white focus:ring-4 focus:outline-none focus:ring-blue-300 shadow-[0_4px_9px_-4px_rgba(0,0,0,0.2)] transition duration-150 ease-in-out hover:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] focus:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)] active:shadow-[0_8px_9px_-4px_rgba(0,0,0,0.1),0_4px_18px_0_rgba(0,0,0,0.2)]"
                    type="submit"
                    id="submit-remove-{{$id}}"
                    data-te-ripple-init
                    data-te-ripple-color="light">
                    {{__('Usuń')}}
                </button>
            </div>
            <input type="text" id="remove-id-{{$id}}" name="remove_id" value="" class="hidden">
        </form>
    </div>
@endif

