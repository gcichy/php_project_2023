@props(['name','id','remove_elem_class','remove_elem_id'])
@if(isset($name) and isset($id) and isset($remove_elem_class) and isset($remove_elem_id))
    <script type="module">
        $(document).ready(function() {
            $('#{{$id}}').on('click', function(){
                $('.{{$remove_elem_class}}').addClass('hidden');
                let activeElem = $('.list-element.active-list-elem');
                if(typeof activeElem.attr('id') === "string") {
                    let id = activeElem.attr('id').split('-');
                    if(id.length > 1) {
                        id = '#{{$remove_elem_id}}' + id[1];
                        $(id).removeClass('hidden')
                    }
                }
            });
        });
    </script>
    <button type="button" id="{{$id}}" disabled class="btn btn-primary on-select remove inline-flex items-center ml-1 lg:ml-3 mr-3 lg:mr-5 px-2 py-1 lg:px-4 lg:py-2 bg-red-600 hover:bg-red-800 border border-transparent rounded-md font-semibold text-sm md:text-md xl:text-lg text-white uppercase tracking-widest focus:bg-gray-700  focus:ring-4 focus:outline-none focus:ring-blue-300  focus:ring-offset-2 transition ease-in-out duration-150">
        {{$name}}
    </button>
@endif

