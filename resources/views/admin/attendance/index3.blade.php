<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Learner Attendance</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    </style>
</head>
<body>
<div class="container py-5 position-relative">
    <!-- X Close Button -->
    <a href="{{ url('/') }}"
       class="position-absolute top-0 end-0 m-3 text-white fs-4 text-decoration-none"
       title="Back to Home"
       style="z-index: 10;">&times;</a>
       
    <div class="text-center mb-4">
        <h2 class="fw-bold">Learner Attendance</h2>
        <p class="text-gray-300">Scan the QR code and log your session</p>
    </div>

    <div class="attendance-container">
        <!-- QR Form -->
        <div class="qr-form-container flex-fill">
            <form method="POST" action="{{ route('admin.attendance.store') }}" class="text-center">
                @csrf
                <div class="mb-4">
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
                            <tr><td colspan="6" class="text-center text-muted">No attendance records for today.</td></tr>
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

<!-- QR Code Script -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let isSubmitting = false;
    const successSound = new Audio("https://notificationsounds.com/storage/sounds/file-sounds-1143-success.mp3");

    function onScanSuccess(decodedText) {
        if (isSubmitting) return;
        isSubmitting = true;

        fetch("{{ route('admin.attendance.lookup-learner') }}", {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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
                successSound.play();

                setTimeout(() => {
                    document.querySelector('form').submit();
                    isSubmitting = false;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Attendance logged successfully',
                        showConfirmButton: false,
                        timer: 2500
                    });
                }, 1200);
            } else {
                isSubmitting = false;
                Swal.fire({
                    icon: 'error',
                    title: 'QR Code not recognized',
                    text: 'This QR code is not registered.',
                });
            }
        })
        .catch(err => {
            isSubmitting = false;
            console.error(err);
            Swal.fire('Error', 'Something went wrong!', 'error');
        });
    }

    const scanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
    scanner.render(onScanSuccess);
</script>
</body>
</html>
