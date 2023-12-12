@extends('layouts.app')
@section('content')
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top" >
            <div class="container">
                <a class="navbar-brand w-50" href="{{ url('/home') }}">
                    <img src="{{URL::asset('storage/images/logos/nrcp.png')}}" height="8%" width="8%" >
                    <img src="{{URL::asset('storage/images/logos/skms.png')}}" height="15%" width="15%" >
                    <img src="{{URL::asset('storage/images/logos/execom.png')}}" height="8%" width="8%"  data-toggle="tooltip" data-placement="top" title="v1.10.11">
                    <span class="font-weight-bold">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                        <a href="" class="btn-link mt-2 mr-2" href="javascript:void(0);" id="show_adv_srch">Advanced Search</a>
                        <div class="form-group has-search mb-0">
                            <span class="fa fa-search text-dark form-control-feedback"></span>
                            <input type="text" class="form-control border border-dark" placeholder="Quick search" id="quick_search">
                        </div>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle text-dark font-weight-bold" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Welcome, {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                 @if (Auth::user()->role == 1)
                                    <a class="btn btn-link dropdown-item"  data-toggle="modal" data-target="#users_modal" onclick="all_users()">
                                    <span class="fas fa-user-friends" style="width:20px"></span> Manage Users
                                    </a> 
                                    <a class="btn btn-link dropdown-item"  data-toggle="modal" data-target="#logs_modal" onclick="activity_logs()">
                                    <span class="far fa-clipboard" style="width:20px"></span> Activity Logs
                                    </a>       
                                    <a class="btn btn-link dropdown-item" data-toggle="modal" data-target="#feedbacks_modal" onclick="view_feedbacks()">
                                    <span class="far fa-edit" style="width:20px"></span> Feedbacks
                                    </a>
                                    <a class="btn btn-link dropdown-item" data-toggle="modal" data-target="#database_modal">
                                    <span class="fas fa-database" style="width:20px"></span> Backup/Restore Database   
                                    </a>
                                    @endif
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="verify_feedback()">
                                        <span class="fas fa-share-square" style="width:20px"></span> {{ __('Logout') }}
                                    </a>

                                    <!-- <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form> -->
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        

