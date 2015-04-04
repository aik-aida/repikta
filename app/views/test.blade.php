@extends('layout.dashgum')

@section('content')
<!-- COMPLEX TO DO LIST -->			
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
                  </div><!-- /col-md-12-->
              </div><!-- /row -->
	
@endsection
