<div class="container">
    @if (count($errors) > 0)
        @foreach($errors->all() as $error)
            <div class="alert alert-danger mt-5 alert-dismissible" role="alert">
                <div>
                    <strong>Error!</strong> {{ $error }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach
    @endif

    @if (session('success'))
        <div class="alert alert-success mt-5 alert-dismissible" role="alert">
            <div>
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('message'))
        <div class="alert alert-warning alert-dismissible mt-5" role="alert">
            <div>
                <strong>Warning!</strong> {{ session('message') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible mt-5" role="alert">
            <div>
                <strong>Error!</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<!-- Include these Bootstrap SVG icons if not already present -->
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8.982 1.566a1 1 0 0 1 1.036 0l6.857 3.964a1 1 0 0 1 0 1.732l-6.857 3.964a1 1 0 0 1-1.036 0l-6.857-3.964a1 1 0 0 1 0-1.732l6.857-3.964z"/>
    <path d="M4.479 3.94a1 1 0 0 1 1.017-.03l2.07 1.137c.33.183.563.5.623.88a1.99 1.99 0 0 0-.43.109l-2.07-1.136a1 1 0 0 1-.21-1.96zm4.986 4.86a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
  </symbol>
  <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zm3.49 5.02a.5.5 0 0 0-.748-.064L7.493 9.197 5.758 7.474a.5.5 0 0 0-.707.708l2.5 2.5a.5.5 0 0 0 .707 0l4.3-5.9a.5.5 0 0 0 .064-.748z"/>
  </symbol>
  <symbol id="exclamation-circle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM5.982 5a.5.5 0 0 1 1 0v3a.5.5 0 0 1-1 0V5zm.5 5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
  </symbol>
</svg>
