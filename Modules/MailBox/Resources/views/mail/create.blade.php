@extends('layouts.main')
@section('page-title')
    {{ __('Compose email') }}
@endsection
@section('page-breadcrumb')
    {{ __('Compose email') }}
@endsection

@push('css')
 <link rel="stylesheet" href="{{ asset('Modules/MailBox/Resources/assets/js/summernote/summernote-bs4.css')  }}">
@endpush
@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                @include('mailbox::layouts.sidebar')
                <div class="col-xl-9">
                    <!--Brand Settings-->
                    <div id="mail-inbox" class="">
                        <div class="card">
                            <form method="post" action="{{ route('mailbox.store') }}" enctype="multipart/form-data">
                            @csrf
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="to">{{__('To') }}</label>
                                            <input type="text" class="form-control" id="to" aria-describedby="to"
                                                placeholder="{{__('Enter email')}}" name="to">
                                        </div>
                                        <div class="form-group">
                                            <label for="cc">{{__('CC')}}</label>
                                            <input type="text" class="form-control" id="cc" aria-describedby="cc"
                                                name="cc" placeholder="{{__('Enter email')}}">
                                        </div>
                                        <div class="form-group">
                                            <label for="subject">{{__('Subject')}}</label>
                                            <input type="text" class="form-control" id="subject"
                                                aria-describedby="subject" placeholder="{{__('Enter Subject')}}" name="subject">
                                        </div>
                                        <textarea class="summernote" name="content"></textarea>
                                        <div id="textBoxContainer" class="mt-2 mb-2">
                                            <div class="row">
                                                <div class="form-group">
                                                    <label for="attachment">{{ __('Attachments') }}</label>
                                                </div>
                                                <div class="col-sm-11">
                                                    <input type="file" class="form-control"  name="attachment[]">
                                                </div>
                                                <div class="col-sm-1 p-0">
                                                    <button type="button" class="btn btn-primary"
                                                            onclick="addTextBox()"><i class="ti ti-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button type="submit" class="btn btn-primary">{{__('Send')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

<script src="{{ asset('Modules/MailBox/Resources/assets/js/summernote/summernote-bs4.js')  }}"></script>

    <script>
        if ($(".summernote").length) {
        setTimeout(function () {
            $('.summernote').summernote({
                placeholder: "Write Here… ",
                dialogsInBody: !0,
                tabsize: 2,
                minHeight: 200,
                toolbar: [
                    ['style', ['style']],
                    ["font", ["bold", "italic", "underline", "clear", "strikethrough"]],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                ]

            });
        },100);
    }
    </script>   
    <script>
        function addTextBox() {
            const container = document.getElementById("textBoxContainer");
            const div = document.createElement("div");
            div.className = "row mt-2 mb-2";
            div.innerHTML = `<div class="col-sm-11">
                <input type="file" class="form-control"  name="attachment[]" accept=".jpg, .jpeg, .png">
                </div><div class="col-sm-1 p-0">
                <button type="button" class="btn btn-danger" onclick="removeTextBox(this)"><i class="ti ti-trash"></i></button></div>
            `;
            container.appendChild(div);
        }

        function removeTextBox(button) {
    const container = document.getElementById("textBoxContainer");
    const grandparent = button.parentElement.parentElement; // Navigate to the grandparent element
    container.removeChild(grandparent);
}
    </script>
    <script type="text/javascript">
        $(".action-all-btn").on('click', function() {
            var checkedValues = [];
            $('input[name="mail_check"]:checked').each(function() {
                alert($(this).val());
                checkedValues.push($(this).val());
            });
            var token = $('meta[name="csrf-token"]').attr('content');
            var id = checkedValues;
            var action = $(this).attr('data-action');
            action = action.trim();
            $.ajax({
                type: 'post',
                url: '{{ url('/mailbox/action') }}',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': id,
                    'action': action
                },
                success: function(data) {
                    if (data == 1 || data == 0) {
                        location.reload(true);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors here, if needed
                }
            });

        });
        $(".action-btn").on('click', function() {
            var checkedValues = [];
            var token = $('meta[name="csrf-token"]').attr('content');
            checkedValues.push($(this).attr('data-id'));
            var temp = $(this);
            var action = $(this).attr('data-action');
            action = action.trim();
            $.ajax({
                type: 'post',
                url: '{{ url('/mailbox/action') }}',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': checkedValues,
                    'action': action
                },
                success: function(data) {


                    if (data == 1) {

                        if (action == 'starred') {

                            var html = '<i class="ti ti-star text-warning"></i>';
                            temp.html(html);
                            temp.attr('data-action', 'unstarred');
                        } else if (action == 'seen') {
                            var html = '<i class="ti ti-mail text-info"></i>';
                            temp.html(html);
                            temp.attr('data-action', 'unseen');
                        } else {}
                    } else if (data == 0) {
                        if (action == 'unstarred') {

                            var html = '<i class="ti ti-star text-secondary"></i>';
                            temp.html(html);
                            temp.attr('data-action', 'starred');
                        } else if (action == 'unseen') {
                            var html = '<i class="ti ti-mail-opened text-secondary"></i>';
                            temp.html(html);
                            temp.attr('data-action', 'seen');
                        } else {}
                    } else {
                        // Handle other cases or errors here
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors here, if needed
                }
            });
        });
    </script>
@endpush
