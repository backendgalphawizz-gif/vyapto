@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Terms & Condition'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{asset('/public/assets/back-end/img/Pages.png')}}" alt="">
                {{\App\CPU\translate('pages')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Inlile Menu -->
        @include('admin-views.business-settings.pages-inline-menu')
        <!-- End Inlile Menu -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{\App\CPU\translate('terms_and_condition')}}</h5>
                    </div>
                    <form action="{{route('admin.business-settings.update-terms')}}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <textarea class="form-control" id="editor" name="value">{{ $terms_condition->value }}</textarea>
                            </div>
                            <div class="form-group termdiv">
                                <input class="form-control btn--primary submitbtn" type="submit" value="{{\App\CPU\translate('submit')}}" name="btn">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{--ck editor--}}
    {{-- <script src="//cdn.ckeditor.com/4.23.0-lts/standard/ckeditor.js"></script>
    <!-- <script src="{{asset('/')}}vendor/ckeditor/ckeditor/ckeditor.js"></script> -->
    <!-- <script src="{{asset('/')}}vendor/ckeditor/ckeditor/adapters/jquery.js"></script> -->
    <script>
        // $('#editor').ckeditor();

        CKEDITOR.replace( 'editor', {
            language: 'en',
            uiColor: '#9AB8F3'
        });


    </script> --}}

    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('value');
    </script>

    {{--ck editor--}}
@endpush
