@extends('layout.blank_page')

@section('content')
            <div class="row mt" style="color:black">
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
                                  {{ Form::open(array('url' => 'rekomendasi', 'method' => 'post')) }}
                                      {{ Form::hidden('nrp', $data->nrp) }}
                                      {{ Form::hidden('nama', $data->nama) }}
                                      {{ Form::hidden('transkrip', $transkrip_mhs) }}
                                      {{ Form::submit('Lihat Rekomendasi Topik TA', array('class' => 'btn btn-primary btn-lg btn-block pull-center')) }}
                                      {{ Form::close() }}
                              </div>
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->

            <div class="row mt" style="color:black">
            
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
               <div class="col-lg-6 col-md-6 col-sm-12" style="color:black">
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
@endsection