@php
 $company_settings = getCompanyAllSetting();   
@endphp
<div class="card" id="mailbox-sidenav">
    {{ Form::open(array('route' => 'mailbox.setting.store', 'enctype' => "multipart/form-data")) }}
    <div class="card-header">
        <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-10">
                <h5 class="">{{ __('EMail Box') }}</h5>
                <small>{{ __('Edit your EMail Box company_settings') }}</small>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 text-end">
                <div class="form-check form-switch custom-switch-v1 float-end">
                    <input type="checkbox" name="mailbox_is_on" class="form-check-input input-primary" id="mailbox_is_on"
                        {{isset($company_settings['mailbox_is_on']) && $company_settings['mailbox_is_on'] == 'on' ? ' checked ' : '' }}>
                    <label class="form-check-label" for="mailbox_is_on"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-6">
                <label class="form-label ">{{ __('IMAP Server') }}</label> <br>
                <input class="form-control mailbox_class" placeholder="{{ __('Enter IMAP Server') }}" name="mailbox_imap_server" type="text" value="{{ isset($company_settings['mailbox_imap_server']) ? $company_settings['mailbox_imap_server'] : '' }}" id="mailbox_imap_server"   {{ isset($company_settings['mailbox_is_on']) && $company_settings['mailbox_is_on']=='on'  ? '' : 'disabled' }}>
            </div>
            <div class="form-group col-md-6">
                <label class="form-label ">{{ __('IMAP Port') }}</label> <br>
                <input class="form-control mailbox_class" placeholder="{{ __('Enter IMAP Port') }}" name="mailbox_imap_port" type="text" value="{{ isset($company_settings['mailbox_imap_port']) ? $company_settings['mailbox_imap_port'] : '' }}" id="mailbox_imap_port"   {{ isset($company_settings['mailbox_is_on']) && $company_settings['mailbox_is_on']=='on'  ? '' : 'disabled' }}>
            </div>
            <div class=" form-group col-md-12">
                <label class="mailbox-label col-form-label" for="mailbox_mode">{{ __('Encryption') }}</label> <br>
                <div class="d-flex">
                    <div class="mr-2">
                        <div class="p-3">
                            <div class="form-check">
                                <label class="form-check-labe text-dark">
                                    <input type="radio" name="company_encryption_mode" value="tls" class="form-check-input"
                                        {{ isset($company_settings['company_encryption_mode']) && $company_settings['company_encryption_mode'] == 'tls' ? 'checked="checked"' : '' }}>

                                    {{ __('TLS') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mr-2">
                        <div class="p-3">
                            <div class="form-check">
                                <label class="form-check-labe text-dark">
                                    <input type="radio" name="company_encryption_mode" value="ssl" class="form-check-input"
                                    {{ isset($company_settings['company_encryption_mode']) && $company_settings['company_encryption_mode'] == 'ssl' ? 'checked="checked"' : '' }}>
                                    {{ __('SSL') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <input class="btn btn-print-invoice  btn-primary m-r-10" type="submit" value="{{ __('Save Changes') }}">
    </div>
    {{Form::close()}}
</div>

    <script>
        $(document).on('click', '#mailbox_is_on', function() {
            if ($('#mailbox_is_on').prop('checked')) {
                $(".mailbox_class").removeAttr("disabled");
            } else {
                $('.mailbox_class').attr("disabled", "disabled");
            }
        });
    </script>
