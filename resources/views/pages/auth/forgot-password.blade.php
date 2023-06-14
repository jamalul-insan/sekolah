<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    @section('title', 'Forgot Password')

    @include('layout.head-css')
</head>

<body class="bg-light">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 rounded shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row p-4">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Forgot Password</h1>
                                        @if (session('status'))
                                            <div class="mb-4 font-medium text-sm text-success font-weight-bold">
                                                {{ session('status') }}
                                            </div>
                                        @endif
                                    </div>
                                    <form action="{{url('forgot-password')}}" class="user" method="POST">

                                        @csrf
                                        <div class="form-group">
                                            <input type="email" name="email"
                                                class="form-control form-control-user @error('email') is-invalid  @enderror"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address..." autofocus>
                                            @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Reset Password
                                        </button>
                                        <hr>
                                        <div class="text-center">
                                            <a class="small" href="{{ route('login') }}">I remember my password</a>
                                        </div>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <p class="small" href="">

                                            ©
                                            <script>
                                                document.write(new Date().getFullYear())
                                            </script>

                                            Crafted with <i class="mdi mdi-heart text-danger"></i> by
                                            Sempoa
                                            <br>

                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    @include('layout.head-js')
</body>

</html>
