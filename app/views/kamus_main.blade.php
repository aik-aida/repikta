@extends('layout.dashgum')

@section('content')
        <h3><i class="fa fa-angle-right"></i> Master</h3>
          <div class="row mt">
            <div class="col-lg-12">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> Kamus Kata Dasar</h4>
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                              <tr>
                                  <th class="numeric">ID</th>
                                  <th>Kata Dasar</th>
                                  <th class="numeric">Nilai Idf Term</th>
                              </tr>
                              </thead>
                              <tbody>
                              
                                  @foreach($kamus as $kata)
                                  <tr>
                                    <td class="numeric">{{$kata->id}}</td>
                                    <td>{{$kata->kata_dasar}}</td>
                                    <td class="numeric">{{$kata->idf}}</td>
                                  </tr>
                                  @endforeach
                              
                              </tbody>
                          </table>
                          </section>
                  </div><!-- /content-panel -->
               </div><!-- /col-lg-4 -->     
        </div><!-- /row -->
@endsection
