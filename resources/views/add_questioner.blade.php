@extends('layouts.app')

    <script type="text/javascript" src="{{ asset('js/jquery/jquery-2.2.4') }}"></script>

    <script type="text/javascript">
    
    $("document").ready(function () {
        $("body").append("hi");
    });
    

    </script>


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add Questioner</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/add_questioner') }}">
                        {!! csrf_field() !!}
                        <input type="hidden" name="project_id" value="{{$project_id}}">
                        

                         <div class="form-group{{ $errors->has('questioners') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Questioners</label>

                            <div class="col-md-6">
                                <input type="list" class="form-control" name="name" value="{{ old('questioners') }}">

                                @if ($errors->has('questioners'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('questioners') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-save"></i>Save
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
