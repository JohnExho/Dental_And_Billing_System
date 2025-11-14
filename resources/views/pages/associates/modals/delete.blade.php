<style>
    /* Apply smooth transition to modal footer buttons */
.modal-footer .btn {
    transition: 
        background 0.4s ease-in-out,
        transform 0.4s ease-in-out,
        box-shadow 0.4s ease-in-out;
}

/* Save Clinic button (btn-success) */
.modal-footer .btn-danger {
    background: #ff6d6d;
    color: #FFFEF2;
    border: none;
    border-radius: 8px;
}

/* Hover: slightly darker blue */
.modal-footer .btn-danger:hover {
    background: #ff3030;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

/* Active: pressed effect */
.modal-footer .btn-danger:active {
    background: #ffa8a8;
    transform: translateY(2px) scale(0.98);
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

/* Cancel button (btn-outline-secondary) */
.modal-footer .btn-secondary {
    background: #e0e0e0;
    color: #333;
    border: none;
    border-radius: 8px;
}

/* Hover: slightly darker gray + lift */
.modal-footer .btn-secondary:hover {
    background: #c2c2c2;
    color: #000000;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

/* Active: pressed effect */
.modal-footer .btn-secondary:active {
    background: #a0a0a0;
    color: #FFFEF2;
    transform: translateY(2px) scale(0.98);
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
</style>

<div class="modal fade" id="delete-associate-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('process-delete-associate') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="associate_id" id="delete_associate_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title text-danger">Confirm associate Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to delete this associate? This action cannot be undone.</p>
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Enter your password to confirm</label>
                        <input type="password" class="form-control" name="password" id="delete_password" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Delete associate</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
