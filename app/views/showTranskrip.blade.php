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
                    <li><a class="logout" href="login.html">Logout</a></li>
            	</ul>
            </div>
        </header>

      <!--main content start-->
      <section id="main-content-single">
        <section class="wrapper">
          <p> {{ $data->status }} </p>
            <p> {{ $data->nrp }} </p>
            <p> {{ $data->nama }} </p>
            <p> {{ $data->ipk }} </p>
            <p> {{ $data->totalsks }} </p>
            <p> {{ $data->tanggal }} </p>
            <br/>
          
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
                              <!--
                                  <td align="center">{{ $dt->kode }}</td>
                                  <td align="left">{{ $dt->nama }}</td>
                                  <td align="center">{{ $dt->sks }}</td>
                                  <td align="left">{{ $dt->catatan }}</td>
                                  <td align="center">{{ $dt->nilai }}</td>
                                -->

                              <tbody>
                              <!--
                              @foreach($data->mkpersiapan as $dt)
                              <tr>
                                  {{ $dt->kode }}
                                  {{ $dt->nama }}
                                  {{ $dt->sks }}
                                  {{ $dt->catatan }}
                                  {{ $dt->nilai }}
                              </tr>
                              @endforeach
                              -->
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
                              <!--
                              @foreach($data->mksarjana as $dt)
                              <tr>
                                  {{ $dt->kode }}
                                  {{ $dt->nama }}
                                  {{ $dt->sks }}
                                  {{ $dt->catatan }}
                                  {{ $dt->nilai }}
                              </tr>

                              @endforeach
                              -->
                              </tbody>
                          </table>
                          </section>
                      </div><!-- /content-panel -->
               </div>
            
          </div><!-- /row -->
            <!--
            <br/>
            <p> ---MK SARJANA--- </p>
            <table style="width:100%">
            @foreach($data->mksarjana as $dt)
              <tr>
                <td>{{ $dt->kode }}</td>
                <td>{{ $dt->nama }}</td>
                <td>{{ $dt->sks }}</td>
                <td>{{ $dt->catatan }}</td>
                <td>{{ $dt->nilai }}</td>
              </tr>
            @endforeach
-->
            </table>
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
