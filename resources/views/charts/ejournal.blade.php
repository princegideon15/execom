@extends('layouts.app')

@section('content')
<div class="container  bg-white"> 
    <div class="row">
      <nav aria-label="breadcrumb" class="bg-transparent">
          <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="home">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">EJOURNAL</li>
          </ol>
        </nav>
    </div>
    <h2>NRCP RESEARCH JOURNAL (EJOURNAL)</h2>
    <div class="row justify-content-center">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body"> 
              Journal <span class="float-right font-weight-bold"> {{ $count_journals }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              Article <span class="float-right font-weight-bold"> {{ $count_articles }}</span>
            </div>
          </div>
        </div>  
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              Citation <span class="float-right font-weight-bold"> {{ $count_cites }}</span>
            </div>
          </div>
        </div>
    </div>
    <div class="row justify-content-center mt-3">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              Abstract View <span class="float-right font-weight-bold"> {{ $count_views }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              PDF Download <span class="float-right font-weight-bold"> {{ $count_downloads }}</span>
            </div>
          </div>
        </div>  
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              Visitor <span class="float-right font-weight-bold"> {{ $count_visitors }}</span>
            </div>
          </div>
        </div>
    </div>
    <div class="row justify-content-center mt-3">
      <div class="col">
        <div class="card">
          <div class="card-body">
          <p class="h4 mb-3">Graphs</p>
          <div class="row">
              <div class="col-md-4">
                Filter: <select class="ej_filter_by" name="ej_filter_by" id="ej_filter_by">
                <option value="1">Journals by Year</option>
                <option value="2">Articles by Journal</option>
                <option value="3">Articles by Downloads</option>
                <option value="4">Articles by Views</option>
                <option value="5">Articles by Citations</option>
                <option value="6">Visitors</option>
                </select>
                Year: <select name="ej_filter_by_year" id="ej_filter_by_year" disabled>
                <option value="">All</option>
                @foreach($years as $value)
                <option value="{{ $value->years }}">{{ $value->years }}</option>
                @endforeach
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-8">
                <canvas id="ej_bar_chart"></canvas>
              </div>
              <div class="col-md-4">
                <canvas id="ej_pie_chart"></canvas>
              </div>
            </div>
           <div class="row mt-2">
              <div class="col-md-4">
                <table class="table table-bordered table-striped" id="journal_year_table">
                  <thead>
                    <tr>
                      <th colspan="2">Journals By Year</th>
                    </tr>
                    <tr>
                    <th>Year</th>
                    <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
           </div>
          </div>
        </div>
      </div>
    </div>


</div>
@endsection
