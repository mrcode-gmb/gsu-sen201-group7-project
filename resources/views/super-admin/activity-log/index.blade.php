@extends('layouts.super-admin')

@section('header', 'Activity Log')

@section('slot')
    @include('activity-log.content')
@endsection
