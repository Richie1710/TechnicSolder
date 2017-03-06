@extends('layouts/master')
@section('title')
		<title>{{ empty($mod->pretty_name) ? $mod->name : $mod->pretty_name }} - TechnicSolder</title>
@stop
@section('content')
<div class="page-header">
<h1>Mod Library</h1>
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
		<ul class="nav nav-tabs" id="tabs">
						<li class="active"><a href="#versions" data-toggle="tab">Versions</a></li>
						<li><a href="#details" data-toggle="tab">Details</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade" id="details">
				<br>
				@if ($errors->all())
					<div class="alert alert-danger">
					@foreach ($errors->all() as $error)
						{{ $error }}<br />
					@endforeach
					</div>
				@endif
				@if (Session::has('success'))
					<div class="alert alert-success">
						{{ Session::get('success') }}
					</div>
				@endif
				<form method="post" action="{{ URL::to('mod/modify/'.$mod->id) }}">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
													<label for="pretty_name">Mod Name</label>
													<input type="text" class="form-control" name="pretty_name" id="pretty_name" value="{{ $mod->pretty_name }}">
											</div>
											<div class="form-group">
													<label for="name">Mod Slug</label>
													<input type="text" class="form-control" name="name" id="name" value="{{ $mod->name }}">
											</div>
											<div class="form-group">
													<label for="author">Author</label>
													<input type="text" class="form-control" name="author" id="author" value="{{ $mod->author }}">
											</div>
											<div class="form-group">
													<label for="description">Description</label>
													<textarea name="description" id="description" class="form-control" rows="5">{{ $mod->description }}</textarea>
											</div>
											<div class="form-group">
													<label for="link">Mod Website</label>
													<input type="text" class="form-control" name="link" id="link" value="{{ $mod->link }}">
											</div>
											<div class="form-group">
													<label for="donatelink">Author Donation Link</label>
													<input type="text" class="form-control" name="donatelink" id="donatelink" value="{{ $mod->donatelink }}">
													<span class="help-block">This is only in use by the official Technic Solder</span>
											</div>
						</div>
					</div>
					{{ Form::submit('Save Changes', array('class' => 'btn btn-success')) }}
					{{ HTML::link('mod/delete/'.$mod->id, 'Delete Mod', array('class' => 'btn btn-danger')) }}
					{{ HTML::link('mod/list/', 'Go Back', array('class' => 'btn btn-primary')) }}
					{{ HTML::link('mod/list/', 'Sync', array('class' => 'btn btn-primary')) }}
				</form>
			</div>
			<div class="tab-pane fade in active" id="versions">
				<br>
				<p>Solder currently does not support uploading files directly to it. Your repository still needs to exist and follow a strict directory structure. When you add versions the URL will be verified to make sure the file exists before it is added to Solder. The directory stucture for mods is as follow:</p>
					<blockquote><strong>/mods/[modslug]/[modslug]-[version].zip</strong></blockquote>
				<table class="table">
					<thead>
						<th style="width: 1%"></th>
						<th style="width: 15%">Version</th>
						<th style="width: 25%">MD5</th>
						<th style="width: 30%">Download URL</th>
						<th style="width: 9%">Filesize</th>
						<th style="width: 10%"></th>
						<th style="width: 10%"></th>
					</thead>
					<tbody>
						<tr id="add-row">
							<form method="post" id="add" action="{{ URL::to('mod/add-version') }}">
								<input type="hidden" name="mod-id" value="{{ $mod->id }}">
								<td></td>
								<td>
									<input type="text" name="add-version" id="add-version" class="form-control"></td>
								<td>
									<input type="text" name="add-md5" id="add-md5" class="form-control" disabled="true"></td>
								</td>
								<td><span id="add-url">N/A</span></td>
								<td>N/A</td>
								<td><button type="submit" class="btn btn-success btn-small add">Add Version</button></td>
								<td><button type="button" id="fileuploadbutton" disabled="true" class="btn btn-success btn-small add" data-toggle="modal" data-target="#FileuploadModal">File Upload</button></td>
							</form>
						</tr>
						@foreach ($mod->versions()->orderBy('id', 'desc')->get() as $ver)
						<tr class="version" rel="{{ $ver->id }}">
							<form method="post" id="rehash" action="{{ URL::to('mod/rehash/') }}">
								<input type="hidden" name="version-id" value="{{ $ver->id }}">
								<input type="hidden" name="repopath" id="repopath" value={{ Config::get('solder.mirror_url')}}>
								<td><i class="version-icon fa fa-plus" rel="{{ $ver->id }}"></i></td>
								<td class="version" rel="{{ $ver->id }}">{{ $ver->version }}</td>
								<td><input type="text" class="md5 form-control" name="md5" id="md5" placeholder="{{ $ver->md5 }}" rel="{{ $ver->id }}"></input></td>
								<td class="url" rel="{{ $ver->id }}"><small><a href="{{ Config::get('solder.mirror_url').'mods/'.$mod->name.'/'.$mod->name.'-'.$ver->version.'.zip' }}">{{ Config::get('solder.mirror_url').'mods/'.$mod->name.'/'.$mod->name.'-'.$ver->version.'.zip' }}</a></small></td>
								<td>{{ $ver->humanFilesize("MB") }}</td>
								<td><button type="submit" class="btn btn-primary btn-xs rehash" rel="{{ $ver->id }}">Rehash</button> <button class="btn btn-danger btn-xs delete" rel="{{ $ver->id }}">Delete</button>
							</form>
						</tr>
						<tr class="version-details" rel="{{ $ver->id }}" style="display: none">
							<td colspan="5">
								<h5>Builds Used In</h5>
								<ul>
								@foreach ($ver->builds as $build)
									<li>{{ HTML::link('modpack/view/'.$build->modpack->id,$build->modpack->name) }} - {{ HTML::link('modpack/build/'.$build->id,$build->version) }}</li>
								@endforeach
								</ul>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>

				<div id="FileuploadModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">File Upload</h4>
      </div>
      <div class="modal-body">
        <p>This function is currently not available</p>
				<form enctype="multipart/form-data" method="post" id="add-file-upload" action="{{ URL::to('mod/add-version-file') }}">
					<div class="form-group">
							<label for="modslaug">Modslaug</label>
							<input type="text" class="form-control" name="modslaug" id="modslaug" readonly value="{{ $mod->name }}">
					</div>
					<div class="form-group">
							<label for="modversion">Modversion</label>
							<input type="text" class="form-control" name="modversion" id="modversion" readonly value="Javasript not run">
					</div>
					<div class="form-group" style="height:30px">
							<label for="fileupload">File for Upload.</label>
							<input type="file" class="form-control" name="fileupload" id="fileupload">
							<span class="help-block">The File will be renamed to given Version</span>
					</div>
				</form>

      </div>
      <div class="modal-footer">
				<button id="submitdownloadbutton" type="submit" class="btn btn-default" data-dismiss="modal">submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">close</button>
      </div>
    </div>

  </div>
