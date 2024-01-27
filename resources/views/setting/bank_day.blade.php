@extends('layouts.dashboard')
@section('title','Bank Day')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Bank Day</h4> 
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Bank Day</li>
                            </ol>
                        </div> 
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card"> 
                        <div class="card-body"> 
                            <div class="d-flex justify-content-between">  
                                <div class="">
                                    <div class="d-flex">   
                                        <div class="input-group">   
                                            <button class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#create_modal">
                                                <span><i class="mdi mdi-clipboard-plus-outline"></i> Add Bank Day</span>
                                            </button> 
                                        </div>
                                    </div>  
                                </div>
                           </div> 
                            <table id=" " class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>S/N</th> 
                                        <th>Month</th> 
                                        <th>Bank Day</th> 
                                    </tr>
                                </thead>
                                <tbody> 
                                    @foreach ($datas as $key => $item)
                                        <tr>
                                            <td class="text-center" data-bs-toggle="tooltip" title="Action"> 
                                                <div class="dropdown">
                                                    <a href="javascript:void(0)" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v align-middle ms-2 cursor-pointer"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-animated">
                                                        <a class="dropdown-item" href="#">Edit</a>
                                                        <a class="dropdown-item" href="javascript:void(0)"onclick="deleteItem('{{ route('bank-day.destroy',$item->id) }}')">Delete</a>  
                                                    </div>
                                                </div> 
                                            </td> 
                                            <td>{{$key+1}}</td>
                                            <td>{{get_date($item->month, 'M-Y')}}</td> 
                                            <td>
                                                @foreach(json_decode($item->bank_day) as $bank_day)
                                                <div class="btn btn-sm btn-primary mb-2">{{$bank_day}}</div>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach 
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

<div class="modal fade" id="create_modal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header"> 
                <h5 class="modal-title">Create Monthly Bank Day</h5><button type="button" class="btn btn-sm btn-label-danger btn-icon" data-bs-dismiss="modal"><i class="mdi mdi-close"></i></button>
            </div> 
            <form action="{{route('bank-day.store')}}" method="POST">  
                @csrf
                <div class="modal-body"> 
                        <div class="form-group mb-2">
                            <label for="month">Month <span class="text-danger">*</span></label>
                            <input type="month" class="form-control" id="month" name="month" placeholder="Enter Bank" required>
                        </div> 

                        <div class="form-group mb-2">
                            <label for="bank_day">Bank Days<span class="text-danger">*</span></label>
                            <select id="bank_day" multiple class="select2" name="bank_day[]" required> 
                                @for($i=1; $i<=31;$i++)
                                <option value="{{$i}}" selected>{{$i}}</option> 
                                @endfor
                            </select> 
                        </div>  
                </div> 
                <div class="modal-footer">
                    <div class="text-end">
                        <button class="btn btn-primary"><i class="fas fa-save"></i> Submit</button> <button class="btn btn-outline-danger"><i class="mdi mdi-refresh"></i> Reset</button>
                    </div> 
                </div> 
            </form>
        </div>
    </div>
</div> 
 

@endsection
