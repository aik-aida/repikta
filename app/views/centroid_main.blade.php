@extends('layout.dashgum')

@section('content')
        <h3><i class="fa fa-angle-right"></i> Centroid</h3>
          <div class="row mt">
            <div class="col-lg-12">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> Daftar Generated Centroid</h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th class="numeric">ID</th>
                                  <th class="numeric">Jumlah Kluster</th>
                                  <th class="numeric">Jumlah Dokumen</th>
                                  <th>Lihat Detail</th>
                              </tr>
                              </thead>
                              <tbody>
                              
                                  @foreach($centroids as $data)
                                  <tr>
                                    <td class="numeric">{{$data->id}}</td>
                                    <td class="numeric">{{$data->k}}</td>
                                    <td class="numeric">{{$data->jumlah_dokumen}}</td>
                                    <td>
                                      {{ Form::open(array('url' => 'centroid/detail', 'method' => 'post')) }}
                                      {{ Form::hidden('idcentroid', $data->id) }}
                                      {{ Form::submit('Detail Centroid', array('class' => 'btn btn-primary btn-sm')) }}
                                      {{ Form::close() }}
                                    </td>
                                  </tr>
                                  @endforeach
                              
                              </tbody>
                          </table>
                          </section>
                  </div><!-- /content-panel -->
               </div><!-- /col-lg-4 -->     
        </div><!-- /row -->
@endsection
