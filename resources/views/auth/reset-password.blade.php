@extends('layout')

@section('content')
<div class="container">
    <h2>Reset Password</h2>

    <form method="POST" action="{{ route('process-reset-password') }}">
        @csrf

        <input type="hidden" name="email" value="{{ $email }}">

        <div class="form-group">
            <label for="password">New Password</label>
            <input id="password" type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   name="password" required>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">Confirm New Password</label>
            <input id="password-confirm" type="password" 
                   class="form-control" 
                   name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">
            Reset Password
        </button>
    </form>
</div>
@endsection