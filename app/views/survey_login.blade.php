@extends('layout.blank_page')

@section('content')

	
	  	<div class="container">
	  		{{ Form::open(array('url' => 'survey/penjelasan', 'method' => 'post', 'class' => 'form-login')) }}
		      <form class="form-login" >
		        <h2 class="form-login-heading">Data Penilai</h2>
		        <div class="login-wrap">
		        	<br>
		        	{{Form::text('nrp', null, array('class' => 'form-control', 'placeholder' => 'nrp'))}}
                    <br>
                    {{Form::text('nama', null, array('class' => 'form-control', 'placeholder' => 'nama'))}}
                    <br>
                    {{ Form::submit('MASUK', array('class' => 'btn btn-theme btn-block')) }} 
		            <!-- <input type="text" class="form-control" placeholder="nrp" autofocus>
		            <br>
		            <input type="text" class="form-control" placeholder="nama">
		            <br>
		            <button class="btn btn-theme btn-block" href="index.html" type="submit"><i class="fa fa-lock"></i> MASUK </button>
		             --><hr>
		            
		            <div class="login-social-link centered">
		            <p>Anda memasuki halaman survey untuk penilaian Sistem Rekomendasi Topik TA [REPIKTA] --- 
		            	Masukkan data nrp dan nama untuk memulai penilaian
		            </p>

		            <p></p>
		            </div>
		
		        </div>
		
		      </form>
		      <br>
		      {{ Form::close() }}
	  	</div>

@endsection