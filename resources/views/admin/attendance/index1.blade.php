@extends('layouts.admin')

@section('title', 'Learner Attendance')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .custom-gray-head {
            background-color:rgb(214, 206, 206) !important;
        }
        .custom-toast-border {
            border: 1px solid rgb(47, 15, 253) !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2) !important;
            /* background-color:rgb(215, 218, 222) !important; */
        }
        .rounded-alert {
            border: 1px solid rgb(47, 15, 253) !important;
            border-radius: 12px !important;
        }
    </style>
@endpush

@section('content')
<div class="container">
    <h4 class="mb-4">Learner Attendance</h4>

    {{--@if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif--}}
<div class="d-flex justify-content-center">
    <div class="w-100" style="max-width: 600px;">
            <form method="POST" action="{{ route('admin.attendance.store') }}" class="text-center">
                    @csrf
                    <div class="mb-3">
                        <label class="d-block">Scan Learner QR Code</label>
                        <div class="mx-auto" style="width: 300px; border-radius: 12px; overflow: hidden;" id="qr-reader"></div>
                        <input type="hidden" id="qr_code" name="qr_code">
                        <div id="learner-info" class="alert alert-info d-none mt-3 mx-auto" style="max-width: 300px;">
                            <strong>Learner:</strong> <span id="learner-name">-</span>
                        </div>
                    </div>

                    <div class="row justify-content-center mb-3">
                        <div class="col-md-4">
                            <label>Session</label>
                            <select name="session" class="form-select" required>
                                <option value="am_in">AM IN</option>
                                <option value="am_out">AM OUT</option>
                                <option value="pm_in">PM IN</option>
                                <option value="pm_out">PM OUT</option>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex align-items-end justify-content-center">
                            <button class="btn btn-primary">Log Attendance</button>
                        </div>
                    </div>
                </form>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="mt-5">
        <h6 class="text-muted mb-3">As of {{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }}</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover">
                <thead class="custom-gray-head">
                    <tr>
                        <th class="text-center" style="width: 1%;">No.</th>
                        <th>Name</th>
                        <th>AM IN</th>
                        <th>AM OUT</th>
                        <th>PM IN</th>
                        <th>PM OUT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $index => $attendance)
                        <tr>
                            <td class="text-center">
                                {{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}
                            </td>
                            <td>{{ $attendance->learner->lname }}, {{ $attendance->learner->fname }}</td>
                            <td>
                                {{ $attendance->am_in ? \Carbon\Carbon::parse($attendance->am_in)->format('h:i A') : '-' }}
                            </td>
                            <td>
                                {{ $attendance->am_out ? \Carbon\Carbon::parse($attendance->am_out)->format('h:i A') : '-' }}
                            </td>
                            <td>
                                {{ $attendance->pm_in ? \Carbon\Carbon::parse($attendance->pm_in)->format('h:i A') : '-' }}
                            </td>
                            <td>
                                {{ $attendance->pm_out ? \Carbon\Carbon::parse($attendance->pm_out)->format('h:i A') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No attendance records for today.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="small text-muted">
                    Showing
                    {{ $attendances->firstItem() ?? 0 }} to
                    {{ $attendances->lastItem() ?? 0 }} of
                    {{ $attendances->total() }} entries
                </div>
                <div>
                    {{ $attendances->links() }} <!-- Laravel pagination links -->
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'custom-toast-border'
                }
            });
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: @json(session('warning')),
                confirmButtonColor: '#f0ad4e',
                timer: 2500,
                customClass: {
                    popup: 'rounded-alert'
                }
            });
        });
    </script>
@endif

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let isSubmitting = false;
    const successSound = new Audio("https://notificationsounds.com/storage/sounds/file-sounds-1143-success.mp3");

    function onScanSuccess(decodedText, decodedResult) {
        if (isSubmitting) return;
        isSubmitting = true;

        // Step 1: Lookup learner name
        fetch("{{ route('admin.attendance.lookup-learner') }}", {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qr_code: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'found') {
                // Show learner name
                document.getElementById('learner-name').textContent = data.learner.name;
                document.getElementById('learner-info').classList.remove('d-none');

                // Step 2: Submit attendance after short delay
                setTimeout(() => logAttendance(decodedText), 1500);
            } else {
                isSubmitting = false;
                Swal.fire({
                    icon: 'error',
                    title: 'QR Code Not Recognized',
                    text: 'This QR code is not registered in the system.',
                });
            }
        })
        .catch(error => {
            isSubmitting = false;
            console.error('Lookup error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Lookup Failed',
                text: 'Error finding learner by QR code.',
            });
        });
    }

    function logAttendance(qrCode) {
        const sessionValue = document.querySelector('select[name="session"]').value;
        const formData = new FormData();

        // First, lookup the learner to get ID
        fetch("{{ route('admin.attendance.lookup-learner') }}", {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qr_code: qrCode })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'found') {
                formData.append('learner_id', data.learner.id);
                formData.append('session', sessionValue);

                // Now POST attendance
                return fetch("{{ route('admin.attendance.store') }}", {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });
            } else {
                throw new Error('Learner not found during re-lookup.');
            }
        })
        .then(async response => {
            const data = await response.json();
            isSubmitting = false;

            if (data.status === 'success') {
                successSound.play();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2500,
                    customClass: { popup: 'custom-toast-border' }
                });
                setTimeout(() => window.location.reload(), 2500);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: data.message,
                    timer: 2500
                });
            }
        })
        .catch(error => {
            isSubmitting = false;
            console.error('Attendance error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Could not submit attendance.',
            });
        });
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 }
    );
    html5QrcodeScanner.render(onScanSuccess);
</script>
@endsection
