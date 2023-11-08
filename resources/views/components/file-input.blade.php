{{--@php--}}
{{--    $classes = ($active ?? false)--}}
{{--                ? 'btn btn-primary inline-flex items-center px-2 py-1 lg:px-4 lg:py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-md lg:text-xl text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900  focus:ring-4 focus:outline-none focus:ring-blue-300  focus:ring-offset-2 transition ease-in-out duration-150'--}}
{{--                : 'btn btn-primary inline-flex items-center px-2 py-1 lg:px-4 lg:py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-md lg:text-xl text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900  focus:ring-4 focus:outline-none focus:ring-gray-300  transition ease-in-out duration-150';--}}
{{--@endphp--}}

{{--<a {{ $attributes->merge(['class' => $classes]) }}>--}}
{{--    {{ $slot }}--}}
{{--</a>--}}

<script type="module">
    $(document).ready(function() {
        var dropzoneFile = $('#{{$name}}');
        var nameContainer = $('#{{$name}}-name-container');
        if(!(dropzoneFile.val() == null || dropzoneFile.val() === '')) {
            $('#{{$name}}-name').text(dropzoneFile.val());
            nameContainer.removeClass('hidden');
        }

        dropzoneFile.on("input", function() {
            if(!($(this).val() == null || $(this).val() === '')) {
                $('#{{$name}}-name').text($(this).val());
                nameContainer.removeClass('hidden');
            }
        });

        $('#{{$name}}-remove-btn').on('click', function () {
            dropzoneFile.val(null);
            nameContainer.addClass('hidden');
        })
    });

</script>
@if(isset($label) and isset($info) and isset($name))
    <label for="image" class="block mb-2 text-sm lg:text-lg font-medium text-gray-900 dark:text-white">{{$label}}</label>
    <div class="flex flex-col items-center justify-center">
        <div id="image" class="flex items-center justify-center w-full">
            <label for="{{$name}}" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-8 h-8 mb-4 text-gray-800 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                    </svg>
                    <p class="mb-2 text-sm text-gray-800 dark:text-gray-400"><span class="font-semibold">Klikni aby dodać</span> lub upuść plik w polu</p>
                    <p class="text-xs text-gray-800 dark:text-gray-400">{{$info}}</p>
                </div>
                <input id="{{$name}}" type="file" name="{{$name}}" value="{{old($name)}}" class="hidden" />
            </label>
        </div>
        <div id="{{$name}}-name-container" class="flex flex-row items-center justify-evenly w-2/3 mt-2 hidden">
            <p id="{{$name}}-name" class="text-sm lg:text-md text-green-500"></p>
            <button id="{{$name}}-remove-btn" type="button" class="inline-block w-6 h-6 lg:w-8 lg:h-8 md:rounded-md rounded-sm rotate-0 transition-all mr-0">
                <img src="{{asset('storage/x_icon.png') }}">
            </button>
        </div>
        <x-input-error :messages="$errors->get($name)" class="mt-2" />
    </div>
@endif

