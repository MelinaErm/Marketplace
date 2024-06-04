<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1 class="mt-5">Admin Dashboard</h1>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Users and Their Products
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Email Verified At</th>
                                <th>Updated At</th>
                                <th>Role</th>
                                <th>Products</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            @if($user->name === 'admin')
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->email_verified_at }}</td>
                                <td>{{ $user->updated_at }}</td>
                                <td>{{ $user->role }}</td>
                                <td>
                                    @foreach($user->products as $product)
                                    {{ $product->name }} <br>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="#" class="btn btn-danger">Delete Products</a>
                                    <a href="#" class="btn btn-warning">Delete User</a>
                                </td>
                            </tr>
                            @endif
                            @endforeach

                            @foreach($users as $user)
                            @if($user->name !== 'admin')
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->email_verified_at }}</td>
                                <td>{{ $user->updated_at }}</td>
                                <td>{{ $user->role }}</td>
                                <td>
                                    @foreach($user->products as $product)
                                    {{ $product->name }} <br>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="#" class="btn btn-danger">Delete Products</a>
                                    <a href="#" class="btn btn-warning">Delete User</a>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