<div class="container">
    @php if($count_feedback > 0 && Auth::user()->role == 1){ @endphp
    @php $msg = ($count_feedback == 1) ? 'New Feedback!' : 'New Feedbacks!'; @endphp
        <div>
            <div class="alert alert-warning alert-dismissible fade show fb_notif" role="alert">
            
            <a data-toggle="modal" data-target="#feedbacks_modal" onclick="view_feedbacks()">
            <span class="badge badge-warning">{!! $count_feedback !!}</span> {!! $msg !!}
            </a>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
        </div>
    @php } @endphp
    <div class="row adv_srch" style="display:none;">
        <div class="col-md-4 mb-4">
                <!-- <div class="card text-white" style="background-image:linear-gradient(0deg, rgba(29,29,29,1) 10%, rgba(29,29,29,0.5) 100%),url('{{URL::asset('storage/images/bg/search.jpeg')}}');background-size:cover; background-repeat:no-repeat"> -->
                <div class="card text-white rounded shadow" style="background-color:#0d0d0d">
                    <div class="card-body">
                    <h5 class="font-weight-bold">Advanced Search 
                    <span class="float-right"><small><a href="javascript:void(0);" id="hide_adv_srch" class="btn-link">Hide</a></small></span></h5>
                    <!-- <form> -->
                    <input type="text" class="form-control" placeholder="Type Keyword" id="search_keyword">
                    Filters
                    <select class="form-control" id="search_filter">
                        <option value="0">Select System</option>
                        <option value="1">Members</option>
                        <option value="2">Basic Research</option>
                        <option value="3">eJournal</option>
                        <option value="4">Scientific Library</option>
                        <option value="5">NRCPnet</option>
                    </select>
                    <span id="sub_option"></span>
                    <span id="sub_option2"></span>
                    <span id="search_result"></span>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-dark text-white"  id="clear_filter" >Clear Filter</button>
                            <button type="button" class="btn btn-dark" id="search_button">Search</button>
                        </div>
                    </div>
                    <!-- </form> -->
                </div>
            </div>
        </div>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <!-- <div class="card text-white rounded" style="min-height:750px;background-image:linear-gradient(0deg, rgba(29,29,29,1) 10%, rgba(29,29,29,0.5) 100%),url('{{URL::asset('storage/images/bg/memisbg.jpeg')}}');background-size:cover; background-repeat:no-repeat"> -->
            <div class="card text-white rounded shadow" style="min-height:750px;background-color:maroon">
                 <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="media">
                                <img class="mr-3" src="{{URL::asset('storage/images/logos/memis.png')}}" width="25%" height="25%" alt="MEMIS">
                                <div class="media-body">
                                    <h5 class="mt-3 font-weight-bold">Membership Information System (MemIS)</h5>
                                    A repository of profile of Filipino researchers, scholars, scientist and engineers.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col pl-0 pr-0">
                            <div class="list-group list-group-flush">
                                <span class="list-group-item list-group-item-action text-white bg-transparent pb-1 pt-1 shadow">
                                    <div class="row">
                                        <div class="col-sm-10"> 
                                            <a  onclick="members('6','','Members')" class="btn text-white">
                                                <span class="badge badge-danger" style="font-size:20px;"> {{ $members }}</span> MEMBERS
                                            </a>
                                        </div>
                                        <div class="col-sm-2"> 
                                            <!-- <a href="memis/0" target="_blank" class="btn bg-white text-danger mt-2" role="button" data-toggle="tooltip" data-placement="top" title="See advanced graph">
                                                <span class="fas fa-chart-pie"></span>
                                            </a> -->
                                        </div>
                                    </div>
                                </span>
                                <span class="list-group-item list-group-item-action text-white bg-transparent pb-1 pt-1 shadow">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-8">
                                            <div class="btn-group dropright">
                                                <a class="btn btn-link dropdown-toggle pl-0" data-toggle="dropdown">
                                                Per Division</a>
                                                <div class="dropdown-menu">
                                                @foreach($division as $row)
                                                    <button href="javascript:void(0);" onclick="members('1', {{ $row->div_id }},' Division {{ $row->div_number }} : {{ $row->div_name }} ')"  class="dropdown-item" type="button">
                                                    <span class="badge badge-dark">{{ $row->total }}</span> Division {{ $row->div_number }}</button>
                                                @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 pt-1">
                                        <!-- <a href="memis/1" target="_blank" class="btn bg-white text-danger mt-1" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph"> -->
                                        <!-- <button type="button" onclick="basic_graph_memis('1','Per Division (Basic Bar Graph)')" class="btn btn-danger mt-1" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph"> -->
                                            <a href="javascript:void(0);" onclick="basic_graph_memis('1','Per Division (Basic Bar Graph)')" class="btn-link text-white shadow rounded "  data-toggle="tooltip" data-placement="top" title="View basic graph"><span class="fas fa-chart-bar shadow" style="font-size:26px"></span></a>
                                        <!-- </button> -->
                                        </div>
                                    </div>
                                </span>
                                <span class="list-group-item list-group-item-action text-white bg-transparent pb-1 pt-1 shadow">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-8">
                                            <div class="btn-group dropright">
                                                <a class="btn btn-link dropdown-toggle pl-0" data-toggle="dropdown">
                                                Per Region</a>
                                                <div class="dropdown-menu">
                                                @foreach($region as $row)
                                                    <button href="javascript:void(0);" onclick="members('2', {{ $row->region_id }}, ' {{ $row->region_name }} ')"  class="dropdown-item" type="button">
                                                    <span class="badge badge-dark">{{ $row->total }}</span> {{ $row->region_name }}</button>
                                                @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 pt-2">
                                            <!-- <a href="memis/2" target="_blank" class="btn bg-white text-danger mt-1" role="button"  data-toggle="tooltip" data-placement="top" title="See quick graph""> -->
                                            <!-- <button type="button" onclick="basic_graph_memis('2','Per Region (Basic Bar Graph)')" class="btn bg-white text-danger mt-1" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph">
                                            <span class="fas fa-chart-pie"></span></button> -->
                                            <a href="javascript:void(0);" onclick="basic_graph_memis('2','Per Region (Basic Bar Graph)')" class="btn-link text-white shadow rounded "  data-toggle="tooltip" data-placement="top" title="View bar graph"><span class="fas fa-chart-bar shadow" style="font-size:26px"></span></a>
                                        </div>
                                    </div>
                                </span>
                                <span class="list-group-item list-group-item-action text-white bg-transparent pb-1 pt-1 shadow">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-8">
                                            <div class="btn-group dropright">
                                                <a class="btn btn-link dropdown-toggle pl-0" data-toggle="dropdown">
                                                Per Category</a>
                                                <div class="dropdown-menu">
                                                @foreach($category as $row)
                                                    <button href="javascript:void(0);" onclick="members('3', {{ $row->membership_type_id }},' {{ $row->membership_type_name }} ')"  class="dropdown-item" type="button">
                                                    <span class="badge badge-dark">{{ $row->total }}</span> {{ $row->membership_type_name }}</button>
                                                @endforeach
                                                </div>
                                            </div>  
                                        </div>
                                        <div class="col-sm-2 pt-2">
                                            <!-- <a href="memis/3" target="_blank" class="btn bg-white text-danger mt-1" role="button"  data-toggle="tooltip" data-placement="top" title="See quick graph""> -->
                                            <!-- <button type="button" onclick="basic_graph_memis('3','Per Category (Basic Bar Graph)')" class="btn bg-white text-danger mt-1" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph">
                                            <span class="fas fa-chart-pie"></span></button> -->
                                            <a href="javascript:void(0);" onclick="basic_graph_memis('3','Per Category (Basic Bar Graph)')" class="btn-link text-white shadow rounded "  data-toggle="tooltip" data-placement="top" title="View bar graph"><span class="fas fa-chart-bar shadow" style="font-size:26px"></span></a>
                                        </div>
                                    </div>
                                </span>
                                <span class="list-group-item list-group-item-action  text-white bg-transparent pb-1 pt-1 shadow">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-8">
                                            <div class="btn-group dropright">
                                                <a class="btn btn-link dropdown-toggle pl-0" data-toggle="dropdown">
                                                Per Status</a>
                                                <div class="dropdown-menu">
                                                @foreach($status as $row)
                                                    <button href="javascript:void(0);" onclick="members('4', {{ $row->membership_status_id }},' {{ $row->membership_status_name }} ')"  class="dropdown-item" type="button">
                                                    <span class="badge badge-dark">{{ $row->total }}</span> {{ $row->membership_status_name }}</button>
                                                @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 pt-2">
                                            <!-- <a href="memis/4" target="_blank" class="btn bg-white text-danger mt-1" role="button"  data-toggle="tooltip" data-placement="top" title="See quick graph""> -->
                                            <!-- <button type="button" onclick="basic_graph_memis('4','Per Status (Basic Bar Graph)')" class="btn bg-white text-danger mt-1" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph">
                                            <span class="fas fa-chart-pie"></span></button> -->
                                            <a href="javascript:void(0);" onclick="basic_graph_memis('4','Per Status (Basic Bar Graph)')" class="btn-link text-white shadow rounded "  data-toggle="tooltip" data-placement="top" title="View bar graph"><span class="fas fa-chart-bar shadow" style="font-size:26px"></span></a>
                                        </div>
                                    </div>
                                </span>
                                <span class="list-group-item list-group-item-action  text-white bg-transparent pb-1 pt-1 shadow">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-8">
                                            <div class="btn-group dropright">
                                                <a class="btn btn-link dropdown-toggle pl-0" data-toggle="dropdown">
                                                Sex</a>
                                                <div class="dropdown-menu">
                                                @foreach($sex as $row)
                                                    <button href="javascript:void(0);" onclick="members('5', {{ $row->s_id }}, '{{ $row->sex }}')"  class="dropdown-item" type="button">
                                                    <span class="badge badge-dark">{{ $row->total }}</span> {{ $row->sex }}</button>
                                                @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 pt-2">
                                            <!-- <a href="memis/5" target="_blank" class="btn bg-white text-danger mt-1" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph""> -->
                                            <!-- <button type="button" onclick="basic_graph_memis('5','Per Sex (Basic Bar Graph)')" class="btn bg-white text-danger mt-1" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph">
                                            <span class="fas fa-chart-pie"></span></button> -->
                                            <a href="javascript:void(0);" onclick="basic_graph_memis('5','Per Sex (Basic Bar Graph)')" class="btn-link text-white shadow rounded "  data-toggle="tooltip" data-placement="top" title="View bar graph"><span class="fas fa-chart-bar shadow" style="font-size:26px"></span></a>
                                        </div>
                                    </div>
                                </span>
                                <span class="list-group-item list-group-item-action  text-white bg-transparent pb-1 pt-1 shadow">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10">
                                            <div class="btn-group dropright">
                                                <a  onclick="members('7','','NRCP Achievement Awardee')" class="btn btn-link pl-0">
                                                NRCP Achievement Awardee</a>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 pt-2">
                                            <!-- <a href="memis/6" target="_blank" class="btn bg-white text-danger mt-1" role="button" data-toggle="tooltip" data-placement="top" title="Click to view graph">
                                            <span class="fas fa-chart-pie"></span></a> -->
                                        </div>
                                    </div>
                                </span>
                                <span class="list-group-item list-group-item-action  text-white bg-transparent pb-1 pt-1 shadow">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-8">
                                        <div class="btn-group dropright">
                                                <a class="btn btn-link dropdown-toggle pl-0" data-toggle="dropdown">
                                                Governing Board</a>
                                                <div class="dropdown-menu">
                                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">
                                                        <span class="badge badge-secondary">+</span> Divison I - XIII</a>
                                                        <ul class="dropdown-menu">
                                                        @foreach($position as $row)
                                                        
                                                            @if($row->pos_div_id > 0)
                                                            <li><a href="javascript:void(0);" 
                                                                   onclick="members('8', '{{ $row->pos_id }}', 'Governing Board : Division {{ $row->pos_name }}')" 
                                                                   class="dropdown-item">
                                                            <span class="badge badge-dark">{{ $row->total }}</span> {{ $row->pos_name }}</a></li>
                                                            @endif

                                                        @endforeach
                                                        </ul>
                                                    </li>
                                                        

                                                    @foreach($position as $row)
                                                        
                                                        @if($row->pos_div_id == 0)
                                                        <button href="javascript:void(0);" onclick="members('8', '{{ $row->pos_id }}', 'Governing Board : {{ $row->pos_name }}')"  class="dropdown-item" type="button">
                                                        <span class="badge badge-dark">{{ $row->total }}</span> {{ $row->pos_name }}</button>
                                                        @endif

                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-sm-2">
                                          graph button
                                        </div> -->
                                    </div>
                                </span>
                                  <!-- <a href="memis/7 target="_blank" class="btn bg-white text-danger mt-1" role="button"  data-toggle="tooltip" data-placement="top" title="Click to view graph">
                                            <span class="fas fa-chart-pie"></span></a> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a role="button" href="memis/0" target="_blank" class="btn btn-danger w-100 mb-2">Generate Graph <span class="fas fa-angle-right"></span></a>
                    <button type="button" class="btn btn-dark w-100" onclick="view_csf_memis()"><span class="badge badge-danger">{{ count($csf_memis)}}</span> Customer Service Feedback <span class="fas fa-angle-right"></span></button>
                <hr/>
                    <a href="https://skms.nrcp.dost.gov.ph/" target="_blank" class="btn-link text-white">skms.nrcp.dost.gov.ph/</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- <div class="card text-white" style="min-height:750px;background-image:linear-gradient(0deg, rgba(29,29,29,1) 10%, rgba(29,29,29,0.5) 100%),url('{{URL::asset('storage/images/bg/brisbg.jpeg')}}');background-size:cover; background-repeat:no-repeat"> -->
            <div class="card text-white rounded shadow" style="min-height:750px;background-color:#eea804">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="media">
                                <img class="mr-3" src="{{URL::asset('storage/images/logos/bris.png')}}" width="25%" height="25%" alt="BRIS">
                                <div class="media-body">
                                    <h5 class="mt-3 font-weight-bold
        ">Basic Research Information System (BRIS)</h5>
                                    A repository of basic researches funded by the NRCP.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col pr-0 pl-0">
                            <div class="list-group list-group-flush mt-4">
                                 <span class="list-group-item list-group-item-action text-white bg-transparent pb-1 pt-1 shadow">
                                    <div class="row">
                                        <div class="col-sm-10"> 
                                            <a onclick="bris_project('0','Projects')" class="btn text-white">
                                                <span class="badge badge-warning" style="font-size:20px;"> {{ $bris_proj }}</span> PROJECTS
                                            </a>
                                        </div>
                                        <div class="col-sm-2 pt-2"> 
                                            <!-- <button type="button" onclick="basic_graph_bris('1','Projects (Basic Bar Graph)')" class="btn bg-white text-warning mt-2" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph">
                                            <span class="fas fa-chart-pie"></span></button> -->
                                            <a href="javascript:void(0);" onclick="basic_graph_bris('1','Projects (Basic Bar Graph)')" class="btn-link text-white shadow rounded\"  data-toggle="tooltip" data-placement="top" title="View basic graph"><span class="fas fa-chart-bar shadow" style="font-size:26px"></span></a>
                                        </div>
                                    </div>
                                </span>
                                <a  onclick="bris_project(2,'Ongoing')" class="btn btn-link list-group-item list-group-item-action text-white bg-transparent pt-2 pb-2 shadow" style="text-indent:">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10 ">
                                        <span class="badge badge-warning" style="font-size:20px;"> {{ $ongoing }} </span> Ongoing</div>
                                    </div>
                                </a>
                                <a  onclick="bris_project(4,'Completed')" class="btn btn-link list-group-item list-group-item-action text-white bg-transparent pt-2 pb-2 shadow"  style="text-indent:">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10 ">
                                        <span class="badge badge-warning" style="font-size:20px;"> {{ $completed }} </span> Completed</div>
                                    </div>
                                </a>
                                <a  onclick="bris_project(3,'Terminated')" class="btn btn-link list-group-item list-group-item-action text-white bg-transparent   pt-2 pb-2 shadow" style="text-indent:">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10 ">
                                        <span class="badge badge-warning" style="font-size:20px;"> {{ $terminated }} </span> Terminated</div>
                                    </div>
                                </a>
                                <a  onclick="bris_project(6,'Extended')" class="btn btn-link list-group-item list-group-item-action text-white bg-transparent  pt-2 pb-2 shadow" style="text-indent:">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10 ">
                                        <span class="badge badge-warning" style="font-size:20px;"> {{ $extended }} </span> Extended</div>
                                    </div>
                                </a>
                                <span class="list-group-item list-group-item-action  text-white bg-transparent pt-1 pb-1 shadow">
                                <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-8">
                                            <div class="btn-group dropright">
                                                <a class="btn btn-link dropdown-toggle pl-0" data-toggle="dropdown">
                                                NIBRA</a>
                                                <div class="dropdown-menu">
                                                @foreach($nibras as $row)
                                                    <button  onclick="bris_nibra({{ $row->nibra_id}}, '{{ $row->nibra_name }}')" class="dropdown-item" type="button">
                                                    <span class="badge badge-dark">{{ $row->total }}</span> {{ $row->nibra_name }}</button>
                                                @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 pt-2">
                                            <!-- <button type="button" onclick="basic_graph_bris('2','Nibras (Basic Bar Graph)')" class="btn bg-white text-warning mt-2" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph">
                                            <span class="fas fa-chart-pie"></span></button> -->
                                            <a href="javascript:void(0);" onclick="basic_graph_bris('2','Nibras (Basic Bar Graph)')" class="btn-link text-white shadow rounded\"  data-toggle="tooltip" data-placement="top" title="View basic graph"><span class="fas fa-chart-bar shadow" style="font-size:26px"></span></a>
                                        </div>
                                    </div>
                                </span>
                                <span class="list-group-item list-group-item-action  text-white bg-transparent pt-1 pb-1 shadow">
                                <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-8">
                                            <div class="btn-group dropright">
                                                <a class="btn btn-link dropdown-toggle pl-0" data-toggle="dropdown">
                                                Other Priority Areas</a>
                                                <div class="dropdown-menu">
                                                    <h6 class="dropdown-header">DOST 11-Point Agenda</h6>
                                                    @foreach($dost_agendas as $row)
                                                    <button  onclick="bris_agenda({{ $row->dost_agenda_id }}, '{{ $row->dost_agenda_code }} : {{ $row->dost_agenda_name }}')" class="dropdown-item" type="button">
                                                    <span class="badge badge-dark">{{ $row->total }}</span> {{ $row->dost_agenda_code }}</button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 pt-2">
                                            <!-- <button type="button" onclick="basic_graph_bris('3','Dost Agendas (Basic Bar Graph)')" class="btn bg-white text-warning mt-1" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph">
                                            <span class="fas fa-chart-pie"></span></button> -->
                                            <a href="javascript:void(0);" onclick="basic_graph_bris('3','Dost Agendas (Basic Bar Graph)')" class="btn-link text-white shadow rounded\"  data-toggle="tooltip" data-placement="top" title="View basic graph"><span class="fas fa-chart-bar shadow" style="font-size:26px"></span></a>
                                        </div>
                                    </div>
                                </span>
                                <span class="list-group-item list-group-item-action text-white bg-transparent pb-1 pt-1 shadow">
                                    <div class="row">
                                        <div class="col-sm-10"> 
                                            <a onclick="bris_program('0', 'Programs')" class="btn text-white">
                                                <span class="badge badge-warning" style="font-size:20px;"> {{ $bris_prog }}</span> PROGRAMS
                                            </a>
                                        </div>
                                        <div class="col-sm-2 pt-2"> 
                                            <!-- <button type="button" onclick="basic_graph_bris('4','Programs (Basic Bar Graph)')" class="btn bg-white text-warning mt-2" role="button" data-toggle="tooltip" data-placement="top" title="See quick graph">
                                            <span class="fas fa-chart-pie"></span></button> -->
                                            <a href="javascript:void(0);" onclick="basic_graph_bris('4','Programs (Basic Bar Graph)')" class="btn-link text-white shadow rounded\"  data-toggle="tooltip" data-placement="top" title="View basic graph"><span class="fas fa-chart-bar shadow" style="font-size:26px"></span></a>
                                        </div>
                                    </div>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <!-- <a role="button" href="memis/0" target="_blank" class="btn btn-warning w-100 mb-2 disabled" data-toggle="tooltip" data-placement="top" title="Sad">Generate Graph <span class="fas fa-angle-right"></span></a> -->
                    <button type="button" class="btn btn-dark w-100" onclick="view_csf_bris()"><span class="badge badge-warning">{{ count($csf_bris)}}</span> Customer Service Feedback <span class="fas fa-angle-right"></span></button>
                <hr/>
                <a href="https://basicresearch.nrcp.dost.gov.ph/" target="_blank" class="btn-link text-white">basicresearch.nrcp.dost.gov.ph/</a>
                    <!-- <a href="bris" target="_blank" class="card-link" hidden>More Details</a> -->
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- <div class="card text-white" style="min-height:750px;background-image:linear-gradient(0deg, rgba(29,29,29,1) 10%, rgba(29,29,29,0.5) 100%),url('{{URL::asset('storage/images/bg/journalbg.jpeg')}}');background-size:cover; background-repeat:no-repeat"> -->
            <div class="card text-white rounded shadow" style="min-height:750px;background-color:#000f74">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="media">
                                <img class="mr-3" src="{{URL::asset('storage/images/logos/ejournal.png')}}" width="25%" height="25%" alt="EJOURNAL">
                                <div class="media-body">
                                    <h5 class="mt-3 font-weight-bold
        ">NRCP Research Journal (eJournal)</h5>
                                    An online publication of research results from the research projects funded by the NRCP.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col pr-0 pl-0">
                            <div class="list-group list-group-flush  mt-3">
                                <a  onclick="ejournal('1','Published Articles');" class="btn btn-link link-group-item list-group-item-action text-white bg-transparent shadow">
                                    <div class="row">
                                        <div class="col-sm-2"><span class="badge badge-primary" style="font-size:20px;"> {{ $count_articles }}</span></div>
                                        <div class="col-sm-10 ">Published Articles</div>
                                    </div>
                                </a>
                                <a  onclick="ejournal('2','Cited Articles');" class="btn btn-link link-group-item list-group-item-action text-white bg-transparent shadow">
                                    <div class="row">
                                        <div class="col-sm-2"><span class="badge badge-primary" style="font-size:20px;"> {{ $count_cites }}</span></div>
                                        <div class="col-sm-10 ">Cited Articles</div>
                                    </div>
                                </a>
                                <a  onclick="ejournal('3','Viewed Articles');" class="btn btn-link link-group-item list-group-item-action  text-white bg-transparent shadow">
                                    <div class="row">
                                        <div class="col-sm-2"><span class="badge badge-primary" style="font-size:20px;"> {{ $count_views }}</span></div>
                                        <div class="col-sm-10 ">Viewed Articles</div>
                                    </div>
                                </a>
                                <a  onclick="ejournal('4','Downloaded Articles');" class="btn btn-link link-group-item list-group-item-action  text-white bg-transparent shadow">
                                    <div class="row">
                                        <div class="col-sm-2"><span class="badge badge-primary" style="font-size:20px;"> {{ $count_downloads }}</span></div>
                                        <div class="col-sm-10 ">Downloaded Articles</div>
                                    </div>
                                </a>
                                <a  onclick="ejournal('5','Most Search Topics','');" class="btn btn-link link-group-item list-group-item-action  text-white bg-transparent shadow">
                                    <div class="row">
                                        <div class="col-sm-2"><span class="badge badge-primary" style="font-size:20px;"> 10</span></div>
                                        <div class="col-sm-10 ">Most Search Topics</div>
                                    </div>
                                </a>
                                <a  onclick="ejournal('6','Full Text PDF Clients');" class="btn btn-link link-group-item list-group-item-action  text-white bg-transparent shadow">
                                    <div class="row">
                                        <div class="col-sm-2"><span class="badge badge-primary" style="font-size:20px;"> {{ $count_clients }}</span></div>
                                        <div class="col-sm-10 ">Full Text PDF Clients</div>
                                    </div>
                                </a>
                                <a  onclick="ejournal('7','Citation Clients');" class="btn btn-link link-group-item list-group-item-action  text-white bg-transparent shadow">
                                    <div class="row">
                                        <div class="col-sm-2"><span class="badge badge-primary" style="font-size:20px;"> {{ $count_citees }}</span></div>
                                        <div class="col-sm-10 ">Citation Clients</div>
                                    </div>
                                </a>
                                <a  onclick="ejournal('8','Visitors Origin','');" class="btn btn-link link-group-item list-group-item-action  text-white bg-transparent shadow">
                                    <div class="row">
                                        <div class="col-sm-2"><span class="badge badge-primary" style="font-size:20px;"> {{ $count_visitors }}</span></div>
                                        <div class="col-sm-10 ">Visitors Origin</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <!-- <a role="button" href="memis/0" target="_blank" class="btn btn-primary w-100 mb-2 disabled">Generate Graph <span class="fas fa-angle-right"></span></a> -->
                    <button type="button" class="btn btn-dark w-100" onclick="view_csf_ej()"><span class="badge badge-primary">{{ count($csf_ej)}}</span> Customer Service Feedback <span class="fas fa-angle-right"></span></button>
                <hr/>
                    <a href="https://researchjournal.nrcp.dost.gov.ph/" target="_blank" class="btn-link text-white">researchjournal.nrcp.dost.gov.ph/</a>
                    <!-- <a href="ejournal" target="_blank" class="card-link" hidden hidden>More Details</a> -->
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center mt-4">
        <div class="col-md-4">
            <!-- <div class="card text-white" style="min-height:370px;background-image:linear-gradient(0deg, rgba(29,29,29,1) 10%, rgba(29,29,29,0.5) 100%),url('{{URL::asset('storage/images/bg/lmsbg.jpeg')}}');background-size:cover; background-repeat:no-repeat"> -->
            <div class="card text-white rounded shadow" style="min-height:750px;background-color:#007503">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="media">
                                <img class="mr-3" src="{{URL::asset('storage/images/logos/lms.png')}}" width="25%" height="25%" alt="LMS">
                                <div class="media-body">
                                    <h5 class="mt-0 font-weight-bold">Library Management System (LMS)</h5>
                                    <small>A repository of terminal reports of research projects funded by the NRCP, policy briefs, monographs and other resources.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col pr-0 pl-0">
                            <div class="list-group list-group-flush">
                                @foreach($categories as $values)
                                <a onclick="librarysys('{{ $values->cat_id }}','{{ $values->category }}');" class="btn btn-link list-group-item list-group-item-action text-white bg-transparent shadow">
                                    <div class="row">
                                        <div class="col-sm-2"><span class="badge badge-success" style="font-size:20px;"> {{ $values->total }}</span></div>
                                        <div class="col-sm-10 ">{{ $values->category }}</div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="card-footer text-center">
                    <!-- <a role="button" href="memis/0" target="_blank" class="btn btn-success w-100 mb-2 disabled">Generate Graph <span class="fas fa-angle-right"></span></a> -->
                    <button type="button" class="btn btn-dark w-100"  onclick="view_csf_lms()"><span class="badge badge-success">{{ count($csf_lms)}}</span> Customer Service Feedback <span class="fas fa-angle-right"></span></button>
                <hr/>
                <a href="https://scientificlibrary.nrcp.dost.gov.ph/" target="_blank" class="btn-link text-white">scientificlibrary.nrcp.dost.gov.ph/</a>
                    <!-- <a href="lms" target="_blank" class="card-link" hidden>More Details</a> -->
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col">
                    <div class="card text-white rounded shadow" style="background-color:#077478">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="media">
                                        <img class="mr-3" src="{{URL::asset('storage/images/logos/nrcpnet.png')}}" width="29%" height="29%" alt="NRCPNET">
                                        <div class="media-body">
                                            <h5 class="mt-0 font-weight-bold">NRCPNET</h5>
                                            Description here...
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        <div class="row mt-2">
                            <div class="col pr-0 pl-0">
                                <div class="list-group list-group-flush">
                                    <a  onclick="nrcpnet(1, 'Plantilla Personnels')" class="btn btn-link list-group-item list-group-item-action text-white bg-transparent shadow">
                                        <div class="row">
                                            <div class="col-sm-2"><span class="badge badge-light" style="font-size:20px;"> {{ $count_plant }}</span></div>
                                            <div class="col-sm-10 ">Plantilla Personnel</div>
                                        </div>
                                    </a>
                                    <a  onclick="nrcpnet(2, 'Contractual Personnels')" class="btn btn-link list-group-item list-group-item-action text-white bg-transparent shadow">
                                        <div class="row">
                                            <div class="col-sm-2"><span class="badge badge-light" style="font-size:20px;"> {{ $count_cont }}</span></div>
                                            <div class="col-sm-10 ">Contractual Personnel</div>
                                        </div>
                                    </a>
                                    <a  onclick="nrcpnet(3, 'Job Orders')" class="btn btn-link list-group-item list-group-item-action text-white bg-transparent shadow">
                                        <div class="row">
                                            <div class="col-sm-2"><span class="badge badge-light" style="font-size:20px;"> {{ $count_jo }}</span></div>
                                            <div class="col-sm-10 ">Job Order</div>
                                        </div>
                                    </a>
                                    <a  onclick="nrcpnet(4, 'Vacant Positions')" class="btn btn-link list-group-item list-group-item-action text-white bg-transparent shadow">
                                        <div class="row">
                                            <div class="col-sm-2"><span class="badge badge-light" style="font-size:20px;"> {{ $count_vac }}</span></div>
                                            <div class="col-sm-10 ">Vacant Position</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                
                    </div>
                </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col">
                    <div class="card rounded shadow text-white" style="background-color:#1D85C1">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="mt-0 font-weight-bold">RDLIP: Research Dissemination in Local and International Platforms</h5>  
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col pr-0 pl-0">
                                    <div class="list-group list-group-flush">
                                        <a  onclick="rdlip(1, 'Paper Presentation')" class="btn btn-link list-group-item list-group-item-action bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-info text-white" style="font-size:20px;"> {{ $count_paper }}</span></div>
                                                <div class="col-sm-10 ">Paper Presentation</div>
                                            </div>
                                        </a>
                                        <a  onclick="rdlip(2, 'Publication Grant')" class="btn btn-link list-group-item list-group-item-action bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-info text-white" style="font-size:20px;"> {{ $count_pub }}</span></div>
                                                <div class="col-sm-10 ">Publication</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <!-- <a role="button" href="memis/0" target="_blank" class="btn btn-info w-100 mb-2 disabled">Generate Graph <span class="fas fa-angle-right"></span></a> -->
                            <button type="button" class="btn btn-dark w-100" onclick="view_csf_rdlip()"><span class="badge badge-info">{{ count($csf_rdlip)}}</span> Customer Service Feedback <span class="fas fa-angle-right"></span></button>
                            <hr/>
                                <a href="https://skms.nrcp.dost.gov.ph/rdlip" target="_blank" class="btn-link text-white">skms.nrcp.dost.gov.ph/rdlip</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col">
                    <div class="card text-dark rounded shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="media">
                                        <div class="media-body">
                                            <h5 class="mt-0 font-weight-bold">SKMS: Thesis and Dissertation</h5>
                                            Applications
                                        </div>
                                    </div> 
                                </div>
                                </div>
                            <div class="row mt-2">
                                <div class="col pr-0 pl-0">
                                    <div class="list-group list-group-flush">
                                        <a  onclick="thds(1, 'Thesis Applications')" class="btn btn-link list-group-item list-group-item-action text-dark bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-dark" style="font-size:20px;"> {{ $count_ths }}</span></div>
                                                <div class="col-sm-10 ">Thesis</div>
                                            </div>
                                        </a>
                                        <a  onclick="thds(2, 'Dissertation Applications')" class="btn btn-link list-group-item list-group-item-action text-dark bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-dark" style="font-size:20px;"> {{ $count_dis }}</span></div>
                                                <div class="col-sm-10 ">Dissertation</div>
                                            </div>
                                        </a>
                                        <a  onclick="thds(3, 'Approved Thesis and Dissertation Applications')" class="btn btn-link list-group-item list-group-item-action text-dark bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-dark" style="font-size:20px;"> {{ $count_app_thds }}</span></div>
                                                <div class="col-sm-10 ">Approved</div>
                                            </div>
                                        </a>
                                        <a  onclick="thds(4, 'Disapproved Thesis and Dissertation Applications')" class="btn btn-link list-group-item list-group-item-action text-dark bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-dark" style="font-size:20px;"> {{ $count_dis_thds }}</span></div>
                                                <div class="col-sm-10 ">Disapproved</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <!-- <a role="button" href="memis/0" target="_blank" class="btn btn-dark w-100 mb-2 disabled">Generate Graph <span class="fas fa-angle-right"></span></a> -->
                            <button type="button" class="btn btn-dark w-100" onclick="view_csf_thds()"><span class="badge badge-secondary">{{ count($csf_thds)}}</span> Customer Service Feedback <span class="fas fa-angle-right"></span></button>
                    
                        </div>
                        
                            <!-- <a href="lms" target="_blank" class="card-link" hidden>More Details</a> -->
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col">
                    <div class="card rounded shadow bg-secondary text-white">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="mt-0 font-weight-bold">Customer Service Feedback</h5>  
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col pr-0 pl-0">
                                    <div class="list-group list-group-flush">
                                        <a  onclick="view_csf_memis()" class="btn btn-link list-group-item list-group-item-action bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-dark" style="font-size:20px;"> {{ count($csf_memis) }}</span></div>
                                                <div class="col-sm-10 ">NRCP Membership Application</div>
                                            </div>
                                        </a>
                                        <a  onclick="view_csf_bris()" class="btn btn-link list-group-item list-group-item-action bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-dark" style="font-size:20px;"> {{ count($csf_bris) }}</span></div>
                                                <div class="col-sm-10 ">Research Grant (Grants-In-Aid)</div>
                                            </div>
                                        </a>
                                        <a  onclick="view_csf_ej()" class="btn btn-link list-group-item list-group-item-action bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-dark" style="font-size:20px;"> {{ count($csf_ej) }}</span></div>
                                                <div class="col-sm-10 ">Journal Service</div>
                                            </div>
                                        </a>
                                        <a  onclick="view_csf_lms()" class="btn btn-link list-group-item list-group-item-action bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-dark" style="font-size:20px;"> {{ count($csf_lms) }}</span></div>
                                                <div class="col-sm-10 ">Library Service</div>
                                            </div>
                                        </a>
                                        <a  onclick="view_csf_thds()" class="btn btn-link list-group-item list-group-item-action bg-transparent shadow">
                                            <div class="row">
                                                <div class="col-sm-2"><span class="badge badge-dark" style="font-size:20px;"> {{ count($csf_thds) }}</span></div>
                                                <div class="col-sm-10 ">Thesis/Dissertation Manuscript Grant</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <!-- <a role="button" href="memis/0" target="_blank" class="btn btn-info w-100 mb-2 disabled">Generate Graph <span class="fas fa-angle-right"></span></a> -->
                            <button type="button" class="btn btn-dark w-100" onclick="view_csf_overall()">Overall CSF <span class="fas fa-angle-right"></span></button>
                    
                        </div>
                        
                            <!-- <a href="lms" target="_blank" class="card-link" hidden>More Details</a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--members modal -->
