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
                              <div class="add-task-row"><b>
                                {{ Form::open(array('url' => 'dashboard/dokumen', 'method' => 'get')) }}
                                {{ Form::submit('Lihat Daftar Dokumen', array('class' => 'btn btn-round btn-primary btn-lg pull-center')) }}
                                {{ Form::close() }}</b>
                              </div>
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->

             @for ($i = 0; $i < $n_bidang; $i++)
                                   
                                    <div class="col-lg-4 col-md-4 col-sm-4 mb pull-left">
                                    {{ Form::open(array('url' => 'dashboard/transkrip', 'method' => 'post')) }}
                                    <h4><b>
                                          {{ Form::hidden('id_klaster', $i) }}
                                          {{ Form::submit('Kriteria Transkrip', array('class' => 'btn btn-info pull-left')) }}
                                          {{ Form::close() }}
                                      </b></h4>
                                      <div class="steps pn"  style="color:black">
                                       {{ Form::open(array('url' => 'dashboard/topik', 'method' => 'post')) }}
                                          {{ Form::hidden('id_klaster', $i) }}
                                          {{ Form::submit($nama_bidang[$i], array('class' => '')) }}
                                          {{ Form::close() }}
                                          <ol>
                                            @for ($j = 0; $j < count($nama_topik_bidang[$i]); $j++)
                                            <li><h5>{{ $nama_topik_bidang[$i][$j] }}</h5></li>
                                            @endfor
                                          </ol>
                                          
                                      </div>
                                    </div>
                                    @endfor
@endsection

