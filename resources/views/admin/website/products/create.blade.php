@extends('layouts.admin')
@section('title', 'Add Product')
@section('content')
@include('admin.website.products._form', ['product' => null])
@endsection
