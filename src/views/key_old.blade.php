<!DOCTYPE html>
<html>
<head>
    <title>Key Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Enter Key</div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <form method="POST" action="{{ route('system.auth.verify') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="key" class="form-label">Key</label>
                                <input type="text" name="key" id="key" class="form-control" required maxlength="14">
                            </div>
                            <button type="submit" class="btn btn-primary">Verify Key</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>