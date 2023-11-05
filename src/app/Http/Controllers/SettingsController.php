<?php

namespace App\Http\Controllers;

use App\Helpers\FunctionsHelper;
use App\Helpers\LoadStaticData;
use App\Models\Country;
use App\Models\State;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private $staticDataHelper;
    private $helper;

    public function __construct(LoadStaticData $staticDataHelper, FunctionsHelper $helper)
    {
        $this->staticDataHelper = $staticDataHelper;
        $this->helper = $helper;
    }

    public function form()
    {
        $settingsData = $this->helper->settings();

        $data = $this->getData($settingsData['country']);

        return view('settings.form', [
            'settings' => $settingsData,
            'data' => $data
        ]);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $data  = $request->all();
            $attributes = [];

            if ($data['type'] == 1) {
                $attributes['email'] = $data['email'];
                $attributes['name'] = $data['name'];
                $attributes['country'] = isset($data['country_id']) ? Country::find($data['country_id'])->name : null;
                $attributes['state'] = isset($data['state_id']) ? State::find($data['state_id'])->name : null;
                $attributes['phone_number'] = $data['phone_number'];
                $attributes['tax_number'] = $data['tax_number'];
                $attributes['address'] = $data['address'];
                $attributes['website'] = $data['website'];
                $attributes['owner_name'] = $data['owner_name'];
                $attributes['business_type'] = $data['bussines_type'];
                $attributes['registration_date'] = date('Y-m-d', strtotime($data['registration_date']));
                $attributes['image_path'] = null;

                $settings = Settings::where('type', 1)
                    ->first();

                if (!$settings) {
                    $settings = new Settings;
                    $settings->settings_description = "Company settings";
                    $settings->type = 1;
                } else {
                    $decoded = json_decode($settings->settings, true);
                    if ($decoded['image_path'] !== null) {
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
                            $imagePathBuilder = asset('storage/images/company') . '/' . $hashedImage;

                            $attributes['image_path'] = $imagePathBuilder;
                        } else {
                            throw new \Exception('Error uploading file');
                        }
                    };
                }
            }

            $attributes = json_encode($attributes, true);

            $settings->settings = $attributes;
            $settings->save();

            DB::commit();
            return redirect()->back()->with('success', 'Settings has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Settings has not been updated');
        }
    }

    private function getData($country)
    {
        $countries = $this->staticDataHelper->callStatesAndCountries('countries');
        $states = [];

        if ($country) {
            $country = Country::where('name', $country)->first();
            $states = $country->states()->select('id', 'name')->get();
        }

        return [
            'countries' => $countries,
            'states' => $states,
        ];
    }
}
