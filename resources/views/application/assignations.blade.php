@extends('layouts.app')
@section('content')
<h2 class="text-center mb-3">{{ __('actions.application.list') }}</h2>
<h3 class="text-center">{{
    __('vocabulary.calltype_short.'.$application->projectcall->typeLabel) }} :
    {{$application->projectcall->year}}
</h3>
<h4 class="text-center">{{$application->projectcall->title}}</h4>
<h5 class="text-center">
    <u>{{__('fields.projectcall.applicant') }} :</u>
    {{ $application->applicant->name }}
</h5>
<h5 class="text-center mb-4">
    <u>{{ __('fields.submission_date') }} :</u>
    {{ \Carbon\Carbon::parse($application->submitted_at)->format(__('locale.datetime_format'))}}
</h5>
<div class="row justify-content-center">
    <table class="table table-striped table-hover table-bordered w-100" id="assignation_list">
        <thead>
            <tr>
                <th>{{ __('fields.id') }}</th>
                <th>{{ __('fields.offer.expert') }}</th>
                <th>{{ __('fields.status') }}</th>
                <th>{{ __('fields.creation_date') }}</th>
                <th data-orderable="false">{{ __('fields.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @php($total=0)
            @foreach($application->offers as $offer)
            <tr>
                <td>{{ $offer->id}}</td>
                <td>{{ $offer->expert->name }}</td>
                <td>
                    @if($offer->accepted === true)
                        @svg('solid/check', 'icon-fw text-success')
                        {{ __('fields.offer.accepted') }}
                        @php($total++)
                    @elseif($offer->accepted === false)
                        @svg('solid/times', 'icon-fw text-danger')
                        {{ __('fields.offer.declined') }}
                    @else
                        @svg('solid/question', 'icon-fw text-primary')
                        {{ __('fields.offer.pending') }}
                        @php($total++)
                    @endif
                </td>
                <td>
                    {{ $offer->creator->name}}<br/>
                    {{ \Carbon\Carbon::parse($offer->created_at)->format(__('locale.datetime_format'))}}
                </td>
                <td>
                    <a href="{{ route('application.unassign',[$offer->id])}}" class="btn btn-sm btn-danger d-block delete-link">
                        @svg('solid/times', 'icon-fw') {{ __('actions.cancel') }}
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if($total < $application->projectcall->number_of_experts)
    <div class="row justify-content-center">
        <div class="col-6 jumbotron">
            <h4 class="text-center pb-2">{{ __('actions.application.assign_expert') }}</h4>
            @if(count($experts) > 0)
                <form method="POST" action="{{ route('application.assign', $application->id) }}" id="assignation_form">
                @csrf @method("POST")
                    @include('forms.select', [
                        'name' => 'expert_id',
                        'label' => __('fields.application.experts'),
                        'allowedValues' => $experts,
                        'allowNone' => false,
                        'allowNew' => false,
                        'multiple' => false,
                        'displayField' => 'name',
                        'valueField' => 'id'
                    ])
                    <p class="text-center">
                        <button type="submit" name="save" class="btn btn-primary">@svg('solid/plus') {{ __('actions.add') }}</button>
                    </p>
                </form>
            @else
                <p class="text-danger text-center">
                    {{ __('fields.offer.no_experts') }}
                </p>
            @endif
        </div>
    </div>
@endif
@include('partials.back_button', ['url' => route('projectcall.applications', ['projectcall' => $application->projectcall->id])])

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('actions.confirm_delete.title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{__('actions.close')}}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ __('actions.confirm_delete.body') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('actions.cancel') }}</button>
                <form id="confirmation-form" action="" method="post">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger" type="submit">{{ __('actions.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.delete-link').click(function (e) {
            e.preventDefault();
            var targetUrl = jQuery(this).attr('href');
            $("form#confirmation-form").attr('action', targetUrl);
            $(".modal#confirm-delete").modal();
        });
    });

</script>
@endpush