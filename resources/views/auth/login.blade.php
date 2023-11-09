<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

<style>

.form-control:focus {
	border-color: #8DB2EC;   
	box-shadow: 0 0 1px 1px #8DB2EC !important;
}

.form-group label {
	color: #3182CE !important;
	font-weight: 600;
}

.underinput-error{
	font-size:14px;
	font-weight: 600;
}

</style>

@extends('layouts.app')

@section('content')


<div class="vh-100 d-flex justify-content-center align-middle">
    <div class="row col-8 col-md-6 col-lg-4 col-xl-3 justify-content-center align-middle">       
		<div class="d-flex justify-content-center align-items-center">      	
			<div class="card text-center bg-default mb-3" style="width: 100%;">
				<div class="card-header">
					<div class="align-middle">
						<img src="{{ asset('images/dogicon_sm.png') }}" height="30" alt="ДокторВет">	
						<div class="h5 align-middle d-inline-flex px-2 mb-0">Доктор Вет</div>
					</div>	
				</div>
				
				<form method="POST" action="{{ route('login') }}" class="mb-0">
				@csrf
			
					<div class="card-body">
							
						<div class="px-2 mb-2">
							<div class="form-group">							
								<label class='control-label font-weight-bold' for="username">Логин <span class="text-danger"></span></label>
								<input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder='Логин' autofocus>
							</div>							
						</div>
							
						<div class="px-2 mb-3">
							<div class="form-group">
								<label class='control-label font-weight-bold' for="password">Пароль <span class="text-danger"></span></label>
								<input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" placeholder='Пароль'>
							</div>							
						</div>
							
						@if(count( $errors ) > 0)
							@foreach ($errors->all() as $error)

							   <div class="px-2 my-2">
							   
							   <span class="error_response underinput-error text-danger justify-content-center d-flex align-items-lg-center">
									{{ $error }}
								</span>	
								
								</div>	
							   
							@endforeach
						@endif

					</div>
					
					<div class="card-footer text-muted">
						<button type="submit" class="btn btn-secondary">
							Вход
						</button>					
					</div>
					
				</form>
			</div>
		</div>
    </div>
</div>

@endsection
