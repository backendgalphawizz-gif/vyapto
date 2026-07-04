@extends('layouts.admin')
@section('title', 'Edit Blog')
@section('content')
@include('admin.website.blogs._form', ['blog' => $blog])
@endsection
