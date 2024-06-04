@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('update') }}" method="POST" id="updateForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Email address:</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                    </div>
                    <div class="form-group">
                        <label for="photo">Photo (Acceptable filetypes: JPEG,PNG,JPG and GIF. Max size: 2 MB):</label>
                        <input type="file" class="form-control" id="photo" name="photo">
                    </div>
                    <div class="form-group">
                        <label for="interests">Interests:</label>
                        <textarea class="form-control" id="interests" name="interests">{{ $user->interests }}</textarea>
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="password">Password:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="input-group-append" style="height: 38px;">
                                <span class="input-group-text" style="height: 100%;">
                                    <i class="fas fa-eye" id="togglePassword" style="font-size: 1rem; line-height: 38px;"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('profile') }}" class="btn btn-primary">Go Back</a>
                </form>
            </div>
        </div>
    </div>

    <!--Font Awesome 6 CSS-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#togglePassword').click(function(){
                var passwordField = $('#password');
                var passwordFieldType = passwordField.attr('type');
                if(passwordFieldType == 'password') {
                    passwordField.attr('type', 'text');
                    $(this).removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        });
    </script>
@endsection
