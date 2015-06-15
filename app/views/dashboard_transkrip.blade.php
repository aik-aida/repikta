@extends('layout.dashgum')

@section('content')
            <div class="row mt" >
                  <div class="col-md-12">
                  <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Kriteria Transkrip Bidang {{ $nama_bidang }}</h2></div>
                    </div>
                    <div class="panel-body" align="center">
                        <div class="task-content">
                          <ul class="task-list">
                            <li>
                              <div class="task-title">
                                {{ Form::open(array('url' => 'dashboard', 'method' => 'get')) }}
                                {{ Form::submit('Kembali ke Daftar Bidang', array('class' => 'btn btn-round btn-warning btn-lg pull-center')) }}
                                {{ Form::close() }}
                              </div>
                            </li>                                 
                          </ul>
                        </div>
                    </div>
                  </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->

            <div class="row mt" style="color:black">
            
               <div class="col-lg-6 col-md-6 col-sm-12">
                      <div class="content-panel">
                          <h4><i class="fa fa-angle-right"></i> Matakuliah Wajib </h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th>Kode</th>
                                  <th>Nama Mata Kuliah</th>
                                  <th>Nilai</th>
                              </tr>
                              </thead>
                              <tbody>
                                @foreach($mk_umum as $dt)
                                <tr>
                                  <td align="center">{{ $dt->kode }}</td>
                                  <td align="left">{{ $dt->nama }}</td>
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
                          <h4><i class="fa fa-angle-right"></i> Matakuliah Bidang Ahli </h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th>Kode</th>
                                  <th>Nama Mata Kuliah</th>
                                  <th>Nilai</th>
                              </tr>
                              </thead>
                              <tbody>
                                @foreach($mk_ahli as $dt)
                                <tr>
                                  <td align="center">{{ $dt->kode }}</td>
                                  <td align="left">{{ $dt->nama }}</td>
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