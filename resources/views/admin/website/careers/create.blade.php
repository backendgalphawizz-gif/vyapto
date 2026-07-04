@extends('layouts.admin')
@section('title', 'Add Career Item')
@section('content')
@include('admin.website.careers._form', ['item' => null, 'categories' => $categories])
@endsection