<div class="modal fade" id="member_modal" tabindex="-1" role="dialog" style="z-index: 1600;" data-backdrop="static" data-keyboard="false">
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
                <table class="table table-hover w-100" id="member_table">
                <thead>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /.members modal -->

<!--ejournal modal -->
<div class="modal fade" id="ejournal_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-hover w-100  " id="ejournal_table">
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
<!-- /.ejournal modal -->

<!-- library modal -->
<div class="modal fade" id="library_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-hover w-100  " id="library_table">
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
<!-- /.library modal -->

<!-- nrcpnet modal -->
<div class="modal fade" id="nrcpnet_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-hover w-100  " id="nrcpnet_table">
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
<!-- /.nrcpnet modal -->

<!-- bris project modal -->
<div class="modal fade" id="bris_modal" tabindex="-1" role="dialog"  style="z-index: 1600;" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-hover w-100 " id="bris_table">
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
<!-- /.bris project modal -->

<!-- thds modal -->
<div class="modal fade" id="thds_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-hover w-100  " id="thds_table">
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
<!-- /.thds modal -->

<!-- rdlip modal -->
<div class="modal fade" id="rdlip_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <table class="table table-hover w-100  " id="rdlip_table">
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
<!-- /.rdlip modal -->
<!-- result modal -->
<div class="modal fade" id="result_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search results</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-dark mb-0 pb-0">
                    <p><strong><span class="fa fa-search"></span> Search query:</strong> <span class="searches"></span></p>
                </div>
                <div class="accordion mt-2" id="result_accordion">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /.result modal -->

