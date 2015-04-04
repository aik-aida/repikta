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
      <!-- **********************************************************************************************************************************************************
      TOP BAR CONTENT & NOTIFICATIONS
      *********************************************************************************************************************************************************** -->
      <!--header start-->
      <header class="header black-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <!--logo start-->
            <a href="index.html" class="logo"><b>Rekomendasi Topik TA</b></a>
            <!--logo end-->
            
            <div class="top-menu">
              <ul class="nav pull-right top-menu">
                    <li><a class="logout" href={{ URL::to('/')}}>Beranda Awal</a></li>
              </ul>
            </div>
        </header>

      <!--main content start-->
      <section id="main-content-single">
        <section class="wrapper">
          <!-- INLINE FORM ELELEMNTS -->
            <div class="row mt" >
                  <div class="col-md-12">
                      <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Transkrip Mata Kuliah </h2></div>
                    </div>
                          <div class="panel-body" align="center">
                              <div class="task-content">

                                  <ul class="task-list">
                                      <li>
                                          <div class="task-title">
                                              <h4>
                                              <span class="badge bg-warning">nrp</span>
                                              <span class="task-title-sp"> {{ $data->nrp }} </span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              <span class="badge bg-theme">nama</span>
                                              <span class="task-title-sp"> {{ $data->nama }} </span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              <span class="badge bg-success">ipk</span>
                                              <span class="task-title-sp"> {{ $data->ipk }} </span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              <span class="badge bg-info">sks</span>
                                              <span class="task-title-sp"> {{ $data->totalsks }} </span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              <span class="badge bg-important">tanggal transkrip</span>
                                              <span class="task-title-sp"> {{ $data->tanggal }} </span>
                                              </h4>
                                          </div>
                                      </li>                                 
                                  </ul>
                              </div>
                              <div class="add-task-row">
                                  <button type="button" class="btn btn-primary btn-lg btn-block pull-center">Lihat Rekomendasi Topik TA</button>
                              </div>
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->

            <div class="row mt">
            
               <div class="col-lg-6 col-md-6 col-sm-12">
                      <div class="content-panel">
                          <h4><i class="fa fa-angle-right"></i> Tahap Persiapan </h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th>Kode</th>
                                  <th>Nama Mata Kuliah</th>
                                  <th>SKS</th>
                                  <th>Historis Nilai</th>
                                  <th>Nilai</th>
                              </tr>
                              </thead>
                              <tbody>
                                @foreach($data->mkpersiapan as $dt)
                                <tr>
                                  <td align="center">{{ $dt->kode }}</td>
                                  <td align="left">{{ $dt->nama }}</td>
                                  <td align="center">{{ $dt->sks }}</td>
                                  <td align="left">{{ $dt->catatan }}</td>
                                  <td align="center">{{ $dt->nilai }}</td>
                                </tr>
                                @endforeach
                              </tbody>
                          </table>
                          </section>
                      </div><!-- /content-panel -->
               </div><!-- /col-lg-4 -->     
               <div class="col-lg-6 col-md-6 col-sm-12">
                      <div class="content-panel">
                          <h4><i class="fa fa-angle-right"></i> Tahap Sarjana </h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th>Kode</th>
                                  <th>Nama Mata Kuliah</th>
                                  <th>SKS</th>
                                  <th>Historis Nilai</th>
                                  <th>Nilai</th>
                              </tr>
                              </thead>
                              <tbody>
                                @foreach($data->mksarjana as $dt)
                                <tr>
                                  <td align="center">{{ $dt->kode }}</td>
                                  <td align="left">{{ $dt->nama }}</td>
                                  <td align="center">{{ $dt->sks }}</td>
                                  <td align="left">{{ $dt->catatan }}</td>
                                  <td align="center">{{ $dt->nilai }}</td>
                                </tr>
                                @endforeach
                              </tbody>
                          </table>
                          </section>
                      </div><!-- /content-panel -->
               </div>
            
          </div><!-- /row -->
  
</section><! --/wrapper -->
      </section><!-- /MAIN CONTENT -->

      <!--main content end-->
      <!--footer start-->
      <footer class="site-footer">
          <div class="text-center">
              2015 - repikTA - Aida Muflichah
              <a href="form_component.html#" class="go-top">
                  <i class="fa fa-angle-up"></i>
              </a>
          </div>
      </footer>
      <!--footer end-->
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
