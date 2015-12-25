@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-sm-offset-2 col-sm-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					Welcome!
				</div>
                <div class="panel-body">
                @if (Auth::check())
				    You are logged in, {{ Auth::user()->name }}.
                @else
				    Please register or login.	
                @endif
				</div>
			</div>
		</div>
	</div>
@endsection
