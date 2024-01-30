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
use App\Http\Requests\SettingsRequest;

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
        $settings = Settings::where('type', 1)->first()->settings;
        $data = [];

        $country = null;
        $states = [];

        if(isset($settings['country'])) {
            $country = Country::find($settings['country'])->first();
            $states = $country->states()->select('id', 'name')->get();
        }

        $settings['country'] = $country->name;
        $data['countries'] = $this->staticDataHelper->callStatesAndCountries('countries');
        $data['states'] =  $country->states()->select('id', 'name')->get();

        $serverInformation = [
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'],
            'server_name' => $_SERVER['SERVER_NAME'],
            'server_address' => $_SERVER['SERVER_ADDR'],
            'server_port' => $_SERVER['SERVER_PORT'],
            'protocol' => $_SERVER['REQUEST_SCHEME'],
            'server_load' => sys_getloadavg(),
        ];
        
        return view('settings.form', [
            'settings' => $settings,
            'data' => $data,
            'server_information' => $serverInformation,
            'cpu_cores' => trim(shell_exec('nproc'))
        ]);
    }

    public function update(SettingsRequest $request)
    {
        DB::beginTransaction();

        try {
            $data  = $request->validated();
            
            $attributes = [];
            
            if(!$data['type'] == 1) {
                throw new \Exception('Invalid type');
            }

            $country = isset($data['country']) ? Country::find($data['country'])->id : null;
            $state = isset($data['state']) ? State::find($data['state'])->id : null;
            $registrationDate = isset($data['registration_date']) ? date('Y-m-d', strtotime($data['registration_date'])) : null;

            $data['country'] = $country;
            $data['state'] = $state;
            $data['registration_date'] = $registrationDate;

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

                        $data['image_path'] = $imagePathBuilder;
                    } else {
                        throw new \Exception('Error uploading file');
                    }
                };
            }
                        
            $companySettings = Settings::where('type', 1)->first()
            ? Settings::where('type', 1)->first() : new Settings;

            dd($data);
            $companySettings->settings = $data;

            $companySettings->save();

            DB::commit();
            return redirect()->back()->with('success', 'Settings has been updated');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Settings has not been updated');
        }
    }
}
