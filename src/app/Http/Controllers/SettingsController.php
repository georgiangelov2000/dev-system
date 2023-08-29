<?php

namespace App\Http\Controllers;

use App\Helpers\LoadStaticData;
use App\Mail\EmailSender;
use App\Models\Country;
use App\Models\State;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private $staticDataHelper;

    public function __construct(LoadStaticData $staticDataHelper)
    {
        $this->staticDataHelper = $staticDataHelper;
    }

    public function form()
    {
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');
        $company = Settings::where('type',1)->first();
        $settings = json_decode($company->settings,true);
        $states = null;

        if($company) {
            $countryName = $settings['country'];
            $country = Country::where('name', $countryName)->first();
            $states = $country->states()->select('id', 'name')->get();
        }

        return view('settings.comapny_settings_form', [
            'countries' => $countries,
            'settings' => $settings,
            'states' => $states,
        ]);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $data  = $request->all();
            $attributes = [];
            
            if($data['type'] == 1) {
                $attributes['email'] = $data['email'];
                $attributes['name'] = $data['name'];
                $attributes['country'] = isset($data['country_id']) ? Country::find($data['country_id'])->name : null;
                $attributes['state'] = isset($data['state_id']) ? State::find($data['state_id'])->name : null;
                $attributes['phone_number'] = $data['phone_number'];
                $attributes['tax_number'] = $data['tax_number'];
                $attributes['address'] = $data['address'];
                $attributes['website'] = $data['website'];
                $attributes['owner_name'] = $data['owner_name'];
                $attributes['bussines_type'] = $data['bussines_type'];
                $attributes['registration_date'] = date('Y-m-d', strtotime($data['registration_date']));
                $attributes['image_path'] = null;

                $settings = Settings::where('type',1)
                ->first();
                
                if(!$settings) {
                    $settings = new Settings;
                    $settings->settings_description = "Company settings";
                    $settings->type = 1;
                } else {
                    $decoded = json_decode($settings->settings,true);
                    if($decoded['image_path'] !== null) {
                        $attributes['image_path'] = $decoded['image_path'];
                    }
                }

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

            }

            $attributes = json_encode($attributes,true);

            $settings->settings = $attributes;
            $settings->save();

            DB::commit();
            return redirect()->back()->with('success', 'Settings has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            return back()->withInput()->with('error', 'Settings has not been updated');
        }
    }

    // public function emailForm()
    // {
    //     $company_email = CompanySettings::first()->email;

    //     return view('settings.send_email_form',[
    //         'company_email'=> $company_email
    //     ]);
    // }

    // public function sendEmailForm(EmailFormRequest $request){
    //     $data = $request->all();

    //     $to = $data["client_email"];
    //     $subject = $data["title"];
    //     $message = $data["content"];

    //     try {
    //         Mail::to($to)->send(new EmailSender($subject, $message));
    //         return response()->json(['message' => 'Email sent successfully']);
    //     } catch (\Exception $e) {
    //         dd($e->getMessage());
    //         return response()->json(['message' => 'Failed to send email'], 500);
    //     }
    // }
}
