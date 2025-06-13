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
          <div class="form-group">
            <label for="scheduled_date">Select Date to Send:</label>
            <input type="date" name="scheduled_date" class="form-control" required min="{{ now()->toDateString() }}">
          </div>

          <div class="form-group">
            <label for="opened_date">Send to users who opened:</label>
            <input type="date" name="opened_date" class="form-control" min="{{ now()->toDateString() }}">
          </div>

          <div class="form-group">
            <label for="clicked_date">Send to users who clicked:</label>
            <input type="date" name="clicked_date" class="form-control" min="{{ now()->toDateString() }}">
          </div>

          <div class="form-group">
            <label for="not_opened_date">Send to users who did not open:</label>
            <input type="date" name="not_opened_date" class="form-control" min="{{ now()->toDateString() }}">
          </div>

          <div class="alert alert-info mt-3">
            Note: Each email will be sent only once per user, based on the selected criteria. For example, users who qualify for multiple actions (open, click, etc.) will not receive duplicate emails.
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Schedule</button>
        </div>
      </div>
    </form>
  </div>
</div>
