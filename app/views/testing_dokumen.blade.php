@extends('layout.dashgum')

@section('content')
        <h3><i class="fa fa-angle-right" style="color:black"></i> Master</h3>
          <div class="row mt" style="color:black">
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
                                  <th>Lihat Transkrip</th>
                              </tr>
                              </thead>
                              <tbody>
                              
                                  
                                  @for ($i = 0; $i < count($data); $i++)
                                  <tr>
                                    <td>{{ ($i+1) }}</td>
                                    <td>{{$data[$i]->nrp}}</td>
                                    <td>{{$data[$i]->nama}}</td>
                                    <td>
                                      {{ Form::open(array('url' => 'testing/transkrip', 'method' => 'post')) }}
                                      {{ Form::hidden('nrp', $data[$i]->nrp) }}
                                      {{ Form::submit('Lihat Transkrip', array('class' => 'btn btn-round btn-success')) }}
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
