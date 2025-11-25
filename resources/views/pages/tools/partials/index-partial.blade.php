<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th class="col-md-3">Name</th>
                <th>Description</th>
                <th class="col-md-4">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td><strong title="Account ID: {{ $log->account_id }}">{{ $log->account_name_snapshot ?? 'N/A' }}</strong></td>
                    <td>{{ $log->description }}</td>
                    <td><span class="text-muted" data-time="{{ $log->created_at ? $log->created_at->diffForHumans() : now()->diffForHumans() }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No recent activities.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-2">
    {{ $logs->links('vendor.pagination.bootstrap-5') }}
</div>