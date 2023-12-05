@props(['unique_id'])
@if(isset($unique_id))
    <script type="module">
        $('#{{$unique_id}}').on('change', function (){
            if($(this).val() != null) {
                $(this).addClass('bg-blue-150');
            } else {
                $(this).removeClass('bg-blue-150');
            }
        });
    </script>
    <div class="p-1 flex justify-center items-center h-full">
        <input type="search" id="{{$unique_id}}"  class=" block w-full text-xs xl:text-sm text-gray-900 border-gray-300 focus:bg-blue-150 focus:ring-blue-450 rounded"
               name="{{$unique_id}}" placeholder="Nazwa">
    </div>

@endif
