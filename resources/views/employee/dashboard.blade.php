<x-app-layout>
    <script type="module">
        function checkActive() {
            //check if any element is active, if not details button's href is set to current url
            if($('.list-element.active-list-elem').length === 0) {
                $('.remove').css('background-color','gray');
                $('.details').css('background-color','gray').attr('href', $(location).attr('href'));
            }
            //else if id is set properly, url is set to be classified as product.details route
            else {
                var emp_no = $('.list-element.active-list-elem').attr('id').split('-');
                console.log(emp_no);
                if(emp_no.length > 1) {
                    emp_no = emp_no[1];

                    var newUrl = $(location).attr('href') + '/' + emp_no;


                    $('.remove').css('background-color','rgb(224 36 36)');
                    $('.details').css('background-color','#1ca2e6').attr('href', newUrl);
                }
                else {
                    $('.details').css('background-color','gray').attr('href', $(location).attr('href'));
                    $('.remove').css('background-color','gray');


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
        @php
            $viewName = 'Pracownicy';
        @endphp
        <x-information-panel :viewName="$viewName">
            {{--    routing for details set in java script above   --}}
            <x-nav-button class="on-select details bg-blue-450 hover:bg-blue-800">
                {{ __('Szczegóły') }}
            </x-nav-button>
            @if(in_array($user->role,array('admin','manager')))
                <x-nav-button :href="route('register')" class="ml-1 lg:ml-3">
                    {{ __('Dodaj') }}
                </x-nav-button>
                <x-nav-button  class="ml-1 lg:ml-3 lg:mr-5 on-select remove bg-red-600">
                    {{ __('Usuń') }}
                </x-nav-button>
            @endif
        </x-information-panel>
        @if (session('status') === 'user-deleted')
            <div class="my-2 flex justify-center">
                <p class="text-green-500">{{ __('Użytkownik '.session('employeeNo').' został usunięty z systemu.') }}</p>
            </div>
        @endif
        @if(isset($employees))
            <div class="max-w-7xl mt-[3%] mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg flex justify-start items-center flex-col">
                    @php
                        $inputPlaceholder = "Wpisz imię lub nazwisko...";
                        $xListElem = "employee";
                    @endphp
                    <x-search-input :inputPlaceholder="$inputPlaceholder" :xListElementUniqueId="$xListElem"></x-search-input>

                    <div class="w-full">
                        @foreach($employees as $emp)
                            <x-list-element class="list-element-{{$xListElem}} list-element flex-col" id="employee-{{$emp->employeeNo}}">
                                <div class="w-[100%] flex justify-between items-center">
                                    <div class="w-[80%] md:w-[60%] flex justify-left items-center">
                                        <p class="inline-block list-element-name ml-[3%]">{{$emp->firstName}} {{$emp->lastName}}</p>
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
