@extends('layouts.admin')

@section('title', 'Verify OTP')

@section('content')
@push('styles')
<style>
    body {
        background: linear-gradient(to right, #1a202c, #2d3748);
        font-family: 'Instrument Sans', sans-serif;
    }
    .otp-container {
        max-width: 400px;
        margin: 60px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 15px rgba(0,0,0,0.15);
        position: relative;
    }
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.15em;
    }
</style>
@endpush

<div class="container">
    <div class="otp-container">
        <!-- Close button -->
        <a href="{{ url('/') }}" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close"></a>

        <h4 class="text-center text-primary mb-4">Verify OTP</h4>

        @if(session('otpSent'))
            <div class="alert alert-success text-center">{{ session('otpSent') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.otp.verify.submit') }}" id="otpForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Enter OTP</label>
                <input type="text" name="otp" class="form-control" placeholder="Enter 6-digit OTP" required>
            </div>
            <button type="submit" id="verifyBtn" class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="spinner"></span>
                <span id="btnText">Verify and Register</span>
            </button>
        </form>
    </div>
</div>

@if ($errors->has('otp'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Invalid OTP',
            text: @json($errors->first('otp')),
            confirmButtonColor: '#d33',
        });
    </script>
@endif

@push('scripts')
<script>
    document.getElementById('otpForm').addEventListener('submit', function () {
        const btn = document.getElementById('verifyBtn');
        const spinner = document.getElementById('spinner');
        const btnText = document.getElementById('btnText');

        spinner.classList.remove('d-none');
        btnText.textContent = 'Verifying...';
        btn.disabled = true;
    });
</script>
@endpush
@endsection
