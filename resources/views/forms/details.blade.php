@extends('admin.master')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Manage Campaigns</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="/">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('campaign') }}">View Campaigns</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('campaign.details', ['id' => $campaign->id]) }}">Campaign Details</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th>Campaign Nddame</th>
                        <th>Subject Line</th>
                        <th>No of Email Addresses Reached</th>
                        <th>Date of Sending</th>
                        <th>No of Viewed Emails</th>
                        <th>View Sending Details</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($histories)> 0)
                        @foreach ($histories as $history)
                            <tr>
                                <td>{{ $campaign->campaign_name }}</td>
                                <td>{{ $campaign->subject_line }}</td>
                                <td>{{ $history->email_count }}</td>
                                <td>{{ $history->date_sent ? $history->date_sent : "-" }}</td>
                                <td>{{ $history->email_open_count }}</td>
                                <td>
                                    <button 
                                        type="button" 
                                        class="btn btn-success px-2 py-1 py-xl-2 px-xl-3" 
                                        data-toggle="modal" 
                                        data-target="#emailModal-{{ $history->id }}"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">No data found</td>
                        </tr>
                    @endif
                        @if (count($test_emails) > 0)
                            @foreach ($test_emails as $item)
                                <tr>
                                    <td>
                                        <p class="m-0">
                                            <span class="badge badge-secondary">Test</span>
                                            {{ $campaign->campaign_name }}
                                        </p>
                                    </td>
                                    <td>{{ $campaign->subject_line }}</td>
                                    <td>{{ 1 }}</td>
                                    <td>{{ $item->date_sent }}</td>
                                    <td>
                                        @if ($item->is_opened == '1')
                                            <i class="fa fa-check text-success"></i>
                                        @else
                                            <i class="fa fa-times text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item->email }}</td>
                                </tr>
                            @endforeach
                        @endif
                </tbody>
            </table>
        </div>

        {{-- @if (!empty($campaign->failed_emails))
            <div class="mt-4">
                <h3 class="fw-bold mb-3">Failed Emails</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($campaign->failed_emails as $key => $failedEmail)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $failedEmail }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif --}}
    </div>
</div>

@if (count($histories)> 0)
    @foreach ($histories as $history)
<!-- Email -->
    <div class="modal fade modal-lg" id="emailModal-{{ $history->id }}" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{ $campaign->campaign_name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table ">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Email</th>
                                    <th>Email Sent</th>
                                    <th>Email Opened</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($history->email_details) > 0)
                                @foreach ($history->email_details as $key => $emailDetail)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $emailDetail['email'] }}</td>
                                    <td>
                                        @if ($emailDetail['sent_status'] == '1')
                                            <i class="fa fa-check text-success"></i>
                                        @else
                                            <i class="fa fa-times text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($emailDetail['opened_status'] == '1')
                                            <i class="fa fa-check text-success"></i>
                                        @else
                                            <i class="fa fa-times text-danger"></i>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="2">No data found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer d-flex flex-wrap ">
                    <a href="{{ route('campaign.download.sent', $history->id) }}" class="btn btn-success">Download Sent Emails</a>
                    <a href="{{ route('campaign.download.clicked', $history->id) }}" class="btn btn-success">Download Clicked Emails</a>
                    <a href="{{ route('campaign.download.opened', $history->id) }}" class="btn btn-primary">Download Opened Emails</a>
                    <a href="{{ route('campaign.download.unopened', $history->id) }}" class="btn btn-warning">Download Unopened Emails</a>
                    <button class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
                
            </div>
        </div>
    </div>
    @endforeach
@endif
@endsection