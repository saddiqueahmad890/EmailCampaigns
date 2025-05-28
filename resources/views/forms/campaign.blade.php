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
            <a href="{{ route('campaign') }}">View Campaigns</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-light">
                    <tr>
                     
                        <th>Campaign Name</th>
                        <th>Subject Line</th>
                        <th>No of Email Addresses</th>
                        <th>Email Template Filename</th>
                        <th>CSV Filename</th>
                        <th>View Emails</th>
                        <th>View HTML</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($campaigns) > 0)
                    @foreach ($campaigns as $item)
                    <tr>
                       
                        <td>
                            <a href="{{ route('campaign.details', ['id' => $item->id]) }}">
                                {{ $item->campaign_name }}
                            </a>
                        </td>
                        <td>{{ $item->subject_line }}</td>
                        <td>{{ $item->email_count }}</td>
                        <td 
                        style="
                            max-width: 70px;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                        "
                        >
                            {{ $item->email_body }}
                        </td>
                        <td 
                        style="
                            max-width: 100px;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                        ">
                            {{ $item->csv_file }}
                        </td>
                        <td>
                            <button type="button" class="btn btn-success px-2 py-1 py-xl-2 px-xl-3" data-toggle="modal" \
                                data-target="#emailModal-{{ $item->id }}">
                                View
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-success px-2 py-1 py-xl-2 px-xl-3" data-toggle="modal"
                                data-target="#htmlModal-{{ $item->id }}">
                                View
                            </button>
                        </td>
                        <td>
                            <div class="d-flex justify-content-start align-items-center gap-2">
                                <i class="far fa-edit p-3" style="cursor: pointer" data-toggle="modal"
                                    data-target="#editCampaingModal-{{ $item->id }}"></i>
                                <form method="POST" action="{{ route('delete-campaign', ['id' => $item->id]) }}"
                                    id="deleteForm-{{ $item->id }}">
                                    @csrf
                                    @method('DELETE')
    
                                    <i class="fas fa-trash p-3" style="cursor: pointer"
                                        onclick="confirmDelete({{ $item->id }})"></i>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="8">No data found</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@if (count($campaigns) > 0)
@foreach ($campaigns as $item)
<!-- Edit Campaign Modal -->
<div class="modal fade" id="editCampaingModal-{{ $item->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Edit Campaign</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('edit-campaign' , ['id' => $item->id])}}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <input type="hidden" data-campaign-id="{{ $item->id }}" value="{{ $item->id }}">
                                <label for="campaign">New Campaign Name</label>
                                <input type="text" class="form-control" data-campaign-name="{{ $item->id }}" 
                                       name="campaign_name" value="{{ $item->campaign_name }}" 
                                       placeholder="Enter new campaign name" />
                                <p data-error-content="{{ $item->id }}" class="text-danger --bs-danger"></p>
                            </div>

                            <div class="form-group">
                                <label for="leads-csv">Add New Leads to CSV
                                </label>
                                <br>
                                <input type="file" accept=".csv" name="leads_csv" class="form-control-file" id="leads-csv" value="{{ $item->leads_csv }}"/>
                            </div>

                            <div class="form-group">
                                <label for="index-number">Index Column Number</label>
                                <input type="number" class="form-control" name="column_number" id="index-number"
                                    placeholder="Enter Number"  />
                            </div>

                            <div class="form-group">
                                <label for="subject">New Subject Line</label>
                                <input type="text" class="form-control" id="subject" value="{{ $item->subject_line }}"
                                    name="subject_line" placeholder="Enter Subject Line" />
                            </div>

                            <div class="form-group">
                                <label for="email-file">New Email Template (HTML)
                                </label>
                                <br>
                                <input type="file" name="email_body" class="form-control-file" id="email-file" accept=".html" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Email -->
<div class="modal fade" id="emailModal-{{ $item->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ $item->campaign_name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($item->emails) > 0)
                            @foreach ($item->emails as $key=>$email)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $email }}</td>
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
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="htmlModal-{{ $item->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ $item->campaign_name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="{{ route('campaign.html', $item->id) }}" width="100%" height="400px"></iframe>
                {{-- <iframe src="{{ $item->html_file_path }}" width="100%" height="400px"></iframe> --}}
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection

@section('scripts')
{{-- Script for campaign name validation --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-campaign-name]').forEach((inputElement) => {
            inputElement.addEventListener('keyup', function () {
                const campaignId = inputElement.getAttribute('data-campaign-name');
                const campaignName = inputElement.value;
                const errorDisplay = document.querySelector(`[data-error-content="${campaignId}"]`);

                fetch('/check-campaign', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ campaign_name: campaignName, id: campaignId })
                })
                .then(response => response.json())
                .then(data => {
                    // Display error if campaign name is taken
                    if (data.exists) {
                        errorDisplay.textContent = 'Campaign Name is already taken.';
                    } else {
                        errorDisplay.textContent = '';
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>

{{-- Script for remove campaign confirmation --}}
<script>
    function confirmDelete(Id) {
          if (confirm("Are you sure you want to delete this campaign?")) {
              document.getElementById('deleteForm-' + Id).submit();
          }
      }
</script>
@endsection