@extends('layouts.main')
@section('page-title')
    {{__('Student')}}
@endsection
@section('page-breadcrumb')
    {{ __('Student') }}
@endsection
@section('page-action')
<div class="text-end align-items-end d-flex justify-content-end">
    @permission('student logs manage')
        <a href="{{route('student.logs')}}" class="btn btn-primary btn-sm ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Student Logs')}}"><i class="ti ti-user-check"></i>
        </a>
    @endpermission
</div>
@endsection
@section('content')
    <!-- Listing -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <!-- Table -->
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Student Avatar')}}</th>
                                    <th scope="col">{{ __('Name')}}</th>
                                    <th scope="col">{{ __('Email')}}</th>
                                    <th scope="col">{{ __('Phone No')}}</th>
                                    <th scope="col" class="text-center">{{ __('Action')}}</th>
                                </tr>
                            </thead>
                            @if(count($students) > 0 && !empty($students))
                                <tbody class="list">
                                    @foreach ($students as $student)
                                        <tr>
                                            <td>
                                                @if(!empty($student->avatar))
                                                    <a href="{{get_file($student->avatar)}}" target="_blank">
                                                        <img alt="Image placeholder" src="{{get_file($student->avatar)}}" class="img-fluid rounded-circle card-avatar" style="width: 35px;">
                                                    </a>
                                                @else
                                                    <a href="{{get_file('/uploads/users-avatar/avatar.png')}}" target="_blank">
                                                        <img alt="Image placeholder" src="{{get_file('/uploads/users-avatar/avatar.png')}}" class="img-fluid rounded-circle card-avatar" style="width: 35px;">
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{$student->name}}</td>
                                            <td>{{$student->email}}</td>
                                            <td>{{$student->phone_number}}</td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <!-- Actions -->
                                                    @permission('student show')
                                                        <div class="actions ml-3">
                                                            <div class="action-btn bg-warning ms-2">
                                                                <a href="{{route('student.show',$student->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ __('Details') }}"> <span class="text-white"> <i class="ti ti-eye"></i></span></a>
                                                            </div>
                                                        </div>
                                                    @endpermission
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @else
                                <tbody>
                                    <tr>
                                        <td colspan="7">
                                            <div class="text-center">
                                                <i class="fas fa-folder-open text-primary" style="font-size: 48px;"></i>
                                                <h2>{{__('Opps')}}...</h2>
                                                <h6>{{__('No data Found')}}. </h6>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            @endif

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
