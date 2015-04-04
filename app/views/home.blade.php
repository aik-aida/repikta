<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>repikTA</title>

    {{ HTML::style('assets/css/bootstrap.css') }}
    {{ HTML::style('assets/font-awesome/css/font-awesome.css') }}
    {{ HTML::style('assets/js/bootstrap-datepicker/css/datepicker.css') }}
        
    {{ HTML::style('assets/css/style-responsive.css') }}
    {{ HTML::style('assets/css/style.css') }}
  </head>

  <body>

      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
        <div class="container">
        
            <div id="showtime"></div>
                <div class="col-lg-4 col-lg-offset-4">
                    <div class="lock-screen">
                        <div class="form-panel-home">
                            {{ Form::open(array('url'=>'read_transkrip','files'=>true)) }}
                            
                            {{ Form::file('file','',array('id'=>'','class'=>'', 'align' => 'right')) }}
                        </div>
                            {{ Form::submit('Upload Transkrip', array('class' => 'btn btn-round btn-info')) }}
                        <br />
                        <br />
                        <br />
                        <h2><a data-toggle="modal" href="#myModal"><i class="fa fa-user fa-3x"></i></a></h2>
                        <p>admin LOGIN</p>
                        
                          <!-- Modal -->
                          
                          <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="0" id="myModal" class="modal fade">
                              <div class="modal-dialog">
                                  <div class="modal-content">
                                    <form class="form-login" action="index.html">
                                      <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                          <h2 class="form-login-heading">admin log-in</h2>
                                      </div>
                                      <div class="modal-body">
                                          <p class="centered"><img class="img-circle" width="80" src="assets/img/ui-sam.jpg"></p>
                                          <div class="login-wrap">
                                            <input type="text" class="form-control" placeholder="User ID" autofocus>
                                            <br>
                                            <input type="password" class="form-control" placeholder="Password">
                                            <label class="checkbox">
                                                <span class="pull-right">
                                                    <a data-toggle="modal" href="login.html#myModal"> Forgot Password?</a>
                                                </span>
                                            </label>
                                            <button class="btn btn-theme btn-block" href="index.html" type="submit"><!-- <i class="fa  fa-lock"></i> --> SIGN IN</button>
                                            <hr>
                                
                                        </div>
                
                                      </div>
                                    </form>
                                  </div>
                              </div>
                          </div>
                          
                          <!-- modal -->
                        
                        
                    </div><! --/lock-screen -->
                </div><!-- /col-lg-4 -->
        
        </div><!-- /container -->



    <!-- js placed at the end of the document so the pages load faster -->
    {{ HTML::script('assets/js/jquery.js') }}
    {{ HTML::script('assets/js/bootstrap.min.js') }}
    {{ HTML::script('assets/js/jquery.backstretch.min.js') }}

    <script>
        $.backstretch("assets/img/login-bg.jpg", {speed: 500});
    </script>

  </body>
</html>
