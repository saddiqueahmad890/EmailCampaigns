<div class="modal fade" id="scheduleEmailModal" tabindex="-1" role="dialog" aria-labelledby="scheduleEmailModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('email-campaign.schedule') }}" method="POST">
      @csrf
      <input type="hidden" name="id" value="{{ $campaign->id }}">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Schedule Email</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <label for="scheduled_date">Select Date to Send:</label>
          <input type="date" name="scheduled_date" class="form-control" required min="{{ now()->toDateString() }}">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Schedule</button>
        </div>
      </div>
    </form>
  </div>
</div>
