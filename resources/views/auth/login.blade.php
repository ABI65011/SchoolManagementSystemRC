@extends('layouts.auth')


@section('content')
    <div class="card">
        <div class="card-body rounded login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>
            <form action="{{ route('authenticate') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label class="lable" for="email-id">Email</label>
                            <div class="input-group mb-3">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email-id" placeholder="eg. 'example@domain.com'" name="email"
                                    value="{{ old('email') }}" required>
                                <div class="input-group-text"><span class="bi bi-envelope"></span></div>
                            </div>
                            @error('email')
                                <span class="error invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="password-id">Password</label>
                            <div class="input-group mb-3">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password-id" placeholder="" name="password" value="{{ old('password') }}" required>
                                <div class="input-group-text">
                                    <a href="#" class="text-default" id="password-toggle">
                                        <span class="bi bi-lock-fill" id="password-icon"></span>
                                    </a>
                                </div>
                            </div>
                            @error('password')
                                <span class="error invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <!--begin::Row-->
                <div class="row">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                name="rememberMe" />
                            <label class="form-check-label" for="flexCheckDefault"> Remember Me </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-12 my-2">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Sign In</button>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!--end::Row-->
            </form>
            <div class="text-center">
                <p class="mb-1"><a href="forgot-password.html">I forgot my password</a></p>
                <p class="mb-0">
                    Don't have an account? <a href="register.html" class="text-center"> Create one </a>
                </p>
            </div>
        </div>
        <!-- /.login-card-body -->
    </div>
@endsection

@section('javascript')
    <script>
        document.getElementById('password-toggle').addEventListener('click', function() {
            var icon = this.querySelector('.bi');
            var input = document.getElementById('password-id');

            console.log(this, icon, input);

            if (input.type === 'password') {
                input.type = 'text'
                icon.classList.remove('bi-lock-fill');
                icon.classList.add('bi-unlock-fill');
            } else {
                input.type = 'password'
                icon.classList.remove('bi-unlock-fill');
                icon.classList.add('bi-lock-fill');
            }
        })
    </script>
@endsection
