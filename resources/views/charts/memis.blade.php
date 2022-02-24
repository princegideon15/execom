@extends('layouts.app')

@section('content')
<div class="container  bg-white shadow p-4"> 
    <div class="row">
      <nav aria-label="breadcrumb" class="bg-transparent">
          <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="../home"><span class="	fas fa-home"></span> Home</a></li>
            <li class="breadcrumb-item" aria-current="page">MEMIS</li>
          </ol>
        </nav>
    </div>
    <div class="card text-white bg-secondary mb-3">
      <div class="card-body">
        <h3 class="card-title font-weight-bold">MEMBERSHIP INFORMATION SYSTEM (MemIS)</h3>
        <p class="card-text">A repository of profile of Filipino researchers.</p>
        
      </div>
    </div>
    <hr/>
    <!-- Overall -->
    @if($id == 0)
    <div class="alert alert-dark  d-flex justify-content-between align-items-center" role="alert">
      <span class="text-left"><span class="fas fa-exclamation-circle"></span> Select filters to generate specific charts</span> <button id="reset_filters" class="btn btn-secondary btn-sm float-right"><span class="fas fa-sync-alt"></span> Reset Filters</button>
    </div>
    <form id="generate_chart_form">
     <div class="row">
      <div class="col">
        <div class="form-group row">
          <label for="memis_division" class="font-weight-bold col-sm-2 col-form-label">Division</label>
          <div class="col-sm-10">
            <select class="form-control" id="memis_division" name="memis_division" style="text-align-last:center">
              <option value="0">--- No Selection ---</option>
              <option value="999">All Division</option>
              @foreach($division_list as $row)
              <option value="{{ $row->div_id }}">Division {{ $row->div_number }} : {{ $row->div_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="form-group row">
          <label for="memis_category" class="font-weight-bold col-sm-2 col-form-label">Category</label>
          <div class="col-sm-10">
            <select class="form-control" id="memis_category" name="memis_category" style="text-align-last:center">
              <option value="0">--- No Selection ---</option>
              <option value="999">All Category</option>
              @foreach($category_list as $row)
              <option value="{{ $row->membership_type_id }}">{{ $row->membership_type_name }}</option>
              @endforeach
              <option value="9">New Members Only</option>
            </select>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="form-group row">
          <label for="memis_status" class="font-weight-bold col-sm-2 col-form-label">Status</label>
          <div class="col-sm-10">
            <select class="form-control" id="memis_status" name="memis_status" style="text-align-last:center">
              <option value="0">--- No Selection ---</option>
              <option value="999">All Status</option>
              @foreach($status_list as $row)
              <option value="{{ $row->membership_status_id }}">{{ $row->membership_status_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
       </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="form-group row">
            <label for="memis_region" class="font-weight-bold col-sm-2 col-form-label">Region</label>
            <div class="col-sm-10">
              <select class="form-control" id="memis_region" name="memis_region" style="text-align-last:center">
              <option value="0">--- No Selection ---</option>
                <option value="999">All Region</option>
                @foreach($region_list as $row)
                <option value="{{ $row->region_id }}">{{ $row->region_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group row">
            <label for="memis_province" class="font-weight-bold col-sm-2 col-form-label">Province</label>
            <div class="col-sm-10">
              <select class="form-control" id="memis_province" name="memis_province" style="text-align-last:center">
              <option value="0">--- No Selection ---</option>
              </select>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group row">
            <label for="memis_city" class="font-weight-bold col-sm-2 col-form-label">City</label>
            <div class="col-sm-10">
              <select class="form-control" id="memis_city" name="memis_city" style="text-align-last:center">
              <option value="0">--- No Selection ---</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="form-group row">
            <label for="memis_country" class="font-weight-bold col-sm-2 col-form-label">Country</label>
            <div class="col-sm-10">
              <select class="form-control" id="memis_country" name="memis_country"  style="text-align-last:center">
                <option value="0">--- No Selection ---</option>
                <option value="999">All Country</option>
                @foreach($country_list as $row)
                <option value="{{ $row->country_id }}">{{ $row->country_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group row">
            <label for="memis_sex" class="font-weight-bold col-sm-2 col-form-label">Sex</label>
            <div class="col-sm-10">
              <select class="form-control" id="memis_sex" name="memis_sex"  style="text-align-last:center">
                <option value="0">--- No Selection ---</option>
                <option value="999">All Sex</option>
                @foreach($sex_list as $row)
                <option value="{{ $row->s_id }}">{{ $row->sex }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="form-group row">
            <label for="memis_age" class="font-weight-bold col-sm-2 col-form-label">Age</label>
            <div class="col-sm-10">
              <select class="form-control" id="memis_age" name="memis_age"  style="text-align-last:center">
                <option value="0">--- No Selection ---</option>
                <option value="999">All Age</option>
                <option value="1">21 - 30</option>
                <option value="2">31 - 40</option>
                <option value="3">41 - 50</option>
                <option value="4">51 - 60</option>
                <option value="5">61 - 70</option>
                <option value="6">71 and above</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-4">
          <div class="form-group row">
            <label for="memis_educ" class="font-weight-bold col-sm-2 col-form-label small">Highest Educational Attainment</label>
            <div class="col-sm-10">
              <select class="form-control" id="memis_educ" name="memis_educ"  style="text-align-last:center">
                <option value="0">--- No Selection ---</option>
                <option value="999">All Highest Educational Attainment</option>
                <option value="1">Doctor of Philosophy (PhD)</option>
                <option value="2">Masteral Degree (MS)</option>
                <option value="3">Bachelor Degree (BS)</option>
                <option value="4">Short Courses</option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="form-group row">
            <label for="memis_island" class="font-weight-bold col-sm-2 col-form-label small">Island Groups</label>
            <div class="col-sm-10">
              <select class="form-control" id="memis_island" name="memis_island"  style="text-align-last:center">
                <option value="0">--- No Selection ---</option>
                <option value="999">All</option>
                <option value="1">Luzon</option>
                <option value="2">Visayas</option>
                <option value="3">Mindanao</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      
    <!-- Timeline   -->
    <div class="alert alert-dark" role="alert">
      <!-- <span class="fas fa-exclamation-circle"></span> Additional filters if there is selected <strong>Category</strong> -->
      <span class="fas fa-exclamation-circle"></span> Filters for specific period/time/duration
    </div>
    <ul class="nav nav-tabs font-weight-bold " id="time_tab" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="nav-link active" id="duration-tab" data-toggle="tab" href="#duration" role="tab" aria-controls="duration" aria-selected="true">Duration</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link" id="period-tab" data-toggle="tab" href="#period" role="tab" aria-controls="period" aria-selected="false">Periodical</a>
      </li>
    </ul>
    <div class="tab-content" id="time_content">
      <div class="tab-pane fade show active" id="duration" role="tabpanel" aria-labelledby="duration-tab">
        <div class="row p-3">
          <div class="col-3">
            <div class="form-group">
              <label for="memis_start_year" class="font-weight-bold">Start Year</label>
                <select class="form-control" id="memis_start_year" name="memis_start_year"  style="text-align-last:center">
                  <option value="0">--- No Selection ---</option>
                    @php $firstYear = (int)date('Y'); @endphp
                    @php $lastYear = 1833; @endphp
                    @for($i=$firstYear;$i>=$lastYear;$i--)
                  <option value='{{ $i }}'>{{ $i }}</option>';
                  @endfor
                </select>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label for="memis_end_year" class="font-weight-bold">End Year</label>
                <select class="form-control" id="memis_end_year" name="memis_end_year"  style="text-align-last:center">
                  <option value="0">--- No Selection ---</option>
                    @php $firstYear = (int)date('Y'); @endphp
                    @php $lastYear = 1833; @endphp
                    @for($i=$firstYear;$i>=$lastYear;$i--)
                  <option value='{{ $i }}'>{{ $i }}</option>';
                  @endfor
                </select>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="period" role="tabpanel" aria-labelledby="period-tab">
        <div class="row p-3">
            <div class="col-3">
              <div class="form-group">
                <label for="memis_period" class="font-weight-bold">Period</label>
                  <select class="form-control" id="memis_period" name="memis_period" style="text-align-last:center">
                    <option value="0">--- No Selection ---</option>
                    <option value="1">Monthly</option>
                    <option value="2">Quarterly</option>
                    <option value="3">Semestral</option>
                  </select>
              </div></div>
            <div class="col-3">
              <div class="form-group">
                <label for="memis_year" class="font-weight-bold">Year</label>
                  <select class="form-control" id="memis_year" name="memis_year" style="text-align-last:center">
                    <option value="0">--- No Selection ---</option>
                      @php $firstYear = (int)date('Y'); @endphp
                      @php $lastYear = 1833; @endphp
                      @for($i=$firstYear;$i>=$lastYear;$i--)
                    <option value='{{ $i }}'>{{ $i }}</option>';
                    @endfor
                  </select>
              </div></div>
          </div>
      </div>
    </div>
    <!-- <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="memis_start_year" class="font-weight-bold">Start Year</label>
                    <select class="form-control" id="memis_start_year" name="memis_start_year"  style="text-align-last:center">
                      <option value="0">--- No Selection ---</option>
                        @php $firstYear = (int)date('Y'); @endphp
                        @php $lastYear = 1833; @endphp
                        @for($i=$firstYear;$i>=$lastYear;$i--)
                      <option value='{{ $i }}'>{{ $i }}</option>';
                      @endfor
                    </select>
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="memis_end_year" class="font-weight-bold">End Year</label>
                    <select class="form-control" id="memis_end_year" name="memis_end_year"  style="text-align-last:center">
                      <option value="0">--- No Selection ---</option>
                        @php $firstYear = (int)date('Y'); @endphp
                        @php $lastYear = 1833; @endphp
                        @for($i=$firstYear;$i>=$lastYear;$i--)
                      <option value='{{ $i }}'>{{ $i }}</option>';
                      @endfor
                    </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="memis_period" class="font-weight-bold">Period</label>
                    <select class="form-control" id="memis_period" name="memis_period" style="text-align-last:center">
                      <option value="0">--- No Selection ---</option>
                      <option value="1">Monthly</option>
                      <option value="2">Quarterly</option>
                      <option value="3">Semestral</option>
                    </select>
                </div></div>
              <div class="col">
                <div class="form-group">
                  <label for="memis_year" class="font-weight-bold">Year</label>
                    <select class="form-control" id="memis_year" name="memis_year" style="text-align-last:center">
                      <option value="0">--- No Selection ---</option>
                        @php $firstYear = (int)date('Y'); @endphp
                        @php $lastYear = 1833; @endphp
                        @for($i=$firstYear;$i>=$lastYear;$i--)
                      <option value='{{ $i }}'>{{ $i }}</option>';
                      @endfor
                    </select>
                </div></div>
            </div>
          </div>
        </div>
      </div>
    </div> -->

    <!-- Charts -->
    <div class="alert alert-dark mt-3" role="alert">
      <span class="fas fa-exclamation-circle"></span> Select applicable chart based on selected filters above
      <a class="float-right" onclick="chart_info(999)">Chart <i class="far fa-question-circle"></i></a>
    </div>
    <div class="alert alert-danger chart_filter_alert" role="alert" hidden><span class="fas fa-exclamation-triangle"></span> Select atleast one (1) filter</div>
    <ul class="nav nav-tabs font-weight-bold" id="graph_tab" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="nav-link disabled" onclick="memis_generate_chart(1)" id="basic_bar_tab" data-toggle="tab" href="#basic_bar" role="tab" aria-controls="basic_bar" aria-selected="true">Basic Bar 
          <i class="far fa-question-circle text-dark" data-toggle="tooltip" data-placement="top" title="What is a Basic Bar Chart?" onclick="chart_info(1)"></i></a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link disabled" onclick="memis_generate_chart(2)" id="pie_tab" data-toggle="tab" href="#pie" role="tab" aria-controls="pie" aria-selected="false">Pie 
        <i class="far fa-question-circle text-dark" data-toggle="tooltip" data-placement="top" title="What is a Pie Chart?" onclick="chart_info(2)"></i></a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link disabled" onclick="memis_generate_chart(3)" id="stacked_bar_tab" data-toggle="tab" href="#stacked_bar" role="tab" aria-controls="stacked_bar" aria-selected="false">Stacked Bar
        <i class="far fa-question-circle text-dark" data-toggle="tooltip" data-placement="top" title="What is a Stacked Bar Chart?" onclick="chart_info(3)"></i></a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link disabled" onclick="memis_generate_chart(4)" id="column_bar_tab" data-toggle="tab" href="#column_bar" role="tab" aria-controls="column_bar" aria-selected="true">Column Bar
        <i class="far fa-question-circle text-dark" data-toggle="tooltip" data-placement="top" title="What is a Column Chart?" onclick="chart_info(4)"></i></a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link disabled" onclick="memis_generate_chart(5)" id="stacked_column_tab" data-toggle="tab" href="#stacked_column_bar" role="tab" aria-controls="stacked_column_bar" aria-selected="false">Stacked-Column
        <i class="far fa-question-circle text-dark" data-toggle="tooltip" data-placement="top" title="What is a Stacked-Column Chart?" onclick="chart_info(5)"></i></a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link disabled" onclick="memis_generate_chart(6)" id="bar_drilldown_tab" data-toggle="tab" href="#bar_drilldown_bar" role="tab" aria-controls="bar_drilldown_bar" aria-selected="false">Bar with Drilldown</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link disabled" onclick="memis_generate_chart(7)" id="adv_stacked_col_tab" data-toggle="tab" href="#adv_sc_bar" role="tab" aria-controls="adv_sc_bar" aria-selected="false">Advanced Stacked-Column Bar</a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link disabled" onclick="memis_generate_chart(8)" id="line_tab" data-toggle="tab" href="#line_bar" role="tab" aria-controls="line_bar" aria-selected="false">Line Chart</a>
      </li>
    </ul>
    <div class="tab-content" id="graph_tab_content">
      <div class="tab-pane fade show active" id="basic_bar" role="tabpanel" aria-labelledby="basic_bar_tab">
        <div class="w-100 mt-3" id="container1"style="width:100%; height:auto;"></div>
      </div>
      <div class="tab-pane fade" id="pie" role="tabpanel" aria-labelledby="pie_tab">
        <div class="w-100 mt-3" id="container2"style="width:100%; height:auto;"></div>
      </div>
      <div class="tab-pane fade" id="stacked_bar" role="tabpanel" aria-labelledby="stacked_bar_tab">
        <div class="w-100 mt-3" id="container3"style="width:100%; height:auto;"></div>
      </div>
      <div class="tab-pane fade" id="column_bar" role="tabpanel" aria-labelledby="column_bar_tab">
        <div class="w-100 mt-3" id="container4"style="width:100%; height:auto;"></div>
      </div>
      <div class="tab-pane fade" id="stacked_column_bar" role="tabpanel" aria-labelledby="stacked_column_tab">
        <div class="w-100 mt-3" id="container5"style="width:100%; height:auto;"></div>
      </div>
      <div class="tab-pane fade" id="bar_drilldown_bar" role="tabpanel" aria-labelledby="bar_drilldown_tab">
        <div class="w-100 mt-3" id="container6"style="width:100%; height:auto;"></div>
      </div>
      <div class="tab-pane fade" id="adv_sc_bar" role="tabpanel" aria-labelledby="adv_sc_bar">
        <div class="w-100 mt-3" id="container7"style="width:100%; height:auto;"></div>
      </div>
      <div class="tab-pane fade" id="line_bar" role="tabpanel" aria-labelledby="line_bar">
        <div class="w-100 mt-3" id="container8"style="width:100%; height:auto;"></div>
      </div>
      <div class="row pl-3 mt-3 mb-2">
          <div class="col-2 font-weight-bold">Count/Percentage:</div>
          <div class="col-10"><input type="checkbox" id="chart_numbers" checked  data-size="sm" data-toggle="toggle" data-on="Show" data-off="Hide" data-onstyle="secondary" data-width="60"></div>
      </div>
      <div class="row pl-3 mb-2">
          <div class="col-2 font-weight-bold">Orientation:</div>
          <div class="col-10"><input id="chart_orientation" type="checkbox" checked  data-size="sm" data-toggle="toggle" data-on="Horizontal" data-off="Vertical" data-onstyle="secondary" data-width="90"></div>
      </div>
    </div>
    </form>
    <!-- <span class="notice_radio_chart"></span>
    <div class="row pl-3 pr-3" >
      <div class="col border border-default rounded mr-2">
        <div class="custom-control custom-radio pt-2 pb-2">
          <input type="radio" class="custom-control-input" id="radio_bar_chart" name="radio_generate_chart" value="1" disabled>
          <label class="custom-control-label" for="radio_bar_chart">Basic Bar Chart </label> 
          <i class="far fa-question-circle text-dark" data-toggle="tooltip" data-placement="top" title="What is a Basic Bar Chart?" onclick="chart_info(1)"></i>
        </div>
      </div>
      <div class="col border border-default rounded mr-2">
        <div class="custom-control custom-radio pt-2 pb-2">
          <input type="radio" class="custom-control-input" id="radio_pie_chart" name="radio_generate_chart" value="2" disabled>
          <label class="custom-control-label" for="radio_pie_chart">Pie Chart</label>
          <i class="far fa-question-circle text-dark" data-toggle="tooltip" data-placement="top" title="What is a Pie Chart?" onclick="chart_info(2)"></i>
        </div>
      </div>
      <div class="col border border-default rounded mr-2">
        <div class="custom-control custom-radio pt-2 pb-2">
          <input type="radio" class="custom-control-input" id="radio_stacked_chart" name="radio_generate_chart" value="3" disabled>
          <label class="custom-control-label" for="radio_stacked_chart">Stacked Bar Chart</label>
          <i class="far fa-question-circle text-dark" data-toggle="tooltip" data-placement="top" title="What is a Stacked Bar Chart?" onclick="chart_info(3)"></i>
        </div>
      </div>
      <div class="col border border-default rounded mr-2">
        <div class="custom-control custom-radio pt-2 pb-2">
          <input type="radio" class="custom-control-input" id="radio_column_chart" name="radio_generate_chart" value="4" disabled>
          <label class="custom-control-label" for="radio_column_chart">Column Chart</label>
          <i class="far fa-question-circle text-dark" data-toggle="tooltip" data-placement="top" title="What is a Column Chart?" onclick="chart_info(4)"></i>
        </div>
      </div>
      <div class="col border border-default rounded">
        <div class="custom-control custom-radio pt-2 pb-2">
          <input type="radio" class="custom-control-input" id="radio_scolumn_chart" name="radio_generate_chart" value="5" disabled>
          <label class="custom-control-label" for="radio_scolumn_chart">Stacked Column Chart</label>
          <i class="far fa-question-circle text-dark" data-toggle="tooltip" data-placement="top" title="What is a Column Chart?" onclick="chart_info(5)"></i>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <button class="btn btn-primary mt-4 w-100 font-weight-bold" id="btn_generate_chart" type="button">Generate</button>
      </div>
    </div>
    </form> -->
    @endif
     <!-- <div class="row mt-3">
      <div class="col-md-12">
        <div class="alert alert-dark no_data_available text-center" role="alert">
           <button id="btn_generate_chart" class="btn btn-dark font-weight-bold "><span class="fas fa-angle-double-down"></span> Generate Chart</button>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="row">
                <div class="col p-0">
                  <div class="w-100" id="container"style="width:100%; height:auto;"></div>
                </div>
            </div>
            
            <div class="row mb-2">
                <div class="col-2 font-weight-bold">Count/Percentage:</div>
                <div class="col-10"><input type="checkbox" id="chart_numbers" checked  data-size="sm" data-toggle="toggle" data-on="Show" data-off="Hide" data-onstyle="secondary" data-width="60"></div>
            </div>
            <div class="row mb-2">
                <div class="col-2 font-weight-bold">Orientation:</div>
                <div class="col-10"><input id="chart_orientation" type="checkbox" checked  data-size="sm" data-toggle="toggle" data-on="Horizontal" data-off="Vertical" data-onstyle="secondary" data-width="90"></div>
            </div>
          </div>
        </div>
      </div>
    </div> -->
    <!-- Per Division -->
    @if($id == 1)
      <div class="row pt-3">
        <div class="col">
          <table class="table table-striped table-hover table-bordered">
            <thead>
              <tr>
                <th scope="col">Division</th>
                <th scope="col">Sub-total</th>
              </tr>
            </thead>
            <tbody>
              @php $gtotal = 0; @endphp
              @foreach($division as $row)
              @php $gtotal += $row->total; @endphp
              <tr>
                <td>Division {{ $row->div_number }} : {{ $row->div_name }}</td>
                <td>{{ $row->total }}</td>
              </tr>
              @endforeach
              <tr class="font-weight-bold"><td>TOTAL</td><td>{{ $gtotal }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    <!-- Per Region -->
    @elseif($id == 2)
      <div class="row pt-3">
        <div class="col">
          <table class="table table-striped table-hover table-bordered">
            <thead>
              <tr>
                <th scope="col">Region</th>
                <th scope="col">Sub-total</th>
              </tr>
            </thead>
            <tbody>
              @php $gtotal = 0; @endphp
              @foreach($region as $row)
              @php $gtotal += $row->total; @endphp
              <tr>
                <td>{{ $row->region_name }}</td>
                <td>{{ $row->total }}</td>
              </tr>
              @endforeach
              <tr class="font-weight-bold"><td>TOTAL</td><td>{{ $gtotal }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    <!-- Per Category -->
    @elseif($id == 3)
      <div class="row pt-3">
        <div class="col">
          <table class="table table-striped table-hover table-bordered">
            <thead>
              <tr>
                <th scope="col">Category</th>
                <th scope="col">Sub-total</th>
              </tr>
            </thead>
            <tbody>
              @php $gtotal = 0; @endphp
              @foreach($category as $row)
              @php $gtotal += $row->total; @endphp
              <tr>
                <td>{{ $row->membership_type_name }}</td>
                <td>{{ $row->total }}</td>
              </tr>
              @endforeach
              <tr class="font-weight-bold"><td>TOTAL</td><td>{{ $gtotal }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    <!-- Per Status -->
    @elseif($id == 4)
      <div class="row pt-3">
        <div class="col">
          <table class="table table-striped table-hover table-bordered">
            <thead>
              <tr>
                <th scope="col">Category</th>
                <th scope="col">Sub-total</th>
              </tr>
            </thead>
            <tbody>
              @php $gtotal = 0; @endphp
              @foreach($status as $row)
              @php $gtotal += $row->total; @endphp
              <tr>
                <td>{{ $row->membership_status_name }}</td>
                <td>{{ $row->total }}</td>
              </tr>
              @endforeach
              <tr class="font-weight-bold"><td>TOTAL</td><td>{{ $gtotal }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    <!-- Per Sex -->
    @elseif($id == 5)
      <div class="row pt-3">
        <div class="col">
          <table class="table table-striped table-hover table-bordered">
            <thead>
              <tr>
                <th scope="col">Category</th>
                <th scope="col">Sub-total</th>
              </tr>
            </thead>
            <tbody>
              @php $gtotal = 0; @endphp
              @foreach($sex as $row)
              @php $gtotal += $row->total; @endphp
              <tr>
                <td>{{ $row->sex }}</td>
                <td>{{ $row->total }}</td>
              </tr>
              @endforeach
              <tr class="font-weight-bold"><td>TOTAL</td><td>{{ $gtotal }}</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      @endif
</div>

<!-- Modal -->
<div class="modal fade" id="perProvince" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
              <div class="col-6">
                  <canvas id="per_prov_bar_chart" width="400" height="400"></canvas>
              </div>
              <div class="col-6">
                  <canvas id="per_prov_pie_chart" width="400" height="400"></canvas>
              </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="chart_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <small class="text-muted">*IMAGE ARE FOR ILLUSTRATION PURPOSE ONLY</small>
        <div class="card">
          <img src="" class="card-img-top">
          <div class="card-body bg-light border-top">
            <p class="card-text"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--members modal -->
<div class="modal fade" id="member_modal" tabindex="-1" role="dialog" style="z-index: 1600;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <span></span>
                <table class="table table-striped w-100" id="member_table">
                <thead>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.members modal -->

@endsection


