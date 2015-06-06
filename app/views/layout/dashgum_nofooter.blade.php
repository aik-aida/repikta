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
      <!--header end-->
      
      
      <!-- **********************************************************************************************************************************************************
      MAIN SIDEBAR MENU
      *********************************************************************************************************************************************************** -->
      <!--sidebar start-->
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <!-- sidebar menu start-->
              <ul class="sidebar-menu" id="nav-accordion">
              
              	  <p class="centered"><a href="profile.html"><img src="assets/img/ui-sam.jpg" class="img-circle" width="60"></a></p>
              	  <h5 class="centered"></h5>
              	  	
                  <li class="mt">
                      <a  href={{ URL::to('dashboard')}}>
                          <i class="fa fa-dashboard"></i>
                          <span>Dashboard</span>
                      </a>
                  </li>

                  <li class="sub-menu">
                      <a href="javascript:;" >
                          <i class="fa fa-desktop"></i>
                          <span>Master</span>
                      </a>
                      <ul class="sub">
                          <li><a  href={{ URL::to('kamus')}}>Kamus</a></li>
                          <li><a  href={{ URL::to('dokumen')}}>Dokumen</a></li>
                          <!-- <li><a  href={{ URL::to('centroid')}}>Centroid Mean</a></li> -->
                      </ul>
                  </li>

                  <li class="sub-menu">
                      <a href="javascript:;" >
                          <i class="fa fa-cogs"></i>
                          <span>Kluster Dokumen</span>
                      </a>
                      <ul class="sub">
                          <!-- <li><a  href={{ URL::to('kamus')}}>Eksekusi Kluster Baru</a></li> -->
                          <li><a  href={{ URL::to('kluster')}}>Daftar Kluster</a></li>
                      </ul>
                  </li>
                  <li class="sub-menu">
                      <a href="javascript:;" >
                          <i class="fa fa-book"></i>
                          <span>Ekstraksi Topik</span>
                      </a>
                      <!-- 
                      <ul class="sub">
                          <li><a  href="blank.html">Blank Page</a></li>
                          <li><a  href="login.html">Login</a></li>
                          <li><a  href="lock_screen.html">Lock Screen</a></li>
                      </ul>
                       -->
                  </li>
                  <li class="sub-menu">
                      <a href="javascript:;" >
                          <i class="fa fa-cogs"></i>
                          <span>Testing</span>
                      </a>
                      <ul class="sub">
                          <li><a  href={{ URL::to('testing')}}>Rekomendasi Topik</a></li>
                          <li><a  href={{ URL::to('akurasi')}}>Akurasi</a></li>
                      </ul>
                  </li>
                  <!-- 
                  <li class="sub-menu">
                      <a class="active" href="javascript:;" >
                          <i class="fa fa-tasks"></i>
                          <span>Forms</span>
                      </a>
                      <ul class="sub">
                          <li class="active"><a  href="form_component.html">Form Components</a></li>
                      </ul>
                  </li>
                  <li class="sub-menu">
                      <a href="javascript:;" >
                          <i class="fa fa-th"></i>
                          <span>Data Tables</span>
                      </a>
                      <ul class="sub">
                          <li><a  href="basic_table.html">Basic Table</a></li>
                          <li><a  href="responsive_table.html">Responsive Table</a></li>
                      </ul>
                  </li>
                  <li class="sub-menu">
                      <a href="javascript:;" >
                          <i class=" fa fa-bar-chart-o"></i>
                          <span>Charts</span>
                      </a>
                      <ul class="sub">
                          <li><a  href="morris.html">Morris</a></li>
                          <li><a  href="chartjs.html">Chartjs</a></li>
                      </ul>
                  </li>
                   -->
              </ul>
              <!-- sidebar menu end-->
          </div>
      </aside>
      <!--sidebar end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
        <section class="wrapper">
            @yield('content')
        </section><! --/wrapper -->
      </section><!-- /MAIN CONTENT -->

      <!--main content end-->
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
