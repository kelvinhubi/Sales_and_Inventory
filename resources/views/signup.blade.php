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

                    <img src="logo"  class="logo" alt="logo" style="border-radius: 70%">

                </a>

            </div>

            <!-- End Header Navigation -->

            <!-- Collect the nav links, forms, and other content for toggling -->

            <div class="collapse navbar-collapse" id="navbar-menu">

                <ul class="nav navbar-nav navbar-right">

                    <li class="active scroll"><a href="#home">Home</a></li>

                    <li class="scroll"><a href="#about">About</a></li>

                    <li class="scroll"><a href="#contact">Contact</a></li>

                    <li class="button-holder">

                     <button type="button" onclick="window.location.href='{{ route('Login') }}'" class="btn btn-blue navbar-btn" >Login</button>

                    </li>

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

                <div class="col-md-5">

                    {{-- @if(session()->has('mgs'))
                    <div class="alert alert-success">
                      {{ session()->get('mgs') }}
                    </div>
                  @endif --}}

                  <form class="signup-form" action="{{ route('createUser') }}" method="POST">
                    @csrf
                    <div id="login-Message"></div>
                    {{-- action="{{ route('user.login') }}" --}}
                        <h2 class="text-center">Sign up</h2>
                        <hr>
                        <div class="form-group">

                            <input type="name" name="name" class="form-control" id="name" placeholder="Name" value="{{old('name')}}">
                            @error('name')
                            <span class="text-danger" id="name_mgs">{{ $message }}</span>
                            @enderror
                        </div>
                    <!--
                        <div class="form-group">

                            <input type="lastname" class="form-control" id="lastname" placeholder="Lastname">
                            <span class="text-danger" id="Lastname_mgs"></span>
                        </div>
                    -->
                        <div class="form-group">

                            <input type="phone" name="phone" class="form-control" id="phone" placeholder="Phone number" value="{{old('phone')}}">
                            <span class="text-danger" id="contact_mgs"></span>
                        </div>

                        <div class="form-group">

                            <input type="email" name="email" class="form-control" id="email" placeholder="Email Address" value="{{old('email')}}">
                            @error('email')
                            <span class="text-danger" id="email_mgs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">

                            <input type="address" name="address" class="form-control" id="address" placeholder="Address" value="{{old('address')}}">
                            <span class="text-danger" id="email_mgs"></span>
                        </div>

                        <div class="form-group">

                            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                            @error('password')
                            <span class="text-danger" id="password_mgs">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr>

                        <div class="form-group text-center">

                            <button type="submit" class="btn btn-blue btn-block btn-login">Register</button>
                            {{-- <a href="{{ url('/login/microsoft') }}" class="btn microsoft-login-btn btn-block mt-2 d-flex">
                              <img src="{{asset('assets/img/4202105_microsoft_logo_social_social media_icon.svg')}}" height="30px" width="30px">&nbsp;&nbsp;Login with Microsoft
                            </a> --}}

                            {{-- <a href="" class="btn microsoft-login-btn btn-block mt-2 d-flex">
                                <img src="" height="30px" width="30px">&nbsp;&nbsp;Login with Google
                              </a> --}}

                        </div>


                        {{-- <div class="form-group">
                            Don't Have an account?<a href="{{route('main')}}"> <b style="color:#a31414">Sign up here</b></a>
                         </a>
                         </div> --}}
                    </form>

                </div>

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

                        <i class="material-icons">emailss</i>

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

