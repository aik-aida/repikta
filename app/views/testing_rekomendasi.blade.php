@extends('layout.dashgum')

@section('content')
            <div class="row mt" style="color:black">
                  <div class="col-md-12">
                      <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Rekomendasi Topik Tugas Akhir </h2></div>
                    </div>
                          <div class="panel-body" align="center">
                           
                                      
                                              <h4>
                                              <span class="badge bg-warning">nrp</span>
                                              <span class="task-title-sp"> {{ $data->nrp }} </span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              <span class="badge bg-theme">nama</span>
                                              <span class="task-title-sp"> {{ $data->nama }} </span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              </h4>
                              

                                <ul class="task-list">
                                  <li>
                                    <div class="task-title">
                                      Anda direkomendasikan memilih Tugas Akhir pada bidang : <br />
                                      <div class="" align="center"><h2>{{ $bidang }}</h2></div>
                                    </div>
                                  </li>                                 
                                </ul>
                              <div class="add-task-row">
                              <br /><h5><b>Berikut Topik yang direkomendasikan menurut prosentase kedekatan :</h5></b>
                              (*)tekan pada salah satu kata untuk memahami lebih mengenai topik<br />
                                <table class="table table-bordered table-striped table-condensed">
                                      <thead>
                                      <tr>
                                          <th>Topik</th>
                                          <th>Kedekatan Topik</th>
                                          <th>Kata-Kata Topik</th>
                                      </thead>
                                      <tbody>
                                      
                                      @for ($i = 0; $i < $ktopik; $i++)
                                      @if($muncul_topik[$i]!=0)
                                        <tr>
                                          <td> Topik {{ ($idmuncul[$i]+1) }}</td>
                                          <td>{{ ($muncul_topik[$i]/$n)*100 }}%</td>
                                          <td>
                                            @for ($x = 0; $x < $nshow; $x++)
                                              <a  href={{ URL::to('testing/dokumen', array('kata' =>$topic[$idmuncul[$i]][$x] ))}} target="_blank">{{ $topic[$idmuncul[$i]][$x] }}</a>, &nbsp;
                                            @endfor
                                          </td>
                                        </tr>  
                                      @endif         
                                      @endfor
                                                                      
                                      </tbody>
                                </table>
                              </div>
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->
@endsection