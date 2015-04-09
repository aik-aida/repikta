@extends('layout.dashgum')

@section('content')
        <h3><i class="fa fa-angle-right"></i> Master</h3>
          <div class="row mt">
            <div class="col-lg-12">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> Dokumen repikTA</h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th>No</th>
                                  <th>Nrp</th>
                                  <th>Nama</th>
                                  <th>Judul Tugas Akhir</th>
                                  <th>Abstraksi Tugas Akhir</th>
                                  <th>Nilai <br /> Tf Dokumen</th>
                                  <th>Nilai <br /> Tf-Idf Dokumen</th>
                              </tr>
                              </thead>
                              <tbody>
                              
                                  
                                  @for ($i = 0; $i < count($corpus); $i++)
                                  <tr>
                                    <td>{{ ($i+1) }}</td>
                                    <td>{{$corpus[$i]->nrp}}</td>
                                    <td>{{$corpus[$i]->nama}}</td>
                                    <td>{{$corpus[$i]->judul_ta}}</td>
                                    <td>
                                      {{ Form::open(array('url' => 'dokumen/detail', 'method' => 'post')) }}
                                      {{ Form::hidden('iddoc', $corpus[$i]->nrp) }}
                                      {{ Form::submit('Lihat Abstraksi', array('class' => 'btn btn-round btn-success')) }}
                                      {{ Form::close() }}
                                    </td>
                                    <td>
                                      {{ Form::open(array('url' => 'dokumen/nilai_tf', 'method' => 'post')) }}
                                      {{ Form::hidden('iddoc', $corpus[$i]->nrp) }}
                                      {{ Form::submit('Lihat Nilai Tf', array('class' => 'btn btn-round btn-info')) }}
                                      {{ Form::close() }}
                                    </td>
                                    <td>
                                      {{ Form::open(array('url' => 'dokumen/nilai_tfidf', 'method' => 'post')) }}
                                      {{ Form::hidden('iddoc', $corpus[$i]->nrp) }}
                                      {{ Form::submit('Lihat Nilai Tf-Idf', array('class' => 'btn btn-round btn-primary')) }}
                                      {{ Form::close() }}
                                    </td>
                                  </tr>
                                  @endfor
                              
                              </tbody>
                          </table>
                          </section>
                  </div><!-- /content-panel -->
               </div><!-- /col-lg-4 -->     
        </div><!-- /row -->
@endsection
