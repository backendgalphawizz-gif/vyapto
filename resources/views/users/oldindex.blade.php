<!DOCTYPE html>
<html>
<head>
  <title>Email Sender | Registered Users</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    rel="stylesheet"
  >
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
          /* Loader overlay */
        #loader {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            display: flex;
            flex-direction: column;           /* Stack loader and text vertically */
            align-items: center;              /* Center horizontally */
            justify-content: center;          /* Center vertically */
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">

  <!-- Loader Overlay -->
  <div id="loader">
    <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
      <span class="visually-hidden">Sending email...</span>
    </div>
    <div class="mt-3 text-primary">
      Sending email...
    </div>
  </div>

  <div class="container mt-2">
    
    <!-- Sticky header -->
    <div class="sticky-top bg-white shadow-sm py-2 mb-4">
      <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row flex-wrap justify-content-between align-items-start align-items-md-center">
          
          <!-- Title -->
          <h3 class="mb-2 mb-md-0">Registered Users</h3>
          
          <!-- Buttons -->
          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.dashboard') }}"
              class="btn btn-outline-secondary">
              <i class="bi bi-house-door-fill"></i>
            </a>
            <a href="{{ route('register.form') }}"
              class="btn btn-primary">
              <i class="bi bi-person-plus-fill"></i> Register
            </a>
          </div>

        </div>
      </div>
    </div>


    <!-- 1. The mail‐sending form wraps the table & send button only -->
    <form
      id="sendMailForm"
      method="POST"
      action="{{ route('users.sendmail') }}"
    >
      @csrf

      <table class="table table-sm table-bordered table-hover bg-white">
        <thead class="table-light">
          <tr>
            <th style="width:1%">
              <input type="checkbox" id="selectAll">
            </th>
            <th style="width:1%">No.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Date Registered</th>
            <th class="text-center" style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <!-- checkbox for mail form -->
              <td>
                <input
                  type="checkbox"
                  name="recipients[]"
                  value="{{ $user->id }}"
                  class="recipient-checkbox"
                  form="sendMailForm"
                >
              </td>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->created_at->format('M d, Y h:i A') }}</td>

              <!-- Actions: each has its own standalone form  -->
              <td class="text-center">
                <!--  EDIT (not wired yet)  -->
                <a href="#" class="btn btn-sm btn-outline-secondary me-1">
                  <i class="bi bi-pencil-fill"></i>
                </a>

                <!-- DELETE -- the button points to a separate form by ID  -->
                <button
                  class="btn btn-sm btn-outline-danger"
                  title="Delete"
                  form="delete-user-{{ $user->id }}"
                  onclick="return confirm('Delete {{ $user->name }}?')"
                >
                  <i class="bi bi-trash3-fill"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center">No users found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <!-- Record Count Summary and Pagination -->
      <div class="d-flex justify-content-between align-items-center">
          <div class="small text-muted mb-0">
              Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
          </div>
          <div class="mb-0">
              <div class="pagination-wrapper small mb-0">
                  {{ $users->links() }}
              </div>
          </div>
           <!-- send button still inside this form -->
          <div class="d-flex justify-content-end mb-3">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-send-fill"></i> Send Email to Selected
            </button>
          </div>
      </div>
    </form>

    <!-- 2. Generate one hidden DELETE form per user, outside the sendMailForm -->
    @foreach($users as $user)
      <form
        id="delete-user-{{ $user->id }}"
        method="POST"
        action="{{ route('users.destroy', $user) }}"
        class="d-none"
      >
        @csrf
        @method('DELETE')
      </form>
    @endforeach

  </div>

  <!--SweetAlert success after sending -->
  @if(session('emailSuccess'))
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Emails Sent',
        text: '{{ session('emailSuccess') }}',
        timer: 4000,
        showConfirmButton: false
      });
    </script>
  @endif

  <!--Select All” JS-->
  <script>
    document.getElementById('selectAll').addEventListener('change', function() {
      document.querySelectorAll('.recipient-checkbox')
              .forEach(cb => cb.checked = this.checked);
    });

    //  <!-- Show on submit / hide on load script -->
    const loader = document.getElementById('loader');
    const form   = document.getElementById('sendMailForm');

    form.addEventListener('submit', () => {
      loader.style.display = 'flex';
    });

    window.addEventListener('load', () => {
      loader.style.display = 'none';
    });
  </script>

</body>
</html>
