@extends('layout.dashgum')

@section('content')
            <div class="row mt" >
                  <div class="col-md-12">
                  <section class="task-panel tasks-widget">
                    <div class="panel-heading">
                          <div class="" align="center"><h2><i class="fa fa-tasks"></i> Daftar Topik Bidang {{ $nama_bidang }} [{{$ktopik}} topik] </h2></div>
                    </div>
                    <div class="panel-body" align="center">
                        <div class="task-content">
                          <ul class="task-list">
                            <li>
                              <div class="task-title">
                                {{ Form::open(array('url' => 'dashboard', 'method' => 'get')) }}
                                {{ Form::submit('Kembali ke Daftar Bidang', array('class' => 'btn btn-round btn-warning btn-lg pull-center')) }}
                                {{ Form::close() }}
                              </div>
                            </li>                                 
                          </ul>
                        </div>
                    </div>
                  </section>
                  </div><!-- /col-md-12-->
            </div><!-- /row -->

            <div class="row mt">
             @for ($i = 0; $i < $ktopik; $i++)
                   <div class="col-lg-2 col-md-2 col-sm-12">
                          <div class="content-panel">
                              <h4><i class="fa fa-angle-right"></i> Topik {{ ($i+1) }}</h4>
                              <section id="unseen">
                                <table class="table table-bordered table-striped table-condensed">
                                  <thead>
                                  <tr>
                                      <th>Daftar Kata</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                    @for ($x = 0; $x < count($list_topic[$i]); $x++)
                                        <tr>
                                          <td align="center">
                                            <a  href={{ URL::to('rekomendasi/dokumen', array('kata' =>$list_topic[$i][$x] ))}}>{{ $list_topic[$i][$x] }}</a>
                                            
                                          </td>
                                        </tr>
                                    @endfor
                                  </tbody>
                              </table>
                              </section>
                          </div>
                   </div>   
              @endfor
            </div>
@endsection