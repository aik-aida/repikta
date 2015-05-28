@extends('layout.dashgum')

@section('content')
        <h3><i class="fa fa-angle-right"></i> Daftar Dokumen</h3>
          <div class="row mt">
            <div class="col-lg-12">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> kata : {{ $term }}</h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th>No</th>
                                  <th>Nrp</th>
                                  <th>Nama</th>
                                  <th>Judul Tugas Akhir</th>
                                  <th>Detail Dokumen</th>
                              </tr>
                              </thead>
                              <tbody>
                                  @for ($i = 0; $i < count($daftar_doc); $i++)
                                  <tr>
                                    <td>{{ ($i+1) }}</td>
                                    <td>{{$daftar_doc[$i]->nrp}}</td>
                                    <td>{{$daftar_doc[$i]->nama}}</td>
                                    <td>{{$daftar_doc[$i]->judul_ta}}</td>
                                    <td>
                                      {{ Form::open(array('url' => 'rekomendasi/dokumen/detail', 'method' => 'post')) }}
                                      {{ Form::hidden('iddoc', $daftar_doc[$i]->nrp) }}
                                      {{ Form::hidden('term', $term) }}
                                      {{ Form::submit('Lihat Abstraksi', array('class' => 'btn btn-round btn-primary')) }}
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
