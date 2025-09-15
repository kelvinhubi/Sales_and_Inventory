@include('layout.header')

<section id="home" class="image">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @yield('content')
            </div>
        </div>
    </div>
</section>

@include('layout.footer')