@extends('layout.dashgum_nofooter')

@section('content')
            <div class="row mt" >
                  <div class="col-md-12">
                      <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Data Master REPIKTA </h2></div>
                          <div class="" align="center"><h4></i> Sistem Rekomendasi Topik Tugas Akhir </h4></div>
                    </div>
                          <div class="panel-body" align="center">
                              <div class="task-content">

                                  <ul class="task-list">
                                      <li>
                                          <div class="task-title">
                                              <h4>
                                              </h4>
                                          </div>
                                      </li>                                 
                                  </ul>
                              </div>
                              <div class="add-task-row">
                                   
                              </div>
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->

             @for ($i = 0; $i < $n_bidang; $i++)
                                    {{ Form::open(array('url' => 'dashboard/topik', 'method' => 'post')) }}
                                    <div class="col-lg-4 col-md-4 col-sm-4 mb">
                                      <div class="steps pn">
                                          {{ Form::hidden('id_klaster', $i) }}
                                          {{ Form::submit($nama_bidang[$i], array('class' => '')) }}
                                          @for ($j = 0; $j < count($nama_topik_bidang[$i]); $j++)
                                          <label>{{ $nama_topik_bidang[$i][$j] }}</label>
                                          @endfor
                                      </div>
                                    </div>
                                    {{ Form::close() }}
                                    @endfor
@endsection

