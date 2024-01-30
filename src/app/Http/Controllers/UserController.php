<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use App\Helpers\FunctionsHelper;
use Illuminate\Support\Facades\Log;
use Auth;
class UserController extends Controller
{

    private $helper;
    private $dir = 'public/images/users';
    private $pdfDir = "public/pdfs";

    public function __construct(FunctionsHelper $helper)
    {
        $this->helper = $helper;
    }

    public function index()
    {
        $roles = Role::all();
        return view('users.index',['roles' => $roles]);
    }
    public function create()
    {
        $roles = Role::all();
        return view('users.create',['roles' => $roles]);
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $roles = Role::pluck('id')->toArray();

            if( !in_array($data['role_id'], $roles) ) {
                throw new \Exception("Invalid type");
            }

            // Set up User
            $user = new User();
            $user->email = $data['email'];
            $user->username = $data['username'];
            $user->password = $data['password'];
            $user->first_name = $data['first_name'];
            $user->middle_name = $data['middle_name'];
            $user->last_name = $data['last_name'];
            $user->gender = $data['gender'];
            $user->role_id = $data['role_id'];
            $user->phone = $data['phone'];

            if ($data['birth_date']) {
                $user->birth_date = date('Y-m-d', strtotime($data['birth_date']));
            }
            if ($data['card_id']) {
                $user->card_id = $data['card_id'];
            }

            $user->address = $data['address'];
            $user->password = Hash::make($data['password']);

            $user->save();

            // if user is saved set the pdf and picture of the user;
            if ($user) {
                if (isset($data['pdf']) && $data['pdf'] instanceof UploadedFile) {
                    $file = $data['pdf'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo, $file->path());
                    finfo_close($finfo);

                    if ($mime_type === 'application/pdf') {
                        $this->helper->imageUploader($file,$user,$this->pdfDir,'pdf_file_path');
                    }
                }
                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    $file = $data['image'];
                    $extension = $file->getClientOriginalExtension();
                    if (in_array($extension, ['png', 'jpg', 'jpeg'], true)) {
                        $this->helper->imageUploader($file,$user,$this->dir,'image_path');
                    }
                }
            }

            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'User has not been created');
        }
        return redirect()->route('user.index')->with('success', 'User has been created');
    }

    public function edit(User $user)
    {
        $user->load('role');
        $roles = Role::all();
        return view('users.edit', compact('user','roles'));
    }

    public function update(User $user, UserRequest $request)
    {
        DB::beginTransaction();
        try {
            
            $data = $request->validated();
            $roles = Role::pluck('id')->toArray();
            
            if( !in_array($data['role_id'], $roles) ) {
                throw new \Exception("Invalid type");
            }

            $user->email = $data['email'];
            $user->username = $data['username'];
            $user->password = $data['password'];
            $user->first_name = $data['first_name'];
            $user->middle_name = $data['middle_name'];
            $user->last_name = $data['last_name'];
            $user->gender = $data['gender'];
            $user->role_id = $data['role_id'];
            $user->phone = $data['phone'];
            if ($data['birth_date']) {
                $user->birth_date = date('Y-m-d', strtotime($data['birth_date']));
            }
            if ($data['card_id']) {
                $user->card_id = $data['card_id'];
            }
            $user->address = $data['address'];
            $user->password = Hash::make($data['password']);
            
            if ($user) {
                if (isset($data['pdf']) && $data['pdf'] instanceof UploadedFile) {
                    $file = $data['pdf'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo, $file->path());
                    finfo_close($finfo);

                    if ($mime_type === 'application/pdf') {
                        $this->helper->imageUploader($file,$user,$this->pdfDir,'pdf_file_path');
                    }
                }
                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    $file = $data['image'];
                    $extension = $file->getClientOriginalExtension();
                    if (in_array($extension, ['png', 'jpg', 'jpeg'], true)) {
                        $this->helper->imageUploader($file,$user,$this->dir,'image_path');
                    }
                }
            }

            $user->save();
            DB::commit();
        } catch (\Exception $e) 
        {
            DB::rollback();
            dd($e->getMessage());
            return back()->withInput()->with('error', 'User has not been updated');
        }
        return redirect()->route('user.index')->with('success', 'User has been updated');
    }
}
