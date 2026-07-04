@extends('layouts.admin')
@section('title', 'Edit Career Item')
@section('content')
@include('admin.website.careers._form', ['item' => $item, 'categories' => $categories])
@endsection
