<?php



namespace App\Http\Controllers\Admin;



use App\CPU\ImageManager;

use App\Http\Controllers\Controller;

use App\Models\Setting;

use App\Models\StaticPage;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;



class SettingsController extends Controller

{



    public function companyInfo()

    {

        $company_name = Setting::where('type', 'company_name')->first();

        $company_email = Setting::where('type', 'company_email')->first();

        $company_phone = Setting::where('type', 'company_phone')->first();

        $company_address = Setting::where('type', 'company_address')->first();

        $app_marque_text = Setting::where('type', 'app_marque_text')->first();



        return view('admin.settings.website-info', [

            'company_name' => $company_name,

            'company_email' => $company_email,

            'company_phone' => $company_phone,

            'company_address' => $company_address,

            'app_marque_text' => $app_marque_text,

        ]);
    }



    public function updateInfo(Request $request)

    {

        $request->validate([

            'company_name' => 'nullable|string|max:255',

            'company_email' => 'nullable|email|max:255',

            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'company_start_time' => 'nullable|string',
            'company_half_time' => 'nullable|string',
            'company_end_time' => 'nullable|string',
            'company_web_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',


        ]);



        DB::beginTransaction();



        try {



            $data = [
                ['type' => 'company_name',      'value' => $request->company_name],
                ['type' => 'company_email',     'value' => $request->company_email],
                ['type' => 'company_phone',     'value' => $request->company_phone],
                ['type' => 'company_address',   'value' => $request->company_address],
                ['type' => 'company_latitude',  'value' => $request->latitude],
                ['type' => 'company_longitude', 'value' => $request->longitude],
                ['type' => 'company_start_time', 'value' => $request->company_start_time],
                ['type' => 'company_half_time', 'value' => $request->company_half_time],
                ['type' => 'company_end_time',   'value' => $request->company_end_time],
            ];

            foreach ($data as $item) {

                if ($item['value'] !== null) {
                    Setting::updateOrCreate(['type' => $item['type']], ['value' => $item['value']]);
                }
            }




            if ($request->hasFile('company_web_logo')) {

                $oldLogo = Setting::where('type', 'company_web_logo')->value('value');



                $fileName = ImageManager::update(

                    'company/',

                    $oldLogo,

                    'png',

                    $request->file('company_web_logo')

                );



                Setting::updateOrCreate(

                    ['type' => 'company_web_logo'],

                    ['value' => $fileName]

                );
            }



            DB::commit();

            return back()->with('success', 'Company info updated successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }



    public function index(Request $request)

    {

        $query = StaticPage::query();



        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('key', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status === '1' ? 1 : 0);
        }

        $pages = $query->orderBy('id')->get();

        return view('admin.static-pages.index', compact('pages'));
    }



    public function store(Request $request)

    {

        $request->validate([

            'key' => 'required|unique:static_pages,key',

            'title' => 'required',

            'content' => 'required'

        ]);



        StaticPage::create([

            'key' => $request->key,

            'title' => $request->title,

            'content' => $request->content,

            'status' => $request->status ? 1 : 0

        ]);



        return back()->with('success', 'Page created');
    }



    public function update(Request $request, $id)

    {

        $request->validate([

            'title' => 'required',

            'content' => 'required'

        ]);



        $page = StaticPage::findOrFail($id);



        $page->update([
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->boolean('status') ? 1 : 0,
        ]);



        return back()->with('success', 'Page updated');
    }



    public function destroy($id)

    {

        $page = StaticPage::findOrFail($id);

        $page->delete();

        return back()->with('success', 'Page deleted');
    }

    
}
