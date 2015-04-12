@extends('layout.dashgum')

@section('content')
        <h3><i class="fa fa-angle-right"></i> Kluster</h3>
          <div class="row mt">
            <div class="col-lg-12">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> Daftar Kluster Dokumen</h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th class="numeric">ID Kluster</th>
                                  <th class="numeric">Jumlah Kluster</th>
                                  <th class="numeric">ID Centroid Awal</th>
                                  <th class="numeric">Jumlah Dokumen</th>
                                  <th class="numeric">Jumlah Iterasi</th>
                                  <th class="numeric">Lama Eksekusi</th>
                                  <th> Lihat Kluster Dokumen </th>
                              </tr>
                              </thead>
                              <tbody>
                                  
                                  @for($i=0 ; $i<$jumlah ; $i++)
                                  <tr>
                                    <td class="numeric">{{ $data[$i]->id_group }}</td>
                                    <td class="numeric">{{ $data[$i]->jumlah_kluster }}</td>
                                    <td class="numeric">{{ $data[$i]->centroid_awal }}</td>
                                    <td class="numeric">{{ $data[$i]->jumlah_dokumen }}</td>
                                    <td class="numeric">{{ $niterasi[$i] }} iterasi</td>
                                    <td class="numeric">{{ $nwaktu[$i] }} detik</td>
                                    <td>
                                      {{ Form::open(array('url' => 'kluster/detail', 'method' => 'post')) }}
                                      {{ Form::hidden('idkluster', $iddata[$i]) }}
                                      {{ Form::submit('Lihat Kluster', array('class' => 'btn btn-primary btn-sm')) }}
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
