@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Add Fees</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="fees-collections.html">Accounts</a></li>
                            <li class="breadcrumb-item active">Add Fees</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('fees/collection/save') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="form-title"><span>Fees Information</span></h5>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Student Name <span class="login-danger">*</span></label>
                                            <select class="select select2s-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="student_id" name="student_id">
                                                <option selected disabled>-- Select --</option>
                                                @foreach($users as $key => $names)
                                                    <option value="{{ $names->id }}" data-code="{{ $names->user_id }}" @selected(old('student_id') == $names->id)>{{ $names->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Student ID <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control" id="student_code" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Fees Type <span class="login-danger">*</span></label>
                                            <select class="form-control select" id="fee_type_id" name="fee_type_id">
                                                <option selected disabled>-- Select Type --</option>
                                                @foreach($feeTypes as $feeType)
                                                    <option value="{{ $feeType->id }}" @selected(old('fee_type_id') == $feeType->id)>{{ $feeType->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms">
                                            <label>Fees Amount <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control" id="amount" name="amount" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" value="{{ old('amount') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group local-forms calendar-icon">
                                            <label>Paid Date <span class="login-danger">*</span></label>
                                            <input type="text" class="form-control datetimepicker" id="paid_date" name="paid_date" placeholder="DD-MM-YYYY" value="{{ old('paid_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="student-submit">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('script')
    <script>
        // select auto id and email
        $('#student_id').on('change',function()
        {
            $('#student_code').val($(this).find(':selected').data('code'));
        });
    </script>
    @endsection

@endsection
