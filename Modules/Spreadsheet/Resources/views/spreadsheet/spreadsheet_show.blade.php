@extends('layouts.main')
@section('page-title')
    {{ __('Spreadsheet') }}
@endsection
@section('page-breadcrumb')
    {{ __('View') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('Modules/Spreadsheet/Resources/assets/css/xspreadsheet.css') }}">
@endpush
@section('content')
    <div id="spreadsheet-container"></div>
@endsection

@push('scripts')
<script src="{{ asset('Modules/Spreadsheet/Resources/assets/js/xspreadsheet.js')}}"></script>
    <script>
        const spreadsheetData = @json($spreadsheetData);
        const sheetDataArray = [];
        $.each(spreadsheetData, function(indexInArray, valueOfElement) {
            const spreadsheetDat = valueOfElement;
            const newObj = {};
            spreadsheetDat.forEach((arr, index) => {
                newObj[index] = {
                    cells: arr.map((value, i) => ({
                        text: value !== null ? value : ''
                    }))
                };
            });
            newObj.len = 100;
            sheetDataArray.push({
                name: indexInArray,
                rows: newObj
            });
        });

            const container = document.getElementById('spreadsheet-container');
            const hot = x_spreadsheet(container, {
                showToolbar: true,
                showGrid: true,
            }).loadData(sheetDataArray).change((cdata) => {
                console.log('>>>', hot.getData());
            });

    </script>
@endpush

