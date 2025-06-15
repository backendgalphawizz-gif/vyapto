@extends('layouts.admin')

@section('title', 'LEMS')

@section('content')

    @push('styles')
        <style>
            .card-border-left {
                border-left: 8px solid !important;
                border-radius: 0.75rem;
            }
            .card-users { border-color: #0d6efd !important; }
            .card-learners { border-color: #198754 !important; }
            .card-employees { border-color: #6f42c1 !important; }
            .card-mails { border-color: #fd7e14 !important; }
            .card-announcements { border-color: #dc3545 !important; }
            .card-attendance { border-color: #20c997 !important; }

            .text-purple { color: #6f42c1 !important; }
            .text-teal { color: #20c997 !important; }
        </style>
    @endpush

    <div class="row g-3 mt-1">
        <!-- Total Users -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-users text-center shadow-sm">
                <div class="card-body text-primary">
                    <i class="bi bi-people-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Users</h5>
                    <p class="display-6 mb-0">{{ $userCount }}</p>
                </div>
            </div>
        </div>

        <!-- Total Learners -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-learners text-center shadow-sm">
                <div class="card-body text-success">
                    <i class="bi bi-person-workspace display-6 mb-2"></i>
                    <h5 class="card-title">Total Learners</h5>
                    <p class="display-6 mb-0">{{ $learnerCount }}</p>
                </div>
            </div>
        </div>
        <!-- Total Employees -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-employees text-center shadow-sm">
                <div class="card-body text-purple">
                    <i class="bi bi-person-badge-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Employees</h5>
                    <p class="display-6 mb-0">0</p>
                </div>
            </div>
        </div>

        <!-- Total Mail Logs -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-mails text-center shadow-sm">
                <div class="card-body text-warning">
                    <i class="bi bi-envelope-paper-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Mail Logs</h5>
                    <p class="display-6 mb-0">{{ $mailLogCount }}</p>
                </div>
            </div>
        </div>

        <!-- Total Announcements -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-announcements text-center shadow-sm">
                <div class="card-body text-danger">
                    <i class="bi bi-megaphone-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Announcements</h5>
                    <p class="display-6 mb-0">{{ $announcementCount }}</p>
                </div>
            </div>
        </div>

        <!-- Total Attendance Logs -->
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-attendance text-center shadow-sm">
                <div class="card-body text-teal">
                    <i class="bi bi-clipboard2-check-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Attendance</h5>
                    <p class="display-6 mb-0">{{ $attendanceCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Learner vs Employee Distribution</h5>
                    <canvas id="userChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Email & Announcement Logs</h5>
                    <canvas id="logChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="progress mt-3" style="height: 6px;">
        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $learnerCount / max(1, $userCount) * 100 }}%"></div>
    </div>
@endsection

@push('scripts')
    @if(session('emailSuccess'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Email Sender',
                text: '{{ session('emailSuccess') }}',
                confirmButtonColor: '#3085d6',
                timer: 4000,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        const userChart = new Chart(document.getElementById('userChart'), {
            type: 'doughnut',
            data: {
                labels: ['Learners', 'Employees'],
                datasets: [{
                    data: [{{ $learnerCount }}, 0], // replace 0 with $employeeCount when available
                    backgroundColor: ['#198754', '#6f42c1']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        const logChart = new Chart(document.getElementById('logChart'), {
            type: 'bar',
            data: {
                labels: ['Mails', 'Announcements'],
                datasets: [{
                    label: 'Total',
                    data: [{{ $mailLogCount }}, {{ $announcementCount }}],
                    backgroundColor: ['#fd7e14', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });
    </script>

@endpush
