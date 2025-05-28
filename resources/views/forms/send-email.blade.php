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
            <a href="#">Manage Campaigns</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('forms.send-email') }}">Send Email</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Send Email</div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="">
                            <p>Emails left for today</p>
                            {{-- @dd($smtpAccounts); --}}
                            @if (count($smtpAccounts) > 0)
                                @foreach ($smtpAccounts as $key => $account)
                                    <p>SMTP Acc # {{ $key + 1 }} = {{ $account->emails_remaining_today }}</p>
                                @endforeach
                            @else
                                <p>No SMTP Acc found</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="leads-csv">Select Campaign
                            </label>
                            <br>
                            <select name="campaign_id" id="campaign-id" class="form-select">
                                <option value="">Select Campaign</option>
                                @foreach ($campaigns as $item)
                                    <option value="{{ $item->id }}" {{ session('campaign_id') == $item->id ? "selected" : "" }}>{{ $item->campaign_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-4" id="campaign-details"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Script for campaign name validation --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let campaignNameInput = document.getElementById('campaign-id');
        
        function fetchCampaign (){
            const campaignDetailsDiv = document.getElementById('campaign-details');
            const campaignId = campaignNameInput.value;

            if (campaignId) {
                fetch('/fetch-campaign', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ campaign_id: campaignId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("success" , data);
                        campaignDetailsDiv.innerHTML = data.result;
                    } else {
                        console.log("Error" , data);
                        campaignDetailsDiv.innerHTML = ``;
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        fetchCampaign();

        campaignNameInput.addEventListener('change', fetchCampaign);
    });

</script>
@endsection