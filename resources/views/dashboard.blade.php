@extends("layout")
@section('title', 'Already Logged In | Chomply')

@section("content")
<div class="container vh-100 d-flex flex-column justify-content-center">
    <div class="alert alert-warning text-center mb-5">
        <h4 class="mb-0">⚠️ You are already logged in.</h4>
        <p class="mb-0">Please choose what you’d like to do:</p>
    </div>

    <div class="row justify-content-center g-4">
        {{-- Continue as current role --}}
        <div class="col-md-4">
            <div class="card shadow-lg h-100 text-center">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title mb-4">Continue as {{ ucfirst(session('active_role')) }}</h5>
                    @if(session('active_role') === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">Go to Admin Dashboard</a>
                    @else
                        <a href="{{ route('staff.dashboard') }}" class="btn btn-success btn-lg">Go to Staff Dashboard</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Logout --}}
        <div class="col-md-4">
            <div class="card shadow-lg h-100 text-center border-danger">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h5 class="card-title mb-4 text-danger">Logout</h5>
                    <form method="POST" action="{{ route('process-logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg">Logout & Login as another user</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
