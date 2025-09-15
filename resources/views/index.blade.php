@extends('layout.main')
@section('content')


    <!--======================================
           Preloader
    ========================================-->

    <div class="page-preloader">
        <div class="spinner">

            <div class="rect1"></div>

            <div class="rect2"></div>

            <div class="rect3"></div>

            <div class="rect4"></div>

            <div class="rect5"></div>

        </div>
    </div>


    <!--======================================
           Header
    ========================================-->

    <!--//** Navigation**//-->

    <nav class="navbar navbar-default navbar-fixed white no-background bootsnav navbar-scrollspy" data-minus-value-desktop="70" data-minus-value-mobile="55" data-speed="1000">

        <div class="container">

            <!-- Start Header Navigation -->

            <div class="navbar-header">

                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">

                    <i class="fa fa-bars"></i>

                </button>

                <a class="navbar-brand" href="#brand">

                    <img src="{{asset('/storage/icons/logo.jpg')}}"  class="logo" alt="logo" style="border-radius: 50%">

                </a>

            </div>

            <!-- End Header Navigation -->

            <!-- Collect the nav links, forms, and other content for toggling -->

            <div class="collapse navbar-collapse" id="navbar-menu">

                <ul class="nav navbar-nav navbar-right">

                    <li class="active scroll"><a href="#home">Home</a></li>

                    <li class="scroll"><a href="#about">About</a></li>

                    <li class="scroll"><a href="#contact">Contact</a></li>
                    @auth
                    <li class="button-holder">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-blue navbar-btn" >Logout</button>
                        </form>
                     {{-- <button type="button" onclick="window.location.href='{{ route('logout') }}'" class="btn btn-blue navbar-btn" >Logout</button> --}}
                    </li>
                    @else
                    @endauth
                </ul>

            </div>

            <!-- /.navbar-collapse -->

        </div>

    </nav>

    <!--//** Banner**//-->

    <section id="home"  class="image">

        <div class="container" >

            <div class="row">

                <!-- Introduction -->

                <div class="col-md-7 caption">

                    <h2 style="font-weight:bold">

                        {{-- @foreach ($systeminformation as $sysinfor)
                        {{ $sysinfor->system_name }}
                        @endforeach --}}
                    </h2>

                    <h2>

                            Why choose us?

                            <br>

                            <span class="animated-text"></span>

                            <span class="typed-cursor"></span>

                        </h2>

                    <p>Track smarter, sell faster, and manage your business with confidence! Stay ahead of the competition with real-time inventory insights, seamless sales control,
                        and powerful tools that help you make smarter decisions, boost efficiency, and drive growth—every step of the way.</p>

                    {{-- <a href="#" class="btn btn-transparent">View Projects</a> --}}


                </div>

                <!-- Sign Up -->

                @auth
                    <div class="form-group text-center">

                            <button type="submit" onclick="window.location.href='{{ route(strtolower(Auth::user()->Role)) }}'" class="btn btn-blue btn-block btn-login">Go to dasboard {{Auth::user()->Role}}</button>
                            

                        </div>
                    @else
                        <div class="col-md-5">

                    
                @if(session('status'))
                    <div class="alert alert-success">
                      {{ session('status') }}
                    </div>
                  @endif
                  <form class="signup-form" action='{{route('loginUser') }}' method="POST">
                    <div id="login-Message"></div>
                    {{-- action="{{ route('user.login') }}" --}}
                    @csrf


                        <h2 class="text-center">Login</h2>

                        <hr>


                        <div class="form-group">

                            <input type="email" name="email" class="form-control" id="email" placeholder="Email Address" value="{{old('email')}}">
                            @error('email')
                            <span class="text-danger" id="email_mgs">{{$message}}</span>
                            @enderror
                        </div>


                        <div class="form-group">

                            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                            @error('password')
                            <span class="text-danger" id="password_mgs">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            {{-- This div helps with vertical spacing for the whole group --}}{{-- This is the key Bootstrap 3/4 class for alignment --}}
                            <label for="remember"><input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me</label>
                         </div>
                         

                        <div class="form-group text-center">

                            <button type="submit" class="btn btn-blue btn-block btn-login">Login</button>
                            

                        </div>

                        <div class="form-group text-center">
                            <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                        </div>

                       
                        
                    </form>

                </div>
                @endauth

            </div>

        </div>

    </section>


    <!--======================================
           About Us
    ========================================-->

    <section id="team" class="section-padding">

        <div class="container">

            <h2>About Us</h2>

            <p>
                {{-- @foreach ($systeminformation as $sysinfor)
                {{ $sysinfor->about }}
                @endforeach --}}
            </p>

            <div class="row">

                <div class="col-md-12">

                    <div class="icon-box">

                        {{-- <i class="material-icons">favorite</i> --}}

                        <h4>How It Works:</h4>

                        <p>
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown
                            printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
                            It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsums
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!--======================================
           Our Services
    ========================================-->

    <section id="team" class="section-padding">

        <div class="container">

            <h2>Our Services</h2>

                <p>
                {{-- @foreach ($systeminformation as $sysinfor)
                {{ $sysinfor->about }}
                @endforeach --}}
            </p>

            <div class="row">

                <div class="col-md-12">

                    <div class="icon-box">

                        {{-- <i class="material-icons">favorite</i> --}}

                        <h4>summary</h4>

                        <p>
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown
                            printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
                            It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!--======================================
           Contact
    ========================================-->

    <section id="contact" class="section-padding" style="margin-bottom :10% ">

        <div class="container">

            <h2>Contact Us</h2>

            <p>We're Here to Help</p>


        </div>

        <!-- Contact Info -->

        <div class="container contact-info">

            <div class="row">

                <div class="col-md-4">

                    <div class="icon-box">

                        <i class="material-icons">place</i>

                        <h4>Address</h4>

                        <p>
                            {{-- @foreach ($systeminformation as $sysinfor)
                            {{ $sysinfor->address}}
                            @endforeach --}}
                        </p>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="icon-box">

                        <i class="material-icons">phone</i>

                        <h4>Call Us On</h4>

                        <p>
                            {{-- @foreach ($systeminformation as $sysinfor)
                            {{ $sysinfor->contact_number}}
                            @endforeach --}}
                        </p>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="icon-box">

                        <i class="material-icons">email</i>

                        <h4>Email us on</h4>

                        <p>
                            {{-- @foreach ($systeminformation as $sysinfor)
                            {{ $sysinfor->email}}
                            @endforeach --}}
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>
   @endsection