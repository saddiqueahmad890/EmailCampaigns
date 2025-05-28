@include('modals.schedule-email-modal')
<div>

    <div class="card-title pt-3 fs-4">Send Email ({{ count($campaign->emails) }})</div>

    <div style="height:fit-content; max-height: 210px; overflow-y:scroll">
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($campaign->emails) > 0)
                    @foreach ($campaign->emails as $key=>$email)
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

    <div class="card-title pt-3 fs-4">Email Preview</div>
    <div>
        <iframe src="{{ route('campaign.html', $campaign->id) }}" width="100%" height="400px"></iframe>
    </div>

    <div class="card-title pt-3 fs-4">Subject</div>
    <div class="card-title p-3 bg-gray1 rounded-1">{{ $campaign->subject_line }}</div>

    <div class="card-title py-3 fs-4">Send Test Email</div>
    <div class="">
        <button
            type="button"
            class="btn btn-secondary"
            data-toggle="modal"
            data-target="#sendEmailModal">
            Send Email
        </button>
    </div>

    <div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Send Test Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('email.test') }}" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <input type="hidden" name="id" value="{{ $campaign->id }}">
                                    <label for="email">Enter Email Address</label>
                                    <br>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="card-title py-3 fs-4">Send Campaign Email</div>
    @if ($hasHistory)
    <button
        type="button"
        class="btn btn-success"
        data-toggle="modal"
        data-target="#campaignAlert">
        Send Campaign Email
    </button>
    <!-- Schedule Email Button -->
    <button
        type="button"
        class="btn btn-info ml-2"
        data-toggle="modal"
        data-target="#scheduleEmailModal">
        Schedule Email
    </button>
    @else
    <form action="{{ route('send.emails') }}" method="POST" enctype="multipart/form-data" class="">
        @csrf
        <input type="hidden" name="id" value="{{ $campaign->id }}">
        <button type="submit" class="btn btn-success">Send Campaign Email</button>
        <!-- Schedule Email Button -->
        <button
            type="button"
            class="btn btn-info ml-2"
            data-toggle="modal"
            data-target="#scheduleEmailModal">
            Schedule Email
        </button>
    </form>
    @endif


    <div class="modal fade" id="campaignAlert" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Attenzione</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('send.emails') }}" method="POST" enctype="multipart/form-data" class="">
                    <div class="modal-body">
                        <p>
                            <strong>{{ count($sentEmails) }}</strong> contatti sono stati già contattati in questa campagna, sei sicuro di volerli ricontattare tutti?
                            Ci sono <strong>{{ count($emailsToSend) }}</strong> contatti in questa campagna che non sono stati contattati
                        </p>
                        @csrf
                        <input type="hidden" name="id" value="{{ $campaign->id }}">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="send_option" id="exampleRadios1" value="2" checked>
                            <label class="form-check-label" for="exampleRadios1">
                                Ricontatta tutti
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="send_option" id="exampleRadios2" value="1">
                            <label class="form-check-label" for="exampleRadios2">
                                Contatta i nuovi
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-success">Send Campaign Email</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>