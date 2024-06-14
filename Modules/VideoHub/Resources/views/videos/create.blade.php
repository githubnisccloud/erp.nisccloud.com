{{ Form::open(['route' => ['videos.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('title', __('Title'), ['class' => 'col-form-label']) }}
            {{ Form::text('title', null, ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Enter Video Title']) }}
        </div>
        <div class="form-group">
            <div class="btn-box">
                <label for="module_create"><b>{{ __('Module') }}</b></label>
                <select class="form-control module_create " name="module" id="module_create" tabindex="-1"
                    aria-hidden="true">
                    <option value="">{{ __('Select Module') }}</option>
                    @foreach ($modules as $module)
                        @foreach ($active_modules as $active_module)
                            @if (Module_Alias_Name($active_module) == $module)
                                <option value="{{ $module }}">
                                    {{ Module_Alias_Name($module) }}
                                </option>
                            @endif
                        @endforeach
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12" id="getfields"></div>
        <div class="col-12" id="relatedfields"></div>

        <div class="form-group mt-3">
            <span>{{ __('Things you want to upload?') }}</span><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="video_type" value="video_file" id="video_file"
                    data-name="video_file">
                <label class="form-check-label" for="video_file">
                    {{ 'Upload Video' }}
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="video_type" value="video_url" id="video_url"
                    data-name="video_url">
                <label class="form-check-label" for="video_url">
                    {{ 'Custom Video Link' }}
                </label>
            </div>
        </div>
        <div class="col-6 form-group">
            {{ Form::label('thumbnail', __('Thumbnail Image'), ['class' => 'form-label']) }}
            <div class="choose-file">
                <input class="custom-input-file custom-input-file-link  thumbnail1 d-none" onchange="showimagename()"
                    type="file" name="thumbnail" id="file-7" multiple="">
                <label for="file-7">
                    <button type="button" onclick="selectFile('thumbnail1')" class="btn btn-primary"><svg
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-upload me-2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        {{ __('Choose a image...') }}</button>
                </label><br>
                <span class="uploaded_image_name"></span>
            </div>
        </div>
        <div class="form-group col-6 video_file">
            {{ Form::label('video', __('Upload Video'), ['class' => 'form-label']) }}
            <div class="choose-file">
                <input class="custom-input-file custom-input-file-link  video d-none" type="file" name="video"
                    id="file-6" onchange="showvideoname()" multiple="">
                <label for="file-6">
                    <button type="button" onclick="selectFile('video')" class="btn btn-primary"><svg
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-upload me-2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        {{ __('Choose a video...') }}</button>
                </label><br>
                <span style="color: red;">{{ __($mp4_msg) }}</span>
                <span class="uploaded_video_name"></span>
            </div>
        </div>
        <div class="form-group col-md-12 video_url d-none">
            {{ Form::label('video', __('Custom Video Link'), ['class' => 'form-label']) }}
            {{ Form::text('video', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter Video Link')]) }}
        </div>
        <div class="form-group">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('')]) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn  btn-primary']) }}
</div>
{{ Form::close() }}

{{-- Start Video & Thumbnail --}}
<script src="{{ asset('Modules/VideoHub/Resources/assets/custom/js/main.js') }}"></script>

<script>
    function showimagename() {
        var uploaded_image_name = document.getElementById('file-7');
        $('.uploaded_image_name').text(uploaded_image_name.files.item(0).name);
    };

    function showvideoname() {
        var uploaded_image_name = document.getElementById('file-6');
        $('.uploaded_video_name').text(uploaded_image_name.files.item(0).name);
    };
</script>
{{-- End Video & Thumbnail --}}

{{-- Start Module Selection --}}
<script>
    $(document).on("change", "#module_create", function() {
        var modules = $(this).val();
        $.ajax({
            url: '{{ route('videos.modules') }}',
            type: 'POST',
            data: {
                "module": modules,
            },
            success: function(data) {
                $('#getfields').empty();
                $('.sub_module_create').empty();
                $('#relatedfields').empty();
                var emp_selct = ` <lable class='form-label'><b>Sub Module</b></lable><select class="form-control sub_module_create" name="sub_module"
                                            placeholder="Select Sub Module">
                                            </select>`;
                $('#getfields').html(emp_selct);

                $('.sub_module_create').append(
                    '<option value="0"> {{ __('Select Sub Module') }} </option>');
                $.each(data, function(key, value) {
                    $('.sub_module_create').append('<option value="' + key + '">' + value +
                        '</option>');
                    if (!value) {
                        $('#getfields').empty();
                        field(key, 'nonSubModule');
                    }

                });

            },
        });
    });

    $(document).on("change", ".sub_module_create", function() {
        field($(this), 'subModule');
    });

    function field(sub_module, type) {
        (type == 'subModule') ? sub_module = sub_module.val(): sub_module = sub_module;

        $.ajax({
            url: '{{ route('videos.getfield') }}',
            type: 'POST',
            data: {
                "module": sub_module,
            },
            success: function(data) {
                $('#relatedfields').empty();
                $('#relatedfields').append(data.html)

            },
        });
    }
</script>
{{-- End Module Selection --}}
{{-- Start Video $ Url Select --}}
<script>
    $(document).ready(function() {
        $('input[name="video_type"][id="video_file"]').prop('checked', true);
        $('input[name="video_type"]').trigger("change");
    });
    $('input[name="video_type"]').change(function() {
        var radioValue = $('input[name="video_type"]:checked').val();
        // var video_file = $('.video_file');

        if (radioValue === "video_file") {
            $('.video_file').removeClass('d-none');
            $('.video_url').addClass('d-none');
        } else {
            $('.video_file').addClass('d-none');
            $('.video_url').removeClass('d-none');
        }
    });
</script>
{{-- End Video $ Url Select --}}
