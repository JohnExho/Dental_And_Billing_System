<style>
    /* Modal button transitions */
    .modal-footer .btn {
        transition: 
            background 0.4s ease-in-out,
            transform 0.4s ease-in-out,
            box-shadow 0.4s ease-in-out;
    }

    /* Success button (Restore) */
    .modal-footer .btn-success {
        background: #28a745;
        color: #fff;
        border: none;
        border-radius: 8px;
    }

    .modal-footer .btn-success:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    }

    .modal-footer .btn-success:active {
        background: #1e7e34;
        transform: translateY(2px) scale(0.98);
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    /* Warning button (Archive) */
    .modal-footer .btn-warning {
        background: #ffc107;
        color: #000;
        border: none;
        border-radius: 8px;
    }

    .modal-footer .btn-warning:hover {
        background: #e0a800;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    }

    .modal-footer .btn-warning:active {
        background: #d39e00;
        transform: translateY(2px) scale(0.98);
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    /* Cancel button */
    .modal-footer .btn-secondary {
        background: #e0e0e0;
        color: #333;
        border: none;
        border-radius: 8px;
    }

    .modal-footer .btn-secondary:hover {
        background: #c2c2c2;
        color: #000000;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    }

    .modal-footer .btn-secondary:active {
        background: #a0a0a0;
        color: #FFFEF2;
        transform: translateY(2px) scale(0.98);
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
</style>

{{-- Archive Patient Modal (for active patients) --}}
<div class="modal fade" id="archive-patient-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('process-archive-patient') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="patient_id" id="archive_patient_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title text-warning">
                        <i class="bi bi-archive"></i> Archive Patient
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to archive <strong id="archive_patient_name"></strong>?</p>
                    <p class="text-muted small">
                        <i class="bi bi-info-circle"></i> 
                        Archived patients will be hidden from the main list but can be restored later.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-archive"></i> Archive Patient
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Unarchive Patient Modal (for archived patients) --}}
<div class="modal fade" id="unarchive-patient-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('process-unarchive-patient') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="patient_id" id="unarchive_patient_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title text-success">
                        <i class="bi bi-arrow-clockwise"></i> Restore Patient
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Restore <strong id="unarchive_patient_name"></strong> to active patients?</p>
                    <p class="text-muted small">
                        <i class="bi bi-info-circle"></i> 
                        This patient will be visible in the main patients list again.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-arrow-clockwise"></i> Restore Patient
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>