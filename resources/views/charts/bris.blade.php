@extends('layouts.app')

@section('content')
<div class="container  bg-white"> 
    <div class="row">
      <nav aria-label="breadcrumb" class="bg-transparent">
          <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="home">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">BRIS</li>
          </ol>
        </nav>
    </div>
    <h2>BASIC RESEARCH INFORMATION SYSTEM (BRIS)</h2>
    <div class="row">
        <div class="col">
        Filter: <select id="bris_filter">
                  <option value="1">Research Type</option>
                  <option value="2">Project Status</option>
                  <option value="3">Harmonized National R&D Agenda</option>
                  <option value="4">Program Expenditure Classification</option>
                  <option value="5">Priority Areas of the Government</option>
                  <option value="6">DOST 11-Point Agenda</option>
                  <option value="7">Outcomes in the DOST Strategic Plan 2017-2022</option>
                  <option value="8">Philippine Development Chapters</option>
                  <option value="9">NIBRA Priority Areas</option>
                  <option value="10">NIBRA Sub-categories</option>
                  <option value="11">National Socio-Economic Agenda</option>
                  <option value="12">Classification by S&T Activity</option>
                  <option value="13">Sustainable Development Goals</option>
                </select> 
        </div>
     </div>
     
    <div class="row mt-2">
      <div class="col-md-8">
        <canvas id="bris_bar_chart"></canvas>
      </div>
      <div class="col-md-4">
        <canvas id="bris_pie_chart"></canvas>
      </div>
    </div>
       
    </div>
</div>


        <!-- <div class="col-md-3">
          <div class="list-group list-group-flush">
            
              <li class="list-group-item list-group-item-action">Research Type
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($type as $row)
              <option value="{{ $row->prc_id }}">{{ $row->prc_name }}
              @endforeach
              </select>
              </li>
              <li class="list-group-item list-group-item-action">Project Status
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($status as $row)
              <option value="{{ $row->prs_id }}">{{ $row->prs_name }}
              @endforeach
              </select>
              </li>
              <li class="list-group-item list-group-item-action">Harmonized National R&D Agenda (HNRDA)
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($hnrda as $row)
              <option value="{{ $row->hnrda_id }}">{{ $row->hnrda_name }}
              @endforeach
              </select>
              </li>
              <li class="list-group-item list-group-item-action">Program Expenditure Classification (PREXC)
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($prexc as $row)
              <option value="{{ $row->prexc_id }}">{{ $row->prexc_classification }}
              @endforeach
              </select>
              </li><li class="list-group-item list-group-item-action">Priority Areas of the Government
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($pa as $row)
              <option value="{{ $row->pri_id }}">{{ $row->pri_name }}
              @endforeach
              </select>
              </li><li class="list-group-item list-group-item-action">DOST 11-Point Agenda
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($dost as $row)
              <option value="{{ $row->dost_agenda_id }}">{{ $row->dost_agenda_name }}
              @endforeach
              </select>
              </li><li class="list-group-item list-group-item-action">Outcomes in the DOST Strategic Plan 2017-2022
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($strat as $row)
              <option value="{{ $row->strat_id }}">{{ $row->strat_outcome }}
              @endforeach
              </select>
              </li><li class="list-group-item list-group-item-action">Philippine Development (PDP) Chapters
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($pdp as $row)
              <option value="{{ $row->pdp_id }}">{{ $row->pdp_definition }}
              @endforeach
              </select>
              </li><li class="list-group-item list-group-item-action">NIBRA Priority Areas
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($nibra as $row)
              <option value="{{ $row->nibra_id }}">{{ $row->nibra_name }}
              @endforeach
              </select>
              </li><li class="list-group-item list-group-item-action">NIBRA Sub-Categories
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($nsub as $row)
              <option value="{{ $row->nibra_sub_id }}">{{ $row->nibra_sub_name }}
              @endforeach
              </select>
              </li><li class="list-group-item list-group-item-action">National Socio-Economic Agenda
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($nsea as $row)
              <option value="{{ $row->nsea_id }}">{{ $row->nsea_name }}
              @endforeach
              </select>
              </li><li class="list-group-item list-group-item-action">Classification by S&T Activity
              
              <select class="form-control-sm w-100">
              <option value="">Select</option>
              @foreach($snt as $row)
              <option value="{{ $row->st_id }}">{{ $row->st_name }}
              @endforeach
              </select>
              </li>
              <li class="list-group-item list-group-item-action">Sustainable Development Goals (SDGs)
              
              <select class="form-control-sm w-100" name="bris_type" id="bris_type">
              <option value="">Select</option>
              @foreach($sdg as $row)
              <option value="{{ $row->sdg_id }}">{{ $row->sdg_def }}
              @endforeach
              </select>
              </li>
          </div>
        </div> -->

@endsection