<!-- users modal -->
<div class="modal fade" id="users_modal" tabindex="-1" role="dialog"  data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span class="fas fa-user-friends"></span> Manage Users</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="users-tab" data-toggle="tab" href="#users" role="tab" onclick="all_users();"><span class="fas fa-user-tie"></span> Execom Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="create-tab" data-toggle="tab" href="#create" role="tab"><span class="fas fa-user-plus"></span> Add User</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active p-1 pt-4" id="users" role="tabpanel">
                        <table class="table table-hover table-hovered" id="execom_table">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="float-right pt-4">
                            <button type="button" class="btn btn-outline-secondary"  data-dismiss="modal">Close</button>
                        </div>
                        
                    </div>
                    <div class="tab-pane fade p-1 pt-4" id="create" role="tabpanel">
                        <form id="create_new_form">
                        @csrf
                            <div class="form-group row">
                                <label for="name" class="col-sm-3 col-form-label text-right font-weight-bold">Name</label>
                                <div class="col-sm-9">
                                <input type="text"  class="form-control" id="name" name="name" placeholder="ex. Juan Dela Cruz">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-sm-3 col-form-label text-right font-weight-bold">Email</label>
                                <div class="col-sm-9">
                                <input type="email"  class="form-control" id="email" name="email" placeholder="juandelacruz@email.com">
                                </div>
                            </div>
                            <div class="form-group row">
                                
                                <label for="password" class="col-sm-3 col-form-label text-right font-weight-bold">Password</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter strong password">
                                        <div class="input-group-append" >
                                            <button type="button" id="show_password_add_user" class="btn btn-secondary" ><span class="fa fa-eye icon text-white"></span></button>
                                        </div>
                                    </div>
                                    <div class="hidden result w-100"><span id="result"></span></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="repeatPassword" class="col-sm-3 col-form-label text-right font-weight-bold">Repeat Password</label>
                                <div class="col-sm-9">
                                <input type="password" class="form-control" id="repeat_password" name="repeat_password" placeholder="Repeat password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="role" class="col-sm-3 col-form-label text-right font-weight-bold">Role</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="role" name="role">
                                        <option value="">Select Role</option>
                                        <option value="1">Superadmin</option>
                                        <option value="2">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="float-right">
                                <button type="button" class="btn btn-outline-secondary"  data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary create-execom-user">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- <div class="col-6">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><span class="fas fa-users"></span> SKMS users</a>
                    </li>
                </ul>
                <div class="p-1">
                    <table class="table table-hover table-hovered" id="skms_table">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Email</th>
                            <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div> -->
        </div>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div> -->
    </div>
  </div>
