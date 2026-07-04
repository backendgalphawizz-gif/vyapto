@extends('layouts.admin')
@section('title', 'Add Service')

@section('content')
@include('admin.website.services._form', ['service' => null])
@endsection