</div>
						</div>
		</div>
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">


var mirror_url = '{{ Config::get("solder.mirror_url") }}';
var repo_location = '{{ Config::get("solder.repo_location") }}';

$('#add-version').keyup(function() {
	$("#add-url").html('<a href="' + mirror_url + 'mods/{{ $mod->name }}/{{ $mod->name }}-' + $(this).val() + '.zip" target="_blank">' + mirror_url + 'mods/{{ $mod->name }}/{{ $mod->name }}-' + $(this).val() + '.zip</a>');
	$("#modversion").val($(this).val());
	$("#fileuploadbutton").prop('disabled', false);
});

$('#submitdownloadbutton').click(function(){
	$("#add-file-upload").submit();

})

$('#add-file-upload').submit(function(e) {
	e.preventDefault();
	var file_data = $('#fileupload').prop('files')[0];
	console.log(file_data);
  var form_data = new FormData();
	form_data.append('modslaug', $("#modslaug").val());
	form_data.append('modversion', $("#modversion").val());
	form_data.append('repo_location', repo_location );
	form_data.append('fileupload', file_data);
	for (var pair of form_data.entries()) {
    console.log(pair[0]+ ', ' + pair[1]);
  }
	$.ajax({
		type: "POST",
		url: "{{ URL::to('mod/add-version-file') }}",
		data: form_data,
		dataType: 'json',
		cache: false,
		processData: false, // Don't process the files
    contentType: false, // Set content type to false as jQuery will tell the server its a query string request
		success: function (data) {
			if (data.status == "success") {
				$("#add-row").after('<tr><td></td><td>' + data.version + '</td><td>' + data.md5 + '</td><td><a href="' + mirror_url + 'mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip" target="_blank">{mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip</a></td><td>' + data.filesize + '</td><td></td></tr>');
				$.jGrowl('Added mod version at ' + data.version, { group: 'alert-success' });
			} else if (data.status == "warning") {
				$("#add-row").after('<tr><td></td><td>' + data.version + '</td><td>' + data.md5 + '</td><td><a href="' + mirror_url + 'mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip" target="_blank">' + mirror_url + 'mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip</a></td><td>' + data.filesize + '</td><td></td></tr>');
				$.jGrowl('Added mod version at ' + data.version + ". " + data.reason, { group: 'alert-warning' });
			} else {
				$.jGrowl('Error: ' + data.reason, { group: 'alert-danger' });
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$.jGrowl(textStatus + ': ' + errorThrown, { group: 'alert-danger' });
		}
	})
})

$('#add').submit(function(e) {
	e.preventDefault();
	console.log($("#add").serialize());
	if ($('#add-version').val() != "") {
		$.ajax({
			type: "POST",
			url: "{{ URL::to('mod/add-version/') }}",
			data: $("#add").serialize(),
			success: function (data) {
				if (data.status == "success") {
					$("#add-row").after('<tr><td></td><td>' + data.version + '</td><td>' + data.md5 + '</td><td><a href="' + mirror_url + 'mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip" target="_blank">{mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip</a></td><td>' + data.filesize + '</td><td></td></tr>');
					$.jGrowl('Added mod version at ' + data.version, { group: 'alert-success' });
				} else if (data.status == "warning") {
					$("#add-row").after('<tr><td></td><td>' + data.version + '</td><td>' + data.md5 + '</td><td><a href="' + mirror_url + 'mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip" target="_blank">' + mirror_url + 'mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip</a></td><td>' + data.filesize + '</td><td></td></tr>');
					$.jGrowl('Added mod version at ' + data.version + ". " + data.reason, { group: 'alert-warning' });
				} else {
					$.jGrowl('Error: ' + data.reason, { group: 'alert-danger' });
				}
			},
			error: function (xhr, textStatus, errorThrown) {
				$.jGrowl(textStatus + ': ' + errorThrown, { group: 'alert-danger' });
			}
		})
	}
});

$('.version-icon').click(function() {
	$('.version-details[rel=' + $(this).attr('rel') + "]").toggle(function() {
		$('.version-icon[rel=' + $(this).attr('rel') + "]").toggleClass("fa-minus");
	});
});

$('.rehash').click(function(e) {
	e.preventDefault();
	$(".md5[rel=" + $(this).attr('rel') + "]").fadeOut();

	console.log($("#rehash").serialize());
	$.ajax({
		type: "POST",
		url: "{{ URL::to('mod/rehash/') }}",
		data: $("#rehash").serialize(),
		success: function (data) {
			if (data.status == "success") {
				$.jGrowl('MD5 hashing complete.', { group: 'alert-success' });
			} else if (data.status == "warning") {
				$.jGrowl('MD5 hashing complete. ' + data.reason, { group: 'alert-warning' });
			} else {
				$.jGrowl('Error: ' + data.reason, { group: 'alert-danger' });
			}
			$(".md5[rel=" + data.version_id + "]").attr('placeholder', data.md5);
			$(".md5[rel=" + data.version_id + "]").fadeIn();
		},
		error: function (xhr, textStatus, errorThrown) {
			$.jGrowl(textStatus + ': ' + errorThrown, { group: 'alert-danger' });
		}
	});
});

$('.delete').click(function(e) {
	e.preventDefault();
	$.ajax({
		type: "GET",
		url: "{{ URL::to('mod/delete-version') }}/" + $(this).attr('rel'),
		success: function (data) {
			if (data.status == "success") {
				$('.version[rel=' + data.version_id + ']').fadeOut();
				$('.version-details[rel=' + data.version_id + ']').fadeOut();
				$.jGrowl('Mod version ' + data.version + ' deleted.', { group: 'alert-success' });
			} else {
				$.jGrowl('Error: ' + data.reason, { group: 'alert-danger' });
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$.jGrowl(textStatus + ': ' + errorThrown, { group: 'alert-danger' });
		}
	});
});

</script>
@endsection
