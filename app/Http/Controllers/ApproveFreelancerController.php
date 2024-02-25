<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use App\Models\FreelancerApprovel;
use App\Models\TrainingCategory;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApproveFreelancerController extends Controller
{
    
    public function index()
    {
        $trainings = TrainingCategory::where('status', '1')->get(); 
        $my_employee = my_employee(auth()->user()->id); 
        $datas = Freelancer::where('status',0)->whereIn('last_approve_by',$my_employee)->get();
        return view('freelancer.approve-freelancer',compact('datas','trainings'));
    }

  
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
            $freelancer = Freelancer::where('user_id',$request->freelancer_id)->first(); 
            $freelancer->last_approve_by = auth()->user()->id;
            $freelancer->save(); 

            $input = $request->all();
            $input['approve_by'] = auth()->user()->id;
            if($request->meeting_date && $request->meeting_time){
                $input['meeting_date'] = $request->meeting_date . ' ' . $request->meeting_time;
            } 

            if(isset($request->user_id)){
                $input['status'] = 1;
            }

            FreelancerApprovel::create($input);  
            
            DB::commit(); 
            return redirect()->back()->with('success', 'Freelancer approved successfully');
        }catch(Exception $e){
            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function complete_training($id){ 
        $id = decrypt($id);
        DB::beginTransaction();
        try{
            $user = User::find($id);
            $user->status = 1;
            $user->approve_by = auth()->user()->id;
            $user->save(); 

            FreelancerApprovel::create([
                'freelancer_id' => $id,
                'counselling' => 0,
                'interview' => 0,
                'remarks' => 'All Training Completed.',
                'approve_by' => auth()->user()->id,
                'complete_training' => 1
            ]);

            DB::commit();
            return response()->json(['success' => 'Training Completed Successfully'], 200);
       }catch(Exception $e){
              DB::rollBack();
              return response()->json(['error' => $e->getMessage()], 500); 
       }
    }

    
}
