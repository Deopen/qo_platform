@extends('layouts.app')

<title>Create Questionnaire</title>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create Questionnaire</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('create_questioner') }}">
                        {!! csrf_field() !!}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input type="text" dir="rtl" class="form-control" name="name" value="{{ old('name') }}">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                                
                            </div>
                        </div>

 
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Description</label>

                            <div class="col-md-6">
                                <textarea rows=10 dir="rtl" id="description" class="form-control" name="description" value="{{ old('description') }}">
                                </textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('Description') }}</strong>
                                    </span>
                                @endif
                                
		                        @if ($errors->any())
		                            <span class="help-block">
		                                <strong><font color="red">{{ $errors->first() }}</font></strong>
		                            </span>
		                        @endif
	
                            </div>
                        </div>

 



                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i>Create
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$('document').ready(function() {
        $('#description').val($('#description').val().trim())
})
</script>
@endsection
