<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function index()
    {
        return view('users.index');
    }
    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            $data['password'] = Hash::make($data['password']);

            // Handle for not expected statuses
            $data['gender'] = array_key_exists($data['gender'], config('statuses.genders')) ? $data['gender'] : 3;
            $data['role_id'] = array_key_exists($data['role_id'], config('statuses.roles')) ? $data['role_id'] : 2;

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

            $user->save();

            // if user is saved set the pdf and picture of the user;
            if ($user) {

                if (isset($data['pdf']) && $data['pdf'] instanceof UploadedFile) {
                    $file = $data['pdf'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo, $file->path());
                    finfo_close($finfo);

                    if ($mime_type === 'application/pdf') {
                        try {
                            $pdfPath = $file->store('public/pdfs');
                            $user->pdf_file_path = Storage::url($pdfPath);
                        } catch (\Exception $e) {
                            Log::error('Error storing PDF file: ' . $e->getMessage());
                        }
                    }
                }

                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    $file = $data['image'];
                    $extension = $file->getClientOriginalExtension();

                    if (in_array($extension, ['png', 'jpg', 'jpeg'], true)) {
                        try {
                            $hashedImage = $file->store('public/users');
                            $user->photo = Storage::url($hashedImage);
                        } catch (\Exception $e) {
                            Log::error('Error storing image file: ' . $e->getMessage());
                        }
                    }
                }
            }

            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'User has not been created');
        }
        return redirect()->route('user.create')->with('success', 'User has been created');
    }

    public function edit(User $user)
    {
        $user->load('role');
        return view('users.edit', compact('user'));
    }

    public function update(User $user, UserRequest $request)
    {
        DB::beginTransaction();
        try {
            
            $data = $request->validated();

            $data['password'] = Hash::make($data['password']);

            // Handle for not expected statuses
            $data['gender'] = array_key_exists($data['gender'], config('statuses.genders')) ? $data['gender'] : 3;
            $data['role_id'] = array_key_exists($data['role_id'], config('statuses.roles')) ? $data['role_id'] : 2;

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

            $user->save();

            if ($user) {
                if (isset($data['pdf']) && $data['pdf'] instanceof UploadedFile) {
                    $file = $data['pdf'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo, $file->path());
                    finfo_close($finfo);

                    if ($mime_type === 'application/pdf') {
                        try {
                            $existingPdf = $user->pdf_file_path;

                            if (file_exists(public_path($existingPdf))) {
                                // Delete the existing PDF file
                                unlink(public_path($existingPdf));
                            }

                            // Store the new PDF file
                            $newPdfPath = $file->store('public/pdfs');
                            $user->pdf_file_path = Storage::url($newPdfPath);
                        } catch (\Exception $e) {
                            Log::error('Error storing PDF file: ' . $e->getMessage());
                        }
                    }
                }
                if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                    $file = $data['image'];
                    $extension = $file->getClientOriginalExtension();
                    if (in_array($extension, ['png', 'jpg', 'jpeg'], true)) {
                        try {
                            $existingImage = $user->photo;
                            if ($existingImage !== null) {
                                $imagePath = public_path($existingImage);

                                if (file_exists($imagePath)) {
                                    // Delete the existing image file
                                    unlink($imagePath);
                                }
                            }

                            // Store the new image file
                            $newImagePath = $file->store('public/users');
                            $user->photo = Storage::url($newImagePath);
                        } catch (\Exception $e) {
                            Log::error('Error storing image file: ' . $e->getMessage());
                        }
                    }
                }
            }

            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'User has not been updated');
        }
        return redirect()->route('user.index')->with('success', 'User has been updated');
    }
}
