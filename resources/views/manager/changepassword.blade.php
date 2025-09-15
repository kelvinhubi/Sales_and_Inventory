@extends('manager.olayouts.main')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div id="loader"></div>
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            {{-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="">Home</a></li> --}}
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->


        <section class="content">
            <div class="container-fluid">

                <div class="row">

                    <div class="col-md-3">

                    </div>

                    <div class="col-md-5">


                        <div class="card mt-4">
                            <div class="card-body">
                                <h3 class="text-center">Change Password here</h3>
                                <hr>
                                <form action="{{ route('manager.password.update') }}" method="POST">
                                    @csrf
                                    @method('put')
                                    <div id="mgs2"></div>
                                    <div class="form-group">
                                        <label for="status" class="control-label text-navy"><b>Email:</b></label>
                                        <input type="text" name="email" class="form-control" id="edit_email"
                                            value="{{ $userlog->email }}" readonly>
                                        <span class="text-danger">
                                            <strong id="email-error"></strong>
                                        </span>
                                    </div>

                                    <div class="form-group">
                                        <label for="status" class="control-label text-navy"><b>Password:</b></label>
                                        <input type="password" name="password" class="form-control" id="edit_password">
                                        <span class="text-danger">
                                            <strong id="password-error"></strong>
                                        </span>
                                    </div>

                                    <input type="hidden" id="edit_id" value="{{ $userlog->id }}">
                                    <input type="hidden" id="default_password" value="{{ $userlog->password }}">

                                    <button type="submit" class="btn btn-primary" id="changepassword">Change
                                        password</button>
                                </form>


                            </div>
                        </div>



                    </div>


                </div>
            </div>
        </section>

    </div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


{{-- <script type="text/javascript">

    $(document).ready(function(){


            //edit edit password
            $('body').on('click', '#btn-changepassword', function(e){

                  e.preventDefault();
                  $.ajaxSetup({
                          headers: {
                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                          }
                      });

                 $('#email-error').html("");
                 $('#password-error').html("");

                  let email = $('#edit_email').val();
                  console.log(email);

                  let password = $('#edit_password').val();
                  console.log(password);

                  let defaultpassword =  $('#default_password').val();
                  console.log(defaultpassword);

                  let id = $('#edit_id').val();
                  console.log(id);

                  $.ajax({
                      url: {{ route("manager.password.update") }},
                      method: 'post',
                      data: {
                          email:email,
                          password: password,
                          defaultpassword: defaultpassword,
                          id: id
                      },
                      success: function(response){
                        console.log(response);
                            if(response.errors) {
                                if(response.errors.email){
                                    $('#email-error').html(response.errors.email[0]);
                                }
                                if(response.errors.password){
                                    $('#password-error').html(response.errors.password[0]);
                                }


                            }

                          // console.log(response.status == "Success");
                          if(response.status == 200){

                             $('#mgs2').html('<div class="alert alert-success">Update Password Successfully!</div>');
                               setTimeout(function(){
                                  window.location.reload();
                              }, 2000);
                      }
                      if(response.errors) {
                          console.log("Failed");

                      }

                  },
               });
            });
           //end edit password




      });

    </script> --}}