</div>
<!-- /.user modal -->

<!-- edit users modal -->
<div class="modal fade" id="edit_user_modal" tabindex="-1" role="dialog"  data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span class="fas fa-user-friends"></span> Edit user</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="edit_user_form">
            @csrf
            <input type="hidden" id="user_id" name="user_id">
            <div class="form-group row">
                <label for="name" class="col-sm-3 col-form-label text-right font-weight-bold">Name</label>
                <div class="col-sm-9">
                <input type="text"  class="form-control" id="name" name="name" placeholder="ex. Juan Dela Cruz">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-3 col-form-label text-right font-weight-bold">Email</label>
                <div class="col-sm-9">
                <input type="email"  class="form-control" id="email" name="email" placeholder="juandelacruz@email.com">
                </div>
            </div>
            <div class="form-group row">
                
                <label for="password" class="col-sm-3 col-form-label text-right font-weight-bold">New Password</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter strong password">
                        <div class="input-group-append" >
                            <button type="button" id="show_password_edit_user" class="btn btn-secondary" ><span class="fa fa-eye icon text-white"></span></button>
                        </div>
                    </div>
                    <div class="hidden result w-100"><span id="result"></span></div>
                </div>
            </div>
            <div class="form-group row">
                <label for="repeatPassword" class="col-sm-3 col-form-label text-right font-weight-bold">Repeat New Password</label>
                <div class="col-sm-9">
                <input type="password" class="form-control" id="repeat_password" name="repeat_password" placeholder="Repeat password">
                </div>
            </div>
            <div class="form-group row">
                <label for="role" class="col-sm-3 col-form-label text-right font-weight-bold">Role</label>
                <div class="col-sm-9">
                    <select class="form-control" id="role" name="role">
                        <option value="">Select Role</option>
                        <option value="1">Superadmin</option>
                        <option value="2">Admin</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-3 col-form-label text-right font-weight-bold">Status</label>
                <div class="col-sm-9">
                    <select class="form-control" id="status" name="status">
                        <option value="1">Activated</option>
                        <option value="2">Deactivated</option>
                    </select>
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger remove-execom-user"  data-dismiss="modal">Delete account</button>
            <div class="float-right">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary update-execom-user">Save changes</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.edit user modal -->

<!-- logs modal -->
<div class="modal fade" id="logs_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span class="far fa-clipboard"></span> Activity Logs</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body table-responsive">
        <!-- <div class="table-responsive"> -->
            <table class="table table-hover w-100" id="logs_table">
                <thead>
                <th>#</th>
                <th>Email</th>
                <th>Activity</th>
                <th>IP Address</th>
                <th>Operating System</th>
                <th>Browser</th>
                <th>Date</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        <!-- </div> -->
      </div>
      <div class="modal-footer">
        <div class="btn-group mr-auto" role="group" aria-label="Basic example">
            <a type="button" class="btn btn-outline-danger" href="{{ url('backup/clear_with_export_logs') }}" target="_blank">Clear Logs with Backup</a>
            <a type="button" class="btn btn-outline-secondary" href="{{ url('backup/export_logs') }}" target="_blank">Backup Only</a>
        </div>
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.logs modal -->

<!-- overall result modal -->
<div class="modal fade" id="overall_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Search Results | 
                    <!-- <span class="badge badge-pill badge-dark"> -->
                        <span class="fas fa-search"></span>
                        <span id="quick_search_keyword"></span>
                    <!-- </span> -->
                </h5>

                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs font-weight-bold" id="overall_tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="memis-tab" data-toggle="tab" href="#memis" role="tab">MEMIS</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="bris-tab" data-toggle="tab" href="#bris" role="tab">BRIS</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="ejournal-tab" data-toggle="tab" href="#ejournal" role="tab">EJOURNAL</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="lms-tab" data-toggle="tab" href="#lms" role="tab">LMS</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="nrcpnet-tab" data-toggle="tab" href="#nrcpnet" role="tab">NRCPNET</a>
                    </li>
                </ul>
                <div class="tab-content" id="overall_content">
                    <!-- memis tab -->
                    <div class="tab-pane fade show active" id="memis" role="tabpanel">
                        <div class="accordion" id="memis_accordion">
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light text-left w-100" data-toggle="collapse" data-target="#memis_members">
                                        <h5 class="mb-0">Members <span class="memis_mem_count"></span></h5>
                                    </button>
                                </div>

                                <div id="memis_members" class="collapse" data-parent="#memis_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="memis_members_table">
                                            <thead>
                                                <th>#</th>       
                                                <th>Title</th>
                                                <th>Last Name</th>
                                                <th>First Name</th>
                                                <th>Middle Name</th>
                                                <th>Sex</th>
                                                <th>Contact</th>
                                                <th>Email</th> 
                                                <th>Division</th> 
                                                <th>Region</th>
                                                <th>Province</th>
                                                <th>City</th>
                                                <th>Barangay</th>
                                                <th>Status</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light w-100 text-left" type="button" data-toggle="collapse" data-target="#memis_specializations" aria-expanded="false" aria-controls="collapseTwo">
                                    <h5 class="mb-0">Specializations <span class="memis_spec_count"></span></h5>
                                    </button>
                                </div>
                                <div id="memis_specializations" class="collapse" data-parent="#memis_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="memis_spec_table">
                                            <thead>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Last Name</th>
                                                <th>First Name</th>
                                                <th>Middle Name</th>
                                                <th>Sex</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Division</th>
                                                <th>Specialization</th>
                                                <th>Region</th>
                                                <th>Province</th>
                                                <th>City</th>
                                                <th>Status</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light w-100 text-left" type="button" data-toggle="collapse" data-target="#memis_awards" aria-expanded="false" aria-controls="collapseTwo">
                                    <h5 class="mb-0">Achivement Awards <span class="memis_awa_count"></span></h5>
                                    </button>
                                </div>
                                <div id="memis_awards" class="collapse" data-parent="#memis_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="memis_awards_table">
                                            <thead>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Last Name</th>
                                                <th>First Name</th>
                                                <th>Middle Name</th>
                                                <th>Sex</th>
                                                <th>Division</th>
                                                <th>Award Year</th>
                                                <th>Citation</th>
                                                <th>Status</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light w-100 text-left" type="button" data-toggle="collapse" data-target="#memis_gbs" aria-expanded="false" aria-controls="collapseTwo">
                                    <h5 class="mb-0">Governing Board <span class="memis_gb_count"></span></h5>
                                    </button>
                                </div>
                                <div id="memis_gbs" class="collapse" data-parent="#memis_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="memis_gbs_table">
                                            <thead>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Last Name</th>
                                                <th>First Name</th>
                                                <th>Middle Name</th>
                                                <th>Sex</th>
                                                <th>GB Position</th>
                                                <th>Period From</th>
                                                <th>Period To</th>
                                                <th>Remarks</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.memis tab -->
                    <!-- bris tab -->
                    <div class="tab-pane fade" id="bris">
                        <div class="accordion" id="bris_accordion">
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#bris_projects">
                                    <h5 class="mb-0">Projects <span class="bris_proj_count"></span></h5>
                                    </button>
                                </div>

                                <div id="bris_projects" class="collapse" data-parent="#bris_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="bris_project_table">
                                            <thead>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Project Leader</th>
                                                <th>Status</th> 
                                                <th>Date submitted</th>
                                                <!-- <th>Region</th>
                                                <th>Province</th>
                                                <th>City</th>
                                                <th>Barangay</th> -->
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#bris_programs">
                                    <h5 class="mb-0">Programs
                                    <span class="bris_prog_count"></span>
                                    </button>
                                </div>
                                <div id="bris_programs" class="collapse" data-parent="#bris_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="bris_program_table">
                                            <thead>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Program Manager</th> 
                                                <th>Status</th>
                                                <th>Date submitted</th>
                                                <!-- <th>Region</th>
                                                <th>Province</th>
                                                <th>City</th>
                                                <th>Barangay</th> -->
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="card">
                                <div class="card-header p-0">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#bris_proposals">
                                    Proposal
                                    <span class="bris_prog_count"></span>
                                    </button>
                                </h2>
                                </div>
                                <div id="bris_proposal" class="collapse" data-parent="#overall_accordion">
                                    <div class="card-body p-0">
                                        <table class="table">
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div> -->
                        </div>    
                    </div>
                    <!-- /.bris tab -->
                    <!-- ejournal tab -->
                    <div class="tab-pane fade" id="ejournal" role="tabpanel">
                        <div class="accordion" id="ejournal_accordion">
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#ejournal_titles">
                                    <h5 class="mb-0">Titles <span class="ej_title_count"></span></h5>
                                    </button>
                                </div>

                                <div id="ejournal_titles" class="collapse" data-parent="#ejournal_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="ejournal_title_table">
                                            <thead>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Author</th>
                                                <th>Date submitted</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#ejournal_authors">
                                    <h5 class="mb-0">Authors
                                    <span class="ej_author_count"></span></h5>
                                    </button>
                                </div>
                                <div id="ejournal_authors" class="collapse" data-parent="#ejournal_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="ejournal_author_table">
                                            <thead>
                                                <th></th>
                                                <th>Title</th>
                                                <th>Author</th>
                                                <th>Date submitted</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <!-- /.ejournal tab -->
                    <!-- lms tab -->
                    <div class="tab-pane fade" id="lms" role="tabpanel">
                        <div class="accordion" id="lms_accordion">
                            @php $i=0; @endphp 
                            @foreach($categories as $values)
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#lms_{{ $values->cat_id }}">
                                        <h5 class="mb-0">{{ $values->category }}
                                        <span class="lms_{{ $i }}_count"></span>
                                        </h5>
                                    </button>
                                </div>
                                <div id="lms_{{ $values->cat_id }}" class="collapse" data-parent="#lms_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="lms_{{ $i }}_table"> 
                                            <thead>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Keywords</th>
                                                <th>Date submitted</th>
                                                <th>PDF</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                            @php $i++; @endphp
                            @endforeach
                        </div> 
                    </div>
                    <!-- /.lms tab -->
                    <!-- nrcpnet tab -->
                    <div class="tab-pane fade" id="nrcpnet" role="tabpanel">
                        <div class="accordion" id="nrcpnet_accordion">
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#nrcpnet_employees">
                                    <h5 class="mb-0">Employee Name <span class="net_employee_count"></span></h5>
                                    </button>
                                </div>

                                <div id="nrcpnet_employees" class="collapse" data-parent="#nrcpnet_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="nrcpnet_employee_table">
                                            <thead>
                                                <th>#</th>
                                                <th>Last name</th>
                                                <th>First name</th>
                                                <th>Middle name</th>
                                                <th>Appointment</th>
                                                <th>Division</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header p-0">
                                    <button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#nrcpnet_divisions">
                                    <h5 class="mb-0">Divisions
                                    <span class="net_division_count"></span></h5>
                                    </button>
                                </div>
                                <div id="nrcpnet_divisions" class="collapse" data-parent="#ejournal_accordion">
                                    <div class="card-body p-3 table-responsive">
                                        <table class="table table-hover w-100" id="nrcpnet_division_table">
                                            <thead>
                                                <th>#</th>
                                                <th>Last name</th>
                                                <th>First name</th>
                                                <th>Middle name</th>
                                                <th>Appointment</th>
                                                <th>Division</th>
                                            </thead>
                                            <tbody></tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <!-- /.ejournal tab -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /.overall result modal -->

