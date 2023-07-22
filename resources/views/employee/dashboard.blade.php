<x-app-layout>
    @foreach($employees as $emp)
        <p>{{$emp->firstName}}</p>
    @endforeach
</x-app-layout>
