<x-app-layout>
    @if($status != '')
        <div class="my-2 flex justify-center">
            <p class="text-green-500">{{$status}}</p>
        </div>
    @endif
    @include('layouts.profile-content')
</x-app-layout>
