@extends('layouts.main')
@section('page-title')
    {{ __('EMail Box') }}
@endsection
@section('page-breadcrumb')
    {{ __('EMail Box') }}
@endsection

@push('css')
<style type="text/css">
.text-dark {
    font-weight: bolder;
}
.col-md-6{
    width: 100% !important;
}
.mail_attachment{
	max-width: 150px;
    max-height: 150px;
    width: auto;
    height: auto;
}
</style>
@endpush
 @php
$flag_arr = $message->getFlags();
@endphp
@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                @include('mailbox::layouts.sidebar')
                <div class="col-xl-9">
                    <div id="mail-inbox" class="">
                        <div class="card">
                           	<div class="card-header pb-0">
								<div class="row">
                                  	<div class="col-lg-9">
										<p class="text-dark mb-0">{{ $message->getFrom()[0]->mailbox }}</p>
										<p class="text-mute"><span>{{('to')}} </span>{{ $message->getTo()[0]->mailbox }}</p>
									</div>
									<div class="col-lg-2 text-end">
										<p class="mb-0"><span class="text-mute">{{ $message->getDate() }} </span></p>
									</div>
									<div class="col-lg-1 text-end">
										<span class="action-btn action" style="margin:0px !important" data-action="@if ($flag_arr->has('flagged') || $flag_arr->has('Flagged'))  unstarred @else starred @endif"data-id="{{ $message->getMsgn() }}"><i class="@if ($flag_arr->has('flagged')|| $flag_arr->has('Flagged')) ti ti-star text-warning @else ti ti-star text-secondary @endif" data-bs-toggle="tooltip" title="" data-original-title="@if ($flag_arr->has('flagged') || $flag_arr->has('Flagged')) {{__('Starred')}} @else {{__('Not starred')}} @endif" data-bs-original-title="@if ($flag_arr->has('flagged') || $flag_arr->has('Flagged')) {{__('Starred')}} @else {{__('Not starred')}} @endif"></i></span>
									</div>
								</div>
							</div>
                            <div class="card-body ">
                                <div class="row">
									<div class="col-lg-12 @if(!empty($message->getHTMLBody(true))) text-center @endif mb-4">
										@if(empty($message->getHTMLBody(true)))
										{{$message->getTextBody()}}
										@else
										{!!($message->getHTMLBody(true))!!}
										@endif
									</div>
									<hr>
									@php
									$attachments=$message->getAttachments();
									
									@endphp
									<div class="row">
										@if($message->hasAttachments()==1)
										<div class="col-md-12 text-center"><h5>{{__('Attachments')}}</h5></div>
										@endif
									@foreach ($attachments as $attachment)
										<div class="col-md-2 text-center bg-light m-2">
											 
										@if (strpos($attachment->getContentType(), 'image/') !== false)
											<p>{{ $attachment->getFilename() }}</p>
											<img  src="data:{{ $attachment->getContentType() }};base64,{{ base64_encode($attachment->getContent()) }}" alt="{{ $attachment->getFilename() }}" class="mail_attachment m-auto"/>
										@elseif (strpos($attachment->getContentType(), 'application/pdf') !== false)
											<embed src="data:{{ $attachment->getContentType() }};base64,{{ base64_encode($attachment->getContent()) }}" type="{{ $attachment->getContentType() }}" width="100%" height="500px" />
										@else
											<a href="" class="m-auto">{{ $attachment->getFilename() }}</a>
										@endif
											</div>
									@endforeach
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script type="text/javascript">

	$(".action-btn").on('click', function() {
			var currentUrl = window.location.href;
			var urlSegments = currentUrl.split('/');
			var lastElement = urlSegments[urlSegments.length - 1];
			var checkedValues = [];
            var token = $('meta[name="csrf-token"]').attr('content');
            checkedValues.push($(this).attr('data-id'));
            var temp = $(this);
            var action = $(this).attr('data-action');
			 action= action.trim();
            $.ajax({
                type: 'post',
                url: '{{ url("/mailbox/action")}}',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': checkedValues,
                    'action': action,
					'folder':lastElement
                },
                success: function(data) {
					if (data.status == 1) {
                       
					   if (action == 'starred') {

						   var html = '<i class="ti ti-star text-warning"></i>';
						   temp.html(html);
						   temp.attr('data-action', 'unstarred');
						   toastrs('success',data.msg, 'success');

					   } else if (action == 'seen') {
						   var html = '<i class="ti ti-mail text-info"></i>';
						   temp.html(html);
						   temp.attr('data-action', 'unseen');
						   toastrs('success',data.msg, 'success');
					   } else {}
				   } else if (data.status == 0) {
					  
					   if (action == 'unstarred') {
						   var html = '<i class="ti ti-star text-secondary"></i>';
						   temp.html(html);
						   temp.attr('data-action', 'starred');
						   toastrs('error',data.msg, 'error');
					   } else if (action == 'unseen') {
						   
						   var html = '<i class="ti ti-mail-opened text-secondary"></i>';
						   temp.html(html);
						   temp.attr('data-action', 'seen');
						   toastrs('error',data.msg, 'error');
					   } else {}
				   } else {
					   toastrs('error',data.msg, 'error');
				   }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors here, if needed
                }
            });
        });
    </script>
@endpush
