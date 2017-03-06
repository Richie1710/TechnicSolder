@extends('layouts/master')
@section('title')
		<title>{{ empty($mod->pretty_name) ? $mod->name : $mod->pretty_name }} - TechnicSolder</title>
@stop
@section('content')
<div class="page-header">
<h1>ForgURL Sync Wizzard</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	@if (!empty($mod->pretty_name))
		{{ $mod->pretty_name }} <small>{{ $mod->name }}</small>
	@else
		{{ $mod->name }}
	@endif
	</div>
	<div class="panel-body">

</div>
@endsection
@section('bottom')
@endsection
