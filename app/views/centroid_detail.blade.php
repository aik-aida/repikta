@extends('layout.dashgum')

@section('content')
        <h3><i class="fa fa-angle-right"></i> Centroid</h3>
          <div class="row mt">
            <div class="col-lg-12">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> Dokumen Kluster Centroid {{ $id }} <a class="btn btn-warning pull-right" href={{ URL::to('centroid')}}>kembali</a></h4>
                          <section id="unseen">
                             - 
                            @foreach ($corpus as $dokumen)
                              {{$dokumen}} - 
                            @endforeach
                          </section>
                      </div><!-- /content-panel -->
            </div><!-- /col-lg-4 -->     
          </div><!-- /row -->
          <div class="row mt">
            <div class="col-lg-12">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> Detail Centroid {{ $id }} <a class="btn btn-warning pull-right" href={{ URL::to('centroid')}}>kembali</a></h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th>Kata Kamus</th>
                                  @for ($i = 1; $i <= count($data); $i++)
                                    <th>Centroid {{ $i }}</th>
                                  @endfor
                              </tr>
                              </thead>
                              <tbody>
                              
                                  @for ($i = 0; $i < count($kamus); $i++)
                                    
                                  <tr>
                                    <td>{{ $term = $kamus[$i]->kata_dasar }}</td>
                                    @for ($j = 0; $j < count($data); $j++)
                                      <td class="numeric">{{ $data[$j]->$term }}</td>
                                    @endfor
                                  </tr>
                                  @endfor
                              
                              </tbody>
                          </table>
                          </section>
                  </div><!-- /content-panel -->
               </div><!-- /col-lg-4 -->     
        </div><!-- /row -->
@endsection
