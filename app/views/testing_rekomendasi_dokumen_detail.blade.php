@extends('layout.dashgum')

@section('content')
        <h3><i class="fa fa-angle-right" style="color:black"></i> Dokumen, kata : {{ $term }}</h3>
          <div class="row mt" style="color:black">
            <div class="col-lg-12">
                      <div class="content-panel">
                      <h4><i class="fa fa-angle-right"></i> Detail Dokumen 
                      <a class="btn btn-warning pull-right" href={{ URL::to('testing/dokumen', array('kata' =>$term ))}}>kembali</a> </h4> 
                          <section id="unseen">
                            <table class="table table-bordered table-striped table-condensed">
                              <thead>
                                  <th>Detail</th>
                                  <th>Nilai</th>
                              </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                    <td class="numeric">NRP</td>
                                    <td class="numeric">{{$dokumen->nrp}}</td>
                                  </tr>
                                  <tr>
                                    <td class="numeric">NAMA</td>
                                    <td class="numeric">{{$dokumen->nama}}</td>
                                  </tr>
                                  <tr>
                                    <td class="numeric">EMAIL</td>
                                    <td class="numeric">{{$dokumen->email}}</td>
                                  </tr>
                                  <tr>
                                    <td class="numeric">JUDUL TA</td>
                                    <td class="numeric">{{$dokumen->judul_ta}}</td>
                                  </tr>
                                  <tr>
                                    <td class="numeric">ABSTRAKSI TA</td>
                                    <td class="numeric">{{$dokumen->abstraksi_ta}}</td>
                                  </tr>
                                  <tr>
                                    <td class="numeric">PEMBIMBING 1</td>
                                    <td class="numeric">{{$dokumen->pembimbing1}}</td>
                                  </tr>
                                  <tr>
                                    <td class="numeric">PEMBIMBING 2</td>
                                    <td class="numeric">{{$dokumen->pembimbing2}}</td>
                                  </tr>
                              </tbody>
                          </table>
                          </section>
                  </div><!-- /content-panel -->
               </div><!-- /col-lg-4 -->     
        </div><!-- /row -->
@endsection
