<?php

namespace App\Http\Controllers;

use App\Helpers\LoadStaticData;
use App\Http\Requests\CompanySettingsRequest;
use App\Http\Requests\EmailFormRequest;
use App\Mail\EmailSender;
use App\Models\CompanySettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use stdClass;

class SettingsController extends Controller
{
    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function companySettingsForm()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');
        $company = CompanySettings::first();
        $states = [];

        if($company) {
            $states = $this->staticDataHelper->callStatesAndCountries($company->country_id,'states');
        }
        return view('settings.comapny_settings_form', [
            'countries' => $countries,
            'company' => $company,
            'states' => $states,
        ]);
    }

    public function updateCompanySettings(CompanySettingsRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {

            $attributes = [
                "email" => $data['email'],
                "name" => $data['name'],
                "country_id" => $data['country_id'],
                "state_id" => $data['state_id'],
                "phone_number" => $data['phone_number'],
                "tax_number" => $data['tax_number'],
                "address" => $data['address'],
                "website" => $data['website'],
                "owner_name" => $data['owner_name'],
                "bussines_type" => $data['bussines_type'],
            ];

            if (isset($data['image']) && is_file($data['image'])) {
                $imageInfo  = getimagesize($data['image']);
                $storedFolder = 'public/images/company';

                if ($imageInfo && ($imageInfo[2] == IMAGETYPE_JPEG || $imageInfo[2] == IMAGETYPE_PNG || $imageInfo[2] == IMAGETYPE_GIF)) {
                    $hashedImage = Str::random(10) . '.' . $data['image']->getClientOriginalExtension();

                    if (!Storage::exists($storedFolder)) {
                        Storage::makeDirectory($storedFolder);
                    }

                    if (Storage::putFileAs($storedFolder, $data['image'], $hashedImage)) {
                        $imagePathBuilder = asset('storage/images/company') . '/'. $hashedImage;
                        
                        $attributes['image_path'] = $imagePathBuilder;   
                    } else {
                        throw new \Exception('Error uploading file');
                    }
                };
            }            
            CompanySettings::updateOrCreate([], $attributes);

            DB::commit();
            return redirect()->back()->with('success', 'Company settings has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Company settings has not been updated');
        }
    }

    public function emailForm()
    {
        $company_email = CompanySettings::first()->email;

        return view('settings.send_email_form',[
            'company_email'=> $company_email
        ]);
    }

    public function sendEmailForm(EmailFormRequest $request){
        $data = $request->all();

        $to = $data["client_email"];
        $subject = $data["title"];
        $message = $data["content"];

        try {
            Mail::to($to)->send(new EmailSender($subject, $message));
            return response()->json(['message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['message' => 'Failed to send email'], 500);
        }
    }

    public function serverInformation(){
        $serverName = request()->server();

        $serverObj =  new stdClass;
        
        $serverObj->web_server = $serverName['SERVER_SOFTWARE'];
        $serverObj->http_user_agent = $serverName['HTTP_USER_AGENT'];
        $serverObj->gateway_interface = $serverName['GATEWAY_INTERFACE'];
        $serverObj->server_protocol = $serverName['SERVER_PROTOCOL'];
        $serverObj->php_version = $serverName['PHP_VERSION'];
        $serverObj->php_url = $serverName['PHP_URL'];
        $serverObj->os = php_uname('s');
        $serverObj->ar = php_uname('m');

        return view('settings.server_settings',compact('serverObj'));
    }
}
