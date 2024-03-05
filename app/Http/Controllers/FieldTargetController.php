<?php

namespace App\Http\Controllers;

use App\Models\ColdCalling;
use App\Models\Customer;
use App\Models\FieldTarget;
use App\Models\Lead;
use App\Models\LeadAnalysis;
use App\Models\Prospecting;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class FieldTargetController extends Controller
{
     public function assign_target(){
        $my_employee = my_employee(auth()->user()->id);
        $employeies = User::whereIn('id',$my_employee)->where('status',1)->get(); 
         return view('target.field_target.assign_target',compact('employeies'));
     } 

     public function field_target_save(Request $request){
        try{
            $validatedData = $request->validate([
                'assign_to' => 'required|exists:users,id',
                'month' => 'required|date_format:Y-m',
                'submit_time' => 'required|date_format:H:i',
                'freelancer' => 'nullable|numeric',
                'customer' => 'nullable|numeric',
                'prospecting' => 'nullable|numeric',
                'cold_calling' => 'nullable|numeric',
                'lead' => 'nullable|numeric',
                'lead_analysis' => 'nullable|numeric',
                'project_visit' => 'nullable|numeric',
                'project_visit_analysis' => 'nullable|numeric',
                'follow_up' => 'nullable|numeric',
                'follow_up_analysis' => 'nullable|numeric',
                'negotiation' => 'nullable|numeric',
                'negotiation_analysis' => 'nullable|numeric',
            ]);
            $input = $request->all();
            $input['assign_by'] = auth()->user()->id; 
            $input['month'] = date('Y-m-d',strtotime($input['month']));
            $old = FieldTarget::where('assign_to',$input['assign_to'])->where('month',$input['month'])->first();
            if($old){
                $old->update($input);
                return redirect()->back()->with('success', 'Target updated successfully');
            }else{
                $target = new FieldTarget($input); 
                $target->save(); 
                return redirect()->back()->with('success', 'Target assigned successfully'); 
            } 
        }catch(Exception $e){
            dd($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
     }

     public function my_field_target(Request $request){
        $data = new FieldTarget();
        if(isset($request->month) && $request->month != ''){ 
            $month = date('m',strtotime($request->month));
            $year = date('Y',strtotime($request->month)); 
            $data = $data->whereMonth('month',$month)->whereYear('month',$year);
        }else{
            $data = $data->whereMonth('month',date('m'))->whereYear('month',date('Y'));
        }
        $data =   $data->where('assign_to',auth()->user()->id)->first();  
        $selected = $request->month; 
        return view('target.field_target.my_field_target',compact('data','selected'));
     }


    public function assign_target_list(Request $request){
        $datas = new FieldTarget();
        if(isset($request->month) && $request->month != ''){ 
            $month = date('m',strtotime($request->month));
            $year = date('Y',strtotime($request->month)); 
            $datas = $datas->whereMonth('month',$month)->whereYear('month',$year);
        }else{
            $datas = $datas->whereMonth('month',date('m'))->whereYear('month',date('Y'));
        }

        $datas =   $datas->where('assign_by',auth()->user()->id)->get();  
        $selected = $request->month; 
        return view('target.field_target.assign_target_list',compact('datas','selected'));
    }

    public function today_target(){
        return view('target.today_target');
    } 

    public function marketing_field_report(Request $request){
        $my_all_employee = my_all_employee(auth()->user()->id); 

        if(isset($request->month) && $request->month != ''){ 
            $date = Carbon::parse($request->month);
        }else{ 
            $date = Carbon::now();
        }

        $employees = User::where('user_type',1)->whereHas('employee',function($q){
            $q->where('designation_id',17);
        })
        ->whereIn('id',$my_all_employee)->where('status',1)
        ->get(); 

        $user = User::find(auth()->user()->id);
        $employee_id = $user->id;
        if($request->employee==null || $request->employee==''){
            if($user->employee->designation_id!=17 && isset($employees[0]->id)){
                $employee_id = $employees[0]->id;
            }
        }else{
            $employee_id = $request->employee;
            $employee_id = decrypt($employee_id);
        } 
        $employee = User::find($employee_id);
        $my_all_employee = my_all_employee($employee_id); 

        $monthly_achive['freelancer'] = User::whereIn('ref_id',$my_all_employee)
            ->where('user_type',2)
            ->where('approve_by','!=',null)
            ->whereMonth('created_at',$date)
            ->whereYear('created_at',$date)
            ->count();

        $monthly_achive['customer'] = Customer::whereIn('ref_id',$my_all_employee)
            ->where('approve_by','!=',null)
            ->whereMonth('created_at',$date)
            ->whereYear('created_at',$date)
            ->count();
            
        $monthly_achive['prospecting'] = Prospecting::WhereHas('Customer',function($q) use($my_all_employee){
                $q->whereIn('ref_id',$my_all_employee);
            })
            ->where('approve_by','!=',null)
            ->whereMonth('created_at',$date)
            ->whereYear('created_at',$date)
            ->count();

        $monthly_achive['cold_calling'] = ColdCalling::WhereHas('Customer',function($q) use($my_all_employee){
                $q->whereIn('ref_id',$my_all_employee);
            })
            ->where('approve_by','!=',null)
            ->whereMonth('created_at',$date)
            ->whereYear('created_at',$date)
            ->count(); 

        $monthly_achive['lead'] = Lead::WhereHas('Customer',function($q) use($my_all_employee){
                $q->whereIn('ref_id',$my_all_employee);
            })
            ->where('approve_by','!=',null)
            ->whereMonth('created_at',$date)
            ->whereYear('created_at',$date)
            ->count(); 

        $monthly_achive['lead_analysis'] = LeadAnalysis::WhereHas('Customer',function($q) use($my_all_employee){
                $q->whereIn('ref_id',$my_all_employee);
            })
            ->where('approve_by','!=',null)
            ->whereMonth('created_at',$date)
            ->whereYear('created_at',$date)
            ->count();  
        $datas = User::where('user_type',2)->whereIn('id',$my_all_employee)->where('status',1)->get();
        return view('target.marketing_field_report',compact([
            'employees',
            'datas',
            'employee',
            'date',
            'monthly_achive'
        ]));
    } 

    public function salse_field_report(){
        return view('target.salse_field_report');
    }
}
