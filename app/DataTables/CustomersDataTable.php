<?php

namespace App\DataTables;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CustomersDataTable extends DataTable
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
            ->addColumn('action',function($data){
                return view('customer.customer_action',compact('data'))->render();
            })
            ->addColumn('serial', function () {
                static $serial = 0;
                return ++$serial;
            })  
            ->addColumn('thana', function($data){
                return $data->user->userAddress->upazila->name??"-";
            })
            ->addColumn('union', function($data){
                return $data->user->userAddress->union->name??"-";
            })

            ->addColumn('vilage', function($data){
                return $data->user->userAddress->village->name??"-";
            })
            ->addColumn('mobile_no', function($data){
                return $data->user->phone??"-";
            })
            ->addColumn('profession', function($data){
                return $data->profession->name??'-';
            }) 
            
            ->addColumn('freelancer', function($data){
                if($data->ref_id==null){
                    return '-';
                }  
                $reporting = json_decode($data->reference->user_reporting);
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

            ->addColumn('co-ordinator-applicant', function($data){
                if($data->ref_id==null){
                    return '-';
                } 
                $reporting = json_decode($data->reference->user_reporting);
                if(isset($reporting) && $reporting!= null){
                    $user = User::whereIn('id',$reporting)->whereHas('freelancer',function($q){
                        $q->whereIn('designation_id',[19]);
                    })->first();  
                    if(isset($user) && $user != null){
                        return $user->name.' ['.$user->user_id.']';
                    }
                }
                return "-";  
            }) 
            ->addColumn('co-ordinator', function($data){
                if($data->ref_id==null){
                    return '-';
                } 
                $reporting = json_decode($data->reference->user_reporting);
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
                if($data->ref_id==null){
                    return '-';
                } 
                $reporting = json_decode($data->reference->user_reporting);
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
            ->addColumn('fl', function($data){
                // return $data->status==0?"-":$data->user->user_id;
                return @$data->reference->name.' ['. @$data->reference->user_id.']';
            })
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Customer $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Customer $model, Request $request): QueryBuilder
    {
        if(isset($request->date)){
            $date = explode(' - ',$request->date);
            $start_date = date('Y-m-d',strtotime($date[0]));
            $end_date = date('Y-m-d',strtotime($date[1]));
            $model = $model->whereBetween('created_at',[$start_date.' 00:00:00',$end_date.' 23:59:59']);
        } 

        if(isset($request->employee)){
            $user_id = (int)$request->employee; 
        }else{
            $user_id = auth()->user()->id;
        } 
        $user = User::find($user_id);
        $my_employee = json_decode($user->user_employee);
         
        if(isset($request->status)){
            $status = $request->status;
        }else{
            $status = 0;
        }

        return $model->newQuery()
        ->whereIn('ref_id',$my_employee)
        ->where('status',$status) 
        ->with(['reference','user','profession','user.userAddress.village','user.userAddress.union','user.userAddress.upazila']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('customers-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->pageLength(20)
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'), 
                        Button::make('pdf')->title('Customer List'), 
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
            Column::make('customer_id')->title('Provable CUS ID')->searchable(true), 
            Column::make('name')->title('Customer Name')->searchable(true),
            Column::make('user.phone')->title('Phone Number')->searchable(true),
            Column::make('profession'),
            Column::make('freelancer')->title('Franchise Partner Name & ID'),
            Column::make('co-ordinator-applicant')->title('Co-ordinator Applicant Name & ID'),
            Column::make('co-ordinator')->title('Co-ordinator Name & ID'),
            Column::make('ex-co-ordinator')->title('Executive Co-ordinator Name & ID'),  
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Customers_' . date('YmdHis');
    }
}
