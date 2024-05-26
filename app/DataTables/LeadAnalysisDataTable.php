<?php

namespace App\DataTables;

use App\Models\LeadAnalysis;
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

class LeadAnalysisDataTable extends DataTable
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
            ->addColumn('action',function($lead){
                return view('lead_analysis.lead_analysis_action',compact('lead'))->render();
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
            ->addColumn('income_range', function($data){
                return get_price($data->income_range??0);
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
               return marketingInChargeEmployee($reporting);
            })

            ->addColumn('salse-incharge', function($data){
                if(@$data->customer->ref_id==null){
                    return '-';
                }

                $reporting = json_decode($data->customer->reference->user_reporting);
                return salesInChargeEmployee($reporting);
            })
            ->addColumn('area-incharge', function($data){
                if(@$data->customer->ref_id==null){
                    return '-';
                }

                $reporting = json_decode($data->customer->reference->user_reporting);
                return areaInChargeEmployee($reporting);
            })
            ->addColumn('zonal-manager', function($data){
                if(@$data->customer->ref_id==null){
                    return '-';
                }

                $reporting = json_decode($data->customer->reference->user_reporting);
                return zonalManagerEmployee($reporting);
            })
            ->addColumn('serial', function () {
                static $serial = 0;
                return ++$serial;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\LeadAnalysi $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(LeadAnalysis $model, Request $request): QueryBuilder
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
        ->with('customer.user.userAddress')
        ->with('customer.profession')
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
                  ->addClass('text-center'),
            Column::make('serial')->title('S/L'),
            Column::make('customer.customer_id')->title('Provable Cus ID'),
            Column::make('customer.name')->title('Customer Name'),
            Column::make('customer.user.phone')->title('Mobile Number'),
            Column::make('profession')->title('Profession'),
            Column::make('project')->title('Preferred Project Name'),
            Column::make('unit')->title('Preferred Unit Name'),
            Column::make('income_range')->title('Income Range'),
            Column::make('profession_year')->title('Service Year'),
            Column::make('influencer')->title('Influencer Person Name'),
            Column::make('decision_maker')->title('Decision Maker Person Name'),
            Column::make('freelancer')->title('Franchise Partner Name & ID'),
            Column::make('marketing-incharge')->title('Incharge Marketing Name & ID'),
            Column::make('salse-incharge')->title('Incharge Sales Name & ID'),
            Column::make('area-incharge')->title('Area Incharge Name & ID'),
            Column::make('zonal-manager')->title('Zonal Manager Name & ID'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'LeadAnalysis_' . date('YmdHis');
    }
}
