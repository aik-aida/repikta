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
                                      
                                      @for ($i = 0; $i < $ntopik; $i++)
                                      
                                        <tr>
                                          <td>{{ $nama_topic[$i] }}</td>
                                          <td>{{ $bobot[$i] }}%</td>
                                          <td>
                                            @foreach ($daftar[$i] as $kata)
                                              <a  href={{ URL::to('testing/dokumen', array('kata' =>$kata, 'kelompok'=>$id_klp ))}} target="_blank">{{ $kata }}</a>, &nbsp;
                                            @endforeach
                                          </td>
                                        </tr>  
                                      @endfor
                                                                      
                                      </tbody>
                                </table>
                              </div>
                          </div>
                      </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->
@endsection