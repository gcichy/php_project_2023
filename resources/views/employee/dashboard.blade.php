<x-app-layout>
    @php
        $remove_header_id = 'remove-header';
        $remove_id = 'remove-id';
    @endphp
    <script type="module">
        function checkActive() {
            //check if any element is active, if not details button's href is set to current url
            if($('.list-element.active-list-elem').length === 0) {
                $('.remove').removeClass('bg-red-600').addClass('bg-gray-400');
                $('.details').removeClass('bg-blue-450').addClass('bg-gray-400').attr('href', $(location).attr('href'));
            }
            //else if id is set properly, url is set to be classified as product.details route
            else {
                var emp_no = $('.list-element.active-list-elem').attr('id').split('-');
                if(emp_no.length > 1) {
                    emp_no = emp_no[1];
                    $('#{{$remove_header_id}}').append('<span>' + emp_no + '?</span>');
                    $('#{{$remove_id}}').val(emp_no)
                    var newUrl = $(location).attr('href') + '/' + emp_no;

                    $('.remove').removeClass('bg-gray-400').addClass('bg-red-600').prop('disabled', false);
                    $('.details').removeClass('bg-gray-400').addClass('bg-blue-450').attr('href', newUrl);
                }
                else {
                    $('.details').removeClass('bg-blue-450').addClass('bg-gray-400').attr('href', $(location).attr('href'));
                    $('.remove').removeClass('bg-red-600').addClass('bg-gray-400');
                }
            }
        }

        $(document).ready(function() {
            checkActive();

            $('.list-element').on('click', function () {
                var is_active = ($(this).hasClass('active-list-elem') ? true : false);
                $('.list-element').removeClass('active-list-elem');
                $(this).addClass('active-list-elem');
                if (is_active) {
                    $('.list-element').removeClass('active-list-elem');
                }
                checkActive();
            });

        });
    </script>
    @if(isset($user) and $user instanceof \App\Models\User)
        @if(isset($status))
            <p>{{$status}}</p>
        @endif
        @if(session('status_err'))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-red-700 space-y-1">
                    {{session('status_err')}}
                </p>
            </div>
        @endif
        @if(session('status'))
            <div class="flex justify-center items-center">
                <p class="w-full !text-md lg:text-xl font-medium text-center p-6 text-green-500 space-y-1">
                    {{session('status')}}
                </p>
            </div>
        @endif
        @php
            $viewName = 'Pracownicy';
        @endphp
        <x-information-panel :viewName="$viewName">
            {{--    routing for details set in java script above   --}}
            <x-nav-button class="on-select details bg-blue-450 hover:bg-blue-800">
                {{ __('Szczegóły') }}
            </x-nav-button>
            @if(in_array($user->role,array('admin','manager')))
                <x-nav-button :href="route('register')" class="bg-green-450 ml-1 lg:ml-3">
                    {{ __('Dodaj') }}
                </x-nav-button>
                @php
                    $name = 'użytkownika';
                    $route = route('profile.destroy');
                    $button_id = 'remove-user-modal';
                    $id = '2';
                    $remove_elem_class = 'element-remove';
                    $remove_elem_id = 'user-remove-';
                    $disabled = 'disabled';
                @endphp
                <x-remove-modal :name="$name" :button_id="$button_id" :route="$route" :id="$id" :remove_elem_class="$remove_elem_class" :remove_elem_id="$remove_elem_id" :disabled="$disabled">
                    @foreach($employees as $emp)
                        <div class="{{$remove_elem_class}} hidden" id="{{$remove_elem_id}}{{$emp->employeeNo}}">
                            <x-list-element class="ml-8 flex-col lg:py-0 py-0 w-[80%]">
                                <div class="w-full flex flex-row justify-start">
                                    <div class="w-[85%] flex flex-col justify-between items-center">
                                        <div class="w-full flex justify-left items-center">
                                            <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                {{$emp->firstName}} {{$emp->lastName}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </x-list-element>
                        </div>
                    @endforeach
                </x-remove-modal>
{{--                <x-remove-user-modal :text_lg="__('text-lg')" :header_id="$remove_header_id" :remove_id="$remove_id"></x-remove-user-modal>--}}
            @endif
        </x-information-panel>
        @if (session('status') === 'user-deleted')
            <div class="my-2 flex justify-center">
                <p class="text-green-500">{{ __('Użytkownik '.session('employeeNo').' został usunięty z systemu.') }}</p>
            </div>
        @endif
        @if(isset($employees))
            <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6 flex justify-center">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col w-full md:w-[90%] lg:w-[80%] xl:w-[60%]">
                    @php
                        $inputPlaceholder = "Wpisz imię lub nazwisko...";
                        $xListElem = "employee";
                    @endphp
                    <x-search-input :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElem"></x-search-input>

                    <div class="w-full">
                        @foreach($employees as $emp)
                            <x-list-element class="list-element-{{$xListElem}} list-element flex-col lg:py-0 py-0" id="employee-{{$emp->employeeNo}}">
                                <div class="w-full flex flex-row justify-center">
                                    <div class="w-full flex flex-col justify-between items-center">
                                        <div class="w-full flex justify-left items-center">
                                            <p class="my-2 mr-2 rounded-lg inline-block text-white bg-blue-450 shadow-lg list-element-name py-2 px-3 xl:text-lg text-md whitespace-nowrap overflow-clip">
                                                {{$emp->firstName}} {{$emp->lastName}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </x-list-element>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif
</x-app-layout>
