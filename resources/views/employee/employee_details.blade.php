<x-app-layout>
    @section('page-js-script')
        <script type="module">
            $(document).ready(function() {
                $('#profileBtn').on('click', function (){
                    $('#profile').css('display','block');
                    $('#work').css('display','none');
                });


                $('#workBtn').on('click', function (){
                    $('#profile').css('display','none');
                    $('#work').css('display','block');
                });
            });
        </script>
    @endsection

    <div class="space-x-8 mt-8 flex bg-gray-50 border-gray-300  justify-around">
        <div class="py-5 pr-5 flex justify-center align-middle">
            <x-secondary-button id="workBtn">
                {{ __('Praca') }}
            </x-secondary-button>
        </div>
        <div class="py-5 pr-5 flex justify-center align-middle">
            <x-secondary-button id="profileBtn">
                {{ __('Profil') }}
            </x-secondary-button>
        </div>
    </div>

    <div id="work">
        <div class="space-x-8 mt-8 flex bg-gray-50 border-gray-300  justify-between">
            <a class ='block w-1/2 pl-3 pr-4 py-2 border-l-4 lg:border-l-8 lg:py-8 lg:text-3xl text-left text-base font-medium text-gray-800  transition duration-150 ease-in-out'>
                {{ __('Praca') }}
            </a>

        </div>
        <p>{{$user->firstName}}</p>
    </div>

    <div id="profile" class="hidden">
        @include('layouts.profile-content')
    </div>


</x-app-layout>
