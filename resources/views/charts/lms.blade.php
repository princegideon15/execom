@extends('layouts.app')

@section('content')
<div class="container bg-white"> 
    <div class="row">
      <nav aria-label="breadcrumb" class="bg-transparent">
          <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="home">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">LMS</li>
          </ol>
        </nav>
    </div>
    <h2>LIBRARY MANAGEMENT SYSTEM (LMS)</h2>
    <div class="row">
        <div class="col-md-3">
          <div class="card">
            <div class="card-body"> 
              Articles <span class="float-right font-weight-bold"> {{ $articles }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card">
            <div class="card-body">
              Views <span class="float-right font-weight-bold"> {{ $views }}</span>
            </div>
          </div>
        </div>  
        <div class="col-md-3">
          <div class="card">
            <div class="card-body">
              Downloads <span class="float-right font-weight-bold"> {{ $downloads}}</span>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card">
            <div class="card-body">
              Active Users <span class="float-right font-weight-bold"> {{ $active_users}}</span>
            </div>
          </div>
        </div>
    </div>
    <div class="row mt-3">
      <div class="col">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                Filter: <select class="lms_filter_by" name="lms_filter_by" id="lms_filter_by">
                <option value="1">by Categories</option>
                <option value="2">by Views</option>
                <option value="3">by Downloads</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-8">
                <canvas id="lms_bar_chart"></canvas>
              </div>
              <div class="col-md-4">
                <canvas id="lms_pie_chart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

@endsection
