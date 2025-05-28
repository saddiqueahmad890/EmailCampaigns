@extends('admin.master')

@section('content')
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Manage Campaigns</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
          <a href="/"><i class="bi bi-house"></i></a>
        </li>
        <li class="breadcrumb-item">Manage Campaigns</li>
        <li class="breadcrumb-item active" aria-current="page">Create Campaigns</li>
      </ol>
    </nav>
  </div>

  <form action="{{ route('email-campaigns.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Email Campaign</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6 col-lg-4">
            <div class="mb-3">
              <label for="campaign" class="form-label">Campaign Name</label>
              <input type="text" class="form-control" id="campaign" name="campaign_name"
                placeholder="Enter Campaign Name" value="{{ old('campaign_name') }}" />
              <div id="error-content" class="text-danger small"></div>
            </div>

            <div class="mb-3">
              <label for="leads-csv" class="form-label">Leads CSV</label>
              <input type="file" name="leads_csv" class="form-control" id="leads-csv" accept=".csv" />
            </div>

            <div class="mb-3">
              <label for="index-number" class="form-label">Index Column Number</label>
              <input type="number" class="form-control" name="column_number" id="index-number"
                value="{{ old('column_number') }}" placeholder="Enter Number" />
            </div>

            <div class="mb-3">
              <label for="subject" class="form-label">Subject Line</label>
              <input type="text" class="form-control" id="subject" name="subject_line"
                value="{{ old('subject_line') }}" placeholder="Enter Subject Line" />
            </div>

            <div class="mb-3">
              <label for="email-file" class="form-label">Email Template (HTML)</label>
              <input type="file" name="email_body" class="form-control" id="email-file" accept=".html" />
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer d-flex justify-content-start gap-2">
        <button type="submit" class="btn btn-success">Submit</button>
        <a href="#" class="btn btn-secondary">Cancel</a>
      </div>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const campaignNameInput = document.getElementById('campaign');
    const errorDisplay = document.getElementById('error-content');

    campaignNameInput.addEventListener('keyup', function () {
      const campaign = campaignNameInput.value;

      fetch('/check-campaign', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ campaign_name: campaign })
      })
      .then(response => response.json())
      .then(data => {
        errorDisplay.textContent = data.exists ? 'Campaign Name is already taken.' : '';
      })
      .catch(error => console.error('Error:', error));
    });
  });
</script>
@endsection
