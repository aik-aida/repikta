@extends('layout.dashgum')

@section('content')
            <div class="row mt" >
                  <div class="col-md-12">
                  <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Daftar Topik Bidang {{ $nama_bidang }} [{{$ktopik}} topik] </h2></div>
                    </div>
                    <div class="panel-body" align="center">
                          <ul class="task-list">
                            <li>
                              <div class="task-title">
                                {{ Form::open(array('url' => 'dashboard', 'method' => 'get')) }}
                                {{ Form::submit('Kembali ke Daftar Bidang', array('class' => 'btn btn-round btn-warning btn-lg pull-center')) }}
                                {{ Form::close() }}
                              </div>
                            </li>                                 
                          </ul>

                          <div class="add-task-row" style="color:black">
                              <br />(*)tekan pada salah satu kata untuk memahami lebih mengenai topik<br />
                                <table class="table table-bordered table-striped table-condensed">
                                      <thead>
                                      <tr>
                                          <th>Topik</th>
                                          <th>Kata-Kata Topik</th>
                                      </thead>
                                      <tbody>
                                      
                                      @for ($i = 0; $i < $ktopik; $i++)
                                        <tr>
                                          <td> Topik {{ ($i+1) }}</td>
                                          <td>
                                            @for ($x = 0; $x < count($list_topic[$i]); $x++)
                                              <a  href={{ URL::to('rekomendasi/dokumen', array('kata' =>$list_topic[$i][$x] ))}}>{{ $list_topic[$i][$x] }}</a>, &nbsp;
                                            @endfor
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