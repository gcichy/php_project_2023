@props(['button_id','id', 'name','bg_classes', 'button_text'])
@if(isset($button_id) and isset($id) and isset($name) and isset($bg_classes) and isset($button_text))
    <script type="module">
        $(document).ready(function() {

            // Open modal
            $("#{{$button_id}}").on('click', function () {
                $("#modal-background-{{$id}}, #modal-{{$id}}").removeClass("hidden");
            });

            // Close modal
            $("#close-modal-button-{{$id}}").on('click', function () {
                $("#modal-background-{{$id}}, #modal-{{$id}}").addClass("hidden");
            });
        });
    </script>

    <button type="button" id="{{$button_id}}" class="btn btn-primary on-select remove inline-flex items-center ml-1 lg:ml-3 px-2 py-1 lg:px-4 lg:py-2 {{$bg_classes}} border border-transparent rounded-md font-semibold text-sm md:text-md xl:text-lg text-white uppercase tracking-widest focus:bg-gray-700  focus:ring-4 focus:outline-none focus:ring-gray-300  focus:ring-offset-2 transition ease-in-out duration-150">
        {{$button_text}}
    </button>
    <!-- Modal Background -->
    <div id="modal-background-{{$id}}" class="z-[100] fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden"></div>

    <!-- Modal Container -->
    <div id="modal-{{$id}}" class="z-[100] fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[90%] md:w-2/3 xl:w-1/2 bg-white rounded-lg shadow-md hidden">
        <!-- Modal Header -->
        <div class="mb-6 w-full bg-gray-800 rounded-t-lg text-white p-4 flex flex-row justify-between items-center">
            <h2 class="text-xl lg:text-2xl font-medium">{{$name}}</h2>
            <x-nav-button id="close-modal-button-{{$id}}" class="">
                <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M19.207 6.207a1 1 0 0 0-1.414-1.414L12 10.586 6.207 4.793a1 1 0 0 0-1.414 1.414L10.586 12l-5.793 5.793a1 1 0 1 0 1.414 1.414L12 13.414l5.793 5.793a1 1 0 0 0 1.414-1.414L13.414 12l5.793-5.793z" fill="#ffffff"/></svg>
            </x-nav-button>
        </div>
        <div class="">
            <p>
                {{$slot}}
            </p>
        </div>

    </div>
@endif
