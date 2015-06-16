@extends('layout.survey_header')

@section('content')

            <div class="col-md-9 mb">
                <div class="row" style="color:black">
                  <div class="col-md-12">
                      <section class="task-panel tasks-widget">
                          <div class="panel-body" align="center">
                            <div class="task-title">
                                <div class="" align="center"><h4><i class="fa fa-tasks"></i> Rekomendasi Topik Tugas Akhir  
                                    <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                    <span class="badge bg-warning">nrp</span>
                                    <span class="task-title-sp"> {{ $dokumen->nrp }} </span>
                                    <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                    <span class="badge bg-theme">nama</span>
                                    <span class="task-title-sp"> {{ $dokumen->nama }} </span>
                                    <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                    </h4>
                                </div>
                              </div>
                                    <ul class="task-list" align="center">
                                      <li>
                                        <div class="task-title">
                                          Anda direkomendasikan memilih Tugas Akhir pada bidang :
                                          <div class="" align="center"><h3>{{ $bidang }}</h3></div>
                                          Berikut Topik yang direkomendasikan menurut prosentase kedekatan :
                                        </div>
                                      </li>                                 
                                    </ul>
                                  <div class="add-task-row" align="center">
                                    <table class="table table-hover">
                                      <thead align="center" style="color:blue">
                                      <tr align="center">
                                          <th align="center">PROSENTASI REKOMENDASI</th>
                                          <th align="center">NAMA TOPIK</th>
                                          <th align="center">KATA-KATA YANG MEREPRESENTASIKAN TOPIK</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                      @for ($i = 0; $i < $ntopik; $i++)
                                        <tr>
                                          <td>{{ $bobot[$i] }}%</td>
                                          <td><b >{{ $nama_topic[$i] }}</b></td>
                                          <td>
                                            @foreach ($daftar[$i] as $kata)
                                              {{ $kata }}, &nbsp;
                                            @endforeach
                                          </td>
                                        </tr>
                                      @endfor
                                      </tbody>
                                  </table>
                                  </div>
                          </div>
                            
                          </section>
                      </div><!-- /col-md-12-->
                </div><!-- /row -->

                <div class="row " style="color:white">
                      <div class="col-md-12">
                          <section class="task-panel tasks-widget">
                              <div class="panel-body darkblue-panel" align="center">   
                                  <div class="task-title">
                                      <h4><b>
                                          {{$dokumen->judul_ta}}
                                          </b>
                                      </h4>
                                  </div>
                                  <div class="add-task-row">
                                      {{$dokumen->abstraksi_ta}}
                                  </div>
                              </div>
                          </section>
                      </div><!-- /col-md-12-->
                </div><!-- /row -->
            </div><!-- /col-md-4 -->
              
                  <div class="col-lg-3 ds">
                    <!--COMPLETED ACTIONS DONUTS CHART-->
                      <h3><b>PENILAIAN REKOMENDASI TOPIK TA</b></h3>
                      @if($now==$all)
                        {{ Form::open(array('url' => 'survey/masukan', 'method' => 'post')) }}
                      @else
                        {{ Form::open(array('url' => 'survey/dokumen', 'method' => 'post')) }}
                      @endif
                      <br />
                      Bagaimanakah kedekatan topik yang direkomendasikan dengan Judul dan Abstraksi Tugas Akhir yang dikerjakan ?
                      <div class="desc">
                      <b>
                        <div class="thumb">
                          {{ Form::radio('nilai', '1', true) }}
                        </div>
                        <div class="details">
                          <p>Sangat Mendekati</b><br/>
                             (*) <b>bidang TA cocok</b>, Topik sesuai, &kata-kata penting ditemukan <br/>
                          </p>
                        </div>
                      </div>
                       <div class="desc">
                      <b>
                        <div class="thumb">
                          {{ Form::radio('nilai', '0.7') }}
                        </div>
                        <div class="details">
                          <p>Mendekati</b><br/>
                             (*) <b>bidang TA cocok</b>, & Topik sesuai <br/>
                          </p>
                        </div>
                      </div>
                       <div class="desc">
                      <b>
                        <div class="thumb">
                          {{ Form::radio('nilai', '0.4') }}
                        </div>
                        <div class="details">
                          <p>Tidak Mendekati</b><br/>
                             (*) <b>bidang TA cocok</b>, namun topik tidak cocok <br/>
                          </p>
                        </div>
                      </div>
                       <div class="desc">
                      <b>
                        <div class="thumb">
                          {{ Form::radio('nilai', '0.1') }}
                        </div>
                        <div class="details">
                          <p>Sangat Tidak Mendekati</b><br/>
                             (*) <b>bidang TA tidak cocok</b> <br/>
                          </p>
                        </div>
                      </div>

                      {{ Form::hidden('dokumen', $dokumen->nrp) }}
                      {{ Form::hidden('nrp', $nrp) }}
                      {{ Form::hidden('nama', $nama) }}
                      {{ Form::hidden('number', $now) }}
                      {{ Form::hidden('survey', $survey) }}
                      {{ Form::submit('Simpan Nilai dan Lanjut', array('class' => 'btn btn-primary btn-lg btn-block pull-center')) }}
                      {{ Form::close() }}
                  </div><!-- /col-lg-3 -->
           
@endsection