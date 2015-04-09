@extends('layout.dashgum')

@section('content')
        <h3><i class="fa fa-angle-right"></i> Dokumen</h3>
          <div class="row mt">
            <div class="col-lg-12">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> Vektor Nilai Tf Dokumen {{ $id }} <a class="btn btn-warning pull-right" href={{ URL::to('dokumen')}}>kembali</a> </h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th class="numeric">ID</th>
                                  <th>Term</th>
                                  <th class="numeric">Nilai Tf Term pada Dokumen {{ $id }}</th>
                              </tr>
                              </thead>
                              <tbody>
                              
                                  @for ($i = 0; $i < count($kamus); $i++)
                                  <tr>
                                    <td class="numeric">{{ ($i+1) }}</td>
                                    <td>{{ $term = $kamus[$i]->kata_dasar }}</td>
                                    <td class="numeric">{{$vectortfidf->$term}}</td>
                                  </tr>
                                  @endfor
                              
                              </tbody>
                          </table>
                          </section>
                  </div><!-- /content-panel -->
               </div><!-- /col-lg-4 -->     
        </div><!-- /row -->
@endsection