<!-- feedback modal -->
<div class="modal fade" id="feedbackModal" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header pb-0">
        <p><span class="modal-title font-weight-bold h3">Your feedback</span><br/>
        <small>We would like your feedback to improve our system.</small></p>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body p-4">
        <form id="feedback_form">
            <div class="feedback text-center">
                <h5 class="pt-2">Please rate our system or service. </h5>
            
                <hr/>

                <p class="font-weight-bold h4 text-center">User Interface</p>
                <div class="feedback-container ui-container">
                    <div class="feedback-item">
                        <label for="ui-1" data-toggle="tooltip" data-placement="bottom" title="Sad">
                            <input class="radio" type="radio" name="fb_rate_ui" id="ui-1" value="1" >
                            <span >🙁</span>
                        </label>
                    </div>

                    <div class="feedback-item">
                        <label for="ui-2" data-toggle="tooltip" data-placement="bottom" title="Neutral">
                            <input class="radio" type="radio" name="fb_rate_ui" id="ui-2" value="2">
                            <span>😶</span>
                        </label>
                    </div>

                    <div class="feedback-item">
                        <label for="ui-3" data-toggle="tooltip" data-placement="bottom" title="Happy">
                            <input class="radio" type="radio" name="fb_rate_ui" id="ui-3" value="3">
                            <span>🙂</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fb_suggest_ui"></label>
                    <textarea class="form-control" name="fb_suggest_ui" id="fb_suggest_ui" rows="3" placeholder="Type your suggestions here"></textarea>
                </div>

                <hr/>

                <p class="font-weight-bold h4 text-center">User Experience</p>
                <div class="feedback-container ux-container">
                    <div class="feedback-item">
                        <label for="ux-1" data-toggle="tooltip" data-placement="bottom" title="Sad">
                            <input class="radio" type="radio" name="fb_rate_ux" id="ux-1" value="1">
                            <span>🙁</span>
                        </label>
                    </div>

                    <div class="feedback-item">
                        <label for="ux-2" data-toggle="tooltip" data-placement="bottom" title="Nuetral">
                            <input class="radio" type="radio" name="fb_rate_ux" id="ux-2" value="2">
                            <span>😶</span>
                        </label>
                    </div>

                    <div class="feedback-item">
                        <label for="ux-3" data-toggle="tooltip" data-placement="bottom" title="Happy">
                            <input class="radio" type="radio" name="fb_rate_ux" id="ux-3" value="3">
                            <span>🙂</span>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="fb_suggest_ux"></label>
                    <textarea class="form-control" name="fb_suggest_ux" id="fb_suggest_ux" rows="3" placeholder="Type your suggestions here"></textarea>
                </div>

                <div class="btn-group w-100" role="group">
                    <button class="btn btn-lg btn-outline-dark" type="button" data-dismiss="modal">Later</button>
                    <button type="submit" class="btn btn-lg btn-dark">Submit Feedback</button>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.feedback modal -->

<!-- feedbacks chart and list modal -->
<div class="modal fade" id="feedbacks_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span class="far fa-edit"></span> Feedbacks</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-6">
                <canvas id="fb_ui_chart" width="400" height="400"></canvas></div>
            <div class="col-6">
                <canvas id="fb_ux_chart" width="400" height="400"></canvas></div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-hover" id="feedback_table">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">UI Rating</th>
                        <th scope="col">UI Suggestions</th>
                        <th scope="col">UX Rating</th>
                        <th scope="col">UX Suggestions</th>
                        <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.feedbacks chart and list modal -->

<!-- database modal -->
<div class="modal fade" id="database_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span class="fas fa-database" style="width:20px"></span> Backup/Restore Database </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="card">
            <div class="card-header">
                Backup Database
            </div>
            <div class="card-body">
                <form id="export_db_form" action="{{ url('backup/export') }}" method="POST">
                @csrf
                <strong>Export method:</strong>
                <hr/>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="export_method" id="quick_export" value="1" checked>
                    <label class="form-check-label" for="quick_export">
                        Quick - Create backup of the database
                    </label>
                </div>
                <div class="form-check mt-1 mb-3">
                    <input class="form-check-input" type="radio" name="export_method" id="custom_export" value="2">
                    <label class="form-check-label" for="custom_export">
                        Custom - Select specific table to backup
                    </label>
                </div>
                <!-- <strong>Format:</strong>
                <hr/>
                <select class="form-control w-25 form-control-sm" id="export_format" name="export_format">
                    <option value="sql">SQL</option>
                    <option value="csv">CSV</option>
                </select> -->
                <table id="sd_table" class="table table-hover mt-3 table-sm table-bordered">
                        <thead>
                            <tr>
                            <th scope="col">Table</th>
                            <th scope="col">Structure</th>
                            <th scope="col">Data</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr class="table-warning"><td>Select all</td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="select_all_structure" name="select_all_structure">
                                    <label class="form-check-label" for="defaultCheck1">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="select_all_data" name="select_all_data">
                                    <label class="form-check-label" for="defaultCheck1">
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php  foreach($tables as $table){

                    echo '<tr> 
                              <td>' . $table->Tables_in_dbexecom . '</td> 
                              <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="table_structure[]" value="'. $table->Tables_in_dbexecom .'" id="defaultCheck1">
                                    <label class="form-check-label" for="defaultCheck1">
                                    </label>
                                </div>
                              </td> 
                              <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="table_data[]" value="'. $table->Tables_in_dbexecom .'" id="defaultCheck1">
                                    <label class="form-check-label" for="defaultCheck1">
                                    </label>
                                </div>
                              </td> 
                         </tr>';
                      }?>
                        </tbody>
                    </table>
                    <div class="mt-3">
                      <button type="submit" id="export_button" class="btn btn-outline-dark">Go</button>
                    </div>
                    </form>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">
                Import Backup
            </div>
            <div class="card-body">
                <form id="import_db_form"  enctype="multipart/form-data" method="post">
                @csrf
                    <div class="input-group is-invalid">
                        <div class="custom-file">
                        <input type="file" class="custom-file-input" id="import_file" name="import_file">
                        <label class="custom-file-label" for="import_file">Choose file...</label>
                        </div>
                        <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-dark" >Go</button>
                        </div>
                    </div>
                    <div class="invalid-feedback">
                    </div>
                </form>
                    <span id="success_import" class="mt-3"></span>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button"  class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.database modal -->

