@extends('layouts.app')

@section('content')
    <main-component
            :locale="'{{app()->getLocale()}}'"
            :authUser="{{Auth::user()}}"
    />
@endsection
