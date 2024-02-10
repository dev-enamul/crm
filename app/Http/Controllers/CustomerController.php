<?php

namespace App\Http\Controllers;

use App\Enums\BloodGroup;
use App\Enums\Gender;
use App\Enums\MaritualStatus;
use App\Enums\Religion;
use App\Models\Area;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\Profession;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserContact;
use App\Models\UserFamily;
use App\Models\UserId;
use App\Models\UserTransaction;
use App\Models\Zone;
use App\Rules\AtLeastOneFilledRule;
use App\Traits\AreaTrait;
use App\Traits\ImageUploadTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    use AreaTrait;
    use ImageUploadTrait;

    public function maritalStatus()
    {
        return MaritualStatus::values();
    }

    public function religion()
    {
        return Religion::values();
    }
    
    public function bloodGroup()
    {
        return BloodGroup::values();
    }

    public function gender()
    {
        return Gender::values();
    }

    public function index(){
        $datas       =  Customer::where('status',1)->get();
        $countries   = $this->getCachedCountries();
        $divisions   = $this->getCachedDivisions();
        $districts   = $this->getCachedDistricts();
        $upazilas    = $this->getCachedUpazilas();
        $unions      = $this->getCachedUnions();
        $villages    = $this->getCachedVillages();
        $professions = Profession::where('status',1)->select('id','name')->get();
        $customers   = Customer::where('status', 1)->get();
        return view('customer.customer_list',compact('datas','professions','countries','divisions','districts','upazilas','unions','villages','customers'));

    }

    public function create(){
        $title     = "Customer Create";
        $countries = $this->getCachedCountries();
        $divisions = $this->getCachedDivisions();
        $districts = $this->getCachedDistricts();
        $upazilas  = $this->getCachedUpazilas();
        $unions    = $this->getCachedUnions();
        $villages  = $this->getCachedVillages();
        $genders = $this->gender();
        $religions = $this->religion();
        $bloodGroups = $this->bloodGroup();
        $maritalStatuses = $this->maritalStatus();
        $professions = Profession::where('status',1)->select('id','name')->get();
        $banks = Bank::where('status',1)->where('type',0)->select('id','name')->get();
        $mobileBanks = Bank::where('status',1)->where('type',1)->select('id','name')->get();
        $zones = Zone::where('status',1)->select('id','name')->get();
        $areas = Area::where('status',1)->select('id','name')->get();
        return view('customer.customer_save', compact('title','countries','divisions','districts','upazilas','unions','villages','professions','maritalStatuses','religions','bloodGroups','genders','banks','mobileBanks','zones','areas'));
    }

    public function save(Request $request, $id = null)
    {
        if (!empty($id)) {
            $customer_id = Customer::where('id', $id)->first();
            $customerUserId = $customer_id->user_id;
            $userContactId = UserContact::where('user_id', $customerUserId)->first();
            $user_contact_id = $userContactId->id;

            $validator = Validator::make($request->all(), [
                'full_name'                 => 'required|string|max:255',
                'profession'                => 'required|numeric|exists:professions,id',
                'marital_status'            => 'required|in:1,2,3',
                'dob'                       => 'required',
                'card_id'                   => 'nullable|string',
                'religion'                  => 'required|numeric|in:1,2,3,4,5,6,7,8,9,10',
                'blood_group'               => 'required|numeric|in:1,2,3,4,5,6,7,8',
                'gender'                    => 'required|in:1,2',
                'phone2'                    => 'nullable|string',
                'office_email'              => 'nullable|email',
                'phone1'                    => 'required|string|unique:users,phone,' . $customerUserId,
                'email'                     => 'nullable|email|max:190|unique:user_contacts,personal_email,' . $user_contact_id,
                'facebook_id'               => 'nullable|string',
                'emergency_contact_name'    => 'nullable|string',
                'emergency_person_number'   => 'nullable|string',
                'country'                   => 'required|numeric|exists:countries,id',
                'division'                  => 'required|numeric|exists:divisions,id',
                'district'                  => 'required|numeric|exists:districts,id',
                'upazila'                   => 'required|numeric|exists:upazilas,id',
                'union'                     => 'required|numeric|exists:unions,id',
                'village'                   => 'required|numeric|exists:villages,id',
                'address'                   => 'required|string',
                'zone'                      => 'required|numeric|exists:zones,id',
                'area'                      => 'required|numeric|exists:areas,id',
                'father_name'               => 'required|string',
                'father_phone'              => 'nullable|string',
                'mother_name'               => 'required|string',
                'mother_phone'              => 'nullable|string',
                'spouse_name'               => 'nullable|string',
                'spouse_phone'              => 'nullable|string',
                'bank'                      => 'nullable|numeric|exists:banks,id',
                'branch'                    => 'nullable|string',
                'account_number'            => 'nullable|string',
                'account_holder_name'       => 'nullable|string',
                'mobile_bank'               => 'nullable|numeric|exists:banks,id',
                'mobile_bank_number'        => 'nullable|string',
                'passport_issue_date'       => 'nullable',
                'passport_expire_date'      => 'nullable',
                'tin_number'                => 'nullable|string',
                'profile_image'             => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'nid_file'                  => 'file|mimes:pdf,jpeg,png,jpg|max:2048',
                'birth_certificate_file'    => 'file|mimes:pdf,jpeg,png,jpg|max:2048',
                'upload_passport'           => 'file|mimes:pdf,jpeg,png,jpg|max:2048',
                'at_least_one_field' => [
                    'sometimes', new AtLeastOneFilledRule('nid', 'birth_certificate_number', 'passport_number'),
                ],
            ]);
            if ($validator->fails()) {
                if ($validator->fails()) {
                    return redirect()->back()->withInput()->withErrors($validator)->with('error', 'Validation failed.');
                }
                
                return redirect()->back()->withInput()->withErrors($validator)->with('error', 'Validation failed.');
            }
            
            $info = Customer::find($id);
            if (!empty($info)){
                DB::beginTransaction();
                try {
                    $userId = $info->user_id;
                $user = User::findOrFail($userId);
                // Update user attributes
                $user->name             = $request->full_name;
                $user->phone            = isset($request->phone1) ?? $request->phone1;
                $user->marital_status   = $request->marital_status;
                $user->dob              = date('Y-m-d', strtotime($request->dob));
                $user->finger_id        = $request->card_id;
                $user->religion         = $request->religion;
                $user->blood_group      = $request->blood_group;
                $user->gender           = $request->gender;
                $user->updated_by       = $userId;
                if ($request->hasFile('profile_image')) {
                    $user->profile_image = $this->uploadImage($request, 'profile_image', 'users', 'public');
                    $user->save();
                }
                $user->save();

                $userAddress = UserAddress::where('user_id', $userId)->first();
                $userAddress->update([
                    'country_id'    => $request->country,
                    'division_id'   => $request->division,
                    'district_id'   => $request->district,
                    'upazila_id'    => $request->upazila,
                    'union_id'      => $request->union,
                    'village_id'    => $request->village,
                    'address'       => $request->address,
                    'zone_id'       => $request->zone,
                    'area_id'       => $request->area,
                    'updated_at'    => now()
                ]);

                $userContact = UserContact::where('user_id', $userId)->first();
                $userContact->update([
                    'personal_phone'            => $request->phone1,
                    'office_phone'              => $request->phone2,
                    'office_email'              => $request->office_email,
                    'personal_email'            => $request->email,
                    'imo_number'                => $request->imo_whatsapp_number,
                    'facebook_id'               => $request->facebook_id,
                    'user_contactscol'          => $request->user_contactscol,
                    'emergency_contact_person'  => $request->emergency_contact_name,
                    'emergency_contact_number'  => $request->emergency_person_number,
                    'updated_at'                => now(),
                ]);

                $userFamily = UserFamily::where('user_id', $userId)->first();
                $userFamily->update([
                    'father_name'           => $request->father_name,
                    'father_mobile'         => $request->father_phone,
                    'mother_name'           => $request->mother_name,
                    'mother_mobile'         => $request->mother_phone,
                    'spouse_name'           => $request->spouse_name,
                    'spouse_contact'        => $request->spouse_phone,
                    'updated_at'            => now(),
                ]);

                $customer = Customer::where('user_id', $userId)->first();
                $customer->update([
                    'profession_id'             => $request->profession,
                    'designation_id'            => $user->user_type,    #dummy
                    'status'                    => 1,
                    'updated_at'                => now(),
                    'last_approve_by'           => $userId,    #dummy
                ]);

                $userTransaction = UserTransaction::where('user_id', $userId)->first();
                $userTransaction->update([
                    'bank_id'                       => $request->bank,
                    'branch'                        => $request->branch,
                    'bank_account_number'           => $request->account_number,
                    'bank_details'                  => $request->account_holder_name,
                    'mobile_bank_id'                => $request->mobile_bank,
                    'mobile_bank_account_number'    => $request->mobile_bank_number,
                    'updated_at'                    => now(),
                ]);

                $userIds = UserId::where('user_id', $userId)->first();

                if ($request->hasFile('nid_file')) {
                    $nid_file = $this->uploadImage($request, 'nid_file', 'users', 'public');
                    $userIds->nid_image = $nid_file;
                    $userIds->save();
                }
                if ($request->hasFile('birth_certificate_file')) {
                    $birth_certificate_file = $this->uploadImage($request, 'birth_certificate_file', 'users', 'public');
                    $userIds->birth_cirtificate_image = $birth_certificate_file;
                    $userIds->save();
                }
                if ($request->hasFile('upload_passport')) {
                    $upload_passport = $this->uploadImage($request, 'upload_passport', 'users', 'public');
                    $userIds->passport_image = $upload_passport;
                    $userIds->save();
                }
                $userIds->update([
                    'nid_number'                => $request->nid,
                    'birth_cirtificate_number'  => $request->birth_certificate_number,
                    'passport_number'           => $request->passport_number,
                    'passport_issue_date'       => date('Y-m-d', strtotime($request->passport_issue_date)),
                    'passport_exp_date'         => date('Y-m-d', strtotime($request->passport_expire_date)),
                    'tin_number'                => $request->tin_number,
                    'updated_at'                => now(),
                ]);
                    
                    DB::commit();
                    return redirect()->route('customer.index')->with('success', 'Customer updated successfully');
                } catch (Exception $e) {
                    DB::rollback();
                    Log::info($e->getMessage());
                    return redirect()->back()->withInput()->with('error', $e->getMessage());
                }
            }
            else{
                return  redirect()->back('error', 'Customer not found');
            }
        }
        else {
            $validator = Validator::make($request->all(), [
                'full_name'                 => 'required|string|max:255',
                'profession'                => 'required|numeric|exists:professions,id',
                'marital_status'            => 'required|in:1,2,3',
                'dob'                       => 'required|date_format:m/d/Y',
                'card_id'                   => 'nullable|string',
                'religion'                  => 'required|numeric|in:1,2,3,4,5,6,7,8,9,10',
                'blood_group'               => 'required|numeric|in:1,2,3,4,5,6,7,8',
                'gender'                    => 'required|in:1,2',
                'phone1'                    => 'required|string',
                'phone2'                    => 'nullable|string',
                'office_email'              => 'nullable|email',
                'email'                     => 'nullable|email',
                'imo_whatsapp_number'       => 'nullable|string',
                'facebook_id'               => 'nullable|string',
                'emergency_contact_name'    => 'nullable|string',
                'emergency_person_number'   => 'nullable|string',
                'country'                   => 'required|numeric|exists:countries,id',
                'division'                  => 'required|numeric|exists:divisions,id',
                'district'                  => 'required|numeric|exists:districts,id',
                'upazila'                   => 'required|numeric|exists:upazilas,id',
                'union'                     => 'required|numeric|exists:unions,id',
                'village'                   => 'required|numeric|exists:villages,id',
                'address'                   => 'required|string',
                'zone'                      => 'required|numeric|exists:zones,id',
                'area'                      => 'required|numeric|exists:areas,id',
                'father_name'               => 'required|string',
                'father_phone'              => 'nullable|string',
                'mother_name'               => 'required|string',
                'mother_phone'              => 'nullable|string',
                'spouse_name'               => 'nullable|string',
                'spouse_phone'              => 'nullable|string',
                'bank'                      => 'nullable|numeric|exists:banks,id',
                'branch'                    => 'nullable|string',
                'account_number'            => 'nullable|string',
                'account_holder_name'       => 'nullable|string',
                'mobile_bank'               => 'nullable|numeric|exists:banks,id',
                'mobile_bank_number'        => 'nullable|string',
                'passport_issue_date'       => 'nullable|date_format:m/d/Y',
                'passport_expire_date'      => 'nullable|date_format:m/d/Y',
                'tin_number'                => 'nullable|string',
                'profile_image'             => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'nid_file'                  => 'file|mimes:pdf,jpeg,png,jpg|max:2048',
                'birth_certificate_file'    => 'file|mimes:pdf,jpeg,png,jpg|max:2048',
                'upload_passport'           => 'file|mimes:pdf,jpeg,png,jpg|max:2048',
                'at_least_one_field' => [
                    'sometimes', new AtLeastOneFilledRule('nid', 'birth_certificate_number', 'passport_number'),
                ],
            ]);
            if ($validator->fails()) {
                Log::info($validator->errors());
                return redirect()->back()->withInput()->withErrors($validator)->with('error', 'Validation failed.');
            }
            DB::beginTransaction();
            try {

                # Create new User
                $user = User::create([
                    'user_id'       => User::generateNextUserCustomerId(),
                    'name'          => $request->full_name,
                    'phone'         => $request->phone1,
                    'password'      => bcrypt('123456'),
                    'user_type'     => 3, #Customer
                    'marital_status'=> $request->marital_status,
                    'dob'           => date('Y-m-d', strtotime($request->dob)),
                    'finger_id'     => $request->card_id,
                    'religion'      => $request->religion,
                    'blood_group'   => $request->blood_group,
                    'gender'        => $request->gender,
                    'status'        => 1,
                    'created_by'    => auth()->user()->id,
                ]);

                if ($request->hasFile('profile_image')) {
                    $user->profile_image = $this->uploadImage($request, 'profile_image', 'users', 'public');
                    $user->save();
                }

                #Create user address
                UserAddress::create([
                    'user_id'       => $user->id,
                    'country_id'    => $request->country,
                    'division_id'   => $request->division,
                    'district_id'   => $request->district,
                    'upazila_id'    => $request->upazila,
                    'union_id'      => $request->union,
                    'village_id'    => $request->village,
                    'address'       => $request->address,
                    'zone_id'       => $request->zone,
                    'area_id'       => $request->area,
                    'created_at'    => now(),
                ]);

                #user contacts

                UserContact::create([
                    'user_id'                   => $user->id,
                    'personal_phone'            => $request->phone1,
                    'office_phone'              => $request->phone2,
                    'office_email'              => $request->office_email,
                    'personal_email'            => isset($request->email) ? $request->email : User::generateCustomerNextEmail(),
                    'imo_number'                => $request->imo_whatsapp_number,
                    'facebook_id'               => $request->facebook_id,
                    'user_contactscol'          => $request->user_contactscol,
                    'emergency_contact_person'  => $request->emergency_contact_name,
                    'emergency_contact_number'  => $request->emergency_person_number,
                    'created_at'                => now(),
                ]);

                #user family

                UserFamily::create([
                    'user_id'               => $user->id,
                    'father_name'           => $request->father_name,
                    'father_mobile'         => $request->father_phone,
                    'mother_name'           => $request->mother_name,
                    'mother_mobile'         => $request->mother_phone,
                    'spouse_name'           => $request->spouse_name,
                    'spouse_contact'        => $request->spouse_phone,
                    'created_at'            => now(),
                ]);
                
                #customer info
                $data = [
                    'user_id'                   => $user->id,
                    'profession_id'             => $request->profession,
                    'designation_id'            => $user->user_type,    #dummy
                    'status'                    => 1,
                    'created_at'                => now(),
                    'last_approve_by'           => auth()->user()->id,    #dummy
                ];
                Customer::create($data);

                #user transaction
                $data_transaction = [
                    'user_id'                       => $user->id,
                    'bank_id'                       => $request->bank,
                    'branch'                        => $request->branch,
                    'bank_account_number'           => $request->account_number,
                    'bank_details'                  => $request->account_holder_name,
                    'mobile_bank_id'                => $request->mobile_bank,
                    'mobile_bank_account_number'    => $request->mobile_bank_number,
                    'created_at'                    => now(),
                ];
                UserTransaction::create($data_transaction);

                #user documents
                if ($request->hasFile('nid_file')) {
                    $nid_file = $this->uploadImage($request, 'nid_file', 'users', 'public');
                }
                if ($request->hasFile('birth_certificate_file')) {
                    $birth_certificate_file = $this->uploadImage($request, 'birth_certificate_file', 'users', 'public');
                }
                if ($request->hasFile('upload_passport')) {
                    $upload_passport = $this->uploadImage($request, 'upload_passport', 'users', 'public');
                }
                $user_documents = [
                    'user_id'                   => $user->id,
                    'nid_number'                => $request->nid,
                    'nid_image'                 => $nid_file ?? null,
                    'birth_cirtificate_number'  => $request->birth_certificate_number,
                    'birth_cirtificate_image'   => $birth_certificate_file ?? null,
                    'passport_number'           => $request->passport_number,
                    'passport_issue_date'       => date('Y-m-d', strtotime($request->passport_issue_date)),
                    'passport_exp_date'         => date('Y-m-d', strtotime($request->passport_expire_date)),
                    'passport_image'            => $upload_passport ?? null,
                    'tin_number'                => $request->tin_number,
                    'created_at'                => now(),
                ];
                UserId::create($user_documents);
    
                DB::commit();
                
                return redirect()->route('customer.index')->with('success', 'Customer created successfully');
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }
    }

    public function customerSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'division'       => 'required',
            'district'       => 'required',
            'upazila'        => 'sometimes|required',
            'union'          => 'sometimes|required',
            'village'        => 'sometimes|required',
            'status'         => 'required|in:1,0',
            'customer'       => 'required|numeric|exists:freelancers,id',
            'profession'     => 'required|numeric|exists:professions,id',
            'daterange'      => 'required',
            'employee'       => 'nullable'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = implode('<br>', $errors);
            
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator)->with('error', 'Validation failed.');
            }
        }

        try{
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $division_id    = $request->division;
                $district_id    = $request->district;
                $upazila_id     = $request->upazila;
                $union_id       = $request->union;
                $village_id     = $request->village;
                $status         = $request->status;
                $customer_id    = $request->customer;
                $profession_id  = $request->profession;
                $daterange      = $request->daterange;
                $employee_id    = $request->employee;

                $divisions      = $this->getCachedDivisions();
                $districts      = $this->getCachedDistricts();
                $upazilas       = $this->getCachedUpazilas();
                $unions         = $this->getCachedUnions();
                $villages       = $this->getCachedVillages();
                $professions    = Profession::where('status',1)->select('id','name')->get();
                $customers      = Customer::where('status', 1)->get();

                $selected['division_id']   = $division_id;
                $selected['district_id']   = $district_id;
                $selected['upazila_id']    = $upazila_id;
                $selected['union_id']      = $union_id;
                $selected['village_id']    = $village_id;
                $selected['status']        = $status;
                $selected['customer_id']   = $customer_id;
                $selected['profession_id'] = $profession_id;
                $selected['daterange']     = $daterange;

                $dateParts      = explode(' - ', $daterange);
                $fromDate       = \Carbon\Carbon::createFromFormat('m/d/Y', $dateParts[0])->startOfDay();
                $toDate         = \Carbon\Carbon::createFromFormat('m/d/Y', $dateParts[1])->endOfDay();
                $customer_id    = $customer_id;
                $customerId     = Customer::where('id',$customer_id)->pluck('user_id')->first();
                
                $datas = Customer::where('status', 1)
                                ->where('profession_id', $profession_id)
                                ->where('user_id',  $customerId)
                                ->whereHas('user.userAddress', function ($query) use ($division_id, $district_id, $village_id, $union_id, $upazila_id) {
                                    $query->where('division_id', $division_id)
                                          ->where('district_id', $district_id)
                                          ->where('village_id', $village_id)
                                          ->where('union_id', $union_id)
                                          ->where('upazila_id', $upazila_id);
                                })
                                ->whereBetween('created_at', [$fromDate, $toDate])
                                ->where('status', $status)
                                ->get();
                return view('customer.customer_list', compact('professions','customers','datas','divisions','districts','upazilas','unions','villages','selected'));
            }
        }
        catch (\Throwable $th) {
            dd( $th);
            return redirect()->route('product.edit')->with('error', 'Something went wrong!');
         }
    }

    public function edit($id){
        $title     = "Customer Edit";
        $countries = $this->getCachedCountries();
        $divisions = $this->getCachedDivisions();
        $districts = $this->getCachedDistricts();
        $upazilas  = $this->getCachedUpazilas();
        $unions    = $this->getCachedUnions();
        $villages  = $this->getCachedVillages();
        $professions = Profession::where('status',1)->select('id','name')->get();
        $maritalStatuses = $this->maritalStatus();
        $religions = $this->religion();
        $bloodGroups = $this->bloodGroup();
        $genders = $this->gender();
        $banks = Bank::where('status',1)->where('type',0)->select('id','name')->get();
        $mobileBanks = Bank::where('status',1)->where('type',1)->select('id','name')->get();
        $zones = Zone::where('status',1)->select('id','name')->get();
        $areas = Area::where('status',1)->select('id','name')->get();
        $customer = Customer::find($id);
        $selected['country_id']   = $customer->user->userAddress->country_id;
        $selected['division_id']  = $customer->user->userAddress->division_id;
        $selected['district_id']  = $customer->user->userAddress->district_id;
        $selected['upazila_id']   = $customer->user->userAddress->upazila_id;
        $selected['union_id']     = $customer->user->userAddress->union_id;
        $selected['village_id']   = $customer->user->userAddress->village_id;

        return view('customer.customer_save', compact('title','countries','divisions','districts','upazilas','unions','villages','professions','maritalStatuses','religions','bloodGroups','genders','banks','mobileBanks','zones','areas','customer','selected'));
    }

    public function customerDelete($id){
        try{ 
            $data  = Customer::find($id);
            $data->delete();
            return response()->json(['success' => 'Customer Deleted'],200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()],500);
        }
    }

    public  function customerPrint($id) {
        $title     = "Customer Edit";
        $countries = $this->getCachedCountries();
        $divisions = $this->getCachedDivisions();
        $districts = $this->getCachedDistricts();
        $upazilas  = $this->getCachedUpazilas();
        $unions    = $this->getCachedUnions();
        $villages  = $this->getCachedVillages();
        $professions = Profession::where('status',1)->select('id','name')->get();
        $maritalStatuses = $this->maritalStatus();
        $religions = $this->religion();
        $bloodGroups = $this->bloodGroup();
        $genders = $this->gender();
        $banks = Bank::where('status',1)->where('type',0)->select('id','name')->get();
        $mobileBanks = Bank::where('status',1)->where('type',1)->select('id','name')->get();
        $zones = Zone::where('status',1)->select('id','name')->get();
        $areas = Area::where('status',1)->select('id','name')->get();
        $customer = Customer::find($id);
        $selected['country_id']   = $customer->user->userAddress->country_id;
        $selected['division_id']  = $customer->user->userAddress->division_id;
        $selected['district_id']  = $customer->user->userAddress->district_id;
        $selected['upazila_id']   = $customer->user->userAddress->upazila_id;
        $selected['union_id']     = $customer->user->userAddress->union_id;
        $selected['village_id']   = $customer->user->userAddress->village_id;

        return view('customer.customer_print', compact('title','countries','divisions','districts','upazilas','unions','villages','professions','maritalStatuses','religions','bloodGroups','genders','banks','mobileBanks','zones','areas','customer','selected'));
    }
    
}
