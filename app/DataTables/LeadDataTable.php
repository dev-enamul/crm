<?php

namespace App\DataTables;

use App\Models\Lead;
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

class LeadDataTable extends DataTable
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
                return view('lead.lead_action',compact('lead'))->render();
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
            ->addColumn('phone', function($data){
                return $data->customer->user->phone??"-";
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
            ->addColumn('co-ordinator', function($data){
                if(@$data->customer->ref_id==null){
                    return '-';
                }

                $reporting = json_decode($data->customer->reference->user_reporting);
                if(isset($reporting) && $reporting!= null){
                    $user = User::whereIn('id',$reporting)->whereHas('freelancer',function($q){
                        $q->whereIn('designation_id',[18]);
                    })->first();
                    if(isset($user) && $user != null){
                        return $user->name.' ['.$user->user_id.']';
                    }
                }
                return "-";
            })
            ->addColumn('ex-co-ordinator', function($data){
                if(@$data->customer->ref_id==null){
                    return '-';
                }

                $reporting = json_decode($data->customer->reference->user_reporting);
                if(isset($reporting) && $reporting!= null){
                    $user = User::whereIn('id',$reporting)->whereHas('freelancer',function($q){
                        $q->whereIn('designation_id',[17]);
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
                        $q->whereJsonContains('designations',[16]);
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
                        $q->whereJsonContains('designations',[12,13,14,15]);
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
                        $q->whereJsonContains('designations',[11]);
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
                        $q->whereJsonContains('designations',[10]);
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
     * @param \App\Models\Lead $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Lead $model, Request $request): QueryBuilder
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
                    ->setTableId('lead-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->pageLength(20)
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        Button::make('pdf')->title('Lead List'),
                    ]);
    }
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
            Column::make('phone')->title('Mobile Number'),
            Column::make('profession')->title('Profession'),
            Column::make('project')->title('Preferred Project Name'),
            Column::make('unit')->title('Preferred Unit Name'),
            Column::make('freelancer')->title('Franchise Partner Name & ID'),
            Column::make('co-ordinator')->title('Co-ordinator Name & ID'),
            Column::make('ex-co-ordinator')->title('Executive Co-ordinator Name & ID'),
            Column::make('marketing-incharge')->title('Incharge Marketing Name & ID'),
            Column::make('salse-incharge')->title('Incharge Salse Name & ID'),
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
        return 'Lead_' . date('YmdHis');
    }
}
