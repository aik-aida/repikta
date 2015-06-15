@extends('layout.dashgum')

@section('content')

  <h3><i class="fa fa-angle-right"></i> Kluster</h3>
            <div class="row mt" >
                  <div class="col-md-12">
                  <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Daftar DOkumen Kelompok Bidang Ilmu REPIKTA </h2></div>
                          <div class="" align="center"><h4></i> Sistem Rekomendasi Topik Tugas Akhir </h4></div>
                    </div>
                    <div class="panel-body" align="center">
                        <div class="task-content">
                          <ul class="task-list">
                            <li>
                              <div class="task-title">
                                {{ Form::open(array('url' => 'dashboard', 'method' => 'get')) }}
                                {{ Form::submit('Kembali ke Halaman Utama', array('class' => 'btn btn-round btn-warning btn-lg pull-center')) }}
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
              <div class="col-lg-12">
                        <div class="content-panel">
                        <h4><i class="fa fa-angle-right"></i> Hasil Pengelompokkan Dokumen</h4>
                          <section id="unseen">
                            <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                                @for ($i=0 ; $i<($datamain->jumlah_kluster) ; $i++)
                                  @if($i==0)
                                    <li class="active"><a href={{ $datakluster[$i]['href'] }} data-toggle="tab">{{ $datakluster[$i]['kode'] }}</a></li>
                                  @else
                                    <li><a href={{ $datakluster[$i]['href'] }} data-toggle="tab">{{ $datakluster[$i]['kode'] }}</a></li>
                                  @endif
                                @endfor
                                  
                                  <!-- <li><a href="#orange" data-toggle="tab">Orange</a></li>
                                  <li><a href="#yellow" data-toggle="tab">Yellow</a></li>
                                  <li><a href="#green" data-toggle="tab">Green</a></li>
                                  <li><a href="#blue" data-toggle="tab">Blue</a></li> -->
                              </ul>
                              <div id="my-tab-content" class="tab-content">
                                @for ($i=0 ; $i<($datamain->jumlah_kluster) ; $i++)
                                  @if($i==0)
                                    <div class="tab-pane active" id={{$datakluster[$i]['kode']}}>
                                  @else
                                    <div class="tab-pane" id={{$datakluster[$i]['kode']}}>
                                  @endif
                                        <table style="width:100%">
                                          <tr>
                                            <td align="left"><h2>{{ $datakluster[$i]['nama'] }} </h2></td>
                                            <td align="right"><h4>[ {{ count($datakluster[$i]['file']) }} dokumen ]</h4></td>
                                          </tr>
                                        </table>
                                         
                                        
                                        <table class="table table-bordered table-striped table-condensed">
                                          <thead>
                                          <tr>
                                              <th>No.</th>
                                              <th>Judul Tugas Akhir</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                              @for($j=0 ; $j<count($datakluster[$i]['file']) ; $j++)
                                              <tr>
                                                <td>{{ ($j+1) }}</td>
                                                <td>{{ $datakluster[$i]['file'][$j]->judul_ta }}</td>
                                              </tr>
                                              @endfor
                                          </tbody>
                                        </table>
                                      </div>
                                    <!-- <div class="tab-pane" id="orange">
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
                                    </div> -->
                                @endfor
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


