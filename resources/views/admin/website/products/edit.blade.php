@extends('layouts.admin')
@section('title', 'Edit Product')
@section('content')
@include('admin.website.products._form', ['product' => $product])
@endsection
