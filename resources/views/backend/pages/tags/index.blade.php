@extends('backend.layouts.authenticated', [
    'pageClass' => 'backend--pages--tags--index',
])

@section('title', 'Tags')

@section('content')
    @include('backend.pages.tags.index.create-modal')
    @include('backend.pages.tags.index.edit-modal')

    @include('backend.pages.tags.index.header')
    @include('backend.pages.tags.index.filters-and-table')
@endsection
