@extends('layouts.app')

@section('content')
<div class="container">
{!! Toastr::message() !!}
    <div class="row">
    <div class="col-md-12">
        <div class="card">
        <div class="card-header">{{ __('Post List') }}</div>
        <div class="card-body">
        <div class="row mb-2 search-bar d-flex justify-content-end">
        <div class="col-auto">
    <label for="searchKeyword" class="col-form-label">Keyword:</label>
  </div>
  <div class="col-auto">
    <input type="text" id="searchKeyword" class="form-control">
  </div>
        <div class="col-auto">
        <a class="btn btn-primary header-btn mt-sm-1" href="/post/search">{{ __('Search') }}</a>
        @if(auth()->user() && (auth()->user()->type == 0 || auth()->user()->type == 1))
            <a class="btn btn-primary header-btn mt-sm-1" href="/post/create">{{ __('Create') }}</a>
            <a class="btn btn-primary header-btn mt-sm-1" href="/post/upload">{{ __('Upload') }}</a>
            @endif
            <a class="btn btn-primary header-btn mt-sm-1" href="/post/download">{{ __('Download') }}</a>
            </div>
          </div>

        <table class="table table-hover table-bordered" id="post-list">
            <thead>
              <tr>
                <th class="header-cell" scope="col">Post Title</th>
                <th class="header-cell" scope="col">Post Description</th>
                <th class="header-cell" scope="col">Posted User</th>
                <th class="header-cell" scope="col">Posted Date</th>
                @if(auth()->user() && (auth()->user()->type == 0 || auth()->user()->type == 1))
                <th class="header-cell" scope="col">Operation</th>
                @endif
              </tr>
            </thead>
            <tbody>
              
              <tr>
                <td>
                  <a class="post-name"  data-toggle="modal" data-target="#post-detail-popup">Post one</a>
                <td>description of post one</td>
                <td>admin</td>
                <td>{{date('Y-m-d H:i:s')}}</td>
                @if(auth()->user() && (auth()->user()->type == 0 || auth()->user()->type == 1))
                <td>
                  <a type="button" class="btn btn-primary btn-md" href="">Edit</a>
                  <button  type="button" class="btn btn-danger btn-md" data-toggle="modal" data-target="#post-delete-popup">Delete</button>
                </td>
                @endif
              </tr>
            </tbody>
          </table>
        </div>
        </div>
    </div>
    </div>
</div>
@endsection