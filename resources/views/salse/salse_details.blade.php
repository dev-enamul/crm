@extends('layouts.dashboard')
@section('title','Sales')

@section('style')
    <style>
        @media print {
            @page {
                size: A4;
            }
            .page-break {
                page-break-before: always;
            }  
        } 
        hr{
            margin: 0;
        } 
    </style>
@endsection

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Salse Details</h4> 
                        <div class="page-title-right">
                            <button class="btn btn-primary" onclick="printPage()"> <i class="mdi mdi-printer"></i> Print</button>
                            
                        </div> 
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card"> 
                        <div class="card-header text-center flex-column pb-0">
                            <img src="../assets//images/logo-dark.png" alt="" class="page_header_logo">
                            <h3 class="mb-0">Way Housing Pvt. Ltd</h3>
                            <p class="m-0">Project Note Sheet</p> 
                            <p class="m-0 fs-16 mt-1"><strong>Customer Details</strong></p>
                        </div>
                        <div class="card-body"> 
                            <table class="table  table-bordered dt-responsive nowrap fs-14 mb-2" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <tbody>  
                                    <tr>
                                        <td class="p-2"><label class="bold-lg"> Customer Name : </label> {{@$data->customer->name}}</td> 
                                    </tr> 

                                </tbody>
                            </table>

                            <table class="table  table-bordered dt-responsive nowrap fs-14" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <tbody>  
                                    <tr>
                                        <td class="p-2"><label class="bold-lg"> Customer Mobile : </label> {{@$data->customer->user->phone}}</td> 
                                        <td class="p-2"><label class="bold-lg"> Booking Date : </label> {{get_date($data->created_at)}}</td> 
                                        <td class="p-2"><label class="bold-lg"> Customer ID : </label> {{$data->customer->customer_id}}</td> 
                                    </tr> 

                                </tbody>
                            </table>  
                            <p class="m-0 fs-16 mt-1 text-center"><strong>Salse & Marketing Team Details</strong></p>

                            @foreach ($ref_users as $ref)
                                <table class="table  table-bordered dt-responsive nowrap fs-14 mb-2" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <tbody>  
                                        <tr> 
                                            <td class="p-1" style="width: 70%;">
                                                <label class="bold-lg"> 
                                                    {{$ref->user_type==1? $ref->employee->designation->title:$ref->freelancer->designation->title}} : 
                                                </label> {{$ref->name}}
                                            </td> 
                                            <td class="p-1"><label class="bold-lg">EMP ID : </label> {{$ref->user_id}}</td> 
                                        </tr>  
                                    </tbody>
                                </table> 
                            @endforeach 

                            <p class="m-0 fs-16 mt-1 text-center"><strong>Project & Unit Details</strong></p> 

                            <table class="table  table-bordered dt-responsive nowrap fs-14 mb-2" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <tbody>  
                                    <tr>
                                        <td class="p-1" style="width:50%;"><label class="bold-lg"> Project Name : </label> {{$data->project->name}}</td> 
                                        <td class="p-1">
                                            <label class="bold-lg"> Unit Choice : </label> {{$data->unit->title}}
                                        </td>  
                                    </tr>  
                                </tbody>
                            </table>

                            <table class="table  table-bordered dt-responsive nowrap fs-14 mb-2" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <tbody>  
                                    <tr>
                                        <td class="p-1">
                                            <label class="bold-lg"> Unit Value : </label> {{get_price($data->sold_value)}}
                                        </td> 
                                        <td class="p-1">
                                            <label class="bold-lg"> Unit Qty : </label> {{$data->unit_qty}}
                                        </td>  

                                        <td class="p-1">
                                            <label class="bold-lg"> Unit Facility : </label> {{ \App\Enums\UnitFacility::values()[@$data->facility] }}
                                        </td> 

                                        <td class="p-1">
                                            <label class="bold-lg"> Floor No : </label> 5
                                        </td>  

                                        <td class="p-1">
                                            <label class="bold-lg"> Unit No : </label> 5
                                        </td> 
                                    </tr>  
                                </tbody>
                            </table>

                            <table class="table  table-bordered dt-responsive nowrap fs-14 mb-2" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <tbody>  
                                    <tr>
                                        <td class="p-1">
                                            <label class="bold-lg"> Booking Money : </label> $76766</td> 
                                        <td class="p-1">
                                            <label class="bold-lg"> Duration : </label> 5 December, 2023
                                        </td>  
                                        <td class="p-1">
                                            <label class="bold-lg"> Type : </label> A
                                        </td> 
                                        <td class="p-1">
                                            <label class="bold-lg"> Floor No : </label> 5
                                        </td> 

                                        <td class="p-1">
                                            <label class="bold-lg"> Choice Type : </label> Lottery
                                        </td> 
                                    </tr>  
                                </tbody>
                            </table> 
                        </div> 
                    </div>
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div> <!-- container-fluid -->
    </div> 
    @include('includes.footer') 
</div>
@endsection 

@section('script')
    <script>
        function printPage() {
            window.print();
        }
    </script> 
@endsection