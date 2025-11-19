@extends('layout')
@section('title', 'Settings | Chomply')
@section('content')

<div class="container-fluid mt-4">

    {{-- TOP ROW: CARD 1 + CARD 2 --}}
    <div class="row g-4">
        
        {{-- CARD 1 --}}
        <div class="col-md-6">
            <div class="card shadow-sm pt-1 pb-1 rounded-4 h-100">
                <div class="card-body">

                    <h5 class="text-strong">Profile Information</h5>
                    <p class="text-muted">Update your account's profile information and email address.</p>

                    <form action="{{ route('process-change-name') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3 form-floating">
                            <input type="text" id="profile-last-name" name="last_name"
                                   class="form-control"
                                   value="{{ old('last_name', $account->last_name) }}" required>
                            <label for="last_name" class="form-label">Last Name</label>
                        </div>

                        <div class="mb-3 form-floating">
                            <input type="text" id="profile-middle-name" name="middle_name"
                                   class="form-control"
                                   value="{{ old('middle_name', $account->middle_name) }}">
                            <label for="middle_name" class="form-label">Middle Name</label>
                        </div>

                        <div class="mb-3 form-floating">
                            <input type="text" id="profile-first-name" name="first_name"
                                   class="form-control"
                                   value="{{ old('first_name', $account->first_name) }}" required>
                            <label for="first_name" class="form-label">First Name</label>
                        </div>

                        <div class="mb-4 form-floating">
                            <input type="email" id="profile-email" name="email"
                                   class="form-control"
                                   value="{{ preg_replace_callback('/^(.{2})(.*)(.@.*)$/u', function ($matches) {
                                        $middle = $matches[2];
                                        if (strlen($middle) <= 1) {
                                            return $matches[1] . $middle . $matches[3];
                                        }
                                        return $matches[1] . str_repeat('*', strlen($middle) - 1) . substr($middle, -1) . $matches[3];
                                    }, old('email', $account->email)) }}"
                                   required readonly data-bs-toggle="tooltip"
                                   title="This field is read-only and cannot be edited.">
                            <label for="email" class="form-label">Email</label>
                        </div>

                        <button type="submit" class="btn btn-dark">Save</button>
                    </form>

                </div>
            </div>
        </div>

        {{-- CARD 2 --}}
        <div class="col-md-6">
            <div class="card shadow-sm pt-1 pb-1 rounded-4 h-100">
                <div class="card-body">

                    <h5 class="text-strong">Update Your Password</h5>
                    <p class="text-muted">Ensure your account is using a long, random password to stay secure.</p>

                    <form action="{{ route('process-change-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" id="profile-current-password"
                                   name="current_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" id="profile-password"
                                   name="password" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" id="profile-password-confirmation"
                                   name="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-dark">Save</button>
                    </form>

                </div>
            </div>
        </div>

    </div> {{-- end row --}}

    {{-- BOTTOM ROW: FULL-WIDTH CARD --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm pt-1 pb-1 rounded-4">
                <div class="card-body">

                    <h5 class="text-strong">Delete Account</h5>

                    <form action="{{ route('process-delete-account') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <p class="text-muted">
                            Once your account is deleted, all of its resources and data will be permanently deleted.
                            Before deleting your account, please download any data or information that you wish to retain.
                        </p>

                        <input type="password" name="deletion_password"
                               id="account-deletion-password"
                               class="form-control mb-3"
                               placeholder="Enter the deletion password"
                               required>

                        <button type="submit" class="btn btn-danger">DELETE ACCOUNT</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'right'
            });
        });

        const readonlyInput = document.getElementById('profile-email');
        readonlyInput.addEventListener('click', function () {
            const tooltip = bootstrap.Tooltip.getInstance(this);
            tooltip.show();
            setTimeout(() => tooltip.hide(), 2000);
        });
    });
</script>

@endsection
