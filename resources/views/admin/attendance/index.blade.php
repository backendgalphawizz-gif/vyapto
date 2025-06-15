<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learner Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- QR Code Scanner -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
                body {
            background-color: #0f172a;
            font-family: 'Segoe UI', sans-serif;
            color: #f1f5f9;
        }

        h2, label, .form-label {
            color: white !important;
        }

        .qr-form-container,
        .table-container {
            background: #ffffff;
            color: #111827;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .custom-gray-head {
            background-color: #eaeaea !important;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .attendance-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }
        .custom-gray-head {
            background-color: rgb(90, 88, 88) !important;
        }
        .custom-toast-border {
            border: 1px solid rgb(47, 15, 253) !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2) !important;
        }
        .rounded-alert {
            border: 1px solid rgb(47, 15, 253) !important;
            border-radius: 12px !important;
        }
    </style>
</head>
<body>

<div class="container position-relative">
    <!-- X Close Button -->
    <a href="{{ url('/') }}"
       class="position-absolute top-0 end-0 m-3 text-white fs-4 text-decoration-none"
       title="Back to Home"
       style="z-index: 10;">&times;</a>
       
    <div class="text-center mb-1">
        <h2 class="fw-bold">Learner Attendance</h2>
        <p class="text-gray-300">Scan the QR code and log your session</p>
    </div>
    <div class="text-center">
        <h5 class="fw-semibold mb-1">Current Date and Time</h5>
        <div id="realtime-clock" class="fs-5 text-primary fw-bold"></div>
    </div>

    <div class="attendance-container">
        <!-- QR Form -->
        <div class="qr-form-container flex-fill">
            <form method="POST" action="{{ route('admin.attendance.store') }}" class="text-center">
                @csrf
                <div class="mb-2">
                    <label class="form-label fw-semibold">Scan Learner QR Code</label>
                    <div id="qr-reader" style="width: 100%; max-width: 300px; margin: auto; border-radius: 12px;"></div>
                    <input type="hidden" id="qr_code" name="qr_code">
                    <div id="learner-info" class="alert alert-primary d-none mt-3">
                        <strong>Learner:</strong> <span id="learner-name">-</span>
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label class="form-label">Select Session</label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach (['am_in' => 'AM IN', 'am_out' => 'AM OUT', 'pm_in' => 'PM IN', 'pm_out' => 'PM OUT'] as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="session" id="{{ $value }}" value="{{ $value }}" {{ $loop->first ? 'checked' : '' }}>
                                <label class="form-check-label small text-dark" for="{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <!-- Attendance Table -->
        <div class="table-container flex-fill">
            <h6 class="text-muted mb-3">As of {{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }}</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
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
                                <td class="text-center">{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}</td>
                                <td>{{ $attendance->learner->lname }}, {{ $attendance->learner->fname }}</td>
                                <td>{{ $attendance->am_in ? \Carbon\Carbon::parse($attendance->am_in)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->am_out ? \Carbon\Carbon::parse($attendance->am_out)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->pm_in ? \Carbon\Carbon::parse($attendance->pm_in)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->pm_out ? \Carbon\Carbon::parse($attendance->pm_out)->format('h:i A') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-warning">No attendance records for today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Showing {{ $attendances->firstItem() ?? 0 }} to {{ $attendances->lastItem() ?? 0 }} of {{ $attendances->total() }} entries
                </small>
                <div>{{ $attendances->links() }}</div>
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
                customClass: { popup: 'custom-toast-border' }
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
                customClass: { popup: 'rounded-alert' }
            });
        });
    </script>
@endif

<script>
    let isSubmitting = false;
    const successSound = new Audio("https://notificationsounds.com/storage/sounds/file-sounds-1143-success.mp3");

    function onScanSuccess(decodedText, decodedResult) {
        if (isSubmitting) return;
        isSubmitting = true;

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
                document.getElementById('learner-name').textContent = data.learner.name;
                document.getElementById('learner-info').classList.remove('d-none');

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
            Swal.fire({
                icon: 'error',
                title: 'Lookup Failed',
                text: 'Error finding learner by QR code.',
            });
        });
    }

    function logAttendance(qrCode) {
        const sessionValue = document.querySelector('input[name="session"]:checked')?.value;
        const formData = new FormData();

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
                    html: `<span style="color:#212529;">${data.message}</span>`,
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
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Could not submit attendance.',
            });
        });
    }

    let html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);

    function updateClock() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        };
        document.getElementById('realtime-clock').textContent = now.toLocaleString('en-US', options);
    }

    setInterval(updateClock, 1000);
    updateClock(); // initial call
</script>

</body>
</html>
