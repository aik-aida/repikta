@extends('layout.dashgum')

@section('content')
        <h3><i class="fa fa-angle-right"></i> Dokumen</h3>
          <div class="row mt">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> Vektor Nilai Tf Dokumen {{ $id }} <a class="btn btn-warning pull-right" href={{ URL::to('dokumen')}}>kembali</a> </h4>
                          <section id="unseen">
          </div>
          <div class="row mt" style="color:black">
                      <div class="col-lg-6 col-md-6 col-sm-12">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th class="numeric">ID</th>
                                  <th>Term</th>
                                  <th class="numeric">Nilai Tf Term Judul</th>
                              </tr>
                              </thead>
                              <tbody>
                              
                                  @for ($i = 0; $i < count($kamus_judul); $i++)
                                  <tr>
                                    <td class="numeric">{{ ($i+1) }}</td>
                                    <td>{{ $term = $kamus_judul[$i] }}</td>
                                    <td class="numeric">{{$vectortfjudul[$term]}}</td>
                                  </tr>
                                  @endfor
                              
                              </tbody>
                            </table>
                      </div>
                      <div class="col-lg-6 col-md-6 col-sm-12">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th class="numeric">ID</th>
                                  <th>Term</th>
                                  <th class="numeric">Nilai Tf Term Abstraksi</th>
                              </tr>
                              </thead>
                              <tbody>
                              
                                  @for ($i = 0; $i < count($kamus_abstrak); $i++)
                                  <tr>
                                    <td class="numeric">{{ ($i+1) }}</td>
                                    <td>{{ $term = $kamus_abstrak[$i] }}</td>
                                    <td class="numeric">{{$vectortfabstrak[$term]}}</td>
                                  </tr>
                                  @endfor
                              
                              </tbody>
                            </table>
                      </div>
          </div>
                          </section>
                  </div><!-- /content-panel -->
               
        </div><!-- /row -->
@endsection
