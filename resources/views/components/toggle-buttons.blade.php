@section('page-js-script')
    <script type="module">
        $(document).ready(function() {
            $('#rightBtn').on('click', function (){
                $('#right').css('display','block');
                $('#left').css('display','none');
            });

            $('#leftBtn').on('click', function (){
                $('#right').css('display','none');
                $('#left').css('display','block');
            });
        });
    </script>
@endsection
@if(isset($leftBtn) and isset($rightBtn))
    <div class="space-x-8 my-4 flex border-gray-300  justify-center">
        <div class="py-5 pr-5 flex justify-center align-middle">
            <x-secondary-button id="leftBtn">
                {{ $leftBtn }}
            </x-secondary-button>
        </div>
        <div class="py-5 pr-5 flex justify-center align-middle">
            <x-secondary-button id="rightBtn">
                {{ $rightBtn }}
            </x-secondary-button>
        </div>
    </div>

    <div id="left">
        {{$leftContent}}
    </div>

    <div id="right" class="hidden">
        {{$rightContent}}
    </div>

@endif

