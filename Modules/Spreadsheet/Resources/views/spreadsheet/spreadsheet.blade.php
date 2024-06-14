@extends('layouts.main')
@section('page-title')
    {{ __('Spreadsheet') }}
@endsection
@section('page-breadcrumb')
    {{ __('Create') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('Modules/Spreadsheet/Resources/assets/css/xspreadsheet.css') }}">
@endpush

@section('content')
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex align-items-center justify-content-end">
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                        <div class="btn-box">
                            {{ Form::label('', __('File Name'), ['class' => 'form-label']) }}
                            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'id' => 'name_id']) }}
                        </div>
                    </div>
                    <div class="col-auto float-end ms-2 mt-4">
                        <button class="btn btn-primary" id="save-button" type="submit">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="spreadsheet-container"></div>
@endsection

@push('scripts')
<script src="{{ asset('Modules/Spreadsheet/Resources/assets/js/xspreadsheet.js')}}"></script>
<script>
    const container = document.getElementById('spreadsheet-container');
    const hot = x_spreadsheet(container, {
        showToolbar: true,
        showGrid: true,
    });

    const saveButton = document.getElementById('save-button');
    const inputField = document.getElementById('name_id');

        saveButton.addEventListener('click', function () {
        // Disable the button to prevent multiple clicks
        saveButton.disabled = true;
        var updatedData = hot.getData(); // Get updated data from the editor
        $.ajax({
            url: '{{ route('spreadsheet.file.store') }}',
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                "parent_id": "{{ $parent_id }}",
                "updatedData": updatedData,
                "file_name": inputField.value,
            },

            success: function(response)
            {
                if (response !== false){
                    toastrs('success', 'Spreadsheet data saved successfully', 'success');
                    window.location.href = '{{ route('spreadsheet.index') }}';
                } else {
                    toastrs('Error', 'Something went wrong, please try again!', 'error');
                }
            },
            error: function() {
                // Handle errors here as needed
            }
        });

    });
</script>
@endpush

