@extends('layouts.main')
@section('page-title')
    {{__('Chapters')}}
@endsection
@section('page-breadcrumb')
    {{__('Chapter')}},
    {{__('Create')}}
@endsection
@section('page-action')
    <div>
        <a href="{{route('course.edit',$course_id)}}" class="btn btn-sm btn-white btn-icon-only rounded-circle">
            <i class="fa fa-arrow-left"></i>
        </a>
    </div>
@endsection
@push('css')
<link href="{{  asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css')  }}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $("#type").change(function () {
                $(this).find("option:selected").each(function () {
                    var optionValue = $(this).attr("value");
                    if (optionValue == 'Video Url') {

                        $('#video_url_div').removeClass('d-none');
                        $('#video_url_div').addClass('d-block');

                        $('#duration_div').removeClass('d-none');
                        $('#duration_div').addClass('d-block');

                        $('#iframe_div').addClass('d-none');
                        $('#iframe_div').removeClass('d-block');

                        $('#text_content_div').addClass('d-none');
                        $('#text_content_div').removeClass('d-block');

                        $('#video_file_div').addClass('d-none');
                        $('#video_file_div').removeClass('d-block');

                    } else if (optionValue == 'iFrame') {
                        $('#video_url_div').addClass('d-none');
                        $('#video_url_div').removeClass('d-block');

                        $('#duration_div').removeClass('d-none');
                        $('#duration_div').addClass('d-block');

                        $('#iframe_div').removeClass('d-none');
                        $('#iframe_div').addClass('d-block');

                        $('#text_content_div').addClass('d-none');
                        $('#text_content_div').removeClass('d-block');

                        $('#video_file_div').addClass('d-none');
                        $('#video_file_div').removeClass('d-block');

                    } else if (optionValue == 'Text Content') {

                        $('#video_url_div').addClass('d-none');
                        $('#video_url_div').removeClass('d-block');

                        $('#duration_div').removeClass('d-block');
                        $('#duration_div').addClass('d-none');

                        $('#iframe_div').addClass('d-none');
                        $('#iframe_div').removeClass('d-block');

                        $('#text_content_div').removeClass('d-none');
                        $('#text_content_div').addClass('d-block');

                        $('#video_file_div').addClass('d-none');
                        $('#video_file_div').removeClass('d-block');
                    } else if (optionValue == 'Video File') {

                        $('#video_url_div').addClass('d-none');
                        $('#video_url_div').removeClass('d-block');

                        $('#duration_div').removeClass('d-none');
                        $('#duration_div').addClass('d-block');

                        $('#iframe_div').addClass('d-none');
                        $('#iframe_div').removeClass('d-block');

                        $('#text_content_div').addClass('d-none');
                        $('#text_content_div').removeClass('d-block');

                        $('#video_file_div').removeClass('d-none');
                        $('#video_file_div').addClass('d-block');
                    }
                });
            }).change();
        });
    </script>

    <script>
        var Dropzones = function () {
            var e = $('[data-toggle="dropzone1"]'), t = $(".dz-preview");
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            e.length && (Dropzone.autoDiscover = !1, e.each(function () {
                var e, a, n, o, i;
                e = $(this), a = void 0 !== e.data("dropzone-multiple"), n = e.find(t), o = void 0, i = {
                    url: "{{route('chapters.store',[$course_id,$header_id])}}",
                    headers: {
                        'x-csrf-token': CSRF_TOKEN,
                    },
                    thumbnailWidth: 100,
                    thumbnailHeight: 100,
                    previewsContainer: n.get(0),
                    previewTemplate: n.html(),
                    maxFiles: 10,
                    parallelUploads: 10,
                    autoProcessQueue: false,
                    uploadMultiple: true,
                    success: function (file, response) {
                        if (response.flag == "success") {
                            toastrs('success', response.msg, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            toastrs('Error', response.msg, 'error');
                        }
                    },
                    error: function (file, response) {
                        if (response.error) {
                            toastrs('Error', response.error, 'error');
                        } else {
                            toastrs('Error', response, 'error');
                        }
                    },
                    init: function () {
                        var myDropzone = this;

                        this.on("addedfile", function (e) {
                            !a && o && this.removeFile(o), o = e
                        })
                    }
                }, n.html(""), e.dropzone(i)
            }))
        }();

        $(document).on('click', '#chapter_create_submit', function () {

            var fd = new FormData();
            var file = document.getElementById('video_file').files[0];
            if (file) {
                fd.append('video_file', file);
            }
            var files = $('[data-toggle="dropzone1"]').get(0).dropzone.getAcceptedFiles();

            $.each(files, function (key, file) {
                fd.append('multiple_files[' + key + ']', $('[data-toggle="dropzone1"]')[0].dropzone.getAcceptedFiles()[key]); // attach dropzone image element
            });
            var other_data = $('#frmTarget').serializeArray();
            var valid = this.form.checkValidity();
            $.each(other_data, function (key, input) {
                fd.append(input.name, input.value);
            });


            $.ajax({
                url: "{{route('chapters.store',[$course_id,$header_id])}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function (data) {
                    if (data.flag == "success") {
                        toastrs('success', data.msg, 'success');
                        setTimeout(function() {
                                location.reload();
                            }, 1500);
                    } else {
                        toastrs('Error', data.msg, 'error');
                    }
                },
                error: function (data) {

                    if (data.error) {
                        toastrs('Error', data.error, 'error');
                    } else {
                        toastrs('Error', data, 'error');
                    }
                },
            });
        });
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div id="account_edit" class="tabs-card">
                <div class="card ">
                    <div class="card-body">
                        {{Form::open(array('method'=>'post','id'=>'frmTarget','enctype'=>'multipart/form-data'))}}
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {{Form::label('name',__('Chapter Name'),['class'=>'form-label'])}}
                                    {!! Form::text('name',null,array('class'=>'form-control font-style','required'=>'required'))!!}
                                </div>
                                <div class="form-group col-md-12 col-lg-12">
                                    {{Form::label('chapter_description',__('Chapter Description'),['class'=>'form-label'])}}
                                    <textarea class="form-control summernote" name="chapter_description" id="exampleFormControlTextarea1" rows="3" required></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    {{Form::label('type',__('Chapter Type'),['class'=>'form-label'])}}
                                    {!! Form::select('type',$chapter_type,null,array('class'=>'form-control','id'=>'type')) !!}
                                </div>

                                <div class="form-group col-md-6" id="video_url_div">
                                    {{Form::label('video_url',__('Video URL'),['class'=>'form-label'])}}
                                    {{Form::text('video_url',null,array('class'=>'form-control font-style'))}}
                                </div>
                                <div class="form-group col-lg-6" id="video_file_div">
                                    <div class="form-group">
                                        <div class="field" data-name="attachments">
                                            <div class="attachment-upload">
                                                <div class="attachment-button">
                                                    <div class="pull-left">
                                                        {{Form::label('video_file',__('Video File'),['class'=>'form-label'])}}
                                                        <input type="file" name="video_file" id="video_file" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="attachments"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6" id="iframe_div">
                                    {{Form::label('iframe',__('iFrame'),['class'=>'form-label'])}}
                                    {{Form::text('iframe',null,array('class'=>'form-control font-style'))}}
                                </div>
                                <div class="form-group col-md-6" id="duration_div">
                                    {{Form::label('duration',__('Duration'),['class'=>'form-label'])}}
                                    {{Form::text('duration',null,array('class'=>'form-control font-style'))}}
                                </div>
                                <div class="form-group col-md-12 col-lg-12" id="text_content_div">
                                    {{Form::label('text_content',__('Text Content'),['class'=>'form-label'])}}
                                    <textarea class="form-control summernote" name="text_content" id="exampleFormControlTextarea2" rows="3"></textarea>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        {{Form::label('content',__('External File'),array('class'=>'form-label')) }}
                                        <div class="dropzone dropzone-multiple" data-toggle="dropzone1" data-dropzone-url="http://" data-dropzone-multiple>
                                            <div class="fallback">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="dropzone-1" name="file" multiple>
                                                    <label class="custom-file-label" for="customFileUpload">{{__('Choose file')}}</label>
                                                </div>
                                            </div>
                                            <ul class="dz-preview dz-preview-multiple list-group list-group-lg list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <div class="avatar">
                                                                <img class="rounded" src="" alt="Image placeholder" data-dz-thumbnail>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h6 class="text-sm mb-1" data-dz-name>...</h6>
                                                            <p class="small text-muted mb-0" data-dz-size></p>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="action-btn bg-danger ms-2">
                                                                <a href="#" class="mx-3 btn btn-sm  align-items-center" data-dz-remove>
                                                                    <i class="ti ti-trash text-white"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 text-right text-end float-end">
                                        <input type="button" id="chapter_create_submit" value="{{ __('Create') }}" class="btn btn-primary btn-block btn-submit">
                                    </div>
                                </div>

                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
