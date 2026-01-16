<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">

                @if ($qr)
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $qr->qr_code) }}" alt="QR Code" class="img-fluid mb-3"
                            style="max-width: 250px">


                        <h5>QR Password:</h5>
                        <h4 class="fw-bold">{{ $qr->qr_password }}</h4>

                    </div>

                    <form action="{{ route('process-generate-qr') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100">
                            Regenerate QR Code
                        </button>
                    </form>
                @else
                    <p class="text-center text-muted">No QR Code yet.</p>

                    <form action="{{ route('process-generate-qr') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">
                            Generate QR Code
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>
