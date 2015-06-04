@extends('layout.blank_page')

@section('content')
			<div class="row mt" >
                  <div class="col-md-12">
                      <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Terima Kasih Atas Penilaiannya </h2></div>
                    </div>
                          <div class="panel-body" align="center">
                              <div class="task-content">

                                  <ul class="task-list">
                                      <li>
                                          <div class="task-title">
                                              <h3> <b>
                                              {{$nama}}
                                              </b></h3>
                                          </div>
                                      </li>                                 
                                  </ul>
                              </div>
                              <div class="add-task-row">
                              <h4>mohon masukan agar Sistem Rekomendasi Topik TA lebih baik dan mudah dipahami</h4>
                              {{ Form::open(array('url' => 'survey/selesai', 'method' => 'post')) }}
                              {{ Form::hidden('nrp', $nrp) }}
		                      {{ Form::hidden('nama', $nama) }}
		                      {{ Form::hidden('survey', $survey) }}
                              {{ Form::text('masukan', null, array('class' => 'form-control round-form', 'placeholder' => 'masukan')) }}
                              <br>
                              {{ Form::submit('Simpan dan Selesai', array('class' => 'btn btn-round btn-success btn-lg pull-center')) }}
                              <!-- <button type="button" class="btn btn-round btn-success">Success</button> -->
                              {{ Form::close() }}
                              </div>
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->

@endsection