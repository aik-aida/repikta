@extends('layout.blank_page')

@section('content')
            <div class="row mt" style="color:black">
                  <div class="col-md-12">
                      <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Rekomendasi Topik Tugas Akhir </h2></div>
                    </div>
                          <div class="panel-body" align="center">
                              <div class="task-content">
                                  <ul class="task-list">
                                      <li>
                                          <div class="task-title">
                                              <h4>
                                              <span class="badge bg-warning">nrp</span>
                                              <span class="task-title-sp"> {{ $data->nrp }} </span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              <span class="badge bg-theme">nama</span>
                                              <span class="task-title-sp"> {{ $data->nama }} </span>
                                              <span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span>
                                              </h4>
                                          </div>
                                      </li>                                 
                                  </ul>
                              </div>
                              <div class="task-content">
                                <ul class="task-list">
                                  <li>
                                    <div class="task-title">
                                      Anda direkomendasikan memilih Tugas Akhir pada bidang : <br />
                                      <div class="" align="center"><h2>{{ $bidang }}</h2></div>
                                    </div>
                                  </li>                                 
                                </ul>
                              </div>
                              <div class="add-task-row">
                                Berikut Topik yang direkomendasikan menurut prosentase kedekatan :
                              </div>
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->

            <div class="row mt" style="color:black">
             @for ($i = 0; $i < $ktopik; $i++)
                @if($muncul_topik[$i]!=0)
                   <div class="col-lg-2 col-md-2 col-sm-12">
                          <div class="content-panel">
                              <h4><i class="fa fa-angle-right"></i> Topik {{ ($idmuncul[$i]+1) }}</h4>
                              <section id="unseen">
                                <table class="table table-bordered table-striped table-condensed">
                                  <thead>
                                  <tr>
                                      <th align="center">
                                        [{{ ($muncul_topik[$i]/$n)*100 }}%]
                                      </th>
                                  </tr>
                                  <tr>
                                      <th>Daftar Kata</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                    @for ($x = 0; $x < $nshow; $x++)
                                        <tr>
                                          <td align="center">
                                            <a  href={{ URL::to('rekomendasi/dokumen', array('kata' =>$topic[$idmuncul[$i]][$x] ))}} target="_blank">{{ $topic[$idmuncul[$i]][$x] }}</a>
                                            
                                          </td>
                                        </tr>
                                    @endfor
                                  </tbody>
                              </table>
                              </section>
                          </div><!-- /content-panel -->
                   </div><!-- /col-lg-4 -->
                @endif         
              @endfor
            </div><!-- /row -->

@endsection