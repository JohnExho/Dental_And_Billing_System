            @if($logs->isNotEmpty())
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
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <strong title="Account ID: {{ $log->account_id }}">
                                            {{ $log->account_name_snapshot ?? 'N/A' }}
                                        </strong>
                                    </td>

                                    <td>{{ $log->description }}</td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $log->created_at ? $log->created_at->diffForHumans() : now()->diffForHumans() }}
                                        </span>
                                    </td>            </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No recent activities.</p>
            @endif

