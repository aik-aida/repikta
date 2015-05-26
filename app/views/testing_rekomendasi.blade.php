@extends('layout.dashgum')

@section('content')
            <div class="row mt" >
                  <div class="col-md-12">
                      <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Rekomendasi Topik Tugas Akhir </h2></div>
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
                                              </h4>
                                          </div>
                                      </li>                                 
                                  </ul>
                              </div>
                              <div class="task-content">
                                Anda direkomendasikan memilih Tugas Akhir pada bidang : <br />
                                <div class="" align="center"><h2> RPL </h2></div>
                              </div>
                              <!-- <div class="add-task-row">
                                      {{ Form::open(array('url' => 'testing/rekomendasi', 'method' => 'post')) }}
                                      {{ Form::hidden('nrp', $data->nrp) }}
                                      {{ Form::submit('Lihat Rekomendasi Topik TA', array('class' => 'btn btn-primary btn-lg btn-block pull-center')) }}
                                      {{ Form::close() }}
                              </div> -->
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->
@endsection