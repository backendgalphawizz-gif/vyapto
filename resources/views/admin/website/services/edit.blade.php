@extends('layouts.admin')
@section('title', 'Edit Service')

@section('content')
@include('admin.website.services._form', ['service' => $service])
@endsection