<!-- basic graph modal -->
<div class="modal fade" id="basic_graph_modal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="w-100" id="basic_bar_chart"></div>
        <div class="row mb-2">
            <div class="col-2 font-weight-bold">Count/Percentage:</div>
            <div class="col-10"><input type="checkbox" id="chart_numbers" checked  data-size="sm" data-toggle="toggle" data-on="Show" data-off="Hide" data-onstyle="secondary" data-width="60"></div>
        </div>
        <div class="row mb-2">
            <div class="col-2 font-weight-bold">Orientation:</div>
            <div class="col-10"><input type="checkbox" id="chart_orientation" checked  data-size="sm" data-toggle="toggle" data-on="Horizontal" data-off="Vertical" data-onstyle="secondary" data-width="90"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.basic graph modal -->

<!-- csf graph modal memis -->
<div class="modal fade" id="csf_graph_memis_modal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-dark" role="alert">
            <a href="#" class="alert-link">5</a> - Very Satisfied
            <a href="#" class="alert-link ml-3">4</a> - Satisfied
            <a href="#" class="alert-link ml-3">3</a> - Neither Satisfied nor Dissatisfied
            <a href="#" class="alert-link ml-3">2</a> - Dissatisfied
            <a href="#" class="alert-link ml-3">1</a> - Very Dissatisfied
        </div>
        <ul class="nav nav-tabs" id="memis_nav_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class=" font-weight-bold nav-link active" id="memis_csf_list_tab" data-toggle="tab" href="#memis_csf_list" role="tab" aria-controls="memis_csf_list" aria-selected="true">CSF</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="font-weight-bold nav-link" id="memis_csf_graph_tab" data-toggle="tab" href="#memis_csf_graph" role="tab" aria-controls="memis_csf_graph" aria-selected="false">Graph</a>
            </li>
        </ul>
        <div class="tab-content p-1" id="memis_tab_content">
            <div class="tab-pane fade show active" id="memis_csf_list" role="tabpanel" aria-labelledby="memis_csf_list_tab">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="csf_memis_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Sex</th>
                                    <th>Email</th>
                                    <th>Age</th>
                                    <th>Position</th>
                                    <th>Institution</th>
                                    <th>Region</th>
                                    <th>Division</th>
                                    <th>Date submitted</th>
                                    @foreach($csf_desc as $row)
                                    <th>{{ $row->svc_fdbk_q_desc }}</th>
                                    @endforeach
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="memis_csf_graph" role="tabpanel" aria-labelledby="memis_csf_graph_tab">
                <div class="row">
                    <div class="col-8">
                        <div class="w-100 bg-secondary shadow mt-3" id="memis_csf_chart"></div>
                    </div>
                    <div class="col-4">
                        <div class="w-100 bg-secondary shadow mt-3" id="memis_csf_chart2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="memis_csf_chart3"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="memis_csf_chart4"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="memis_csf_chart5"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.csf graph modal memis -->

<!-- csf graph modal bris -->
<div class="modal fade" id="csf_graph_bris_modal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-dark" role="alert">
            <a href="#" class="alert-link">5</a> - Very Satisfied
            <a href="#" class="alert-link ml-3">4</a> - Satisfied
            <a href="#" class="alert-link ml-3">3</a> - Neither Satisfied nor Dissatisfied
            <a href="#" class="alert-link ml-3">2</a> - Dissatisfied
            <a href="#" class="alert-link ml-3">1</a> - Very Dissatisfied
        </div>
        <ul class="nav nav-tabs" id="bris_nav_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class=" font-weight-bold nav-link active" id="bris_csf_list_tab" data-toggle="tab" href="#bris_csf_list" role="tab" aria-controls="bris_csf_list" aria-selected="true">CSF</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="font-weight-bold nav-link" id="bris_csf_graph_tab" data-toggle="tab" href="#bris_csf_graph" role="tab" aria-controls="bris_csf_graph" aria-selected="false">Graph</a>
            </li>
        </ul>
        <div class="tab-content p-1" id="bris_tab_content">
            <div class="tab-pane fade show active" id="bris_csf_list" role="tabpanel" aria-labelledby="bris_csf_list_tab">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="csf_bris_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Email</th>
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <!-- <th>Position</th> -->
                                    <th>Institution</th>
                                    <th>Region</th>
                                    <th>Division</th>
                                    <th>Date submitted</th>
                                    @foreach($csf_desc as $row)
                                    <th>{{ $row->svc_fdbk_q_desc }}</th>
                                    @endforeach
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="bris_csf_graph" role="tabpanel" >
                <div class="row">
                    <div class="col-8">
                        <div class="w-100 bg-secondary shadow mt-3" id="bris_csf_chart"></div>
                    </div>
                    <div class="col-4">
                        <div class="w-100 bg-secondary shadow mt-3" id="bris_csf_chart2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="bris_csf_chart3"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="bris_csf_chart4"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="bris_csf_chart5"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.csf graph modal bris-->

<!-- csf graph modal ej -->
<div class="modal fade" id="csf_graph_ej_modal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-dark" role="alert">
            <a href="#" class="alert-link">5</a> - Very Satisfied
            <a href="#" class="alert-link ml-3">4</a> - Satisfied
            <a href="#" class="alert-link ml-3">3</a> - Neither Satisfied nor Dissatisfied
            <a href="#" class="alert-link ml-3">2</a> - Dissatisfied
            <a href="#" class="alert-link ml-3">1</a> - Very Dissatisfied
        </div>
        <ul class="nav nav-tabs" id="ej_nav_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class=" font-weight-bold nav-link active" id="ej_csf_list_tab" data-toggle="tab" href="#ej_csf_list" role="tab" aria-controls="ej_csf_list" aria-selected="true">CSF</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="font-weight-bold nav-link" id="ej_csf_graph_tab" data-toggle="tab" href="#ej_csf_graph" role="tab" aria-controls="ej_csf_graph" aria-selected="false">Graph</a>
            </li>
        </ul>
        <div class="tab-content p-1" id="ej_tab_content">
            <div class="tab-pane fade show active" id="ej_csf_list" role="tabpanel" aria-labelledby="ej_csf_list_tab">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="csf_ej_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Sex</th>
                                    <th>Email</th>
                                    <th>Affiliation</th>
                                    <th>Country</th>
                                    <th>Date submitted</th>
                                    @foreach($csf_desc as $row)
                                    <th>{{ $row->svc_fdbk_q_desc }}</th>
                                    @endforeach
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="ej_csf_graph" role="tabpanel" >
                <div class="row">
                    <div class="col-8">
                        <div class="w-100 bg-secondary shadow mt-3" id="ej_csf_chart"></div>
                    </div>
                    <div class="col-4">
                        <div class="w-100 bg-secondary shadow mt-3" id="ej_csf_chart2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="ej_csf_chart3"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="ej_csf_chart4"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="ej_csf_chart5"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.csf graph modal ej -->

<!-- csf graph modal lms -->
<div class="modal fade" id="csf_graph_lms_modal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-dark" role="alert">
            <a href="#" class="alert-link">5</a> - Very Satisfied
            <a href="#" class="alert-link ml-3">4</a> - Satisfied
            <a href="#" class="alert-link ml-3">3</a> - Neither Satisfied nor Dissatisfied
            <a href="#" class="alert-link ml-3">2</a> - Dissatisfied
            <a href="#" class="alert-link ml-3">1</a> - Very Dissatisfied
        </div>
        <ul class="nav nav-tabs" id="lms_nav_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class=" font-weight-bold nav-link active" id="lms_csf_list_tab" data-toggle="tab" href="#lms_csf_list" role="tab" aria-controls="lms_csf_list" aria-selected="true">CSF</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="font-weight-bold nav-link" id="lms_csf_graph_tab" data-toggle="tab" href="#lms_csf_graph" role="tab" aria-controls="lms_csf_graph" aria-selected="false">Graph</a>
            </li>
        </ul>
        <div class="tab-content p-1" id="lms_tab_content">
            <div class="tab-pane fade show active" id="lms_csf_list" role="tabpanel" aria-labelledby="lms_csf_list_tab">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="csf_lms_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <!-- <th>Age</th> -->
                                    <th>Sex</th>
                                    <th>Email</th>
                                    <th>Affiliation</th>
                                    <th>Country</th>
                                    <th>Date submitted</th>
                                    @foreach($csf_desc as $row)
                                    <th>{{ $row->svc_fdbk_q_desc }}</th>
                                    @endforeach
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="lms_csf_graph" role="tabpanel" >
                <div class="row">
                    <div class="col-8">
                        <div class="w-100 bg-secondary shadow mt-3" id="lms_csf_chart"></div>
                    </div>
                    <div class="col-4">
                        <div class="w-100 bg-secondary shadow mt-3" id="lms_csf_chart2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="lms_csf_chart3"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="lms_csf_chart4"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="lms_csf_chart5"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.csf graph modal lms -->

<!-- csf graph modal thds -->
<div class="modal fade" id="csf_graph_thds_modal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-dark" role="alert">
            <a href="#" class="alert-link">5</a> - Very Satisfied
            <a href="#" class="alert-link ml-3">4</a> - Satisfied
            <a href="#" class="alert-link ml-3">3</a> - Neither Satisfied nor Dissatisfied
            <a href="#" class="alert-link ml-3">2</a> - Dissatisfied
            <a href="#" class="alert-link ml-3">1</a> - Very Dissatisfied
        </div>
        <ul class="nav nav-tabs" id="thds_nav_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class=" font-weight-bold nav-link active" id="thds_csf_list_tab" data-toggle="tab" href="#thds_csf_list" role="tab" aria-controls="thds_csf_list" aria-selected="true">CSF</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="font-weight-bold nav-link" id="thds_csf_graph_tab" data-toggle="tab" href="#thds_csf_graph" role="tab" aria-controls="thds_csf_graph" aria-selected="false">Graph</a>
            </li>
        </ul>
        <div class="tab-content p-1" id="thds_tab_content">
            <div class="tab-pane fade show active" id="thds_csf_list" role="tabpanel" aria-labelledby="thds_csf_list_tab">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="csf_thds_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Sex</th>
                                    <th>Email</th>
                                    <th>Age</th>
                                    <th>Position</th>
                                    <th>Institution</th>
                                    <th>Region</th>
                                    <th>Division</th>
                                    <th>Date submitted</th>
                                    @foreach($csf_desc as $row)
                                    <th>{{ $row->svc_fdbk_q_desc }}</th>
                                    @endforeach
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="thds_csf_graph" role="tabpanel" >
                <div class="row">
                    <div class="col-8">
                        <div class="w-100 bg-secondary shadow mt-3" id="thds_csf_chart"></div>
                    </div>
                    <div class="col-4">
                        <div class="w-100 bg-secondary shadow mt-3" id="thds_csf_chart2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="thds_csf_chart3"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="thds_csf_chart4"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="thds_csf_chart5"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.csf graph modal thds -->

