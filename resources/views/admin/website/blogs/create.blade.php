@extends('layouts.admin')
@section('title', 'Add Blog')
@section('content')
@include('admin.website.blogs._form', ['blog' => null])
@endsection
