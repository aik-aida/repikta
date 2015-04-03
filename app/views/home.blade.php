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
	  			<div class="col-lg-8 col-lg-offset-2">
	  				<div class="lock-screen">
                        
                        <div>
                            <input type="text" class="form-control round-form">

                        </div>
                        <br />
                        <!--
                        <div class="col-lg-8 col-lg-offset-4 btn-group btn-group" align="center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-theme">Unggah Dokumen Transkrip</button>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-theme">Lihat Rekomendasi Topik</button>
                            </div>
                        </div>                      
                        -->
                        
                        {{ Form::open(array('url'=>'proses','files'=>true)) }}
                        {{ Form::label('file','File',array('id'=>'','class'=>'')) }}
                        {{ Form::file('file','',array('id'=>'','class'=>'')) }}
                        <br />
		  				<h2><a data-toggle="modal" href="#myModal"><i class="fa fa-lock"></i></a></h2>
		  				<p>UNLOCK</p>
		  				
				          <!-- Modal -->
				          <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
				              <div class="modal-dialog">
				                  <div class="modal-content">
				                      <div class="modal-header">
				                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				                          <h4 class="modal-title">Welcome Back</h4>
				                      </div>
				                      <div class="modal-body">
				                          <p class="centered"><img class="img-circle" width="80" src="assets/img/ui-sam.jpg"></p>
				                          <input type="password" name="password" placeholder="Password" autocomplete="off" class="form-control placeholder-no-fix">
				
				                      </div>
				                      <div class="modal-footer centered">
				                          <button data-dismiss="modal" class="btn btn-theme04" type="button">Cancel</button>
				                          <button class="btn btn-theme03" type="button">Login</button>
				                      </div>
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
