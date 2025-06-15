<?php
// Declare the controller’s namespace so Laravel can autoload it correctly
namespace App\Http\Controllers;

// Import the EmailLog Eloquent model to interact with the `email_logs` table
use App\Models\EmailLog;
// Import the Request class in case you need to handle incoming HTTP data
use Illuminate\Http\Request;

// Define the EmailLogController, which handles HTTP requests related to email logs
class EmailLogController extends Controller
{
    // Define the `index` action to show a paginated list of email logs
    public function index()
    {
        // Build an Eloquent query:
        // 1. with('user') eager-loads the related User model to avoid N+1 queries
        // 2. latest() orders by the default timestamp column (usually `created_at`) descending
        // 3. paginate(20) limits the results to 20 per page and handles page parameters automatically
        $logs = EmailLog::with('user')
                        ->latest()
                        ->paginate(10);

        // Render the Blade view located at resources/views/email_logs/index.blade.php
        // Pass the `$logs` variable to the view (available as $logs in Blade)
        return view('email_logs.index', compact('logs'));
    }
}

