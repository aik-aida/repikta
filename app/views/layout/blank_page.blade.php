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
            @yield('content')
        </section><! --/wrapper -->

        <footer class="site-footer">
          <div class="text-center">
              2015 - repikTA - Aida Muflichah
              <a href="form_component.html#" class="go-top">
                  <i class="fa fa-angle-up"></i>
              </a>
          </div>
      </footer>

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
