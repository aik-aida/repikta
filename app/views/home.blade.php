<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>bismillah</title>

    <!-- Bootstrap core CSS -->
    {{ HTML::style('assets/css/bootstrap.css') }}
    <!--external css-->
    {{ HTML::style('assets/font-awesome/css/font-awesome.css') }}
    {{ HTML::style('assets/js/bootstrap-datepicker/css/datepicker.css') }}
    {{ HTML::style('assets/js/bootstrap-daterangepicker/daterangepicker.css') }}
        
    {{ HTML::style('assets/css/style-responsive.css') }}
    {{ HTML::style('assets/css/style.css') }}
    {{ HTML::style('assets/css/to-do.css') }}
  </head>

  <body>
    <section id="container" >
       <header class="header black-bg">
              <a class="logo"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></a>
              <a class="logo"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></a>
              <a class="logo"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></a>
              <a class="logo"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></a>

              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="center" data-original-title="Toggle Navigation"></div>
              </div>
            <!--logo start-->

            <a class="logo"><b>Rekomendasi Topik Tugas Akhir</b></a>
            <!--logo end-->
        </header>

        
          <section class="wrapper">
           
                <div class="col-lg-6 col-md-6 col-sm-12" style="color:black">
                  <div class="col-lg-12">
                      <section class="task-panel tasks-widget">
                          <div class="panel-heading">
                                <b>
                                <div class="" align="center"><h2><i class="fa fa-tasks"></i> Data Sistem REPIKTA </h2></div>
                                <div class="" align="center"><h4></i> Sistem Rekomendasi Topik Tugas Akhir </h4></div>
                                </b>
                          </div>
                          <div class="panel-body" align="center">
                                          <div class="task-title">
                                              <h5>
                                                  Pada Sistem REPIKTA terdapat {{ $n_bidang }} Bidang Tugas Akhir yang akan direkomendasikan, dengan masing-masing bidang terdapat sub topik TA. Lebih detail dapat dilihat pada overview di bawah :
                                              </h5>
                                          </div>
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
                    @for ($i = 0; $i < $n_bidang; $i++)
                      <div class="col-lg-12">
                          <section class="task-panel tasks-widget">
                          <div class="content-panel">
                              <h4><i class="fa fa-angle-right"></i> {{ $nama_bidang[$i] }}</h4>
                                  <section id="unseen">
                                    <table class="table table-bordered table-striped table-condensed">
                                      <thead>
                                      <tr>
                                          <th>Topik</th>
                                          <th>Kata-Kata Topik</th>
                                      </thead>
                                      <tbody>
                                      
                                      @for ($j = 0; $j < count($nama_topik_bidang[$i]); $j++)
                                        <tr>
                                          <td>{{ $nama_topik_bidang[$i][$j] }}</td>
                                          <td>
                                            @foreach ($kata_topik_bidang[$i][$j] as $kata)
                                              {{ $kata }}, &nbsp;
                                            @endforeach
                                          </td>
                                        </tr>  
                                      @endfor
                                                                      
                                      </tbody>
                                  </table>
                                  </section>
                          </div>
                          </section>
                      </div><!-- /content-panel -->
                      <br />
                    @endfor
                </div>


                    <div class="col-lg-6 col-md-6 col-sm-12">
                      <aside>
                        <div id="sidebar_uptranskrip"  style="color:white">
                              
                                  <div class="panel-body darkblue-panel" align="center" >           
                                              <h3>REPIKTA</h3>
                                              <h5>Rekomendasi Topik TA merupakan sistem cerdas yang coba dibangun untuk memberikan rekomendasi topik tugas akhir berdasarkan kompetensi akademik mahasiswa yang tercatat pada nilai transkrip perkuliahan. Sistem ini diperuntukkan untuk Mahasiswa S1 Jurusan Teknik Informatika ITS.</h5>
                                              <br ><br >
                                              <h4>Unggah Berkas Transkrip Akademik anda :</h4>
                                              <div class="form-panel-home" style="color:black">
                                                  {{ Form::open(array('url'=>'read_transkrip','files'=>true)) }}
                                                  
                                                  {{ Form::file('file','',array('id'=>'','class'=>'', 'align' => 'right')) }}
                                                  
                                              </div>
                                                  <span class="task-title-sp"> (*) berkas transkrip adalah file ekspor excel transkrip dari sistem INTAEGRA ITS </span><br ><br >

                                                  {{ Form::submit('Upload Transkrip', array('class' => 'btn btn-round btn-info')) }}
                                                  {{ Form::close() }}
                                              <br />
                                              <br />
                                              <br />
                                              <h2><a data-toggle="modal" href="#myModal"><i class="fa fa-user fa-3x"></i></a></h2>
                                              <p>admin LOGIN</p>
                                  </div>

                                  <footer class="site-footer">
                                    <div class="text-center">
                                        2015 - repikTA - Aida Muflichah
                                        <a href="form_component.html#" class="go-top">
                                            <i class="fa fa-angle-up"></i>
                                        </a>
                                    </div>
                                </footer>
                        </div>
                      </aside>
                    
                    </div>
                     {{ Form::open(array('url' => 'admin', 'method' => 'post', 'class' => 'form-login')) }}
                                                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                <h3 class="form-login-heading">admin log-in</h3>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="centered"><img class="img-circle" width="80" src="assets/img/ui-sam.jpg"></p>
                                                                
                                                                <div class="login-wrap">
                                                                  
                                                                  {{Form::text('username', null,array('class' => 'form-control', 'placeholder' => 'Username'))}}
                                                                  <br>
                                                                  {{Form::password('password',array('class' => 'form-control', 'placeholder' => 'Password'))}}
                                                                  <br>
                                                                  {{ Form::submit('SIGN IN', array('class' => 'btn btn-theme btn-block')) }} 
                                                                  <hr>
                                                                  {{ Form::close() }}
                                                              </div>
                                      
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- modal -->
                </div>
             <!-- Modal -->
                                               
          </section>
        
    </section>

    <!-- js placed at the end of the document so the pages load faster -->
    {{ HTML::script('assets/js/jquery.js') }}
    {{ HTML::script('assets/js/bootstrap.min.js') }}
    {{ HTML::script('assets/js/jquery.dcjqaccordion.2.7.js') }}
    {{ HTML::script('assets/js/jquery.scrollTo.min.js') }}
    {{ HTML::script('assets/js/jquery.nicescroll.js') }}


    <!--common script for all pages-->
    {{ HTML::script('assets/js/common-scripts.js') }}

    <!--script for this page-->
    {{ HTML::script('assets/js/jquery-ui-1.9.2.custom.min.js') }}

  <!--custom switch-->
  {{ HTML::script('assets/js/bootstrap-switch.js') }}
  
  <!--custom tagsinput-->
  {{ HTML::script('assets/js/jquery.tagsinput.js') }}
  
  <!--custom checkbox & radio-->
  
  {{ HTML::script('assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
  {{ HTML::script('assets/js/bootstrap-daterangepicker/date.js') }}
  {{ HTML::script('assets/js/bootstrap-daterangepicker/daterangepicker.js') }}
  
  {{ HTML::script('assets/js/bootstrap-inputmask/bootstrap-inputmask.min.js') }}
  
  
  {{ HTML::script('assets/js/form-component.js') }}
    
    
  <script>
      //custom select box

      $(function(){
          $('select.styled').customSelect();
      });

  </script>

  </body>
</html>
