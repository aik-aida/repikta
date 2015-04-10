@extends('layout.dashgum')

@section('content')
<!-- COMPLEX TO DO LIST 	
              <div class="row mt">
                  <div class="col-md-12">
                      <section class="task-panel tasks-widget">
	                	<div class="panel-heading">
	                        <div class="pull-left"><h5><i class="fa fa-tasks"></i> Transkrip Mata Kuliah </h5></div>
	                 	</div>
                          <div class="panel-body" align="center">
                              <div class="task-content">

                                  <ul class="task-list">
                                      <li>
                                          <div class="task-title">
                                              <span class="badge bg-warning">nrp</span>
                                              <span class="task-title-sp"> 5111100020 </span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                          	  <span class="badge bg-theme">nama</span>
                                              <span class="task-title-sp"> Aida Muflichah </span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              <span class="badge bg-success">ipk</span>
                                              <span class="task-title-sp">3.8</span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              <span class="badge bg-info">sks</span>
                                              <span class="task-title-sp">144</span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              <span class="badge bg-important">tanggal transkrip</span>
                                              <span class="task-title-sp">januari</span>
                                          </div>
                                      </li>
                                      <li>
                                          <div class="task-title">
                                              
                                          </div>
                                      </li>
                                      <li>
                                          <div class="task-title">
                                          </div>
                                      </li>
                                      <li>
                                          <div class="task-title">
                                          </div>
                                      </li>
                                      <li>
                                          <div class="task-title">
                                          </div>
                                      </li>                                      
                                  </ul>
                              </div>

                              <div class=" add-task-row">
                                  <a class="btn btn-success btn-sm pull-left" href="todo_list.html#">Add New Tasks</a>
                                  <a class="btn btn-default btn-sm pull-right" href="todo_list.html#">See All Tasks</a>
                              </div>
                          </div>
                      </section>
                  </div>
              </div>
	-->    
  <h3><i class="fa fa-angle-right"></i> Kluster</h3>
          <div class="row mt">
            <div class="col-lg-12">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> Daftar Kluster Dokumen</h4>
                        <section id="unseen">
  <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
        <li class="active"><a href="#red" data-toggle="tab">Red</a></li>
        <li><a href="#orange" data-toggle="tab">Orange</a></li>
        <li><a href="#yellow" data-toggle="tab">Yellow</a></li>
        <li><a href="#green" data-toggle="tab">Green</a></li>
        <li><a href="#blue" data-toggle="tab">Blue</a></li>
    </ul>
    <div id="my-tab-content" class="tab-content">
        <div class="tab-pane active" id="red">
            <h1>Red</h1>
            <p>red red red red red red</p>
        </div>
        <div class="tab-pane" id="orange">
            <h1>Orange</h1>
            <p>orange orange orange orange orange</p>
        </div>
        <div class="tab-pane" id="yellow">
            <h1>Yellow</h1>
            <p>yellow yellow yellow yellow yellow</p>
        </div>
        <div class="tab-pane" id="green">
            <h1>Green</h1>
            <p>green green green green green</p>
        </div>
        <div class="tab-pane" id="blue">
            <h1>Blue</h1>
            <p>blue blue blue blue blue</p>
        </div>
    </div>
                      </section>
                  </div><!-- /content-panel -->
               </div><!-- /col-lg-4 -->     
        </div><!-- /row -->
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#tabs').tab();
    });
</script> 

@endsection


