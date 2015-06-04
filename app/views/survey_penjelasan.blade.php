@extends('layout.survey_header')

@section('content')
            <div class="row mt" style="color:black">
                  <div class="col-md-12">
                      <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Survey Sistem Rekomendasi Topik TA </h2></div>
                    </div>
                          <div class="panel-body" align="center">

                              <div class="add-task-row">
                              <h4><b>Rekomendasi Topik Tugas Akhir</b> adalah Sistem yang dibangun untuk memberikan rekomendasi untuk topik tugas akhir mahasiswa berdasarkan nilai transkrip akademiknya</h4>
                              </div>
                              <div class="add-task-row">
                              <h4><b>Data yang dipakai dalam survei ini</b> adalah data dokumen tugas akhir dan data transkrip mahasiswa Teknik Informatika yang sudah Lulus</h4>
                              </div>
                              <div class="add-task-row">
                              <h4><b>Pada Survey Penilaian akan ditampilkan </b> Dokumen TA sesungguhnya yang dipakai untuk proses testing dengan rekomendasi yang dikeluarkan oleh sistem</h4>
                              </div>
                              <div class="add-task-row">
                              <h4><b>lebih jelas perhatikan gambar berkut : </b></h4>
                              </div>
                              <div class="task-content">

                                  <ul class="task-list">
                                      <li>
                                          <div class="task-title">
                                              <h4>
                                              {{ HTML::image('./data/repikta_survey_penjelasan.png"') }}
                                              </h4>
                                          </div>
                                      </li>                                 
                                  </ul>
                              </div>

                              <div class="add-task-row">
                              <h4><b>Daftar Topik yang ditampilkan</b> urut berdasarkan topik yang paling banyak muncul pada dokumen terdekat</h4>
                              </div>
                              <div class="add-task-row">
                              <h4><b>Topik yang ditampilkan</b> berupa kata-kata dasar yang mewakili deskripsi topik</h4>
                              </div>
                              <div class="add-task-row">
                              <h4>Dan setelah rekomendasi topik ditampilakn, ditampilkan pula <b>Realisasi Judul dan Abstraksi Tugas Akhir</b> yang dikerjakan</h4>
                              </div>
                              <div class="add-task-row">
                              <h4><b>Penilaian</b> dilakukan dengan mencocokkan kata-kata topik yang muncul dengan inti realisasi TA dilihat dari judul dan abstraksinya</h4>
                              </div>
                              <div class="add-task-row">
                              <h4><b>Silahkan tekan tombol di bawah untuk memulai penilaian</b></h4>
                              </div>
                              <div class="add-task-row">
                                      {{ Form::open(array('url' => 'survey/dokumen', 'method' => 'post')) }}
                                      {{ Form::hidden('number', $now) }}
                                      {{ Form::hidden('nrp', $nrp) }}
                                      {{ Form::hidden('nama', $nama) }}
                                      {{ Form::hidden('survey', $survey) }}
                                      {{ Form::submit('Mulai Penilaian', array('class' => 'btn btn-warning btn-lg btn-block pull-center')) }}
                                      {{ Form::close() }}
                              </div>
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->
@endsection