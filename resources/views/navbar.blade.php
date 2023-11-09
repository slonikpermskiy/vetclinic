 
<style>
    .navbar-toggler:focus {
        border-color: #A6C7FF;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(166, 199, 255, 0.5);
    }
	
	.nav-item.nav-link.active {
		color: #FF0000 !important
	}
	
		
	@media (max-width: 767px) {
		.dropdown-menu {
			word-wrap: break-word;
			white-space: normal;
		}
	}
	
	@media (min-width: 768px) {
		.dropdown-menu {		
				word-wrap: normal;
				white-space:nowrap;
			}
	}

	
</style>

<script>

	$(document).ready(function () {

	});

</script>


<header>
    <nav name="navbar" id="navbar" class="navbar navbar-expand-lg navbar-light bg-white bg-gradient fixed-top px-3 px-lg-5 py-2 border-bottom mycontainer">
        <a href="{{url('/')}}" class="navbar-brand">
			<div class="">
				<img src="{{ asset('images/dogicon.png') }}" height="50" alt="ДокторВет">	
				<div class="h3 align-middle d-inline-flex px-2">Доктор Вет</div>
			</div>	
        </a>
		
		
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav">
			
				<a href="{{url('/')}}" class="nav-item nav-link {{ Request::is('/') | Request::is('addanimal') | Request::is('patientcard') ? 'active' : '' }}">
                    ПАЦИЕНТЫ
                </a>
				
				<a href="bills" name="billsmenu" id="billsmenu" class="nav-item nav-link {{ Request::is('bills') ? 'active' : '' }}">
                    СЧЕТА
                </a>
								
				@php
				{{
					 $staff = \App\Staff::where('staff_id', Auth::user()->staff_id)->first();
					 $position = 0;
					 
					 if ($staff !== null) {
						$position = $staff->position;
					 }
					 					
					if (Auth::user()->staff_id == 0 | $position ==1) {
					
						if (Request::is('staff')) {		
							echo('<a name="staffmenu" id="staffmenu" href="staff" class="nav-item nav-link active">СОТРУДНИКИ</a>');
						} else {
							echo('<a name="staffmenu" id="staffmenu" href="staff" class="nav-item nav-link">СОТРУДНИКИ</a>');	
						}
						
					}
					
					if (Auth::user()->staff_id == 0 | $position ==1 | $position ==2) {
						
						if (Request::is('guides')) {		
							echo('<a name="guidesmenu" id="guidesmenu" href="guides" class="nav-item nav-link active">СПРАВОЧНИКИ</a>');
						} else {
							echo('<a name="guidesmenu" id="guidesmenu" href="guides" class="nav-item nav-link">СПРАВОЧНИКИ</a>');	
						}
					}
				
				}}
				@endphp

            </div>
            <div class="navbar-nav ms-auto">
			
				<div class="btn-group dropstart">							  
					<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button">	
						<img class="img-responsive" width="40" height="40" src="images/account.png">
					</a>
					<div class="dropdown-menu mt-1">
					
						<div class="flex-none mx-3">
										
						@php
						{{											
							if (Auth::user()->staff_id == 0) {
								echo('<div name="staff_position" id="staff_position" class="text-start text-primary text-break align-self-center">Разработчик</div>');	
							} else {
								if ($position == 1) {
									echo('<div name="staff_position" id="staff_position" class="text-start text-primary text-break align-self-center">Администратор</div>');
									
								} else if ($position == 2) {
									echo('<div name="staff_position" id="staff_position" class="text-start text-primary text-break align-self-center">Врач</div>');
								} else if ($position == 3) {
									echo('<div name="staff_position" id="staff_position" class="text-start text-primary text-break align-self-center">Ассистент</div>');
								}
							}
							
							
							if ($staff !== null) {
								$last_name = $staff->last_name;
								$first_name = ' '.$staff->first_name;
								$middle_name = ' '.$staff->middle_name;
										
								echo('<div name="user" id="user" class="text-start align-self-center">'.$last_name.''.$first_name.''.$middle_name.'</div>');	
							 }
							
						}}
						@endphp
									
						<a name="logout_link"  id='logout_link' class="nav-item nav-link p-0 d-flex justify-content-start my-1 border-primary text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
							Выйти
						</a>

						</div>
															
						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							@csrf
						</form>

					</div>
				</div>
            </div>
        </div>
    </nav>
</header>﻿