<!-- csf graph modal rdlip -->
<div class="modal fade" id="csf_graph_rdlip_modal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-dark" role="alert">
            <a href="#" class="alert-link">5</a> - Very Satisfied
            <a href="#" class="alert-link ml-3">4</a> - Satisfied
            <a href="#" class="alert-link ml-3">3</a> - Neither Satisfied nor Dissatisfied
            <a href="#" class="alert-link ml-3">2</a> - Dissatisfied
            <a href="#" class="alert-link ml-3">1</a> - Very Dissatisfied
        </div>
        <ul class="nav nav-tabs" id="rdlip_nav_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class=" font-weight-bold nav-link active" id="rdlip_csf_list_tab" data-toggle="tab" href="#rdlip_csf_list" role="tab" aria-controls="rdlip_csf_list" aria-selected="true">CSF</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="font-weight-bold nav-link" id="rdlip_csf_graph_tab" data-toggle="tab" href="#rdlip_csf_graph" role="tab" aria-controls="rdlip_csf_graph" aria-selected="false">Graph</a>
            </li>
        </ul>
        <div class="tab-content p-1" id="rdlip_tab_content">
            <div class="tab-pane fade show active" id="rdlip_csf_list" role="tabpanel" aria-labelledby="rdlip_csf_list_tab">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="csf_rdlip_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Sex</th>
                                    <th>Email</th>
                                    <th>Age</th>
                                    <th>Position</th>
                                    <th>Institution</th>
                                    <th>Region</th>
                                    <th>Division</th>
                                    <th>Date submitted</th>
                                    @foreach($csf_desc as $row)
                                    <th>{{ $row->svc_fdbk_q_desc }}</th>
                                    @endforeach
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="rdlip_csf_graph" role="tabpanel" >
                <div class="row">
                    <div class="col-8">
                        <div class="w-100 bg-secondary shadow mt-3" id="rdlip_csf_chart"></div>
                    </div>
                    <div class="col-4">
                        <div class="w-100 bg-secondary shadow mt-3" id="rdlip_csf_chart2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="rdlip_csf_chart3"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="rdlip_csf_chart4"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="rdlip_csf_chart5"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.csf graph modal rdlip -->

<!-- csf graph modal skms -->
<div class="modal fade" id="csf_graph_skms_modal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-dark" role="alert">
            <a href="#" class="alert-link">5</a> - Very Satisfied
            <a href="#" class="alert-link ml-3">4</a> - Satisfied
            <a href="#" class="alert-link ml-3">3</a> - Neither Satisfied nor Dissatisfied
            <a href="#" class="alert-link ml-3">2</a> - Dissatisfied
            <a href="#" class="alert-link ml-3">1</a> - Very Dissatisfied
        </div>
        <ul class="nav nav-tabs" id="overall_nav_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class=" font-weight-bold nav-link active" id="overall_csf_list_tab" data-toggle="tab" href="#overall_csf_list" role="tab" aria-controls="overall_csf_list" aria-selected="true">Overall CSF</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="font-weight-bold nav-link" id="overall_csf_graph_tab" data-toggle="tab" href="#overall_csf_graph" role="tab" aria-controls="overall_csf_graph" aria-selected="false">Graph</a>
            </li>
        </ul>
        <div class="tab-content p-1" id="overall_tab_content">
            <div class="tab-pane fade show active" id="overall_csf_list" role="tabpanel" aria-labelledby="overall_csf_list_tab">
                
                <ul class="nav nav-tabs" id="overall_nav_sub_tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class=" font-weight-bold nav-link active" id="ov_skms_csf_list_tab" data-toggle="tab" href="#ov_skms_csf_list" role="tab" aria-controls="ov_skms_csf_list" aria-selected="true">All Systems</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class=" font-weight-bold nav-link" id="ov_memis_csf_list_tab" data-toggle="tab" href="#ov_memis_csf_list" role="tab" aria-controls="ov_memis_csf_list" aria-selected="true">NRCP Membership Application 
                            <span class="badge badge-danger">{{ count($csf_memis)}}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class=" font-weight-bold nav-link" id="ov_bris_csf_list_tab" data-toggle="tab" href="#ov_bris_csf_list" role="tab" aria-controls="ov_bris_csf_list" aria-selected="true">Research Grant (Grant-in-Aid) 
                            <span class="badge badge-warning">{{ count($csf_bris)}}</span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class=" font-weight-bold nav-link" id="ov_ej_csf_list_tab" data-toggle="tab" href="#ov_ej_csf_list" role="tab" aria-controls="ov_ej_csf_list" aria-selected="true">Journal Service 
                            <span class="badge badge-primary">{{ count($csf_ej)}}</span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class=" font-weight-bold nav-link" id="ov_lms_csf_list_tab" data-toggle="tab" href="#ov_lms_csf_list" role="tab" aria-controls="ov_lms_csf_list" aria-selected="true">Library Service 
                            <span class="badge badge-success">{{ count($csf_lms)}}</span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class=" font-weight-bold nav-link" id="ov_thds_csf_list_tab" data-toggle="tab" href="#ov_thds_csf_list" role="tab" aria-controls="ov_thds_csf_list" aria-selected="true">Thesis/Dissertation Manuscript Grant 
                            <span class="badge badge-dark">{{ count($csf_thds)}}</span></a>
                    </li>
                </ul>
                
                <div class="tab-content p-1" id="overall_tab_sub_content">
                    <div class="tab-pane fade show active" id="ov_skms_csf_list" role="tabpanel" aria-labelledby="ov_skms_csf_list_tab">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="csf_skms_table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Sex</th>
                                            <th>Email</th>
                                            <th>Age</th>
                                            <th>Position</th>
                                            <th>Institution</th>
                                            <th>Region</th>
                                            <th>Division</th>
                                            <th>Date submitted</th>
                                            @foreach($csf_desc as $row)
                                            <th>{{ $row->svc_fdbk_q_desc }}</th>
                                            @endforeach
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ov_memis_csf_list" role="tabpanel" aria-labelledby="ov_memis_csf_list_tab">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="csf_skms_memis_table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Sex</th>
                                            <th>Email</th>
                                            <th>Age</th>
                                            <th>Position</th>
                                            <th>Institution</th>
                                            <th>Region</th>
                                            <th>Division</th>
                                            <th>Date submitted</th>
                                            @foreach($csf_desc as $row)
                                            <th>{{ $row->svc_fdbk_q_desc }}</th>
                                            @endforeach
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ov_bris_csf_list" role="tabpanel" aria-labelledby="ov_bris_csf_list_tab">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="csf_skms_bris_table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Sex</th>
                                            <th>Email</th>
                                            <th>Age</th>
                                            <th>Position</th>
                                            <th>Institution</th>
                                            <th>Region</th>
                                            <th>Division</th>
                                            <th>Date submitted</th>
                                            @foreach($csf_desc as $row)
                                            <th>{{ $row->svc_fdbk_q_desc }}</th>
                                            @endforeach
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ov_ej_csf_list" role="tabpanel" aria-labelledby="ov_ej_csf_list_tab">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="csf_skms_ej_table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Sex</th>
                                            <th>Email</th>
                                            <th>Age</th>
                                            <th>Position</th>
                                            <th>Institution</th>
                                            <th>Region</th>
                                            <th>Division</th>
                                            <th>Date submitted</th>
                                            @foreach($csf_desc as $row)
                                            <th>{{ $row->svc_fdbk_q_desc }}</th>
                                            @endforeach
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ov_lms_csf_list" role="tabpanel" aria-labelledby="ov_lms_csf_list_tab">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="csf_skms_lms_table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Sex</th>
                                            <th>Email</th>
                                            <th>Age</th>
                                            <th>Position</th>
                                            <th>Institution</th>
                                            <th>Region</th>
                                            <th>Division</th>
                                            <th>Date submitted</th>
                                            @foreach($csf_desc as $row)
                                            <th>{{ $row->svc_fdbk_q_desc }}</th>
                                            @endforeach
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ov_thds_csf_list" role="tabpanel" aria-labelledby="ov_thds_csf_list_tab">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="csf_skms_thds_table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Sex</th>
                                            <th>Email</th>
                                            <th>Age</th>
                                            <th>Position</th>
                                            <th>Institution</th>
                                            <th>Region</th>
                                            <th>Division</th>
                                            <th>Date submitted</th>
                                            @foreach($csf_desc as $row)
                                            <th>{{ $row->svc_fdbk_q_desc }}</th>
                                            @endforeach
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="overall_csf_graph" role="tabpanel" >
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="overall_csf_chart"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="overall_csf_chart2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="overall_csf_chart3"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="w-100 bg-secondary shadow mt-3" id="overall_csf_chart4"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="overall_adv_csf" role="tabpanel" aria-labelledby="overall_adv_csf_tab">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="adv_csf_table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Very Satisfied</th>
                                    <th>Satisified</th>
                                    <th>Neither Satisfied nor Dissatisfied</th>
                                    <th>Dissatisfied</th>
                                    <th>Very Dissatisfied</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        
                    </table>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.csf graph modal skms -->
@endsection

    