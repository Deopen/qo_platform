@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Bulk User Input</div>
                <div class="panel-body">
                    <form method="POST" action="/bulk_input_upload" enctype="multipart/form-data">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('input_file') ? ' has-error' : '' }}">
                            
                            <label class="col-md-4 control-label">Input (file-extension: csv,txt) </label>

                            <label class="col-md-4 control-label">Examples: Members.txt , Members.csv</label>

                            <div class="col-md-1">

                                <input type="hidden" name="project_id" value="{{$project_id}}">
                                <input type="file" name="input_file"
                                accept=".csv,.txt">

                                @if ($errors->has('input_file'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('input_file') }}</strong>
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
                                    <i class="fa fa-btn fa-upload"></i>Upload
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
