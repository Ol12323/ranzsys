@extends('layouts.base')

@section('content')
    <div class="container px-6 py-2 mx-auto">
        <h1 class="text-4xl font-semibold tracking-tight text-gray-800">Services</h1>
    </div>
        @livewire('customer-service-list')
@endsection
