@props(['user','text_lg','header_id','remove_id'])
<script type="module">
    $(document).ready(function() {
        $('#remove-btn').on('click', function(){
            if(!$(this).hasClass('hidden')) {
                $('#modal, #modal-background').removeClass('hidden');
            }
        });
    });

    // Close modal
    $("#close-modal-button").on('click', function () {
        $("#modal-background, #modal").addClass("hidden");
    });
</script>
@if(isset($text_lg))
    <button type="button" id="remove-btn" class="btn btn-primary on-select remove inline-flex items-center ml-1 lg:ml-3 mr-3 lg:mr-5 px-2 py-1 lg:px-4 bg-red-600 hover:bg-red-800 border border-transparent rounded-md font-semibold text-sm md:text-md lg:{{$text_lg}} text-white uppercase tracking-widest focus:bg-gray-700  focus:ring-4 focus:outline-none focus:ring-blue-300  focus:ring-offset-2 transition ease-in-out duration-150">
        {{__('Usuń')}}
    </button>
    <div id="modal-background" class="z-[100] fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 hidden"></div>

    <!-- Modal Container -->
    <div id="modal" class="z-[100] fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[90%] md:w-[600px] bg-white p-8 rounded shadow-md hidden">
        <!-- Modal Header -->
        <div class="mb-6">
            <h2 id="{{isset($header_id) ? $header_id : 'remove-header'}}" class="text-xl font-medium">{{__('Czy na pewno chcesz usunąć użytkownika ')}}</h2>
        </div>
        <div><p></p></div>
        <form action="{{ route('profile.destroy') }}" method="POST">
            @method('DELETE')
            @csrf
            <div class="flex items-center flex-row justify-center w-full">
                <div class="flex items-start flex-col w-full text-gray-700">
                    <p class="w-full text-sm xl:text-md pl-2 lg:pl-4 pb-2">
                        {{__('Podaj swoje hasło aby kontynuować .')}}
                    </p>
                    <input type="password" id="password" name="password" value="" class="ml-2 lg:ml-4 shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 pl-2 lg:pl-4" />
                </div>
            </div>
            <div class="my-6 w-[100%] flex flex-row justify-center items-center">
                <button type="button" id="close-modal-button" class="btn btn-primary on-select inline-flex items-center ml-1 lg:ml-3 mr-3 lg:mr-5 px-2 py-1 lg:px-4 lg:py-2 bg-gray-800 hover:bg-gray-700 border border-transparent rounded-md font-semibold text-sm md:text-md lg:{{$text_lg}} text-white uppercase tracking-widest focus:bg-gray-700  focus:ring-4 focus:outline-none focus:ring-blue-300  focus:ring-offset-2 transition ease-in-out duration-150">
                    {{__('Anuluj')}}
                </button>
                <x-submit-button id="submit-remove" type="submit" class="ml-[5%] bg-red-600 hover:bg-red-800 text-md lg:{{$text_lg}}">
                    {{__('Usuń')}}
                </x-submit-button>
            </div>
            <input type="text" id="{{isset($remove_id) ? $remove_id : 'remove-id'}}" name="remove_id" value="{{(isset($user) and $user instanceof \App\Models\User) ? $user->employeeNo : ''}}" class="hidden">
        </form>
    </div>
@endif
