@push('css')
<style type="text/css">
.action-all-btn{
	background:#f8f9fa !imporatant;
}
.action-all-btn{
	background:#f8f9fa !imporatant;
}
.list-group-item-action i {
	font-size: 16px;
	margin-right: 3px;
}
</style>
@endpush
<div class="col-xl-3">
    <a href="{{ route('mailbox.create') }}" class="btn btn-primary text-white mb-2 w-100"><i
            class="ti ti-edit"></i>{{ __('Compose') }}</a>
    <div class="card sticky-top " style="top:30px">
        <div class="list-group list-group-flush" id="useradd-sidenav">
            <a href="{{ route('mailbox.index', 'inbox') }}"
                class="list-group-item list-group-item-action @if (request()->segment(count(request()->segments())) == 'inbox' || request()->segment(count(request()->segments())) == 'index') active @endif "><i
                    class="ti ti-inbox"></i>{{ __('Inbox') }}
                <div class="float-end"></div>
            </a>
            <a href="{{ route('mailbox.index', 'starred') }}"
                class="list-group-item list-group-item-action @if (request()->segment(count(request()->segments())) == 'starred') active @endif ">
                <i class="ti ti-star"></i>{{ __('Starred') }}
                <div class="float-end"></div>
            </a>
            <a href="{{ route('mailbox.index', 'sent') }}"
                class="list-group-item list-group-item-action @if (request()->segment(count(request()->segments())) == 'sent') active @endif">
                <i class="ti ti-send"></i>{{ __('Sent') }}
                <div class="float-end"></div>
            </a>
            <a href="{{ route('mailbox.index', 'drafts') }}"
                class="list-group-item list-group-item-action @if (request()->segment(count(request()->segments())) == 'drafts') active @endif">
                <i class="ti ti-file"></i>{{ __('Draft') }}
                <div class="float-end"></div>
            </a>
			<a href="{{ route('mailbox.index', 'spam') }}"
                class="list-group-item list-group-item-action @if (request()->segment(count(request()->segments())) == 'spam') active @endif">
                <i class="ti ti-alert-octagon"></i>{{ __('Spam') }}
                <div class="float-end"></div>
            </a>
            <a href="{{ route('mailbox.index', 'trash') }}"
                class="list-group-item list-group-item-action @if (request()->segment(count(request()->segments())) == 'trash') active @endif">
                <i class="ti ti-trash"></i> {{ __('Trash') }}
                <div class="float-end"></div>
            </a>
			<a href="{{ route('mailbox.index', 'archive') }}"
                class="list-group-item list-group-item-action @if (request()->segment(count(request()->segments())) == 'archive') active @endif">
                <i class="ti ti-archive"></i> {{ __('Archive') }}
                <div class="float-end"></div>
            </a>

        </div>

    </div>
</div>
