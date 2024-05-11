<?php

namespace App\DataTables;

use App\Models\LeadAnalysis;
use App\Models\Negotiation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class NegotiationDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {  
         
        return (new EloquentDataTable($query))
            ->addColumn('action',function($negotiation){ 
                return view('negotiation.negotiation_action',compact('negotiation'))->render();
            })  
            
            ->addColumn('profession', function($data){
                return $data->customer->profession->name??'-';
            }) 
            ->addColumn('project', function($data){
                return $data->project->name??'-';
            })
            ->addColumn('unit', function($data){
                return $data->unit->name??'-';
            }) 
            ->addColumn('customer_expectation', function($data){ 
                return $data->customer->followup_analysis->customer_expectation??'-';
            }) 
            ->addColumn('need', function($data){
                return $data->customer->followup_analysis->need??'-';
            }) 
            ->addColumn('ability', function($data){
                return $data->customer->followup_analysis->ability??'-';
            }) 
            ->addColumn('influencer_opinion', function($data){
                return $data->customer->followup_analysis->abiliinfluencer_opinionty??'-';
            }) 
            ->addColumn('decision_maker_opinion', function($data){
                return $data->customer->followup_analysis->decision_maker_opinion??'-';
            })  
            ->addColumn('negotiation_amount', function($data){
                return get_price($data->negotiation_amount??0);
            })
            ->addColumn('freelancer', function($data){
                if(@$data->customer->ref_id==null){
                    return '-';
                }  
                $reporting = json_decode($data->customer->reference->user_reporting);
                if(isset($reporting) && $reporting!= null){
                    $user = User::whereIn('id',$reporting)->whereHas('freelancer',function($q){
                        $q->whereIn('designation_id',[20]);
                    })->first();  
                    if(isset($user) && $user != null){
                        return $user->name.' ['.$user->user_id.']';
                    }
                }
                return "-";  
            }) 
            ->addColumn('marketing-incharge', function($data){
                if(@$data->customer->ref_id==null){
                    return '-';
                }  

                $reporting = json_decode($data->customer->reference->user_reporting);
                if(isset($reporting) && $reporting!= null){
                    $user = User::whereIn('id',$reporting)->whereHas('employee',function($q){
                        $q->whereIn('designation_id',[16]);
                    })->first();  
                    if(isset($user) && $user != null){
                        return $user->name.' ['.$user->user_id.']';
                    }
                }
                return "-";  
            }) 

            ->addColumn('salse-incharge', function($data){
                if(@$data->customer->ref_id==null){
                    return '-';
                }  

                $reporting = json_decode($data->customer->reference->user_reporting);
                if(isset($reporting) && $reporting!= null){
                    $user = User::whereIn('id',$reporting)->whereHas('employee',function($q){
                        $q->whereIn('designation_id',[12,13,14,15]);
                    })->first();  
                    if(isset($user) && $user != null){
                        return $user->name.' ['.$user->user_id.']';
                    }
                }
                return "-";  
            })  
            ->addColumn('area-incharge', function($data){
                if(@$data->customer->ref_id==null){
                    return '-';
                }  

                $reporting = json_decode($data->customer->reference->user_reporting);
                if(isset($reporting) && $reporting!= null){
                    $user = User::whereIn('id',$reporting)->whereHas('employee',function($q){
                        $q->whereIn('designation_id',[11]);
                    })->first();  
                    if(isset($user) && $user != null){
                        return $user->name.' ['.$user->user_id.']';
                    }
                }
                return "-";  
            }) 
            ->addColumn('zonal-manager', function($data){
                if(@$data->customer->ref_id==null){
                    return '-';
                }  

                $reporting = json_decode($data->customer->reference->user_reporting);
                if(isset($reporting) && $reporting!= null){
                    $user = User::whereIn('id',$reporting)->whereHas('employee',function($q){
                        $q->whereIn('designation_id',[10]);
                    })->first();  
                    if(isset($user) && $user != null){
                        return $user->name.' ['.$user->user_id.']';
                    }
                }
                return "-";  
            }) 
            ->addColumn('serial', function () {
                static $serial = 0;
                return ++$serial;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Negotiation $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Negotiation $model, Request $request): QueryBuilder
    {
        if(isset($request->employee) && !empty($request->employee)){
            $user_id = (int)$request->employee;
        }else{
            $user_id = Auth::user()->id;
        }  
        
        if(isset($request->date)){
            $date = explode(' - ',$request->date);
            $start_date = date('Y-m-d',strtotime($date[0]));
            $end_date = date('Y-m-d',strtotime($date[1])); 
        }else{
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        } 
        $user = User::find($user_id);
        $user_employee = json_decode($user->user_employee);

        if(isset($request->status) && !empty($request->status)){
            $status = $request->status; 
        }else{
            $status = 0; 
        } 

        $datas =$model->where(function ($q){
            $q->where('approve_by','!=',null)
                ->orWhere('employee_id', Auth::user()->id)
                ->orWhere('created_by', Auth::user()->id);
        }) 
        ->whereHas('customer', function($q) use($user_employee){ 
            $q->whereIn('ref_id', $user_employee);
        })
        ->whereBetween('created_at',[$start_date.' 00:00:00',$end_date.' 23:59:59'])
        ->where('status', $status)
        ->with('customer.reference')
        ->with('customer.followup_analysis')
        ->with('customer.user.userAddress')
        ->with('customer.profession')
        ->with('project')
        ->with('unit')
        ->newQuery();  

        $datas->user_reporting = $user->user_reporting;
        return $datas;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
        ->setTableId('leadanalysis-table')
        ->columns($this->getColumns())
        ->minifiedAjax()
        ->dom('Bfrtip')
        ->orderBy(1)
        ->selectStyleSingle()
        ->buttons([
            Button::make('excel'), 
            Button::make('pdf')->title('Lead List'),
        ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    { 
        return [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->sortable(false), 
            Column::make('serial')->title('S/L')->sortable(false),
            Column::make('customer.customer_id')->title('Provable Cus ID')->sortable(false), 
            Column::make('customer.name')->title('Customer Name')->sortable(false), 
            Column::make('customer.user.phone')->title('Mobile Number')->sortable(false),
            Column::make('profession')->title('Profession')->sortable(false),
            Column::make('project')->title('Preferred Project Name')->sortable(false),
            Column::make('unit')->title('Preferred Unit Name')->sortable(false),
            Column::make('customer_expectation')->title('Customer Expectation')->sortable(false), 
            Column::make('need')->title('Customer Need')->sortable(false),
            Column::make('ability')->title('Customer Ability')->sortable(false),
            Column::make('influencer_opinion')->title('Influencer Openion')->sortable(false),
            Column::make('decision_maker_opinion')->title('Decision Maker Openion')->sortable(false),
            Column::make('negotiation_amount')->title('Negotiation Amount')->sortable(false), 
            Column::make('freelancer')->title('Franchise Partner Name & ID')->sortable(false),
            Column::make('marketing-incharge')->title('Incharge Marketing Name & ID')->sortable(false), 
            Column::make('salse-incharge')->title('Incharge Sales Name & ID')->sortable(false),
            Column::make('area-incharge')->title('Area Incharge Name & ID')->sortable(false),
            Column::make('zonal-manager')->title('Zonal Manager Name & ID')->sortable(false),
        ];   
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Negotiation_' . date('YmdHis');
    }
}