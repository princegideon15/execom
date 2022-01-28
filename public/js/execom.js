       
    var province = [];
    var region = [];
    var city = [];
    var _global_results = 0;
    var _global_memis = 0;
    var _global_bris = 0;
    var _global_ejournal = 0;
    var _global_lms = 0;
    var _global_nrcpnet = 0;
    var coordinator = [];
    var memis_graph_values = [];
    var region_array = [];
    var bar_main_title = '';
    var bar_sub_title = [];
    var category_title = '';
    var stacked_bar_y = [];
    var stacked_bar_exemption = '';
    var exeChart;
    var bar_labels, bar_total;
    var chart_rendered = 0;
    var column_total = 0;
    var chart_orientation = 1;
    var chart_numbers = 1;
    var drilldown_arr = [];

$(document).ready(function(){


    
    $('[data-toggle="tooltip"]').tooltip();

    get_coordinator();

    $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
        if (!$(this).next().hasClass('show')) {
          $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
        }
        var $subMenu = $(this).next(".dropdown-menu");
        $subMenu.toggleClass('show');
      
      
        $(this).parents('div.btn-group.dropright.show').on('hidden.bs.dropdown', function(e) {
          $('.dropdown-submenu .show').removeClass("show");
        });
        return false;
      });

    $('#search_button').click(function(){ 
        $('#search_result').empty();
        var sys = $('#search_filter').val();
        var keyword = $('#search_keyword').val();
        var searches = [];
        var search_arr = {};
        
        search_arr['keyword'] = keyword;
        searches.push(keyword);

            if(keyword == ''){
                $('#search_result').append('<div class="alert alert-danger mt-3"> \
                <span class="fa fa-exclamation-circle"></span> Please enter a keyword. \
                </div>');
            }else{
                $('#search_result').empty();
               
            }

            if(sys > 0){
                search_arr['sys'] = sys;
                searches.push($('#search_filter option[value="'+sys+'"]').text());
            }else if(keyword != '' && sys == 0){
                // $('#search_result').append('<div class="alert alert-danger mt-3"> \
                // <span class="fa fa-exclamation-circle"></span> Find All. \
                // </div>');
                show_overall(keyword);
                return false;
            }

            var filter = $('#sub_filter'+sys).val();
            search_arr['filter'] = filter;
            searches.push($('#sub_filter'+sys+' option[value="'+filter+'"]').text());

            var division = $('#memis_div_filter').val();
            search_arr['division'] =  division;
            searches.push($('#memis_div_filter  option[value="'+division+'"]').text());

            var year = $('#memis_year_filter').val();
            search_arr['year'] =  year;
            searches.push($('#memis_year_filter  option[value="'+year+'"]').text());

            var region = $('#reg_filter').val();
            search_arr['region'] =  region;
            searches.push($('#reg_filter  option[value="'+region+'"]').text());

            var province = $('#prov_filter').val();
            search_arr['province'] =  province;
            searches.push($('#prov_filter  option[value="'+province+'"]').text());

            var city = $('#city_filter').val();
            search_arr['city'] =  city;
            searches.push($('#city_filter  option[value="'+city+'"]').text());

            var brgy = $('#brgy_filter').val();
            search_arr['brgy'] =  brgy;
            searches.push(brgy);

            var nrcpnet_div = $('#nrcpnet_div_filter').val();
            search_arr['nrcpnet_div'] =  nrcpnet_div;
            searches.push($('#nrcpnet_div_filter  option[value="'+nrcpnet_div+'"]').text());
      

            var clean_searches = searches.filter(function(v){return v!==''});
            clean_searches = clean_searches.filter(function(v){return v!=='Select here'});
            clean_searches = clean_searches.filter(function(v){return v!=='Select Region'});
            clean_searches = clean_searches.filter(function(v){return v!=='Select Province'});
            clean_searches = clean_searches.filter(function(v){return v!=='Select Town/City'});
            clean_searches = clean_searches.filter(function(v){return v!==undefined});

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method: 'POST',
                url: APP_URL + '/search',
                async: false,
                data: { 'search' : search_arr, 'keyword' : clean_searches.join(" > ") },
                success: function (response) {
                var options = [];
                var options_result = [];
                var options_data = [];
                var tables = [];
                var options_head = [];
                var options_field = [];

                var count = 0;
                var i = 0;
                var html = '';
                var result;
                var area_head = '';
                var div_head = '<th>Division</th><th>Year</th><th>Citation</th>';
                var gb_head = '<th>GB Position</th><th>Year Covered</th><th>Remarks</th>';
                var default_head = '<th>Contact</th><th>Email</th><th>Specialization</th><th>Region</th><th>Province</th><th>City</th><th>Baranggay</th>';
                
                var default_field = ['pp_contact','pp_email','mpr_gen_specialization','region','province','city','brgy'];
                var div_field = ['div_number','awa_year','awa_citation'];
                var gb_field = ['div_number','ph_from', 'ph_to', 'ph_remarks'];

                if(sys == 1){ // memis
                    count = $.map(response, function(n, i) { return i; }).length;
                    
                    if(count == 5){
                        options.push('Specialization', 'Last Name, First Name', 'All Members', 'NRCP Achievement Awardee', 'Governing Board');
                        var a = 0;
                        
                        $.each(response, function(key, val){
                            if(key < 3){
                                options_head.push(default_head);
                                options_field.push(default_field);
                            }else if(key == 3){
                                options_head.push(div_head);
                                options_field.push(div_field);
                            }else{
                                options_head.push(gb_head);
                                options_field.push(gb_field);
                            }

                            options_result.push(response[a].length);
                            options_data.push(response[a]); 
                            a++;
                           
                        });

              
                        
                        var total = 0;
                        for (var x = 0; x < options_result.length; x++) {
                            total += options_result[x];
                        }
                       result = (total == 0) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : total ;
              
                    }
                    else if(filter == 1){
                        options.push($('#sub_filter'+sys+'  option[value="'+filter+'"]').text());
                        options_result.push(response.length);
                        options_data.push(response);
                        result = (response.length == 0 ) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                        options_head.push(default_head);
                    }else if(filter == 2){
                        options.push($('#sub_filter'+sys+'  option[value="'+filter+'"]').text());
                        options_result.push(response.length);
                        options_data.push(response);
                        result = (response.length == 0 ) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                        options_head.push(default_head);
                    }else if(filter == 3){
                        options.push($('#sub_filter'+sys+'  option[value="'+filter+'"]').text());
                        options_result.push(response.length);
                        options_data.push(response);
                        result = (response.length == 0 ) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                        options_head.push(default_head);
                    }else if(filter == 4){
                        options.push($('#sub_filter'+sys+'  option[value="'+filter+'"]').text());
                        options_result.push(response.length);
                        options_data.push(response);
                        result = (response.length == 0 ) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                        options_head.push(div_head);
                    }else if(filter == 5){
                        options.push($('#sub_filter'+sys+'  option[value="'+filter+'"]').text());
                        options_result.push(response.length);
                        options_data.push(response);
                        result = (response.length == 0 ) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                        options_head.push(gb_head);
                    }

                    $.each(options, function(key, val){
                        tables.push('collapse' + i + '_table');
                        var dis = (options_result[i] == 0) ? 'disabled' : 'text-dark';
                        html += '<div class="card"> \
                                <div class="card-header  p-0" id="headingThree"> \
                                    <h2 class="mb-0"> \
                                    <button class="'+ dis +' btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapse' + i + '"> \
                                    <strong><u>'+ options[i]+ ' ('+ options_result[i] +')</u></strong> | <span class="font-italic">Click to view more details</span>\
                                    </button> \
                                    </h2> \
                                </div> \
                                <div id="collapse' + i + '" class="collapse" data-parent="#result_accordion"> \
                                    <div class="card-body table-responsive"> \
                                        <table class="table table-striped w-100" id="collapse' + i + '_table"> \
                                            <thead> \
                                                <tr><th>#</th> \
                                                    <th>Title</th> \
                                                    <th>Last Name</th> \
                                                    <th>First Name</th> \
                                                    <th>Middle Name</th> \
                                                    <th>Sex</th> \
                                                    '+ options_head[i] +' \
                                                </tr> \
                                            </thead> \
                                            <tbody>';
                                                $.each(options_data[i], function(key, val){

                                                    var title = (val.TITLE == null) ? '-' : val.TITLE;
                                                    var region = (val.REGION == null) ? '-' : val.REGION;
                                                    var province = (val.PROVINCE== null) ? '-' : val.PROVINCE;
                                                    var city = (val.CITY == null) ? '-' : val.CITY;
                                                    var brgy = (val.adr_brgy == null) ? '-' : val.adr_brgy;
                                                    var present  = (val.ph_to == 'Present') ? 'Present' : moment(val.ph_to).format("MMM DD, YYYY");

                                                    html += '<tr><td></td><td>'+ title +'</td> \
                                                            <td>'+ val.pp_last_name +'</td> \
                                                            <td>'+ val.pp_first_name +'</td> \
                                                            <td>'+ val.pp_middle_name +'</td> \
                                                            <td>'+ val.sex +'</td>';
                                                            if(count == 5){
                                                                // no selected sub options
                                                                if(i < 3){
                                                                    html += '<td>'+ val.pp_contact +'</td> \
                                                                    <td>'+ val.pp_email +'</td> \
                                                                    <td>'+ val.mpr_gen_specialization +'</td> \
                                                                    <td>'+ region +'</td> \
                                                                    <td>'+ province +'</td> \
                                                                    <td>'+ city +'</td> \
                                                                    <td>'+ brgy +'</td>';
                                                                }else if(i == 3){
                                                                    html +=     '<td> Division' + val.div_number +'</td> \
                                                                    <td>'+ val.awa_year +'</td> \
                                                                    <td>'+ val.awa_citation +'</td>';
                                                                }
                                                                else{
                                                                    html += '<td> Division '+ val.div_number +'</td> \
                                                                            <td>'+ moment(val.ph_from).format("MMM DD, YYYY") + ' - ' + present +'</td> \
                                                                            <td>'+ val.ph_remarks +'</td>';
                                                                }
                                                            }else{
                                                                // with selected sub options
                                                                if(filter < 4){
                                                                    html += '<td>'+ val.pp_contact +'</td> \
                                                                            <td>'+ val.pp_email +'</td> \
                                                                            <td>'+ val.mpr_gen_specialization +'</td> \
                                                                            <td>'+ region +'</td> \
                                                                            <td>'+ province +'</td> \
                                                                            <td>'+ city +'</td> \
                                                                            <td>'+ brgy +'</td>';
                                                                }else if(filter == 4){
                                                                    html +=     '<td> Division' + val.div_number +'</td> \
                                                                                <td>'+ val.awa_year +'</td> \
                                                                                <td>'+ val.awa_citation +'</td>';
                                                                }else if(filter == 5){
                                                                    html += '<td> Division '+ val.div_number +'</td> \
                                                                            <td>'+ moment(val.ph_from).format("MMM DD, YYYY") + ' - ' + present +'</td> \
                                                                            <td>'+ val.ph_remarks +'</td>';
                                                                }
                                                            }

                                                    html += '</tr>';
                                                });
                        html +=                '</tbody> \
                                        </table> \
                                    </div> \
                                </div> \
                            </div>';
                        i++;
                    });
                }
                else if(sys == 2){ // bris
                    if(response[0] == 'all'){
                        var project = response[1].length;
                        var program = response[2].length; 
                        var bris = [];
                        result = (project == 0 && program == 0) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                        options.push('Project', 'Program');
                        bris.push('Project Leader', 'Program Manager');
                        options_result.push(project, program);
                        options_data.push(response[1], response[2]);
                        // var result = (title == 0 && author == 0) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : 'Found results : <br/>\
                        // // <a href="javascript:void(0);" onclick="ejournal_result('+JSON.stringify(search_arr)+');" class="alert-link">Title (' + title + ')</a> <br/> \
                        // // <a href="javascript:void(0);" onclick="ejournal_result('+JSON.stringify(search_arr)+');" class="alert-link">Author ('+ author + ')</a>';
                    }else{
                        options.push($('#sub_filter'+sys+'  option[value="'+filter+'"]').text());
                        options_result.push(response.length);
                        options_data.push(response);
                        result = (response.length == 0 ) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                        // var result = (response.length > 0) ?  '<a href="javascript:void(0);" data-toggle="modal" data-target="#result_modal" class="alert-link">Found ('+response.length+') result.</a>' : '<span class="fa fa-exclamation-circle"></span> No result/s found..';
                        // var x = JSON.stringify(response);
                    }

                    $.each(options, function(key, val){
                        var proposal;
                        var proposal_label;

                        var show = (i == 0)  ? 'show' : '';
                        tables.push('collapse' + i + '_table');
                        var dis = (options_result[i] == 0) ? 'disabled' : 'text-dark';
                        html += '<div class="card"> \
                                <div class="card-header  p-0" id="headingThree"> \
                                    <h2 class="mb-0"> \
                                    <button class="' + dis + ' btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapse' + i + '"> \
                                    <strong><u>' + options[i] + ' (' + options_result[i] + ') </u></strong> | <span class="font-italic">Click to view more details</span>\
                                    </button> \
                                    </h2> \
                                </div> \
                                <div id="collapse' + i + '" class="collapse" data-parent="#result_accordion"> \
                                    <div class="card-body table-responsive"> \
                                        <table class="table table-striped w-100" id="collapse' + i + '_table"> \
                                            <thead> \
                                                <tr><th></th> \
                                                    <th>Title</th> \
                                                    <th>' + bris[i] +'</th> \
                                                    <th>Status</th> \
                                                    <th>Date submitted</th> \
                                                </tr> \
                                            </thead> \
                                            <tbody>';
                                                $.each(options_data[i], function(key, val){

                                                var proponent = (val.proponent == null || val.proponent == 0) ? '-' : val.proponent;
                                                var status = (val.status == null) ? '-' : val.status;


                                                proposal = (val.prp == 1) ? 'text-muted' : '';
                                                proposal_label = (val.prp == 1) ? '<small><span class="badge badge-secondary">PROPOSAL</span></small>' : '';

                                                html += '<tr  class="' + proposal + '"> \
                                                <td></td> \
                                                <td>' + val.title + ' ' + proposal_label +'</td> \
                                                <td>' + proponent + '</td> \
                                                <td>' + status+'</td> \
                                                <td>' + moment(val.date_created).format("MMM DD, YYYY") +'</td> \
                                                </tr>';
                                                   
                                                });
                        html +=                '</tbody> \
                                        </table> \
                                    </div> \
                                </div> \
                            </div>';
                        i++;

                  
                    });

                }
                else if(sys == 3){ // ejoural
                    if(response[0] == 'all'){
                        var title = response[1].length;
                        var author = response[2].length; 
                        result = (title == 0 && author == 0) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                        options.push('Title', 'Author');
                        options_result.push(title, author);
                        options_data.push(response[1], response[2]);
                        // var result = (title == 0 && author == 0) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : 'Found results : <br/>\
                        // // <a href="javascript:void(0);" onclick="ejournal_result('+JSON.stringify(search_arr)+');" class="alert-link">Title (' + title + ')</a> <br/> \
                        // // <a href="javascript:void(0);" onclick="ejournal_result('+JSON.stringify(search_arr)+');" class="alert-link">Author ('+ author + ')</a>';
                    }else{
                        options.push($('#sub_filter'+sys+'  option[value="'+filter+'"]').text());
                        options_result.push(response.length);
                        options_data.push(response);
                        result = (response.length == 0 ) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                        // var result = (response.length > 0) ?  '<a href="javascript:void(0);" data-toggle="modal" data-target="#result_modal" class="alert-link">Found ('+response.length+') result.</a>' : '<span class="fa fa-exclamation-circle"></span> No result/s found..';
                        // var x = JSON.stringify(response);
                    }

                    $.each(options, function(key, val){
                        var show = (i == 0)  ? 'show' : '';
                        tables.push('collapse' + i + '_table');
                        var dis = (options_result[i] == 0) ? 'disabled' : 'text-dark';
                        html += '<div class="card"> \
                                <div class="card-header  p-0" id="headingThree"> \
                                    <h2 class="mb-0"> \
                                    <button class="' + dis + ' btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapse' + i + '"> \
                                    <strong><u>' + options[i] + ' (' + options_result[i] + ') </u></strong> | <span class="font-italic">Click to view more details</span>\
                                    </button> \
                                    </h2> \
                                </div> \
                                <div id="collapse' + i + '" class="collapse" data-parent="#result_accordion"> \
                                    <div class="card-body table-responsive"> \
                                        <table class="table table-striped w-100" id="collapse' + i + '_table"> \
                                            <thead> \
                                                <tr><th></th> \
                                                    <th>Title</th> \
                                                    <th>Author</th> \
                                                    <th>Date submitted</th> \
                                                </tr> \
                                            </thead> \
                                            <tbody>';
                                                $.each(options_data[i], function(key, val){
                                                    var author = (val.art_author == '') ? 'NA' : val.art_author;
                                                    html += '<tr> \
                                                             <td></td> \
                                                             <td>' + val.art_title + '</td> \
                                                             <td>' + author + '</td> \
                                                             <td>' + moment(val.date_created).format("MMM DD, YYYY"); + '</td> \
                                                             </tr>';
                                                });
                        html +=                '</tbody> \
                                        </table> \
                                    </div> \
                                </div> \
                            </div>';
                        i++;

                  
                    });

                }else if(sys == 4){ // lms
                    $.ajax({
                        method: 'POST',
                        url: APP_URL + '/search',
                        async: false,
                        data: { 'search' : search_arr },
                        success: function (response) {
                            if(response[0] == 'all'){
                                var  total = 0;
                                $.each(response , function(key, val){
                                    
                                    if(key > 0 && key != 13){
                                        options_result.push(response[key].length);
                                        options_data.push(response[key]);
                                        total += response[key].length;
                                    }  
                                    
                                });

                                $.each(response[13],function(key, val){
                                    options.push(val);    
                                });
                                
                                result = (total == 0) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : total;

                                

                            }else{
                                options.push($('#sub_filter'+sys+'  option[value="'+filter+'"]').text());
                                options_result.push(response.length);
                                options_data.push(response);
                                result = (response.length == 0 ) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                            }

                            $.each(options, function(key, val){
                                
                                tables.push('collapse' + i + '_table');     
                                var show = (i == 0)  ? 'show' : '';
                                var dis = (options_result[i] == 0) ? 'disabled' : 'text-dark';
                                
                                html += '<div class="card"> \
                                        <div class="card-header  p-0" id="headingThree"> \
                                            <h2 class="mb-0"> \
                                            <button class="' + dis + ' btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapse' + i + '"> \
                                            <strong><u>'+ options[i]+ ' ('+ options_result[i] +') </u></strong> | <span class="font-italic">Click to view more details</span>\
                                            </button> \
                                            </h2> \
                                        </div> \
                                        <div id="collapse' + i + '" class="collapse" data-parent="#result_accordion"> \
                                            <div class="card-body table-responsive"> \
                                                <table class="table table-striped w-100 " id="collapse' + i + '_table"> \
                                                    <thead> \
                                                        <tr><th></th> \
                                                            <th>Title</th> \
                                                            <th>Author</th> \
                                                            <th>Keywords</th> \
                                                            <th>Date Submitted</th> \
                                                        </tr> \
                                                    </thead> \
                                                    <tbody>';
                                                        $.each(options_data[i], function(key, val){
                                                            
                                                            var href = APP_URL + '/lms/view_pdf/' + val.art_id;
                                                            var author = (val.art_author == '') ? 'NA' : val.art_author;
                                                            var view = (val.art_full_text !== '') ? '<a href="' + href + '" target="_blank" class="btn btn-outline-secondary">View</a>' : 'Unavailable';
                                                            html += '<tr><td></td> \
                                                                        <td>' + val.art_title + '</td> \
                                                                        <td>' + author + '</td> \
                                                                        <td>' + val.art_keywords + '</td> \
                                                                        <td>' + moment(val.created_on).format("MMM DD, YYYY") + '</td> \
                                                                        <td>' + view + '</td> \
                                                                        </tr>';
                                                        });
                                html +=                '</tbody> \
                                                </table> \
                                            </div> \
                                        </div> \
                                    </div>';
                                i++;
                            });
                        }
                    });
                }else{ // nrcpnet

                    if(response[0] == 'all'){
                        // console.log(response);
                        var emp = response[1].length;
                        var div = response[2].length; 
                        result = (emp == 0 && div == 0) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                        options.push('Employee Name', 'Divison');
                        options_result.push(emp, div);
                        options_data.push(response[1], response[2]); 
                        
                    }
                    else if(filter == 1){
                        options.push($('#sub_filter'+sys+'  option[value="'+filter+'"]').text());
                        options_result.push(response.length);
                        options_data.push(response);
                        result = (response.length == 0 ) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                    }else if(filter == 2){
                        options.push($('#sub_filter'+sys+'  option[value="'+filter+'"]').text());
                        options_result.push(response.length);
                        options_data.push(response);
                        result = (response.length == 0 ) ? '<span class="fa fa-exclamation-circle"></span> No result/s found.' : response.length ;
                    }

                    $.each(options, function(key, val){
                        var show = (i == 0)  ? 'show' : '';
                        tables.push('collapse' + i + '_table');
                        var dis = (options_result[i] == 0) ? 'disabled' : 'text-dark';
                        html += '<div class="card"> \
                                <div class="card-header  p-0" id="headingThree"> \
                                    <h2 class="mb-0"> \
                                    <button class="'+ dis +' btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapse' + i + '"> \
                                    <strong><u>'+ options[i]+ ' ('+ options_result[i] +') </u></strong> | <span class="font-italic">Click to view more details</span>\
                                    </button> \
                                    </h2> \
                                </div> \
                                <div id="collapse' + i + '" class="collapse" data-parent="#result_accordion"> \
                                    <div class="card-body table-responsive"> \
                                        <table class="table table-striped w-100" id="collapse' + i + '_table"> \
                                            <thead> \
                                                <tr><th></th> \
                                                    <th>Last Name</th> \
                                                    <th>First Name</th> \
                                                    <th>Middle Name</th> \
                                                    <th>Appointment</th> \
                                                    <th>Division</th> \
                                                </tr> \
                                            </thead> \
                                            <tbody>';
                                                $.each(options_data[i], function(key, val){
                                                    html += '<tr><td></td> \
                                                            <td>' + val.plant_surname+ '</td> \
                                                            <td>' + val.plant_firstname + '</td> \
                                                            <td>' + val.plant_middlename + '</td> \
                                                            <td>' + val.plant_appointment + '</td> \
                                                            <td>' + val.plant_group + '</td> \
                                                            </tr>';
                                                });
                        html +=                '</tbody> \
                                        </table> \
                                    </div> \
                                </div> \
                            </div>';
                        i++;

                  
                    });

                }

                $('#result_accordion').empty(); 
                $('.searches').text(clean_searches.join(" > "));

                if(result > 0){
                    $('#search_result').empty();
                    $('#result_modal').modal('toggle');
                }else{
                    
                    $('#search_result').append('<div class="alert alert-danger mt-3"> \
                    '+result+' \
                    </div>');
                }

                
                $('#result_accordion').append(html);

                $.each(tables, function(key, val){
                    // $('#'+val).DataTable();
                    if ($.fn.DataTable.isDataTable('#'+val)) {
                        $('#'+val).DataTable().clear().destroy();
                    }

                    var t = $('#'+val).DataTable({
                        
                        mark: true,
                        "columnDefs": [ {
                            "searchable": false,
                            "orderable": false,
                            "targets": 0
                        } ],
                        "order": [[ 1, 'asc' ]]
                    } );

                    t.search(keyword).draw();
                 
                    t.on( 'order.dt search.dt', function () {
                        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                            cell.innerHTML = i+1;
                        });
                    } ).draw();

                 
                });
            } 
            });
    });

    $('#clear_filter').click(function(){
        $('#search_filter').val(0).change();
        $('#search_keyword').val('');
        $('#search_result').empty();
    });

    $('#search_filter').change(function()
    {
        var filter = $(this).val();
        var select = 'Sub-Options<select class="form-control" id="sub_filter'+filter+'"><option value="0">Select here</option>';

        if(filter == 1){
            select += '<option value="1">Specialization</option>'
            select += '<option value="2">LN, FN</option>';
            select += '<option value="3">All Members</option>';
            select += '<option value="4">NRCP Achievement Awardee</option>';
            select += '<option value="5">Governing Board</option>';
        }else if(filter == 2){
            select += '<option value="1">Projects</option>'
            select += '<option value="2">Programs</option>';
        }else if(filter == 3){
            select += '<option value="1">Title</option>'
            select += '<option value="2">Author</option>';
        }else if(filter == 4){
            select += '<option value="9">Annual Report</option>'
            select += '<option value="7">Board Resolution</option>';
            select += '<option value="4">Book</option>';
            select += '<option value="3">Dissertion</option>';
            select += '<option value="6">Minutes of Meeting</option>';
            select += '<option value="10">Monographs</option>'
            select += '<option value="11">Policy Briefs</option>';
            select += '<option value="12">Press Release</option>';
            select += '<option value="5>Proceeding</option>';
            select += '<option value="8">S&T Clippings</option>';
            select += '<option value="1">Terminal Report</option>';
            select += '<option value="2">Thesis</option>';
        }else if(filter == 5){
            select += '<option value="1">Employee Name</option>'
            select += '<option value="2">Division</option>';
        }else{
            $('#sub_option').empty();
            $('#sub_option2').empty();
            $('#search_result').empty();
            return false;
        }

            
        select += '</select>';
        $('#sub_option').empty(); 
        $('#sub_option2').empty(); 
        $('#search_result').empty();
        $('#sub_option').append(select);

        if($('#sub_filter1').length){//, #sub_filter2
      
            $('#sub_filter1').on('change', function(){//, #sub_filter2
                
                var filter2 = $(this).val();
                var sys = $('#search_filter').val();


                var select = 'Sub-Sub-Options';
               
                if(filter2 == 1 || filter2 == 3){// || (sys == 2 && filter2 == 2)

                    select += '<select class="form-control" id="reg_filter"><option value="0">Select Region</option>';
                    $.ajax({
                        method: 'GET',
                        url: APP_URL + '/search/reg',
                        async: false,
                        success: function (response) {
                        $.each(response, function(key, val){
                            select += '<option value="' + val.region_id + '">' + val.region_name + '</option>';
                        });
                        }
                    });
                    select += '</select>';

                    select += '<select class="form-control mt-3" id="prov_filter"><option value="0">Select Province</option></select>';
                    select += '<select class="form-control mt-3" id="city_filter"><option value="0">Select Town/City</option></select>';
                    select += '<input type="text" class="form-control mt-3" id="brgy_filter" placeholder="Type Baranggay">';
                }else if(filter2 == 4 || filter2 == 5){
                    select += '<select class="form-control" id="memis_div_filter"><option value="0">Select Division</option>';
                    $.ajax({
                        method: 'GET',
                        url: APP_URL + '/search/divs',
                        async: false,
                        success: function (response) {
                            // console.log(response);
                        $.each(response, function(key, val){
                            select += '<option value="' + val.div_id + '"> Division ' + val.div_number + '</option>';
                        });
                        }
                    });
                 
                    
                    select += '</select>';

                    var start_year = new Date().getFullYear();
                    select += '<select class="form-control mt-3" id="memis_year_filter"><option value="0">Select Year</option>';
                    for (var i = start_year; i > start_year - 88; i--) {
                        select += '<option value="' + i + '">' + i + '</option>';
                      }
                    select += '</select>'; 
                    $('#sub_option2').empty();
                    $('#search_result').empty();
                    $('#sub_option2').append(select);

                

                }else{
                    $('#sub_option2').empty();
                    $('#search_result').empty();
                    return false;
                }

                
                // select += '</select>'; 
                $('#sub_option2').empty();
                $('#search_result').empty();
                $('#sub_option2').append(select);
        
                
            });
        }else if($('#sub_filter5').length){
            $('#sub_filter5').on('change', function(){
                var filter2 = $(this).val();
                var select = '';

                if(filter2 == 2){
                    select += 'Sub-Sub-Options<select class="form-control" id="nrcpnet_div_filter"><option value="0">Select here</option>';
                    $.ajax({
                        method: 'GET',
                        url: APP_URL + '/nrcpnet/divs',
                        async: false,
                        success: function (response) {
                        $.each(response, function(key, val){
                            select += '<option value="' + val.plantillaGroupCode + '">' + val.plantillaGroupName + '</option>';
                        });
                        }
                    });
                    select += '</select>';
                }

                $('#sub_option2').empty();
                $('#search_result').empty();
                $('#sub_option2').append(select);

            });

        }

    });

    $(document).on('change', "#reg_filter", function(e){
        var val = $(this).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            method: 'POST',
            url: APP_URL + '/search/prov',
            async: false,
            data: {'id':val},
            success: function (response) {
                $('#prov_filter').empty();
                $('#city_filter').empty();
                $('#prov_filter').append('<option value="0">Select Province</option>');
                $('#city_filter').append('<option value="0">Select Town/City</option>');
                $.each(response, function(key, val){
                    $('#prov_filter').append('<option value="' + val.province_id + '">' + val.province_name + '</option>');
                });
            }
        });

        e.preventDefault();
    });

    $(document).on('change', "#prov_filter", function(){
        var val = $(this).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            method: 'POST',
            url: APP_URL + '/search/city',
            async: false,
            data: {'id':val},
            success: function (response) {
                $('#city_filter').empty();
                $('#city_filter').append('<option value="0">Select Town/City</option>');
                $.each(response, function(key, val){
                    $('#city_filter').append('<option value="' + val.city_id + '">' + val.city_name + '</option>');
                });
            }
        });
    });
    

    moment().format();

    if($('#bris_bar_chart').length){

        var bris_bar_chart , bris_pie_chart;   
        //initialize BRIS
        var bris_labels = [];
        var bris_total = [];
        var bris_bgcolors = [];
        var bris_title;

        $.ajax({
            method: 'GET',
            url: APP_URL + '/bris/rt',
            async: false,
            success: function (response) {
            $.each(response, function(key, val){
                bris_total.push(val.total);
                bris_labels.push(val.label);
                bris_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
            });
            }
        });

        bris_title = 'Project by classification';
        var bar = document.getElementById('bris_bar_chart').getContext('2d');
        
        bris_bar_chart = new Chart(bar, {
            type: 'horizontalBar',
            data: {
                labels: bris_labels,
                datasets: [{
                    label: bris_title,
                    data: bris_total,
                    backgroundColor: bris_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                title : {
                    display: true,
                    text: bris_title,
                    fontSize: 14,
                },
                legend: {
                    display: false, 
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var pie = document.getElementById('bris_pie_chart').getContext('2d');
        bris_pie_chart = new Chart(pie, {
            type: 'pie',
            data: {
                labels: bris_labels,
                datasets: [{
                    label: bris_title,
                    data: bris_total,
                    backgroundColor: bris_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title : {
                    display: true,
                    // text: 'Articles per journal',
                    fontSize: 14,
                },
                legend: {
                    display: false,
                    position: 'top',
                }
            }
        });
    }

    // /.initialize BRIS

    $('#bris_filter').on('change', function(){
        var filter = $(this).val();
        var _url;

        bris_labels = [];
        bris_total = [];
        bris_bgcolors = [];
        bris_title;

        if(filter == 1){ _url = '/bris/rt'; bris_title = 'Research Type'; }
        else if(filter == 2){ _url = '/bris/ps'; bris_title = 'Project Status'; }
        else if(filter == 3){ _url = '/bris/hnrda'; bris_title = 'Harmonized National R&D Agenda'; }
        else if(filter == 4){ _url = '/bris/prexc'; bris_title = 'Program Expenditure Classification'; }
        else if(filter == 5){ _url = '/bris/pag'; bris_title = 'Priority Areas of the Government'; }
        else if(filter == 6){ _url = '/bris/dost'; bris_title = 'DOST 11-Point Agenda'; }
        else if(filter == 7){ _url = '/bris/strat'; bris_title = 'Outcomes in the DOST Strategic Plan 2017-2022'; }
        else if(filter == 8){ _url = '/bris/pdp'; bris_title = 'Philippine Development Chapters'; }
        else if(filter == 9){ _url = '/bris/nibra'; bris_title = 'NIBRA Priority Areas'; }
        else if(filter == 10){ _url = '/bris/nsub'; bris_title = 'NIBRA Sub-categories'; }
        else if(filter == 11){ _url = '/bris/nsea'; bris_title = 'National Socio-Economic Agenda'; }
        else if(filter == 12){ _url = '/bris/snt'; bris_title = 'Classification by S&T Activity'; }
        else{ _url = '/bris/sdg'; bris_title = 'Sustainable Development Goals'; }
        // alert(_url + ' ' + bris_title);
        // $.ajaxSetup({
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     }
        // });

        $.ajax({
            method: 'GET',
            url: APP_URL +  _url,
            async: false,
            success: function (response) {
            $.each(response, function(key, val){
                bris_total.push(val.total);
                bris_labels.push(val.label);
                bris_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
            });
            }
        });

        var bar = document.getElementById('bris_bar_chart').getContext('2d');
        bris_bar_chart.destroy();
        bris_bar_chart = new Chart(bar, {
            type: 'horizontalBar',
            data: {
                labels: bris_labels,
                datasets: [{
                    label: bris_title,
                    data: bris_total,
                    backgroundColor: bris_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                title : {
                    display: true,
                    text: bris_title,
                    fontSize: 14,
                },
                legend: {
                    display: false, 
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var pie = document.getElementById('bris_pie_chart').getContext('2d');
        bris_pie_chart.destroy();
        bris_pie_chart = new Chart(pie, {
            type: 'pie',
            data: {
                labels: bris_labels,
                datasets: [{
                    label: bris_title,
                    data: bris_total,
                    backgroundColor: bris_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title : {
                    display: true,
                    // text: 'Articles per journal',
                    fontSize: 14,
                },
                legend: {
                    display: false,
                    position: 'top',
                }
            }
        });

    });
    
    if($('#lms_bar_chart').length){
    
        var lms_bar_chart , lms_pie_chart;   
        //initialize LMS
        var lms_labels = [];
        var lms_articles = [];
        var lms_bgcolors = [];
        var lms_title;
        $.ajax({
            method: 'GET',
            url: APP_URL + '/lms/abc',
            async: false,
            success: function (response) {
            $.each(response, function(key, val){
                lms_articles.push(val.total);
                lms_labels.push(val.category);
                lms_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
            });
            }
        });
        lms_title = 'Articles by Category';
        var bar = document.getElementById('lms_bar_chart').getContext('2d');
        
        lms_bar_chart = new Chart(bar, {
            type: 'horizontalBar',
            data: {
                labels: lms_labels,
                datasets: [{
                    label: lms_title,
                    data: lms_articles,
                    backgroundColor: lms_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                title : {
                    display: true,
                    text: lms_title,
                    fontSize: 14,
                },
                legend: {
                    display: false, 
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var pie = document.getElementById('lms_pie_chart').getContext('2d');
        lms_pie_chart = new Chart(pie, {
            type: 'pie',
            data: {
                labels: lms_labels,
                datasets: [{
                    label: lms_title,
                    data: lms_articles,
                    backgroundColor: lms_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title : {
                    display: true,
                    // text: 'Articles per journal',
                    fontSize: 14,
                },
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        });
    }
    // /.initialize LMS
  
    $('#lms_filter_by').on('change', function()
    {
        lms_labels = [];
        lms_articles = [];
        lms_bgcolors = [];
        lms_title;

        var filter = $(this).val();
        if(filter == 1){
        
            $.ajax({
                method: 'GET',
                url: APP_URL + '/lms/abc',
                async: false,
                success: function (response) {
                $.each(response, function(key, val){
                    lms_articles.push(val.total);
                    lms_labels.push(val.category);
                    lms_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
                });
                }
            });

            lms_title = 'Articles by Category';

        }else if(filter == 2){
            $.ajax({
                method: 'GET',
                url: APP_URL + '/lms/abv',
                async: false,
                success: function (response) {
                $.each(response, function(key, val){
                    lms_articles.push(val.total);
                    lms_labels.push(val.category);
                    lms_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
                });
                }
            });

            lms_title = 'Articles by Views';

        }else{
            $.ajax({
                method: 'GET',
                url: APP_URL + '/lms/abd',
                async: false,
                success: function (response) {
                $.each(response, function(key, val){
                    lms_articles.push(val.total);
                    lms_labels.push(val.category);
                    lms_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
                });
                }
            });

            lms_title = 'Articles by Downloads';
        }

        var bar = document.getElementById('lms_bar_chart').getContext('2d');
        lms_bar_chart.destroy();
        lms_bar_chart = new Chart(bar, {
            type: 'horizontalBar',
            data: {
                labels: lms_labels,
                datasets: [{
                    label: lms_title,
                    data: lms_articles,
                    backgroundColor: lms_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                title : {
                    display: true,
                    text: lms_title,
                    fontSize: 14,
                },
                legend: {
                    display: false, 
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    
        var pie = document.getElementById('lms_pie_chart').getContext('2d');
        lms_pie_chart.destroy();
        lms_pie_chart = new Chart(pie, {
            type: 'pie',
            data: {
                labels: lms_labels,
                datasets: [{
                    label: lms_title,
                    data: lms_articles,
                    backgroundColor: lms_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title : {
                    display: true,
                    // text: 'Articles per journal',
                    fontSize: 14,
                },
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        });  
    });

    if($('#ej_bar_chart').length){
        var ej_bar_chart , ej_pie_chart;   
        //initialize EJOURNAL
        var ej_labels = [];
        var ej_articles = [];
        var ej_bgcolors = [];
        var ej_title;
        $.ajax({
            method: 'GET',
            url: APP_URL + '/ej/jby',
            async: false,
            success: function (response) {
            $.each(response, function(key, val){
                ej_articles.push(val.total);
                ej_labels.push(val.label);
                ej_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
                $('#journal_year_table').append('<tr><td>'+val.label+'</td><td>'+val.total+'</td></tr>');
             
            });

            }
        });
        ej_title = 'Journals by Year';
        var bar = document.getElementById('ej_bar_chart').getContext('2d');
        ej_bar_chart = new Chart(bar, {
            type: 'horizontalBar',
            data: {
                labels: ej_labels,
                datasets: [{
                    label: ej_title,
                    data: ej_articles,
                    backgroundColor: ej_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                title : {
                    display: true,
                    text: ej_title,
                    fontSize: 14,
                },
                legend: {
                    display: false, 
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                onClick: function(c, i){
                    e = i[0];
                    var jor_year = this.data.labels[e._index];
                    // var y_value = this.data.datasets[0].data[e._index];
                    show_table_ejournal(jor_year);
                },
            }
        });

        var pie = document.getElementById('ej_pie_chart').getContext('2d');
        ej_pie_chart = new Chart(pie, {
            type: 'pie',
            data: {
                labels: ej_labels,
                datasets: [{
                    label: ej_title,
                    data: ej_articles,
                    backgroundColor: ej_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title : {
                    display: true,
                    // text: 'Articles per journal',
                    fontSize: 14,
                },
                legend: {
                    display: true,
                    position: 'top',
                },
                events : ['click'],
                onClick: function(c, i){
                    e = i[0];
                    var jor_year = this.data.labels[e._index];
                    // var y_value = this.data.datasets[0].data[e._index];
                    show_table_ejournal(jor_year);
                }
            }
        }); 
    } 
    // /.initialize EJOURNAL

    $('#ej_filter_by_year').on('change', function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        ej_labels = [];
        ej_articles = [];
        ej_bgcolors = [];

        $.ajax({
            method: 'POST',
            url: APP_URL + '/ej/vby',
            data: { year : $(this).val() },
            async: false,
            success: function (response) {
            $.each(response, function(key, val){
                ej_articles.push(val.total);
                ej_labels.push(moment(val.label, 'MM').format('MMM'));
                ej_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
            });
            }
        });

    
        ej_title = 'Visitors as of Year ' + $(this).val();

        var bar = document.getElementById('ej_bar_chart').getContext('2d');
        ej_bar_chart.destroy();
        ej_bar_chart = new Chart(bar, {
            type: 'horizontalBar',
            data: {
                labels: ej_labels,
                datasets: [{
                    label: ej_title,
                    data: ej_articles,
                    backgroundColor: ej_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                title : {
                    display: true,
                    text: ej_title,
                    fontSize: 14,
                },
                legend: {
                    display: false, 
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        var pie = document.getElementById('ej_pie_chart').getContext('2d');
        ej_pie_chart.destroy();
        ej_pie_chart = new Chart(pie, {
            type: 'pie',
            data: {
                labels: ej_labels,
                datasets: [{
                    label: ej_title,
                    data: ej_articles,
                    backgroundColor: ej_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title : {
                    display: true,
                    // text: 'Articles per journal',
                    fontSize: 14,
                },
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        }); 

    });

    $('#ej_filter_by').on('change', function()
    {

        $('#ej_filter_by_year').attr('disabled', true);
        $('#ej_filter_by_year').val($('#ej_filter_by_year option:eq(0)').val());
        
        ej_labels = [];
        ej_articles = [];
        ej_bgcolors = [];
        ej_title;

        var filter = $(this).val();
        var _url;

        if(filter == 1){ _url = '/ej/jby'; ej_title = 'Journals by Year'; }
        else if(filter == 2){ _url = '/ej/abj'; ej_title = 'Articles by Journal'; }
        else if(filter == 3){ _url = '/ej/pdf'; ej_title = 'Articles by Downloads'; }
        else if(filter == 4){ _url = '/ej/abs'; ej_title = 'Articles by Views'; }
        else if(filter == 5){ _url = '/ej/cbj'; ej_title = 'Articles by Citations'; }
        else{
            $('#ej_filter_by_year').removeAttr('disabled');
            // $('#ej_filter_by_year').val($('#ej_filter_by_year option:eq(1)').val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: 'POST',
                url: APP_URL + '/ej/vby',
                async: false,
                success: function (response) {
                $.each(response, function(key, val){
                    ej_articles.push(val.total);
                    ej_labels.push(moment(val.label, 'MM').format('MMM'));
                    ej_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
                });
                }
            });
            ej_title = 'Visitors';
            var bar = document.getElementById('ej_bar_chart').getContext('2d');
            ej_bar_chart.destroy();
            ej_bar_chart = new Chart(bar, {
                type: 'horizontalBar',
                data: {
                    labels: ej_labels,
                    datasets: [{
                        label: ej_title,
                        data: ej_articles,
                        backgroundColor: ej_bgcolors,
                        borderColor: 'white',
                        borderWidth: 1
                    }],
                },
                options: {
                    title : {
                        display: true,
                        text: ej_title,
                        fontSize: 14,
                    },
                    legend: {
                        display: false, 
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                    events : ['click'],
                    onClick: function(c, i){
                        e = i[0];
                        var jor_year = this.data.labels[e._index];
                        // var y_value = this.data.datasets[0].data[e._index];
                        show_table_ejournal(jor_year);
                    }
                }
            });

            var pie = document.getElementById('ej_pie_chart').getContext('2d');
            ej_pie_chart.destroy();
            ej_pie_chart = new Chart(pie, {
                type: 'pie',
                data: {
                    labels: ej_labels,
                    datasets: [{
                        label: ej_title,
                        data: ej_articles,
                        backgroundColor: ej_bgcolors,
                        borderColor: 'white',
                        borderWidth: 1
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    title : {
                        display: true,
                        // text: 'Articles per journal',
                        fontSize: 14,
                    },
                    legend: {
                        display: true,
                        position: 'top',
                    }
                }
            }); 
            return false;
            

         }

        $.ajax({
            method: 'GET',
            url: APP_URL + _url,
            async: false,
            success: function (response) {
            $.each(response, function(key, val){
                ej_articles.push(val.total);
                ej_labels.push(val.label);
                ej_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
            });
            }
        });
        ej_title = 'Journals by Year';
        var bar = document.getElementById('ej_bar_chart').getContext('2d');
        ej_bar_chart.destroy();
        ej_bar_chart = new Chart(bar, {
            type: 'horizontalBar',
            data: {
                labels: ej_labels,
                datasets: [{
                    label: ej_title,
                    data: ej_articles,
                    backgroundColor: ej_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                title : {
                    display: true,
                    text: ej_title,
                    fontSize: 14,
                },
                legend: {
                    display: false, 
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                events : ['click'],
                onClick: function(c, i){
                    e = i[0];
                    var jor_year = this.data.labels[e._index];
                    // var y_value = this.data.datasets[0].data[e._index];
                    show_table_ejournal(jor_year);
                }
            }
        });

        var pie = document.getElementById('ej_pie_chart').getContext('2d');
        ej_pie_chart.destroy();
        ej_pie_chart = new Chart(pie, {
            type: 'pie',
            data: {
                labels: ej_labels,
                datasets: [{
                    label: ej_title,
                    data: ej_articles,
                    backgroundColor: ej_bgcolors,
                    borderColor: 'white',
                    borderWidth: 1
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title : {
                    display: true,
                    // text: 'Articles per journal',
                    fontSize: 14,
                },
                legend: {
                    display: true,
                    position: 'top',
                },
                events : ['click'],
                onClick: function(c, i){
                    e = i[0];
                    var jor_year = this.data.labels[e._index];
                    // var y_value = this.data.datasets[0].data[e._index];
                    show_table_ejournal(jor_year);
                }
            }
        }); 
    });

    $("#skms_table").on('click', '.btn-success', function () {
        $(this).closest('tr').remove();
        execom_users();
    });

    $("#execom_table").on('click', '.btn-danger', function () {
        $(this).closest('tr').remove();
         all_users();
    });
    
    $("#create_new_form").validate({
        debug: true,
        errorClass: 'text-danger',
        rules: {
            name: {
                required: true,
                minlength: 3
            },
            email: {
                required: true,
            },
            password: {
                required: true,
                minlength: 8,
            },
            repeat_password: {
                required: true,
                minlength: 8,
                equalTo: "#password"
            },
            role: {
                required: true,
            },
        },
        submitHandler: function() {

            var formdata = $("#create_new_form").serialize();

            $.ajax({
                type: "POST",
                url: APP_URL + '/execom/create',
                data:  formdata,
                cache: false,
                crossDomain: true,
                success: function(response) {
                    // console.log(response);
                    if(response == '1'){
                        $('#create_new_form ._alert').remove();
                        $('#create_new_form').hide().prepend('<div class="row _alert"> \
                                                            <div class="col-sm-3"></div> \
                                                            <div class="col-sm-9"> \
                                                                <div class="alert alert-danger" role="alert"> \
                                                                <span class="fas fa-exclamation-triangle"></span> \
                                                                Email already exists. Please enter another email. \
                                                                </div> \
                                                            </div> \
                                                      </div>').fadeIn();
                    }else{
                        $('#create_new_form ._alert').remove();
                        $('#create_new_form')[0].reset();
                        $('#create_new_form').hide().prepend('<div class="row _alert"> \
                                                            <div class="col-sm-3"></div> \
                                                            <div class="col-sm-9"> \
                                                                <div class="alert alert-success" role="alert"> \
                                                                <span class="fas fa-user-check"></span> \
                                                                New user created successfully! \
                                                                </div> \
                                                            </div> \
                                                      </div>').fadeIn();
                        
                    }
                }
            });
        }
    });

    $('#create_new_formx').on('submit', function(e){
        e.preventDefault();

        // console.log($(this).serializeArray());

        var formdata = $(this).serialize();

        $.ajax({
            type: "POST",
            url: APP_URL + '/execom/create',
            data:  formdata,
            cache: false,
            crossDomain: true,
            success: function(response) {
                if(response == '1'){
                    $('#create_new_form .error').text('Email already exists');
                }else{
                    $('#create_new_form')[0].reset();
                    execom_users();
                    $('#users-tab').click();

                }
            }
        });
    });

    $('#generate_chart_form input:radio[name="fb_rate_ui"]').change(
    function(){
        if (this.checked) {
                $(".ui-container .alert-danger").remove();
        }
    });

    $('#generate_chart_form input:radio[name="fb_rate_ux"]').change(
    function(){
        if (this.checked) {
                $(".ux-container .alert-danger").remove();
        }
    });
      
    $('#feedback_form').on('submit', function(e){

        e.preventDefault();

        var alert = '<div class="alert alert-danger w-100" role="alert"> \
                            Please select your rating. \
                            </div>';

        if (!$("input[name='fb_rate_ui']").is(':checked')) {
            $(".ui-container .alert-danger").remove();
            $(alert).hide().appendTo(".ui-container").fadeIn();
        }
        
        if (!$("input[name='fb_rate_ux']").is(':checked')) {
            $(".ux-container .alert-danger").remove();
            $(alert).hide().appendTo(".ux-container").fadeIn();
        }

        if($("input[name='fb_rate_ui']").is(':checked') && $("input[name='fb_rate_ux']").is(':checked')){
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formdata = $(this).serializeArray();
            $.ajax({
                type: "POST",
                url: APP_URL + '/submit_feedback',
                data:  formdata,
                cache: false,
                crossDomain: true,
                success: function(response) {
                    $('#feedback_form').remove();

                    var thanks = '<p class="text-center h2">Thank you for your feedback.</p> \
                                  <p class="text-center btn-link font-weight-bold"><u><a href="/logout">Proceed to logout</a></u></p>';
                             
                    
                    $(thanks).hide().appendTo("#feedbackModal .modal-body").fadeIn();

                }
            });
        }
    });

    
    $("#show_password").on('click',function() {
        var pass = $('.password');
        var icon = $('.icon');

        if (pass.attr('type') === 'password') {
            pass.attr('type', 'text');
            icon.addClass('fa-eye-slash').removeClass('fa-eye');

        } else {
            pass.attr('type', 'password');
            icon.addClass('fa-eye').removeClass('fa-eye-slash');
        }
    });

    $("#show_password_add_user").on('click',function() {
        
        var pass = $('#password');
        var icon = $('.icon');

        if (pass.attr('type') == 'password') {
            pass.attr('type', 'text');
            icon.addClass('fa-eye-slash').removeClass('fa-eye');

        } else {
            pass.attr('type', 'password');
            icon.addClass('fa-eye').removeClass('fa-eye-slash');
        }
    });

    $("#show_new_password").on('click',function() {
        var pass = $('.new_password');
        var icon = $('.icon');

        if (pass.attr('type') === 'password') {
            pass.attr('type', 'text');
            icon.addClass('fa-eye-slash').removeClass('fa-eye');

        } else {
            pass.attr('type', 'password');
            icon.addClass('fa-eye').removeClass('fa-eye-slash');
        }
    });

    $("#show_rep_password").on('click',function() {
        var pass = $('.rep_password');
        var icon = $('.rep_icon');

        if (pass.attr('type') === 'password') {
            pass.attr('type', 'text');
            icon.addClass('fa-eye-slash').removeClass('fa-eye');

        } else {
            pass.attr('type', 'password');
            icon.addClass('fa-eye').removeClass('fa-eye-slash');
        }
    });

    $('#quick_search').on('keydown', function(e){
        if(e.which == 13 && $(this).val() != '') {
            show_overall($(this).val());
            $('#quick_search_keyword').text($(this).val());
        }
    });

    $('#time_tab .nav-link').click(function(){
        $('#time_content select').val('0');
        $('#time_content select').removeClass('bg bg-dark text-white font-weight-bold');
        $('#column_bar_tab, #stacked_column_tab').removeClass('text-success').addClass('disabled');
    });

    // $('#search_keyword').on('keydown', function(e){
    //     if(e.which == 13 && $(this).val() != '') {
    //         show_overall($(this).val());
    //         $('#quick_search_keyword').text($(this).val());
    //     }
    // });

    // memis graph

    // $('#btn_generate_chart').click(function(e){
    //     var default_x = $('input[type=radio]:checked').attr('id');

    

    //     $('.no-data-found').remove();
    //     bar_sub_title = [];
    //     $("#generate_chart_form option:selected").each(function() {
    //         if($(this).val() > 0){
    //             bar_sub_title.push($(this).text());
    //         }
    //     });

        
    //     var selections = [];
    //     var filter_counter = 0;
        
    //     $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function() {
    //         selections.push($(this).val());
    //     });
    //     for (let i = 0; i < selections.length; i++) {
    //         if (selections[i] != '0') filter_counter++;
    //     }

    //     if(filter_counter == 0){
    //         $('.chart_filter_alert').fadeOut('slow');
    //         $('#generate_chart_form').prepend('<div class="alert alert-danger chart_filter_alert" role="alert"> \
    //         <span class="fas fa-exclamation-triangle"></span> Select atleast one (1) filter \
    //         </div>').hide().fadeIn('slow');
    //     }else{

    //         if ($('input[name="radio_generate_chart"]:checked').length == 0) {
    //             $('.chart_radio_alert').fadeOut('slow');
    //             $('.notice_radio_chart').append('<div class="alert alert-danger chart_radio_alert" role="alert"> \
    //             <span class="fas fa-exclamation-triangle"></span> Select atleast one (1) chart \
    //             </div>').hide().fadeIn('slow');
    //         }
    //     }

    //     var chart = $('input[name="radio_generate_chart"]:checked').val();
    //     var total = 0;

    //     if(chart == 1){ // bar chart
            
            
    //         chart_numbers = 1;
    //         $('#chart_numbers').prop('checked', true); // Unchecks it
    //         $('#chart_numbers').change(); // Unchecks it
    //         chart_orientation = 1;
    //         $('#chart_orientation').prop('checked', true); // Unchecks it
    //         $('#chart_orientation').change(); // Unchecks it
    //         chart_rendered = 1;

    //         var memis_labels = [];
    //         var memis_total = [];
    //         var memis_bgcolors = [];
    //         var memis_ids = [];
    //         var memis_drill_data = [];
    //         var memis_series_drill = [];
    //         $.ajax({
    //             method: 'POST',
    //             url: APP_URL + '/memis/bar_graph',
    //             async: false,
    //             data:  $('#generate_chart_form').serialize(),
    //             datatype: 'json',
    //             success: function (response) {
    //                 if(response.length > 0){
    //                     $.each(response, function(key, val){   
    //                         memis_total.push(parseInt(val.total));
    //                         memis_labels.push(val.label);
    //                         memis_bgcolors.push('#800000');
    //                         memis_ids.push(val.bar_id);
    //                         total += parseInt(val.total);

                              
    //                         memis_drill_data.push({
    //                             name:val.label,
    //                             y: val.total,
    //                             drilldown: val.label
    //                         })

    //                     });

                        
    //                 }else{
    //                     $('.no-data-found').remove();
    //                     $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
    //                     <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
    //                   </div>').hide().fadeIn();
    //                 }
    //             }
    //         });

    //         bar_labels = memis_labels;
    //         bar_total = total;
           
    //         if(default_x == 'default_region'){

    //             var y = 0;
    //             $.each(memis_labels, function(key, val){
    //                 y++;
    //                 memis_series_drill.push({
    //                     name:val,
    //                     id: val,
    //                     data:  JSON.parse(get_drilldown(y))
    //                 })
    //             });

    //             exeChart = new Highcharts.chart('container', {
    //                 chart: {
    //                     type: 'bar',
    //                     events: {
    //                     load: function() {
    //                         var chart = this,
    //                         barsLength = chart.series[0].data.length;
                    
    //                         chart.update({
    //                         chart: {
    //                             height: 100 + (50 * barsLength)
    //                         }
    //                         }, true, false, false);
    //                     }
    //                     }
    //                 },
    //                 title: {
    //                     text: bar_main_title
    //                 },
    //                 subtitle: {
    //                     text: 'Source: http://execom.nrcp.dost.gov.ph/'
    //                 },
    //                 accessibility: {
    //                     announceNewData: {
    //                         enabled: true
    //                     }
    //                 },
    //                 xAxis: {
    //                     // categories: memis_labels,
    //                     type: 'category',
    //                     title: {
    //                         text: null
    //                     },
    //                     labels: {
    //                         style: {
    //                             fontSize:'14px'
    //                         }
    //                     }
    //                 },
    //                 yAxis: {
    //                     min: 0,
    //                     title: {
    //                         text: 'Total Members (' + total + ')',
    //                           align: 'high'
    //                     },
    //                     labels: {
    //                         overflow: 'justify'
    //                     }
    //                 },
                    
    //                 plotOptions: {
    //                     series: {
    //                         events: {
    //                             click: function(event) {
    //                                 if (chart.drillUpButton) {
    //                                     alert("testing" + event.point.name) //todo
    //                                } 
    //                             }
    //                           },
    //                         dataLabels:{
    //                             enabled:true,
    //                             formatter:function() {
    //                                 var pcnt = (this.y / total) * 100;
    //                                 return this.y + '(' + Highcharts.numberFormat(pcnt) + '%)';
    //                             }
    //                         }
    //                     },
    //                 },
    //                 tooltip: {
    //                     headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
    //                     pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}<br/>'
    //                 },
    //                 legend: {
    //                     layout: 'vertical',
    //                     x: -40,
    //                     y: 80,
    //                     floating: true,
    //                     shadow: true
    //                 },
    //                 credits: {
    //                     enabled: false
    //                 },
    //                 series: [
    //                     {
    //                         name: "Regions",
    //                         colorByPoint: true,
    //                         data: memis_drill_data
    //                     }
    //                 ],
    //                 drilldown: { series : memis_series_drill}
    //             });
                
               
    //         }else{

    //             exeChart = new Highcharts.chart('container', {
    //                 chart: {
    //                     type: 'bar',
    //                     events: {
    //                     load: function() {
    //                         var chart = this,
    //                         barsLength = chart.series[0].data.length;
                    
    //                         chart.update({
    //                         chart: {
    //                             height: 100 + (50 * barsLength)
    //                         }
    //                         }, true, false, false);
    //                     }
    //                     }
    //                 },
    //                 title: {
    //                     text: bar_main_title
    //                 },
    //                 subtitle: {
    //                     text: 'Source: http://execom.nrcp.dost.gov.ph/'
    //                 },
    //                 xAxis: {
    //                     categories: memis_labels,
    //                     title: {
    //                         text: null
    //                     },
    //                     labels: {
    //                         style: {
    //                             fontSize:'14px'
    //                         }
    //                     }
    //                 },
    //                 yAxis: {
    //                     min: 0,
    //                     title: {
    //                         text: 'Total Members (' + total + ')',
    //                         align: 'high'
    //                     },
    //                     labels: {
    //                         overflow: 'justify'
    //                     }
    //                 },
    //                 plotOptions: {
    //                     bar: {
    //                         dataLabels:{
    //                             enabled:true,
    //                             formatter:function() {
    //                                 var pcnt = (this.y / total) * 100;
    //                                 return this.y + '(' + Highcharts.numberFormat(pcnt) + '%)';
    //                             }
    //                         } 
    //                     },
    //                     series: {
    //                         colorByPoint: true,
    //                         colors: memis_bgcolors,
    //                         pointWidth: '30',
    //                         point: {
    //                             events: {
    //                                 click: function () {
    //                                     click_overall(memis_ids[parseInt(this.index)], default_x);
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 },
    //                 legend: {
    //                     layout: 'vertical',
    //                     x: -40,
    //                     y: 80,
    //                     floating: true,
    //                     shadow: true
    //                 },
    //                 credits: {
    //                     enabled: false
    //                 },
    //                 series: [{
    //                     name: 'Members',
    //                     data: memis_total,
    //                 }]
    //             });
            
    //         }

            
    //     }else if(chart == 2){ // pie chart

    //         var memis_labels = [];
    //         var memis_total = [];
    //         var memis_bgcolors = [];
    //         var total = 0;
    //         var memis_ids = [];
            
    //         $.ajax({
    //             method: 'POST',
    //             url: APP_URL + '/memis/bar_graph',
    //             async: false,
    //             data:  $('#generate_chart_form').serialize(),
    //             datatype: 'json',
    //             success: function (response) {

    //                 if(response.length > 0){
    //                     $.each(response, function(key, val){   
                            
    //                         memis_labels.push({
    //                             name: val.label, 
    //                             y: parseFloat(val.total),
    //                         });

    //                         total += val.total;

    //                         memis_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
    //                         memis_ids.push(val.bar_id);
        
    //                     });
            
    //                 }else{
    //                     $('.no-data-found').remove();
    //                     $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
    //                     <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
    //                   </div>').hide().fadeIn();
    //                 }
    //             }
    //         });

    //         Highcharts.chart('container', {
    //             chart: {
    //               plotBackgroundColor: null,
    //               plotBorderWidth: null,
    //               plotShadow: false,
    //               type: 'pie',
    //               marginBottom: 50 
    //             },
    //             title: {
    //               text: bar_main_title
    //             },
    //             subtitle: {
    //                 text: 'Source: http://execom.nrcp.dost.gov.ph/'
    //             },
    //             tooltip: {
    //               pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
    //             },
    //             accessibility: {
    //               point: {
    //                 valueSuffix: '%'
    //               }
    //             },
    //             plotOptions: {
    //               pie: {
    //                 allowPointSelect: true,
    //                 cursor: 'pointer',
    //                 dataLabels: {
    //                   enabled: true,
    //                   format: '<b>{point.name}</b>: {point.percentage:.1f} %'
    //                 },
    //                 point: {
    //                     events: {
    //                         click: function () {
    //                             click_overall(memis_ids[parseInt(this.index)], default_x);
    //                         }
    //                   }
    //                 }
    //               }
    //             },    
    //             credits: {
    //                 text: 'Total Members (' + total + ')',
    //                 position: {
    //                     align: 'right',
    //                 },
    //                 style: {
    //                     fontSize: '9pt', // you can style it!,
    //                     // color: '#ffffff',
    //                 }
    //             },
    //             colors: memis_bgcolors,
    //             series: [{
    //               name: '',
    //               data: memis_labels
    //             }]
    //           });
    //     }else if(chart == 3){ // stacked chart
            
    //         chart_numbers = 1;
    //         $('#chart_numbers').prop('checked', true); // Unchecks it
    //         $('#chart_numbers').change(); // Unchecks it
    //         chart_orientation = 1;
    //         $('#chart_orientation').prop('checked', true); // Unchecks it
    //         $('#chart_orientation').change(); // Unchecks it
    //         chart_rendered = 1;
    //         // var default_label = $('input[name=radio_default]:checked').attr('id');
    //         // var chart_height = (default_label == 'default_category') ? '' : '';


    //         var memis_labels = [];
    //         var memis_bgcolors = [];
    //         var total = 0;

    //         $.ajax({
    //             method: 'POST',
    //             url: APP_URL + '/memis/stack_graph',
    //             async: false,
    //             data:  $('#generate_chart_form').serialize(),
    //             datatype: 'json',
    //             success: function (response) {
    //                 // console.log(response);
    //                 if(response.length > 0){
    //                     if(stacked_bar_exemption == 1){stacked_bar_y = [];} // y axis label is based from php query result
                        
    //                     var i = 0 ;
    //                     for(i; i < response.length;i++){
    //                         $.each(response[i], function(key, val){   
                                        
    //                             var memis_total = [];
    //                             $.each(val, function(k,v){
                                    
    //                                 if(v.length > 0){

    //                                     $.each(v, function(x, y){
    //                                         memis_total.push(y.total);
    //                                         if(stacked_bar_exemption == 1){stacked_bar_y.push(y.country_name);}
    //                                         total += y.total;
    //                                     });
                
    //                                     memis_labels.push({
    //                                         name:k,
    //                                         data:memis_total
    //                                     })

    //                                     memis_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
    //                                 }
    //                             });
    //                         });
    //                     }

    //                     if(stacked_bar_exemption == 1){
    //                         uniqueCountry = stacked_bar_y.filter(function(item, i, stacked_bar_y) {
    
                                
    //                             return i == stacked_bar_y.indexOf(item);
                            
    //                         });
    //                         uniqueCountry.sort();
                            
    //                         stacked_bar_y = [];
    
    //                         $.each(uniqueCountry.sort(), function(key, val){
                                
    //                             stacked_bar_y.push(val);
    //                         });
    //                     }

    //                 }else{
    //                     $('.no-data-found').remove();
    //                     $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
    //                     <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
    //                   </div>').hide().fadeIn();
    //                 }
    //             }
    //         });

            
    //         exeChart = new Highcharts.chart('container', {
    //             chart: {
    //               type: 'bar',
    //               events: {
    //                 load: function() {
    //                   var chart = this,
    //                     barsLength = chart.series[0].data.length;
              
    //                   chart.update({
    //                     chart: {
    //                       height: 150 + (70 * stacked_bar_y.length)
    //                     }
    //                   }, true, false, false);
    //                 }
    //               }
    //             },
    //             title: {
    //               text: bar_main_title
    //             },
    //             xAxis: {
    //               categories: stacked_bar_y,
    //               title: {
    //                   text: 'Total Members (' + total + ')',
    //                   align: 'high'
    //               },
    //               labels: {
    //                   style: {
    //                       fontSize:'14px'
    //                   }
    //               }
    //             },
    //             yAxis: {
    //               min: 0,
    //               title: {
    //                 text: bar_sub_title
    //               }
    //             },
    //             legend: {
    //               reversed: true
    //             },
    //             tooltip: {
    //                 headerFormat: '<b>{point.x}</b><br/>',
    //                 pointFormat: '{series.name}: {point.y} ({point.percentage:.2f}%)<br/>'
    //             },
    //             plotOptions: {
    //               series: {
    //                 stacking: 'normal',
    //                 pointWidth: '30',
    //                 dataLabels: {
    //                     enabled: true,
    //                     formatter: function () {
    //                         return this.y + ' (' + Math.round(100 * this.y / this.total) + '%)';
    //                     },
    //                 },
    //               }
    //             },
    //             colors :  memis_bgcolors,
    //             series: memis_labels
    //           });

    //           chart_rendered = 1;
    //     }else if(chart == 4){ // column chart
    //         chart_numbers = 1;
    //         $('#chart_numbers').prop('checked', true); // Unchecks it
    //         $('#chart_numbers').change(); // Unchecks it
    //         chart_orientation = 2;
    //         $('#chart_orientation').prop('checked', false); // Unchecks it
    //         $('#chart_orientation').change(); // Unchecks it
    //         chart_rendered = 1;

    //         var memis_labels = [];
    //         var memis_total = [];
    //         var y_total = [];
    //         var total = 0;
            

            
    //         var start = $('#memis_start_year').val();
    //         var end = $('#memis_end_year').val();
    //         if(start > 0 && end > 0){
    //             stacked_bar_y = [];
    //             for (start; start <= end; start++) {
    //                 stacked_bar_y.push(start);
    //             }

    //             bar_main_title = category_title + '(' + start + '-' + end + ')';
    //         }

    //         var period = $('#memis_period').val();
    //         var month = 0, quarter = 0, semestral = 0;
    //         var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    //         var quarters = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Qurter'];
    //         var sems = ['1st Semestral', '2nd Semestral'];
    //         if(period == 1){
    //             stacked_bar_y = [];
    //             for (month; month <  12; month++) {
    //                 stacked_bar_y.push(months[month]);
    //             }
                
    //             var p_year = (period_year > 0) ? ' '+ period_year : '';
                
    //             bar_main_title = category_title + '(Monthly' + p_year + ')';
    //         }else if(period == 2){
    //             stacked_bar_y = [];
    //             for (quarter; quarter <  4; quarter++) {
    //                 stacked_bar_y.push(quarters[quarter]);
    //             }
    //             bar_main_title = category_title + '(Quarterly' + p_year + ')';
    //         }else if(period == 3){
    //             stacked_bar_y = [];
    //             for (semestral; semestral <  2; semestral++) {
    //                 stacked_bar_y.push(sems[semestral]);
    //             }
    //             bar_main_title = category_title + '(Semestral' + p_year + ')';
    //         }


    //         $.ajax({
    //             method: 'POST',
    //             url: APP_URL + '/memis/column_graph',
    //             async: false,
    //             data:  $('#generate_chart_form').serialize(),
    //             datatype: 'json',
    //             success: function (response) {
    //                 // console.log(response);
    //                 if(response.length > 0){
                        
                        
    //                     var i = 0 ;
    //                     for(i; i < response.length;i++){
    //                         $.each(response[i], function(key, val){   
                                        
    //                             var memis_total = [];
    //                             $.each(val, function(k,v){
                                    
    //                                 if(v.length > 0){

    //                                     $.each(v, function(x, y){
    //                                             memis_total.push(y.total);
    //                                             total += y.total;
    //                                     });


                                        
    //                                     memis_labels.push({
    //                                         name:k,
    //                                         data:memis_total
    //                                     })

    //                                 }

    //                             });

                                
    //                         });
    //                     }
                    
    //                     chart_rendered = 1;
    //                 }else{
    //                     $('.no-data-found').remove();
    //                     $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
    //                     <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
    //                   </div>').hide().fadeIn();
    //                 }
    //             }
    //         });

    //         exeChart = new Highcharts.chart('container', {
    //             chart: {
    //               type: 'column',
    //               events: {
    //                 load: function() {
    //                   var chart = this,
    //                     barsLength = chart.series[0].data.length;
              
    //                   chart.update({
    //                     chart: {
    //                       height: 700
    //                     }
    //                   }, true, false, false);
    //                 }
    //               }
    //             },
    //             title: {
    //               text: bar_main_title
    //             },
    //             xAxis: {
    //               categories: stacked_bar_y
    //             },
    //             yAxis: {
    //               min: 0,
    //               title: {
    //                 text: bar_sub_title
    //               }
    //             },
    //             tooltip: {
    //                 formatter: function() {
    //                     var s = '<b>'+ this.x +'</b>', sum = 0;
                             
    //                     $.each(this.points, function(i, point) {
    //                         sum += point.y;
    //                     });     

    //                     $.each(this.points, function(i, point) {
    //                         s += '<br/><span style="color:{series.color};padding:0">'+ point.series.name +'</span>: '+
    //                             point.y + '(' + (( point.y / sum) * 100).toFixed(2) + '%)';

    //                     });                
    //                     s += '<br/>Total: '+sum               
    //                     return s;
    //                 },
    //                 shared: true,
    //                 useHTML: true
    //             },
    //             legend: {
    //               reversed: true
    //             },
    //             plotOptions: {
    //                 column: {
    //                     pointPadding: 1,
    //                     borderWidth: 0,
    //                     pointWidth: '30',
    //                     dataLabels: {
    //                         enabled: true,
    //                         formatter:function() {
    //                             return this.y ;
    //                         }
    //                     }
    //                 }
    //             },
    //             series: memis_labels,
    //           });
                          
    //     }else { // stack column chart
            
    //         chart_numbers = 1;
    //         $('#chart_numbers').prop('checked', true); // Unchecks it
    //         $('#chart_numbers').change(); // Unchecks it
    //         chart_orientation = 2;
    //         $('#chart_orientation').prop('checked', false); // Unchecks it
    //         $('#chart_orientation').change(); // Unchecks it
    //         chart_rendered = 1;

    //         var memis_labels = [];
    //         var memis_total = [];
    //         var memis_bgcolors = [];
    //         var y_total = [];
    //         var total = 0;
            

            
    //         var start = $('#memis_start_year').val();
    //         var end = $('#memis_end_year').val();
    //         if(start > 0 && end > 0){
                
    //             bar_main_title = category_title + '(' + start + '-' + end + ')';
    //             stacked_bar_y = [];
    //             for (start; start <= end; start++) {
    //                 stacked_bar_y.push(start);
    //             }

    //         }
    //         var period = $('#memis_period').val();
    //         var month = 0, quarter = 0, semestral = 0;
    //         var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    //         var quarters = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Qurter'];
    //         var sems = ['1st Semestral', '2nd Semestral'];
    //         var period_year = $('#memis_year').val();

    //         if(period == 1){
    //             stacked_bar_y = [];
    //             for (month; month <  12; month++) {
    //                 stacked_bar_y.push(months[month]);
    //             }
                
    //             var p_year = (period_year > 0) ? period_year : '';
                
    //             bar_main_title = category_title + '(Monthly ' + p_year + ')';
    //         }else if(period == 2){
    //             stacked_bar_y = [];
    //             for (quarter; quarter <  4; quarter++) {
    //                 stacked_bar_y.push(quarters[quarter]);
    //             }
    //             bar_main_title = category_title + '(Quarterly ' + p_year + ')';
    //         }else if(period == 3){
    //             stacked_bar_y = [];
    //             for (semestral; semestral <  2; semestral++) {
    //                 stacked_bar_y.push(sems[semestral]);
    //             }
    //             bar_main_title = category_title + '(Semestral ' + p_year + ')';
    //         }


    //         $.ajax({
    //             method: 'POST',
    //             url: APP_URL + '/memis/stack_column_graph',
    //             async: false,
    //             data:  $('#generate_chart_form').serialize(),
    //             datatype: 'json',
    //             success: function (response) {
    //                 if(response.length > 0){
                    
    //                     var i = 0 ;
    //                     for(i; i < response.length;i++){
    //                         $.each(response[i], function(key, val){   
    //                             var memis_total = [];
    //                             $.each(val, function(k,v){
    //                                 if(v.length > 0){

    //                                     $.each(v, function(x, y){
    //                                             memis_total.push(y.total);
    //                                             total += y.total;
    //                                     });

    //                                     memis_labels.push({
    //                                         name:k,
    //                                         data:memis_total,
    //                                         stack: 'STACK'
    //                                     })

    //                                     memis_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));

    //                                 }

    //                             });
    //                         });
    //                     }
                    
    //                     chart_rendered = 1;
    //                 }else{
    //                     $('.no-data-found').remove();
    //                     $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
    //                     <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
    //                   </div>').hide().fadeIn();
    //                 }
    //             }
    //         });

    //         exeChart = new Highcharts.chart('container', {

    //             chart: {
    //                 type: 'column',
    //                 events: {
    //                   load: function() {
    //                     var chart = this,
    //                       barsLength = chart.series[0].data.length;
                
    //                     chart.update({
    //                       chart: {
    //                         height: 700
    //                       }
    //                     }, true, false, false);
    //                   }
    //                 }
    //             },
            
    //             title: {
    //                 text: bar_main_title
    //             },
            
    //             xAxis: {
    //                 categories: stacked_bar_y
    //             },
    //             yAxis: {
    //               min: 0,
    //               title: {
    //                 text: bar_sub_title
    //               }
    //             },
    //             tooltip: {
    //                 formatter: function () {
    //                     return '<b>' + this.x + '</b><br/>' +
    //                         this.series.name + ': ' + this.y + '<br/>' +
    //                         'Total: ' + this.point.stackTotal;
    //                 }
    //             },
    //             plotOptions: {
    //                 column: {
    //                     stacking: 'normal',
    //                     pointPadding: 1,
    //                     borderWidth: 0,
    //                     pointWidth: '30',
    //                     dataLabels: {
    //                         enabled: true,
    //                         formatter:function() {
    //                             return this.y ;
    //                         }
    //                     }
    //                 }
    //             },
    //             colors :  memis_bgcolors,
    //             series: memis_labels
    //         });
                     
    //     }

    //     e.preventDefault();
    // });

    $('#chart_numbers').change(function(e){
        
        var chart = $('input[name="radio_generate_chart"]:checked').val();

        if(chart_rendered > 0){
            if($(this).is(":checked")){
                chart_numbers = 1;
                if(chart_orientation == 1){
                
                    exeChart.update({
                        plotOptions: {
                            bar: {
                                pointPadding: 1,
                                borderWidth: 0,
                                pointWidth: '30',
                                dataLabels: {
                                    enabled: true,
                                }
                            }
                        },
                    });

                }else{

                    exeChart.update({
                        plotOptions: {
                            column: {
                                pointPadding: 1,
                                borderWidth: 0,
                                pointWidth: '30',
                                dataLabels: {
                                    enabled: true,
                                }
                            }
                        },
                    });
                }

            }else{
                chart_numbers = 0; 
                if(chart_orientation == 1){
                
                    exeChart.update({
                        plotOptions: {
                            bar: {
                                pointPadding: 1,
                                borderWidth: 0,
                                pointWidth: '30',
                                dataLabels: {
                                    enabled: false,
                                }
                            }
                        },
                    });

                }else{

                    exeChart.update({
                        plotOptions: {
                            column: {
                                pointPadding: 1,
                                borderWidth: 0,
                                pointWidth: '30',
                                dataLabels: {
                                    enabled: false,
                                }
                            }
                        },
                    });
                }


            }
        }

    });

    $('#chart_orientation').change(function(e){
        var chart_type = $('input[name="radio_generate_chart"]:checked').val();
        var datalabels = (chart_numbers == 1) ? true : false;
        if(chart_rendered > 0){
            if($(this).is(":checked")){
                chart_orientation = 1;
                if(chart_type == 4){
                    exeChart.update({
                        chart: { type: "bar" },
                        events: {
                          load: function() {
                            var chart = this;
                          }
                        },
                        legend: {
                        reversed: true
                        },
                        plotOptions: {
                            bar: {
                                pointPadding: 1,
                                borderWidth: 0,
                                pointWidth: '30',
                                dataLabels: {
                                    enabled: datalabels,
                                    formatter:function() {
                                        return this.y;
                                    }
                                }
                            }
                        },
                      });
                }else if(chart_type == 5){
                    exeChart.update({
                        chart: { type: "bar" },
                        events: {
                          load: function() {
                            var chart = this;
                          }
                        },
                        legend: {
                        reversed: true
                        },
                        plotOptions: {
                            bar: {
                                stacking: 'normal',
                                pointPadding: 1,
                                borderWidth: 0,
                                pointWidth: '30',
                                dataLabels: {
                                    enabled: datalabels,
                                    formatter:function() {
                                        return this.y;
                                    }
                                }
                            }
                        },
                      });
                }else{

                    exeChart.update({
                        chart: { type: "bar" },
                        plotOptions: {
                            bar: {
                                pointPadding: 1,
                                borderWidth: 0,
                                pointWidth: '30',
                                dataLabels: {
                                    enabled: datalabels,
                                }
                            }
                        },
                        xAxis: {
                            labels: {
                                style: {
                                    fontSize:'14px'
                                }
                            }
                        },
                      });
                }  
            }else{
                chart_orientation = 2;
                if(chart_type == 4){
                    exeChart.update({
                        chart: { type: "column" },
                        plotOptions: {
                            bar: {
                                pointPadding: 1,
                                borderWidth: 0,
                                pointWidth: '30',
                                dataLabels: {
                                    enabled: datalabels,
                                }
                            }
                        },
                    });
                }else if(chart_type == 5){
                    exeChart.update({
                        chart: { type: "column" },
                        plotOptions: {
                            bar: {
                                stacking: 'normal',
                                pointPadding: 1,
                                borderWidth: 0,
                                pointWidth: '30',
                                dataLabels: {
                                    enabled: datalabels,
                                }
                            }
                        },
                    })
                }else{ 
                    exeChart.update({
                        chart: { type: "column" },   
                       
                        xAxis: {
                            min: 0,
                            title: {
                                text: 'Total (' + bar_total + ')',
                                align: 'high'
                            },
                            labels: {
                                overflow: 'justify'
                            }
                        },
                        plotOptions: {
                            column: {
                                dataLabels:{
                                    enabled:datalabels,
                                    formatter:function() {
                                        var pcnt = (this.y / bar_total) * 100;
                                        return this.y + '(' + Highcharts.numberFormat(pcnt) + '%)';
                                    }
                                } 
                            },  
                        },
                    });
                }
            }
        }
    });

    $('#reset_filters').click(function(){
        $('#generate_chart_form')[0].reset();
        $('#generate_chart_form input:radio[name="radio_default"]').parent().remove();
        $('#generate_chart_form input:radio[name="radio_generate_chart"]').attr('disabled', true);
        $('.no-data-found, .chart_filter_alert, .chart_radio_alert').remove();
        $('#generate_chart_form select').removeClass('bg bg-dark text-white font-weight-bold');

        $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');

        stacked_bar_y = [];
        category_title = '';
        bar_sub_title = '';
        

    });

    $('#generate_chart_form input:radio').change(function(){
        $('.chart_radio_alert').fadeOut('slow');
    });

    $("#memis_island").change(function(e){
        category_title = 'Members by Island ';
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }

        if($(this).val() == '999'){
            bar_main_title = 'Members by Island as of ' + moment().format("MMM DD, YYYY");
            $(this).parent().append('<div class="custom-control custom-radio main_label_division mt-2"> \
                <input type="radio" id="default_island" name="radio_default" class="custom-control-input" value="memis_island" checked> \
                <label class="custom-control-label" for="default_division">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
            </div>').fadeIn('slow');

            
            stacked_bar_y = [];

            $("#memis_island option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    // stacked_bar_y.push($(this).val());
                    stacked_bar_y.push($(this).text());

                }
            });

            // console.log(stacked_bar_y);

        }else{
            $('.main_label_division').fadeOut('slow');
        }

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {
            selections.push($(this).val());
        });
        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }

        if(filter_counter == 1){
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled'); 
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');

                if(memis_start_year > 0 && memis_end_year > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
        
                if(memis_period > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
            }
        }else if(filter_counter == 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }else if(all_filter_counter > 1){
                $('#stacked_bar_tab').removeClass('disabled').addClass('text-success');
            }
        }else if(filter_counter > 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }
        }

        // console.log('ALL:' +all_filter_counter);
        // console.log('FILTER:' +filter_counter);

        e.preventDefault();
    });

    $(document).on('click','.main_label_island',function(){
        stacked_bar_y = [];

            $("#memis_island option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });

    $("#memis_division").change(function(e){
        category_title = 'Members by Division ';
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }

        if($(this).val() == '999'){
            bar_main_title = 'Members by Division as of ' + moment().format("MMM DD, YYYY");
            $(this).parent().append('<div class="custom-control custom-radio main_label_division mt-2"> \
                <input type="radio" id="default_division" name="radio_default" class="custom-control-input" value="memis_division" checked> \
                <label class="custom-control-label" for="default_division">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
            </div>').fadeIn('slow');

            
            stacked_bar_y = [];
            $("#memis_division option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    // stacked_bar_y.push($(this).val());
                    stacked_bar_y.push($(this).text());

                }
            });
        }else{
            $('.main_label_division').fadeOut('slow');
        }

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value

        var memis_start_year = $("#memis_start_year").val();
        var memis_end_year = $("#memis_end_year").val();
        var memis_period = $("#memis_period").val();

        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {   
                selections.push($(this).val());
        });

        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0' && selections[i] <= '999') filter_counter++;
        }

        
        if(filter_counter == 1){
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled'); 
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');

                if(memis_start_year > 0 && memis_end_year > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
        
                if(memis_period > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
            }
        }else if(filter_counter == 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }else if(all_filter_counter > 1){
                $('#stacked_bar_tab').removeClass('disabled').addClass('text-success');
            }
        }else if(filter_counter > 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }
        }

      

      
        e.preventDefault();
    });

    $(document).on('click','.main_label_division',function(){
        stacked_bar_y = [];

            $("#memis_division option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });

    $("#memis_region").change(function(e){
        category_title = 'Members by Region ';
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }

        if($(this).val() == '999'){
            bar_main_title = 'Members by Region as of ' + moment().format("MMM DD, YYYY");
            $(this).parent().append('<div class="custom-control custom-radio main_label_region mt-2"> \
                <input type="radio" id="default_region" name="radio_default" class="custom-control-input" value="memis_region" checked> \
                <label class="custom-control-label" for="default_region">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
            </div>').fadeIn('slow');

            
            stacked_bar_y = [];

            $("#memis_region option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());
                }
            });

        }else{
            $('.main_label_region').fadeOut('slow');
        }

        
        $('.main_label_province, .main_label_city').remove();

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value

        var memis_start_year = $("#memis_start_year").val();
        var memis_end_year = $("#memis_end_year").val();
        var memis_period = $("#memis_period").val();
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {
            selections.push($(this).val());
        });
        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }

        if(filter_counter == 1){
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled'); 
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
                $('#bar_drilldown_tab').removeClass('disabled').addClass('text-success');

                if(memis_start_year > 0 && memis_end_year > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
        
                if(memis_period > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
            }
        }else if(filter_counter == 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }else if(all_filter_counter > 1){
                $('#stacked_bar_tab').removeClass('disabled').addClass('text-success');
            }
        }else if(filter_counter > 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }
        }


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        var val = $(this).val();

        $.ajax({
            method: 'POST',
            url: APP_URL + '/search/prov',
            async: false,
            data: {'id':val},
            success: function (response) {
                $('#memis_province :not(:first-child)').remove();
                $('#memis_province').append("<option value='999'>All Province</option>");
                $.each(response, function(key, val){
                    $('#memis_province').append('<option value="' + val.province_id + '">' + val.province_name + '</option>');
                });
            }
        });
        
        e.preventDefault();
    });

    $(document).on('click','.main_label_region',function(){
        stacked_bar_y = [];

            $("#memis_region option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });
 
    $("#memis_province").change(function(e){
        category_title = 'Members by Province ';
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }

        if($(this).val() == '999'){
            bar_main_title = 'Members by Province as of ' + moment().format("MMM DD, YYYY");
            $(this).parent().append('<div class="custom-control custom-radio main_label_province mt-2"> \
                <input type="radio" id="default_province" name="radio_default" class="custom-control-input" value="memis_province" checked> \
                <label class="custom-control-label" for="default_province">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
            </div>').fadeIn('slow');


            stacked_bar_y = [];

            $("#memis_province option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());
    
                }
            });

        }else{
            $('.main_label_province').fadeOut('slow');
        }

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value

        var memis_start_year = $("#memis_start_year").val();
        var memis_end_year = $("#memis_end_year").val();
        var memis_period = $("#memis_period").val();
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {
            selections.push($(this).val());
        });
        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }

        if(filter_counter == 1){
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled'); 
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');

                if(memis_start_year > 0 && memis_end_year > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
        
                if(memis_period > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
            }
        }else if(filter_counter == 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }else if(all_filter_counter > 1){
                $('#stacked_bar_tab').removeClass('disabled').addClass('text-success');
            }
        }else if(filter_counter > 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }
        }

        // console.log('ALL:' +all_filter_counter);
        // console.log('FILTER:' +filter_counter);

        e.preventDefault();

        // console.log('ALL:' +all_filter_counter);
        // console.log('FILTER:' +filter_counter);
        
        var val = $(this).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            method: 'POST',
            url: APP_URL + '/search/city',
            async: false,
            data: {'id':val},
            success: function (response) {
                $('#memis_city :not(:first-child)').remove();
                $('#memis_city').append("<option value='999'>All City</option>");
                $.each(response, function(key, val){
                    $('#memis_city').append('<option value="' + val.city_id + '">' + val.city_name + '</option>');
                });
            }
        });

        e.preventDefault();
    });

    $(document).on('click','.main_label_province',function(){
        stacked_bar_y = [];

            $("#memis_province option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });

    $("#memis_city").change(function(e){
        category_title = 'Members by City ';
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }

        if($(this).val() == '999'){
            bar_main_title = 'Members by City as of ' + moment().format("MMM DD, YYYY");
            $(this).parent().append('<div class="custom-control custom-radio main_label_city mt-2"> \
                <input type="radio" id="default_city" name="radio_default" class="custom-control-input" value="memis_city" checked> \
                <label class="custom-control-label" for="default_city">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
            </div>').fadeIn('slow');

            stacked_bar_y = [];
            
            $("#memis_city option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());
                }
            });
            
        }else{
            $('.main_label_city').fadeOut('slow');
        }

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value

        var memis_start_year = $("#memis_start_year").val();
        var memis_end_year = $("#memis_end_year").val();
        var memis_period = $("#memis_period").val();
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {
            selections.push($(this).val());
        });
        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }

        if(filter_counter == 1){
            if(all_filter_counter == 1){
                $('#generate_chart_form input:radio').attr('disabled', false);  
                $('#radio_stacked_chart').attr('disabled', true);   
            }else{
                $('#generate_chart_form input:radio').attr('disabled', true);   
            }
        }else if(filter_counter == 2){
            if(all_filter_counter == 1){
                $('#generate_chart_form input:radio').attr('disabled', false);  
                $('#radio_stacked_chart,#radio_column_chart').attr('disabled', true);    
            }else if(all_filter_counter > 1){
                $('#generate_chart_form input:radio').attr('disabled', false); 
                $('#radio_column_chart').attr('disabled', true);    
            }else{
                $('#generate_chart_form input:radio').attr('disabled', true);  
                // $('#radio_bar_chart, #radio_pie_chart').attr('disabled', false);    
            }
        }else if(filter_counter == 3){
            if(all_filter_counter == 1 || all_filter_counter == 2 ){
                $('#generate_chart_form input:radio').attr('disabled', false); 
                $('#radio_stacked_chart, #radio_column_chart').attr('disabled', true);    
            }else if(all_filter_counter == 0){
                $('#generate_chart_form input:radio').attr('disabled', true);  
            }
        }else if(filter_counter >= 4){
            if(all_filter_counter == 1){
                $('#generate_chart_form input:radio').attr('disabled', false); 
                $('#radio_stacked_chart, #radio_column_chart').attr('disabled', true);  
            }else if(all_filter_counter >= 2){
                $('#generate_chart_form input:radio').attr('disabled', false); 
            }
            else if(all_filter_counter == 0){
                $('#generate_chart_form input:radio').attr('disabled', true);  
            }
        }else{
            $('#generate_chart_form input:radio').attr('disabled', true); 
        }

        // console.log('ALL:' +all_filter_counter);
        // console.log('FILTER:' +filter_counter);

        e.preventDefault();
    });

    $(document).on('click','.main_label_city',function(){
        stacked_bar_y = [];

            $("#memis_city option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });
    
    $("#memis_category").change(function(e){

        category_title = 'Members by Category ';
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }

        // if($(this).val() > 0){
            
            if($(this).val() == '999'){
                bar_main_title = 'Members by Category as of ' + moment().format("MMM DD, YYYY");
                $(this).parent().append('<div class="custom-control custom-radio main_label_category mt-2 "> \
                    <input type="radio" id="default_category" name="radio_default" class="custom-control-input" value="memis_category" checked> \
                    <label class="custom-control-label" for="default_category">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
                </div>').fadeIn('slow');
    
                stacked_bar_y = [];
    
                $("#memis_category option").each(function() {
                    if($(this).val() > 0 && $(this).val() != 999){
                        // stacked_bar_y.push($(this).val());
                        stacked_bar_y.push($(this).text());
    
                    }
                });
    
    
            }
        //     else{
        //         $('#memis_start_year, #memis_end_year').attr('disabled', false);  
        //         $('.main_label_category').fadeOut('slow');
        //     }
        // }
        // else{
        //     $('#memis_start_year, #memis_end_year').attr('disabled', true);  
        //     $('.main_label_category').fadeOut('slow');
        // }

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value

        var memis_start_year = $("#memis_start_year").val();
        var memis_end_year = $("#memis_end_year").val();
        var memis_period = $("#memis_period").val();
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {
            selections.push($(this).val());
        });
        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }

        if(filter_counter == 1){
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled'); 
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');

                if(memis_start_year > 0 && memis_end_year > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
        
                if(memis_period > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
            }
        }else if(filter_counter == 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }else if(all_filter_counter > 1){
                $('#stacked_bar_tab').removeClass('disabled').addClass('text-success');
            }
        }else if(filter_counter > 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }
        }

        // console.log('ALL:' +all_filter_counter);
        // console.log('FILTER:' +filter_counter);

        e.preventDefault();
    });

    $(document).on('click','.main_label_category',function(){
        stacked_bar_y = [];

            $("#memis_category option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });

    $("#memis_status").change(function(e){
        category_title = 'Members by Status ';
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }

        if($(this).val() == '999'){
            
            bar_main_title = 'Members by Status as of ' + moment().format("MMM DD, YYYY");
            $(this).parent().append('<div class="custom-control custom-radio main_label_status mt-2"> \
                <input type="radio" id="default_status" name="radio_default" class="custom-control-input" value="memis_status" checked> \
                <label class="custom-control-label" for="default_status">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
            </div>').fadeIn('slow');


            stacked_bar_y = [];

            $("#memis_status option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    // stacked_bar_y.push($(this).val());
                    stacked_bar_y.push($(this).text());

                }
            });
        }else{
            $('.main_label_status').fadeOut('slow');
        }

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value

        var memis_start_year = $("#memis_start_year").val();
        var memis_end_year = $("#memis_end_year").val();
        var memis_period = $("#memis_period").val();
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {
            selections.push($(this).val());
        });
        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }

        if(filter_counter == 1){
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled'); 
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');

                if(memis_start_year > 0 && memis_end_year > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
        
                if(memis_period > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
            }
        }else if(filter_counter == 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }else if(all_filter_counter > 1){
                $('#stacked_bar_tab').removeClass('disabled').addClass('text-success');
            }
        }else if(filter_counter > 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }
        }

        // console.log('ALL:' +all_filter_counter);
        // console.log('FILTER:' +filter_counter);

        e.preventDefault();
    });

    $(document).on('click','.main_label_status',function(){
        stacked_bar_y = [];

            $("#memis_status option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });
    
    $("#memis_sex").change(function(e){
        category_title = 'Members by Sex ';
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }

     
        if($(this).val() == '999'){
            bar_main_title = 'Members by Sex as of ' + moment().format("MMM DD, YYYY");
            $(this).parent().append('<div class="custom-control custom-radio main_label_sex mt-2"> \
                <input type="radio" id="default_sex" name="radio_default" class="custom-control-input" value="memis_sex" checked> \
                <label class="custom-control-label" for="default_sex">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
            </div>').fadeIn('slow');


            stacked_bar_y = [];

            $("#memis_sex option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    // stacked_bar_y.push($(this).val());
                    stacked_bar_y.push($(this).text());

                }
            });
        }else{
            
            $('.main_label_sex').fadeOut('slow');
        }
   

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value

        var memis_start_year = $("#memis_start_year").val();
        var memis_end_year = $("#memis_end_year").val();
        var memis_period = $("#memis_period").val();
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {
            selections.push($(this).val());
        });
        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }

        if(filter_counter == 1){
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled'); 
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');

                if(memis_start_year > 0 && memis_end_year > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
        
                if(memis_period > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
            }
        }else if(filter_counter == 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }else if(all_filter_counter > 1){
                $('#stacked_bar_tab').removeClass('disabled').addClass('text-success');
            }
        }else if(filter_counter > 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }
        }

        // console.log('ALL:' +all_filter_counter);
        // console.log('FILTER:' +filter_counter);

        e.preventDefault();
    });

    $(document).on('click','.main_label_sex',function(){
        stacked_bar_y = [];

            $("#memis_sex option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });
    
    $("#memis_age").change(function(e){
        category_title = 'Members by Age ';
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }

        if($(this).val() == '999'){
            bar_main_title = 'Members by Age as of ' + moment().format("MMM DD, YYYY");
            $(this).parent().append('<div class="custom-control custom-radio main_label_age mt-2"> \
                <input type="radio" id="default_age" name="radio_default" class="custom-control-input" value="memis_age" checked> \
                <label class="custom-control-label" for="default_age">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
            </div>').fadeIn('slow');


            stacked_bar_y = [];

            $("#memis_age option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    // stacked_bar_y.push($(this).val());
                    stacked_bar_y.push($(this).text());

                }
            });

        }else{
            $('.main_label_age').fadeOut('slow');
        }

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value

        var memis_start_year = $("#memis_start_year").val();
        var memis_end_year = $("#memis_end_year").val();
        var memis_period = $("#memis_period").val();
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {
            selections.push($(this).val());
        });
        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }

        if(filter_counter == 1){
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled'); 
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');

                if(memis_start_year > 0 && memis_end_year > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
        
                if(memis_period > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
            }
        }else if(filter_counter == 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }else if(all_filter_counter > 1){
                $('#stacked_bar_tab').removeClass('disabled').addClass('text-success');
            }
        }else if(filter_counter > 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }
        }

        // console.log('ALL:' +all_filter_counter);
        // console.log('FILTER:' +filter_counter);

        e.preventDefault();
    });

    $(document).on('click','.main_label_age',function(){
        stacked_bar_y = [];

            $("#memis_age option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });
 
    $("#memis_educ").change(function(e){
        category_title = 'Members by Educational ';
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }
        
        if($(this).val() == '999'){
            bar_main_title = 'Members by Highest Educational Attainment as of ' + moment().format("MMM DD, YYYY");
            $(this).parent().append('<div class="custom-control custom-radio main_label_educ mt-2"> \
                <input type="radio" id="default_educ" name="radio_default" class="custom-control-input" value="memis_educ" checked> \
                <label class="custom-control-label" for="default_educ">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
            </div>').fadeIn('slow');


            stacked_bar_y = [];

            $("#memis_educ option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    // stacked_bar_y.push($(this).val());
                    stacked_bar_y.push($(this).text());

                }
            });

        }else{
            $('.main_label_educ').fadeOut('slow');
        }

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value

        var memis_start_year = $("#memis_start_year").val();
        var memis_end_year = $("#memis_end_year").val();
        var memis_period = $("#memis_period").val();
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {
            selections.push($(this).val());
        });
        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }

        if(filter_counter == 1){
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled'); 
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');

                if(memis_start_year > 0 && memis_end_year > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
        
                if(memis_period > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
            }
        }else if(filter_counter == 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }else if(all_filter_counter > 1){
                $('#stacked_bar_tab').removeClass('disabled').addClass('text-success');
            }
        }else if(filter_counter > 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }
        }

        // console.log('ALL:' +all_filter_counter);
        // console.log('FILTER:' +filter_counter);

        e.preventDefault();
    });

    $(document).on('click','.main_label_educ',function(){
        stacked_bar_y = [];

            $("#memis_educ option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });

    $("#memis_year").change(function(e){

        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }
        
        e.preventDefault();
    });

    $("#memis_start_year").change(function(e){

        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
            if($('#memis_end_year').val() > 0){
                $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
            }
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
            $('#column_bar_tab, #stacked_column_tab').removeClass('text-success').addClass('disabled');
        }

        e.preventDefault();
    });

    $("#memis_end_year").change(function(e){

        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
            if($('#memis_start_year').val() > 0){
                $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
            }
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
            $('#column_bar_tab, #stacked_column_tab').removeClass('text-success').addClass('disabled');
        }

        e.preventDefault();
    });

    $("#memis_period").change(function(e){
        
        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
            if($('#memis_period').val() > 0){
                $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
            }
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
            $('#column_bar_tab, #stacked_column_tab').removeClass('text-success').addClass('disabled');
        }

        e.preventDefault();

    });

    $("#memis_country").change(function(e){

        if($(this).val() > 0){
            $(this).addClass('bg bg-dark text-white font-weight-bold');
        }else{
            $(this).removeClass('bg bg-dark text-white font-weight-bold');
        }

        if($(this).val() == '999'){
            stacked_bar_exemption = 1;
            bar_main_title = 'Members by Country as of ' + moment().format("MMM DD, YYYY");
            $(this).parent().append('<div class="custom-control custom-radio main_label_country mt-2"> \
                <input type="radio" id="default_country" name="radio_default" class="custom-control-input" value="memis_country" checked> \
                <label class="custom-control-label" for="default_country">Set as default Y-axis label</label> \
                    <i class="far fa-question-circle text-muted" data-toggle="tooltip" data-placement="top" title="What is Y-axis Label?" onclick="chart_info(999)"></i> \
            </div>').fadeIn('slow');


        }else{
            $('.main_label_country').fadeOut('slow');
        }

        $('.chart_filter_alert').fadeOut('slow');
        var selections = [];
        var filter_counter = 0; // selected filters
        var all_filter_counter = 0; // selected filters with all/999 value

        var memis_start_year = $("#memis_start_year").val();
        var memis_end_year = $("#memis_end_year").val();
        var memis_period = $("#memis_period").val();
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function()
        {
            selections.push($(this).val());
        });
        
        for (let i = 0; i < selections.length; i++) {
            if (selections[i] === '999') all_filter_counter++;
        }

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }

        if(filter_counter == 1){
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled'); 
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');

                if(memis_start_year > 0 && memis_end_year > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
        
                if(memis_period > 0){
                    $('#column_bar_tab, #stacked_column_tab').removeClass('disabled').addClass('text-success');
                }
            }
        }else if(filter_counter == 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }else if(all_filter_counter > 1){
                $('#stacked_bar_tab').removeClass('disabled').addClass('text-success');
            }
        }else if(filter_counter > 2){     
            $('#graph_tab .nav-link').removeClass('text-success').addClass('disabled');
            if(all_filter_counter == 1){
                $('#basic_bar_tab, #pie_tab').removeClass('disabled').addClass('text-success');
            }
        }
        // console.log('ALL:' +all_filter_counter);
        // console.log('FILTER:' +filter_counter);

        e.preventDefault();
    });

    $(document).on('click','.main_label_country',function(){
        stacked_bar_y = [];

            $("#memis_country option").each(function() {
                if($(this).val() > 0 && $(this).val() != 999){
                    stacked_bar_y.push($(this).text());

                }
            });
    });

    $("#select_all_structure").change(function() {
        if (this.checked) {
            $("input[name='table_structure[]']").each(function() {
                this.checked=true;
            });
        } else {
            $("input[name='table_structure[]']").each(function() {
                this.checked=false;
            });
        }
    });

    $("#select_all_data").change(function() {
        if (this.checked) {
            $("input[name='table_data[]']").each(function() {
                this.checked=true;
            });
        } else {
            $("input[name='table_data[]']").each(function() {
                this.checked=false;
            });
        }
    });

    $('#sd_table').hide();

    $('#quick_export').change(function(){

        $('#sd_table').hide();
    });

    $('#custom_export').change(function(){

        $('#sd_table').show();
    });

    $('#import_file').change(function(){
        $('.custom-file-label').text($(this).val().split('\\').pop());
    });

    $("#import_db_form").validate({
        debug: true,
        errorClass: 'text-danger',
        rules: {
            import_file: {
                required: true,
            },
        },
        errorLabelContainer: '.invalid-feedback',
        submitHandler: function() {

             // Get the selected file
            var files = $('#import_file')[0].files;
            var fd = new FormData();

            // Append data 
            fd.append('file',files[0]);

            $.ajax({
                type: "POST",
                url: APP_URL + '/backup/import',
                data: fd,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    
                    
                        if(response == 1){
                            $('#success_import').hide().append('<div class="alert alert-success" role="alert"> \
                                SQL file imported successfully! \
                            </div>').fadeIn(1000);
                        }
                }
            });
        }
    });

    $('#password').keyup(function(){
        $('#result').html(checkStrength($('#password').val()))
    })  

    $('#resend_login_otp').click(function(){
        var crypted_email = window.location.pathname.split("/").pop();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            method: 'POST',
            url: APP_URL + '/resend-login-otp' ,
            data: { 'email' : crypted_email },
            async: false,
            success: function (response) {
            
            location.reload();

            }
        });
    });

    $('#resend_reset_otp').click(function(){
        var crypted_email = window.location.pathname.split("/").pop();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            method: 'POST',
            url: APP_URL + '/resend-reset-otp' ,
            data: { 'email' : crypted_email },
            async: false,
            success: function (response) {
            
            location.reload();

            }
        });
    });

    $(document).on('change', '#awards_per_year', function(){
        $('#member_table').DataTable().search($(this).val()).draw();
    });

    $(document).on('change', '#awards_per_division', function(){
        $('#member_table').DataTable().search($(this).val()).draw();
    });

    $('#memis_csf').click(function(){

        if ($.fn.DataTable.isDataTable("#csf_table")) {
            $('#csf_table').DataTable().clear().destroy();
        }


        $('#csf_graph_modal').modal('toggle');
        $('#csf_graph_modal .modal-title').text('MemIS : Customer Service Feedback');

        var memis_labels = [];
        var memis_bgcolors = [];
        var total = 0;
        var title = 'CSF Stacked Bar Graph';
        var sub_title = '';

        $.ajax({
            method: 'GET',
            url: APP_URL + '/csf_quest',
            async: false,
            datatype: 'json',
            success: function (response) {
                $.each(response, function(key, val){
                    stacked_bar_y.push(val.svc_fdbk_q);
                });
            }
        });

        $.ajax({
            method: 'GET',
            url: APP_URL + '/memis/basic/csf',
            async: false,
            datatype: 'json',
            success: function (response) {
                if(response.length > 0){
                    
                    var i = 0 ;
                    for(i; i < response.length;i++){
                        $.each(response[i], function(key, val){   
                                    
                            var memis_total = [];
                            $.each(val, function(k,v){
                                if(v.length > 0){

                                    $.each(v, function(x, y){
                                        memis_total.push(y.total);
                                        total += y.total;
                                    });
                                    memis_labels.push({
                                        name:k,
                                        data:memis_total
                                    })
                                    
                                    memis_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
                                }
                            });
                        });
                    }
                }else{
                   
                }
            }
        });

        Highcharts.chart('csf_chart', {
            chart: {
              type: 'bar'
            },
            title: {
              text: title
            },
            xAxis: {
              categories: stacked_bar_y
            },
            yAxis: {
              min: 0,
              title: {
                text: sub_title,
                align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            legend: {
              reversed: true
            },
            plotOptions: {
              series: {
                stacking: 'normal',
                pointWidth: '30',
              },
            },
            colors :  memis_bgcolors,
            series: memis_labels
        });

        $.ajax({
            method: 'GET',
            url: APP_URL + '/csf_list',
            async: false,
            datatype: 'json',
            success: function (response) {
                $.each(response, function(key, val){
                    var body = '<tr> \
                                    <td></td> \
                                    <td>' + val.pp_last_name + ', ' + val.pp_first_name + '</td> \
                                    <td>' + val.pp_email + '</td> \
                                    <td>' + val.sex + '</td> \
                                    <td>' + val.date_created + '</td> \
                                </tr>';
                                // <td><button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="view_csf(' + val.svc_fdbk_usr_id + ')">View Feedback</button></td> \
                                $('#csf_table tbody').append(body);
                });
            }
        });

        var t = $('#csf_table').DataTable({
            mark: true,
            "columnDefs": [ {
                "searchable": false,
                "orderable": false,
                "targets": 0
            } ],
            "order": [[ 1, 'asc' ]]
        } );
     
        t.on( 'order.dt search.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();

    });

});


// FUNCTIONS

// graphs
function basic_graph_memis(id,title){

    chart_numbers = 1;
    $('#chart_numbers').prop('checked', true); // Unchecks it
    $('#chart_numbers').change(); // Unchecks it
    chart_orientation = 1;
    $('#chart_orientation').prop('checked', true); // Unchecks it
    $('#chart_orientation').change(); // Unchecks it
    chart_rendered = 1;

    $('#basic_graph_modal').modal('toggle');
    $('#basic_graph_modal .modal-title').text(title);
    $('#basic_graph_modal .modal-body .list-group').empty();

    var app_url = ((id == 1) ? APP_URL + '/memis/basic/per_div' :
                  ((id == 2) ? APP_URL + '/memis/basic/per_reg' :
                  ((id == 3) ? APP_URL + '/memis/basic/per_cat' :
                  ((id == 4) ? APP_URL + '/memis/basic/per_stat' : APP_URL + '/memis/basic/per_sex'))));
    
                  
    var title = ((id == 1) ? 'Members Per Division' :
                ((id == 2) ? 'Members Per Region' :
                ((id == 3) ? 'Members Per Category' :
                ((id == 4) ? 'Members Per Status' : 'Members Per Sex'))));

                
        var memis_labels = [];
        var memis_total = [];
        var memis_bgcolors = [];
        var memis_titles = [];
        var total = 0;

        $.ajax({
            method: 'GET',
            url: app_url,
            async: false,
            datatype: 'json',
            success: function (response) {

                $.each(response.labels, function(key ,val){
                    memis_labels.push(val);
                });

                $.each(response.values, function(key ,val){
                    memis_total.push(val);
                    total += val;
                });
                        // console.log(total);
                $.each(response.colors, function(key ,val){
                    memis_bgcolors.push('#800000');
                });

                $.each(response.titles, function(key ,val){
                    memis_titles.push(val);

                });

                bar_total = total;
            }
        });

        if(id == 2){
            // discrepancies
            $.ajax({
                method: 'GET',
                url: APP_URL + '/get_disc',
                async: false,
                datatype: 'json',
                success: function (response) {
                    var abr = response['ABROAD'].length;
                    var noreg = response['NO_REGION'].length;
                    var nostat = response['NO_STATUS'].length;
    
                    $('#basic_graph_modal .modal-body').append('<ul class="list-group list-group-horizontal"> \
                    <li class="list-group-item d-flex justify-content-between align-items-center w-100 font-weight-bold">Abroad <span class="badge badge-danger">' + abr +'</span></li> \
                    <li class="list-group-item d-flex justify-content-between align-items-center w-100 font-weight-bold">No Region <span class="badge badge-danger">' + noreg + '</span></li> \
                    <li class="list-group-item d-flex justify-content-between align-items-center w-100 font-weight-bold">No Status <span class="badge badge-danger">' + nostat +'</span></li> \
                  </ul>');
    
                }
            });
    
        }
        exeChart = new Highcharts.chart('basic_bar_chart', {
            chart: {
                type: 'bar',
                events: {
                  load: function() {
                    var chart = this,
                      barsLength = chart.series[0].data.length;
            
                    chart.update({
                      chart: {
                        height: 100 + (50 * barsLength)
                      }
                    }, true, false, false);
                  }
                }
            },
            title: {
                text: title,
            },
            subtitle: {
                text: 'Source: http://execom.nrcp.dost.gov.ph/'
            },
            xAxis: {
                categories: memis_labels,
                title: {
                    text: null
                },
                labels: {
                    style: {
                        fontSize:'14px'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total (' + total + ')',
                    align: 'high'
                },
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                },
                series: {
                    colorByPoint: true,
                    colors: memis_bgcolors,
                    pointWidth: '30',
                    point: {
                        events: {
                            click: function () {
                                members(id, parseInt(this.index) + 1, memis_titles[parseInt(this.index)]);
                            }
                        }
                    }
                }
            },
            legend: {
                layout: 'vertical',
                x: -40,
                y: 80,
                floating: true,
                borderWidth: 1,
                shadow: true    
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Members',
                data: memis_total,
            }]
        });
}

function basic_graph_bris(id,title){
    
            
    chart_numbers = 1;
    $('#chart_numbers').prop('checked', true); // Unchecks it
    $('#chart_numbers').change(); // Unchecks it
    chart_orientation = 1;
    $('#chart_orientation').prop('checked', true); // Unchecks it
    $('#chart_orientation').change(); // Unchecks it
    chart_rendered = 1;

    $('#basic_graph_modal').modal('toggle');
    $('#basic_graph_modal .modal-title').text(title);

    var app_url = ((id == 1) ? APP_URL + '/bris/basic/per_proj' :
                  ((id == 2) ? APP_URL + '/bris/basic/per_nibr' :
                  ((id == 3) ? APP_URL + '/bris/basic/per_prior' :
                  ((id == 4) ? APP_URL + '/bris/basic/per_prog' : APP_URL + '/bris/basic/per_sex'))));
    
                  
    var title = ((id == 1) ? 'Projects Per Status ' :
                ((id == 2) ? 'Nibras' :
                ((id == 3) ? 'Dost Agendas' :
                ((id == 4) ? 'Programs Per Status' : 'Members Per Sex'))));

        var bris_labels = [];
        var bris_total = [];
        var bris_bgcolors = [];
        var bris_titles = [];
        var total = 0;

        $.ajax({
            method: 'GET',
            url: app_url,
            async: false,
            datatype: 'json',
            success: function (response) {

                $.each(response.labels, function(key ,val){
                    bris_labels.push(val);
                });

                $.each(response.values, function(key ,val){
                    bris_total.push(val);
                    total += val;
                });

                $.each(response.colors, function(key ,val){
                    bris_bgcolors.push('#EDA803');

                });

                $.each(response.titles, function(key ,val){
                    bris_titles.push(val);

                });

                
                bar_total = total;
            }
        });

        exeChart = new Highcharts.chart('basic_bar_chart', {
            chart: {
                type: 'bar',
                events: {
                  load: function() {
                    var chart = this,
                      barsLength = chart.series[0].data.length;
            
                    chart.update({
                      chart: {
                        height: 100 + (50 * barsLength)
                      }
                    }, true, false, false);
                  }
                }
            },
            title: {
                text: title,
            },
            subtitle: {
                text: 'Source: http://execom.nrcp.dost.gov.ph/'
            },
            xAxis: {
                categories: bris_labels,
                title: {
                    text: null
                },
                labels: {
                    style: {
                        fontSize:'14px'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total (' + total + ')',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                },
                series: {
                    colorByPoint: true,
                    colors: bris_bgcolors,
                    pointWidth: '30',
                    point: {
                        events: {
                            click: function () {
                                if(id == 1){
                                    bris_project(parseInt(this.index) + 1, bris_titles[parseInt(this.index)]);
                                }else if(id == 2){
                                    bris_nibra(parseInt(this.index) + 1, bris_titles[parseInt(this.index)]);
                                }else if(id == 3){
                                    bris_agenda(parseInt(this.index) + 1, bris_titles[parseInt(this.index)]);
                                }else{
                                    bris_program(parseInt(this.index) + 1, bris_titles[parseInt(this.index)]);
                                }
                            }
                        }
                    }
                }
            },
            legend: {
                layout: 'vertical',
                x: -40,
                y: 80,
                floating: true,
                borderWidth: 1,
                shadow: true    
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Projects',
                data: bris_total,
            }]
        });
}

function ejournal(val, title, config = null){

    if ($.fn.DataTable.isDataTable("#ejournal_table")) {
        $('#ejournal_table').DataTable().clear().destroy();
    }

    $modal = (config == 'modal-sm') ? 'modal-sm' : (config == '') ? '' : 'modal-lg';
    $remove_modal = (config == 'modal-sm') ? 'modal-lg' : (config == '') ? 'modal-sm modal-lg' : 'modal-sm';
    $('#ejournal_modal .modal-dialog').removeClass($remove_modal).addClass($modal);   
    $('#ejournal_modal').modal('toggle');
    $('#ejournal_modal .modal-title').text(title);
    $('#ejournal_table thead').empty();
    $('#ejournal_table tfoot').empty();
    $('#ejournal_table tbody').empty();


    if(val == 1){
        $.ajax({
            method: 'GET',
            url: APP_URL + '/ej/pa',
            async: false,
            success: function (response) {
            
                $('#ejournal_table thead').append('<tr><th></th><th>Title</th><th>Main Author</th></tr>');
                $('#ejournal_table tfoot').append('<tr><th></th><th>Title</th><th>Main Author</th></tr>');
                $.each(response, function(key, val){
                    $('#ejournal_table tbody').append('<tr><td></td><td>'+val.art_title+'</td><td>'+val.art_author+'</td></tr>');
                });

            }
        });
    }else if(val == 2){
        $.ajax({
            method: 'GET',
            url: APP_URL + '/ej/ca',
            async: false,
            success: function (response) {
                $('#ejournal_table thead').append('<tr><th></th><th>Title</th><th>Main Author</th><th>No. of citations</th></tr>');
                $('#ejournal_table tfoot').append('<tr><th></th><th>Title</th><th>Main Author</th><th>No. of citations</th></tr>');
                $.each(response, function(key, val){
                    $('#ejournal_table tbody').append('<tr><td></td><td>'+val.art_title+'</td><td>'+val.art_author+'</td><td>'+val.total+'</td></tr>');
                });

            }
        });
    }else if(val == 3){
        $.ajax({
            method: 'GET',
            url: APP_URL + '/ej/va',
            async: false,
            success: function (response) {
                $('#ejournal_table thead').append('<tr><th></th><th>Title</th><th>Main Author</th><th>No. of views</th></tr>');
                $('#ejournal_table tfoot').append('<tr><th></th><th>Title</th><th>Main Author</th><th>No. of views</th></tr>');
                $.each(response, function(key, val){
                    $('#ejournal_table tbody').append('<tr><td></td><td>'+val.art_title+'</td><td>'+val.art_author+'</td><td>'+val.total+'</td></tr>');
                });

            }
        });
    }else if(val == 4){
        $.ajax({
            method: 'GET',
            url: APP_URL + '/ej/da',
            async: false,
            success: function (response) {
                $('#ejournal_table thead').append('<tr><th></th><th>Title</th><th>Main Author</th><th>No. of downloads</th></tr>');
                $('#ejournal_table tfoot').append('<tr><th></th><th>Title</th><th>Main Author</th><th>No. of downloads</th></tr>');
                $.each(response, function(key, val){
                    $('#ejournal_table tbody').append('<tr><td></td><td>'+val.art_title+'</td><td>'+val.art_author+'</td><td>'+val.total+'</td></tr>');
                });

            }
        });
    }else if(val == 5){
        $.ajax({
            method: 'GET',
            url: APP_URL + '/ej/mst',
            async: false,
            success: function (response) {
                var html = '';
                $('#ejournal_table thead').append('<tr><th></th><th>Keyword/Topic</th><th>Frequency</th></tr>');
                $('#ejournal_table tfoot').append('<tr><th></th><th>Keyword/Topic</th><th>Frequency</th></tr>');
                $.each(response, function(key, val){
                    html += '<tr><td></td> \
                                 <td>' + val.topic + '</td> \
                                 <td>' + val.frequency + '</td> \
                             </tr>';

                    
                });
                $('#ejournal_table tbody').append(html);

            }
        });
    }else if(val == 6){
        $.ajax({
            method: 'GET',
            url: APP_URL + '/ej/mtc',
            async: false,
            success: function (response) {
                $('#ejournal_table thead').append('<tr><th></th><th>Cliet Name</th><th>Affiliation</th><th>Email</th></tr>');
                $('#ejournal_table tfoot').append('<tr><th></th><th>Cliet Name</th><th>Affiliation</th><th>Email</th></tr>');
                $.each(response, function(key, val){
                    $('#ejournal_table tbody').append('<tr><td></td><td>'+val.clt_name+'</td> \
                                                                <td>'+val.clt_affiliation+'</td> \
                                                                <td>'+val.clt_email+'</td></tr>');
                });

            }
        });
    }else{
        $.ajax({
            method: 'GET',
            url: APP_URL + '/ej/vo',
            async: false,
            success: function (response) {
                $('#ejournal_table thead').append('<tr><th></th><th>Location</th></tr>');
                $('#ejournal_table tfoot').append('<tr><th></th><th>Location</th></tr>');
                $.each(response, function(key, val){
                    $('#ejournal_table tbody').append('<tr><td></td><td>'+val.vis_location+'</td></tr>');
                });

            }
        });
    }

    // $('#ejournal_table').DataTable();
    var order = (val == 5) ? 2 : 1;
    var by = (val == 5) ? 'desc' : 'asc';
  

    var t = $('#ejournal_table').DataTable({
        mark: true,
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
        "order": [[ order, by ]],
    } );
 
    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
}

function librarysys(id, title){  

    if ($.fn.DataTable.isDataTable("#library_table")) {
        $('#library_table').DataTable().clear().destroy();
    }

    $('#library_modal').modal('toggle');
    $('#library_modal .modal-title').text(title);
    $('#library_table thead').empty();
    $('#library_table tfoot').empty();
    $('#library_table tbody').empty();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        method: 'POST',
        url: APP_URL + '/lms/cat',
        data: { 'cat' : id },
        async: false,
        success: function (response) {
            var html = '';
            var head = '<tr> \
                            <th></th> \
                            <th>Title</th> \
                            <th>Main Author</th> \
                            <th>Keywords</th> \
                            <th>Full Text PDF</th> \
                        </tr>';

            $('#library_table thead').append(head);
            $('#library_table tfoot').append(head);

            $.each(response, function(key, val){
                // var href = APP_URL + '/lms/download_file/' + val.art_id;
                var href = APP_URL + '/lms/view_pdf/' + val.art_id;
                var view = (val.art_full_text !== '') ? '<a href="' + href + '" target="_blank" class="btn btn-outline-secondary">View</a>' : 'Unavailable';
                // var download = (val.art_full_text !== '') ? '<a href="' + href + '" class="btn btn-outline-secondary">Download</a>' : 'Unavailable';
                var author = (val.art_author == '') ? 'NA' : val.art_author;
                html += '<tr> \
                        <td></td> \
                        <td>' + val.art_title + '</td> \
                        <td>' + author + '</td> \
                        <td>' + val.art_keywords + '</td> \
                        <td>' + view +  '</td> \
                        </tr>';     
            });

            
    
    $('#library_table tbody').append(html);
        }
    });

   

    var t = $('#library_table').DataTable({
        mark: true,
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
        "order": [[ 1, 'asc' ]]
    } );
 
    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
}

function members(filter, id, title){

    if ($.fn.DataTable.isDataTable("#member_table")) {
        $('#member_table').DataTable().clear().destroy();
    }


    $('#member_modal .modal-title').text(title);
    $('#member_modal').modal('toggle');
    $('#member_table thead').empty();
    $('#member_table tfoot').empty();
    $('#member_table tbody').empty();
    // $('#member_modal .modal-body span').empty();

    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    var add_region_header = '';
    var add_awards_header = '';
    var add_gb_header = '';
    var add_default_header = '';
    var add_division_header = '';
    // var awa_per_year = [];
    // var awa_per_div = [];

    if(filter == 1){
        var _url = '/memis/div';
        add_default_header = '<th>Contact</th><th>Email</th>';
    }else if(filter == 2){
        var _url = '/memis/reg';
        add_region_header = '<th>Province</th><th>Town/City</th>';
    }else if(filter == 3){
        var _url = '/memis/cat';
        add_default_header = '<th>Contact</th><th>Email</th>';
    }else if(filter == 4){
        var _url = '/memis/stat';
        add_default_header = '<th>Contact</th><th>Email</th>';
    }else if(filter == 5){
        var _url = '/memis/sex';
        add_default_header= '<th>Contact</th><th>Email</th>';
    }else if(filter == 6){
        var _url = '/memis/all';
        add_default_header = '<th>Contact</th><th>Email</th>';
    }else if(filter == 7){
        var _url = '/memis/awa';
        add_awards_header = '<th>Division</th><th>Year</th><th>Awards</th><th>Citation</th>';
        // $('#member_modal .modal-body span').prepend('<div class="row mb-3"> \
        //                                                 <div class="col-4"> \
        //                                                     Year: <select class="form-control" id="awards_per_year"><option value="">All</option></select> \
        //                                                 </div> \
        //                                                 <div class="col-4"> \
        //                                                     Division: <select class="form-control" id="awards_per_division"><option value="">All</option></select> \
        //                                                 </div> \
        //                                             </div>');
    }else{
        var _url = '/memis/gb';
        add_gb_header = '<th>Year Covered</th><th>Remarks</th>';
        add_division_header = (id == 0) ? '<th>Division</th>' : '';
    }



    $.ajax({
        method: 'POST',
        url: APP_URL + _url,
        data: { 'id' : id },
        async: false,
        success: function (response) {

            
            var head = '<tr><th></th><th> Title </th><th> Last Name </th> \
            <th> First Name </th> \
            <th> Middle Name </th> \
            <th> Sex </th> \
            ' + add_default_header + ' \
            ' + add_awards_header + ' \
            ' + add_region_header + ' \
            ' + add_division_header + ' \
            ' + add_gb_header + ' \
            </tr>';

            $('#member_table thead').append(head);
            $('#member_table tfoot').append(head);

            var body = '';  var uni_year = [];
            $.each(response, function(key, val){

                var present  = (val.ph_to == 'Present') ? 'Present' : moment(val.ph_to).format("MMM DD, YYYY");
                var add_region_field = (filter == 2) ? '<td>' + val.PROVINCE +'</td><td>' + val.CITY +'</td>' : '';

                var add_awards_field =  (filter == 7) ? '<td> Division ' + val.div_number +'</td> \
                                                         <td>' + val.awa_year +'</td> \
                                                         <td>' + val.awa_title +' | '+ val.awa_giving_body+'</td> \
                                                         <td>' + ((val.awa_citation == '' ) ? 'Not available' : val.awa_citation) + '</td>' 
                                                         : '';

                var add_gb_field = (filter == 8) ? '<td>' + moment(val.ph_from).format("MMM DD, YYYY") + ' - ' + present + '</td> \
                                                   <td>' + val.ph_remarks + '</td>' : '';
                var add_default_field = (add_default_header != '') ? '<td>' + val.pp_contact + '</td><td>' + val.pp_email + '</td>' : '';
                var add_division_field = (add_division_header != '') ? '<td>' + 'Division ' + val.div_number + '</td>' : '';
                
                // if(filter == 7){
                //     awa_per_year.push(val.awa_year);
                // }

                body += '<tr><td></td> \
                            <td>' + val.TITLE + '</td> \
                            <td>' + val.pp_last_name + '</td> \
                            <td>' + val.pp_first_name + '</td> \
                            <td>' + val.pp_middle_name + '</td> \
                            <td>' + val.sex + '</td> \
                            ' + add_default_field + '\
                            ' + add_awards_field + '\
                            ' + add_region_field + '\
                            ' + add_division_field + '\
                            ' + add_gb_field + '\
                        </tr>';
                
            });

            // if(filter == 7){
                
            //     var sort_uni_year = $.unique(awa_per_year.sort(function(a, b){return b-a}));
            //     $.each(sort_uni_year, function(key, val){
            //         $('#awards_per_year').append('<option value="' + val + '">' + val + '</option>');
            //     });
                
            //     $.ajax({
            //         method: 'GET',
            //         url: APP_URL + '/memis_divs',
            //         async: false,
            //         success: function (response) {
            //             $.each(response, function(key, val){
            //                 $('#awards_per_division').append('<option>Division ' + val.div_number + '</option>');
            //             });
            //         }
            //     });
            // }
            

            $('#member_table tbody').append(body);

            if(filter == 7){
                
                var t = $('#member_table').DataTable({
                    
                    mark: true,
                    "columnDefs": [ {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    } ],
                    
                    initComplete: function () {
                        this.api().columns([6,7]).every(function () {
                        
                            var column = this;
                            var header = $('<select><option value="">' + column.header().textContent + '</option></select>')
                                .appendTo($(column.header()).empty())
                                .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
                    
                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                                });
                    
                            column.data().unique().sort().each(function (d, j) {
                                header.append('<option value="' + d + '">' + d + '</option>')
                            });

                            
                            var footer = $('<select><option value="">' + column.footer().textContent + '</option></select>')
                                .appendTo($(column.footer()).empty())
                                .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
                    
                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                                });
                    
                            column.data().unique().sort().each(function (d, j) {
                                footer.append('<option value="' + d + '">' + d + '</option>')
                            });
                        });
                    }
                } );

            }else{
                
                var t = $('#member_table').DataTable({
                    
                    // dom: 'Bfrtip',
                    // buttons: [
                    //     'csvHtml5',
                    // ],
                    mark: true,
                    "columnDefs": [ {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    } ],
                    
                });

            }
         
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();

        }
    });
}

function nrcpnet(filter, title){

    if ($.fn.DataTable.isDataTable("#nrcpnet_table")) {
        $('#nrcpnet_table').DataTable().clear().destroy();
    }

    $('#nrcpnet_modal .modal-title').text(title);
    $('#nrcpnet_modal').modal('toggle');
    $('#nrcpnet_table thead').empty();
    $('#nrcpnet_table tfoot').empty();
    $('#nrcpnet_table tbody').empty();

    if(filter == 1){
        var _url = '/nrcpnet/plant';
    }else if(filter == 2){
        var _url = '/nrcpnet/cont';
    }else if(filter == 3){
        var _url = '/nrcpnet/jo';
    }else{
        var _url = '/nrcpnet/vac';
    }   
        
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + _url,
        async: false,
        success: function (response) {
            var head = '<tr><th></th>\
            <th>Last Name</th> \
            <th>First Name</th> \
            <th>Middle Name</th> \
            <th>Appointment</th> \
            </tr>';

            $('#nrcpnet_table thead').append(head);
            $('#nrcpnet_table tfoot').append(head);

            var body = '';
            $.each(response, function(key, val){
                body += '<tr><td></td><td>' + val.plant_surname+ '</td> \
                        <td>' + val.plant_firstname + '</td> \
                        <td>' + val.plant_middlename + '</td> \
                        <td>' + val.plant_appointment + '</td> \
                        </tr>';
                
            });

            $('#nrcpnet_table tbody').append(body);

      

            var t = $('#nrcpnet_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
         
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
    
}

function bris_project(filter, title){

    if ($.fn.DataTable.isDataTable("#bris_table")) {
        $('#bris_table').DataTable().clear().destroy();
    }


    $('#bris_modal .modal-title').text(title);
    $('#bris_modal').modal('toggle');
    $('#bris_table thead').empty();
    $('#bris_table tfoot').empty();
    $('#bris_table tbody').empty();


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/bris/gps',
        data: {'status' : filter},
        async: false,
        success: function (response) {
            // console.log(response);
            var head = '<tr><th></th> \
                        <th>Project Title </th> \
                            <th>Project Leader</th> \
                            <th>Status</th> \
                            <th>Duration</th> \
                            <th>Date submitted</th> \
                        </tr>';

            $('#bris_table thead').append(head);
            $('#bris_table tfoot').append(head);

            var body = '';
            $.each(response, function(key, val){

                var proponent = (val.proponent == null || val.proponent == 0) ? '-' : coordinator[val.proponent];
                var status = (val.status == null) ? '-' : val.status;
                var duration = (val.prd_duration == null || val.prd_duration == 0) ? '-' : val.prd_duration;

                body += '<tr><td></td>\
                         <td>' + val.prd_title + '</td> \
                         <td>' + proponent + '</td> \
                         <td>' + status + '</td> \
                         <td>' + duration + '</td> \
                         <td>' + moment(val.date_created).format("MMM DD, YYYY") +'</td> \
                         </tr>';
                        
                
            });

            $('#bris_table tbody').append(body);
            // $('#bris_table').DataTable();

       
            var t = $('#bris_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
         
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
            
        }
    });

}

function bris_nibra(filter, title){

    if ($.fn.DataTable.isDataTable("#bris_table")) {
        $('#bris_table').DataTable().clear().destroy();
    }

    $('#bris_modal .modal-title').text(title);
    $('#bris_modal').modal('toggle');
    $('#bris_table thead').empty();
    $('#bris_table tbody').empty();


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/bris/gbi',
        data: {'id' : filter},
        async: false,
        success: function (response) {
            // console.log(response);
            var head = '<tr><th></th> \
                        <th>Project Title</th> \
                        <th>Project Leader</th> \
                        <th>Status</th> \
                        <th>Date submitted</th> \
                        </tr>';

            $('#bris_table thead').append(head);

            var body = '';
            $.each(response, function(key, val){

                var proponent = (val.proponent == null || val.proponent == 0) ? '-' : coordinator[val.proponent];
                var status = (val.status == null) ? '-' : val.status;

                body += '<tr><td></td> \
                         <td>' + val.prd_title + '</td> \
                         <td>' + proponent + '</td> \
                         <td>' + status + '</td> \
                         <td>' + moment(val.date_created).format("MMM DD, YYYY") +'</td> \
                         </tr>';
                        
                
            });

            $('#bris_table tbody').append(body);
            // $('#bris_table').DataTable();
          

            var t = $('#bris_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
         
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
}

function bris_agenda(filter, title){

    if ($.fn.DataTable.isDataTable("#bris_table")) {
        $('#bris_table').DataTable().clear().destroy();
    }

    $('#bris_modal .modal-title').text(title);
    $('#bris_modal').modal('toggle');
    $('#bris_table thead').empty();
    $('#bris_table tbody').empty();


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/bris/age',
        data: {'id' : filter},
        async: false,
        success: function (response) {
            // console.log(response);
            var head = '<tr><th></th> \
                        <th>Project Title</th> \
                        <th>Project Leader</th> \
                        <th>Status</th> \
                        <th>Date submitted</th> \
                        </tr>';

            $('#bris_table thead').append(head);

            var body = '';
            $.each(response, function(key, val){
                
                var proponent = (val.proponent == null || val.proponent == 0) ? '-' : coordinator[val.proponent];
                var status = (val.status == null) ? '-' : val.status;

                body += '<tr><td></td>\
                         <td>' + val.prd_title + '</td> \
                         <td>' + proponent + '</td> \
                         <td>' + status + '</td> \
                         <td>' + moment(val.date_created).format("MMM DD, YYYY") +'</td> \
                         </tr>';
            });

            $('#bris_table tbody').append(body);
            // $('#bris_table').DataTable();
          

            var t = $('#bris_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
         
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
}

function bris_program(filter, title){
    
    if ($.fn.DataTable.isDataTable("#bris_table")) {
        $('#bris_table').DataTable().clear().destroy();
    }


    $('#bris_modal .modal-title').text(title);
    $('#bris_modal').modal('toggle');
    $('#bris_table thead').empty();
    $('#bris_table tbody').empty();


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/bris/prg',
        data: {'status' : filter},
        async: false,
        success: function (response) {
            var head = '<tr><th></th> \
                        <th>Project Title</th> \
                        <th>Program Manager</th> \
                        <th>Status</th> \
                        <th>Duration</th> \
                        <th>Date submitted</th> \
                        </tr>';

            $('#bris_table thead').append(head);

            var body = '';
            $.each(response, function(key, val){
               
                var proponent = (val.proponent == null || val.proponent == 0) ? '-' : coordinator[val.proponent];
                var status = (val.status == null) ? '-' : val.status;
                var duration = (val.prg_duration == null || val.prg_duration == 0) ? '-' : val.prg_duration;

                body += '<tr><td></td>\
                         <td>' + val.prg_title + '</td> \
                         <td>' + proponent + '</td> \
                         <td>' + status + '</td> \
                         <td>' + duration + '</td> \
                         <td>' + moment(val.date_created).format("MMM DD, YYYY") +'</td> \
                         </tr>';
                        
                
            });

            $('#bris_table tbody').append(body);
            // $('#bris_table').DataTable();
           
            var t = $('#bris_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
         
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
}

function execom_users(){

    if ($.fn.DataTable.isDataTable("#execom_table")) {
        $('#execom_table').DataTable().clear().destroy();
    }
    
    $('#execom_table tbody').empty();

    $.ajax({
        method: 'GET',
        url: APP_URL + '/execom_users',
        async: false,
        success: function (response) {
            var body = '';
            $.each(response, function(key, val){
                body += '<tr><td></td><td>'+ val.email +'</td>'+
                        '<td><button class="btn btn-sm btn-danger" onclick="remove_user(\''+ val.user_id +'\')">Remove</button></td></tr>';
            });

            $('#execom_table tbody').append(body);
            
            var t = $('#execom_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
            
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });

}

function all_users(){

    
    $('#create_new_form ._alert').remove();
    
    var exists = [];

    // execom users

    if ($.fn.DataTable.isDataTable("#execom_table")) {
        $('#execom_table').DataTable().clear().destroy();
    }
    
    $('#execom_table tbody').empty();

    $.ajax({
        method: 'GET',
        url: APP_URL + '/execom_users',
        async: false,
        success: function (response) {
            var body = '';
            $.each(response, function(key, val){
                var role = (val.role == 1) ? 'Superadmin' : 'Admin';
                exists.push(val.email);
                body += '<tr> \
                            <td></td> \
                            <td>' + val.name + '</td> \
                            <td>' + val.email + '</td> \
                            <td>' + role + '</td> \
                            <td><button class="btn btn-sm btn-danger" onclick="remove_user(\''+ val.user_id +'\')">Remove</button></td></tr>';
            });

            $('#execom_table tbody').append(body);
            
            var t = $('#execom_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
            
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });

   
    // skms users
    if ($.fn.DataTable.isDataTable("#skms_table")) {
        $('#skms_table').DataTable().clear().destroy();
    }
    
    $('#skms_table tbody').empty();

    $.ajax({
        method: 'GET',
        url: APP_URL + '/skms_users',
        async: false,
        success: function (response) {

            var body = '';
            $.each(response, function(key, val){

                if(exists.includes(val.usr_name)){}else{

                    body += '<tr><td></td><td>'+ val.usr_name +'</td>'+
                    '<td><button class="btn btn-sm btn-success" onclick="add_user(\''+ val.usr_id +'\')">Add</button></td></tr>';

                }
                   
            });

            $('#skms_table tbody').append(body);
           
            var t = $('#skms_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
         
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });

  


    
}

function add_user(id){


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/execom/add',
        data: { 'id' : id },
        async: false,
        success: function (response) {
            // console.log(response);
        }
    });
}

function activity_logs(){

    
    if ($.fn.DataTable.isDataTable("#logs_table")) {
        $('#logs_table').DataTable().clear().destroy();
    }

    $.ajax({
        method: 'GET',
        url: APP_URL + '/execom/logs',
        async: false,
        success: function (response) {
        
            var html = '';

            $.each(response, function(key, val){
                html += '<tr> \
                            <td></td> \
                            <td>' + val.log_email + '</td> \
                            <td>' + val.log_description + '</td> \
                            <td>' + moment(val.created_at).format("MMM DD, YYYY, hh:mm a") +'</td> \
                            </tr>';
            });
        
            $('#logs_table').append(html);

            var t = $('#logs_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
            } );
         
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
}

function remove_user(id){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/execom/remove',
        data: { 'id' : id },
        async: false,
        success: function (response) {
            // console.log(response);
        }
    });
}


function show_overall(keyword){

    $('#overall_modal').modal('toggle');
    $('#quick_search_keyword').text(keyword);

    /**
     * function_name( [keyword] , [system value] , [filter/section value])
     * do not rearrange functions
     * total count of results will be affected
     */

    memis_specializations(keyword, '1', '1');
    memis_members(keyword, '1', '3');
    memis_awards(keyword, '1', '4');
    memis_gbs(keyword, '1', '5');


    bris_projects_ov(keyword, '2', '1');
    bris_programs_ov(keyword, '2', '2');

    ej_titles(keyword, '3', '1');
    ej_authors(keyword, '3', '2');

    lms_all(keyword, '4', '1');

    net_employees(keyword, '5' ,'1' );
    net_divisions(keyword, '5' ,'2' );

    
    $("#memis-tab span").remove();
    $("#bris-tab span").remove();
    $("#ejournal-tab span").remove();
    $("#lms-tab span").remove();
    $("#nrcpnet-tab span").remove();

    if(_global_memis > 0){ $('#memis-tab').append(' <span class="text-success fas fa-check-circle"></span>');
    }else{ $('#memis-tab').append(' <span class="text-danger fas fa-times-circle"></span>'); }

    if(_global_bris > 0){ $('#bris-tab').append(' <span class="text-success fas fa-check-circle"></span>');
    }else{ $('#bris-tab').append(' <span class="text-danger fas fa-times-circle"></span>'); }

    if(_global_ejournal > 0){ $('#ejournal-tab').append(' <span class="text-success fas fa-check-circle"></span>');
    }else{ $('#ejournal-tab').append(' <span class="text-danger fas fa-times-circle"></span>'); }

    if(_global_lms > 0){ $('#lms-tab').append(' <span class="text-success fas fa-check-circle"></span>');
    }else{ $('#lms-tab').append(' <span class="text-danger fas fa-times-circle"></span>'); }

    if(_global_nrcpnet > 0){ $('#nrcpnet-tab').append(' <span class="text-success fas fa-check-circle"></span>');
    }else{ $('#nrcpnet-tab').append(' <span class="text-danger fas fa-times-circle"></span>'); }

}

function net_employees(keyword, sys, section){

    if ($.fn.DataTable.isDataTable("#nrcpnet_employee_table")) {
        $('#nrcpnet_employee_table').DataTable().clear().destroy();
    }
    

    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/nrcpnet',
        async: false,
        data: { 'search' : search_arr },
        success: function (response){
            var html = '';
            _global_nrcpnet = response.length;

            $.each(response,function(key, val){
                html += '<tr> \
                        <td></td> \
                        <td>' + val.plant_surname + '</td> \
                        <td>' + val.plant_firstname + '</td> \
                        <td>' + val.plant_middlename + '</td> \
                        <td>' + val.plant_appointment + '</td> \
                        <td>' + val.plant_group + '</td> \
                        </tr>';
            });

            var cls = (response.length > 0) ? 'dark' : 'light';
            var results = (response.length > 0) ? response.length + ' result/s found' : 'No results found';
            var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';

           $('.net_employee_count').empty().append(badge);

           if(response.length > 0){
               var highlight = new RegExp(keyword, 'gi');
               var html = html.replace(highlight, '<strong><u>' + keyword + '</u></strong>');
           }

           $('#nrcpnet_employees table tbody').append(html);
           
            var t = $('#nrcpnet_employee_table').DataTable({
                    mark: true,
                    "columnDefs": [ {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    } ],
                    "order": [[ 1, 'asc' ]]
                } );
        
                t.on( 'order.dt search.dt', function () {
                    t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();
        }
    });
}

function net_divisions(keyword, sys, section){

    if ($.fn.DataTable.isDataTable("#nrcpnet_division_table")) {
        $('#nrcpnet_division_table').DataTable().clear().destroy();
    }
    
    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/nrcpnet',
        async: false,
        data: { 'search' : search_arr },
        success: function (response){
            var html = '';
            _global_nrcpnet += response.length;

            $.each(response,function(key, val){
                html += '<tr> \
                        <td></td> \
                        <td>' + val.plant_surname + '</td> \
                        <td>' + val.plant_firstname + '</td> \
                        <td>' + val.plant_middlename + '</td> \
                        <td>' + val.plant_appointment + '</td> \
                        <td>' + val.plant_group + '</td> \
                        </tr>';
            });

            var cls = (response.length > 0) ? 'dark' : 'light';
           var results = (response.length > 0) ? response.length + ' result/s found' : 'No results found';
           var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';

            $('.net_division_count').empty().append(badge);

            if(response.length > 0){
                var highlight = new RegExp(keyword, 'gi');
                var html = html.replace(highlight, '<strong><u>' + keyword + '</u></strong>');
            }
            
            $('#nrcpnet_divisions table tbody').empty().append(html);
            
           var t = $('#nrcpnet_division_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
     
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
}

function lms_all(keyword, sys, section){


    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/lms',
        async: false,
        data: { 'search' : search_arr },
        success: function (response){
            var html = '';
            var lms_length = [];
            var lms_data = [];
            var counter = 0;
            $.each(response , function(key, val){

                lms_length.push(val.length);
                lms_data.push(val);
                var sum = 0;

                $.each(lms_length,function(){sum+=parseFloat(this) || 0;});
                _global_lms = sum;
              
                var cls = (lms_length[counter] > 0) ? 'dark' : 'light';
                var results = (lms_length[counter] > 0) ? lms_length[counter] + ' result/s found' : 'No results found';
                var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';

               

                $('.lms_' + counter + '_count').empty().append(badge);

                $.each(val, function(k, v){

                    var href = APP_URL + '/lms/view_pdf/' + v.art_id;
                    var view = (v.art_full_text !== '') ? '<a href="' + href + '" target="_blank" class="btn btn-outline-secondary">View</a>' : 'Unavailable';

                    html += '<tr> \
                        <td></td> \
                        <td>' + v.art_title + '</td> \
                        <td>' + v.art_keywords +'</td> \
                        <td>' + moment(v.created_on).format("MMM DD, YYYY") + '</td> \
                        <td>' + view +'</td> \
                        </tr>';
                });

                if(lms_length[counter] > 0){
                    var highlight = new RegExp(keyword, 'gi');
                    var html = html.replace(highlight, '<strong><u><u>' + keyword + '</u></u></strong>');
                }
                
                $('#lms_' + counter + '_table tbody').empty().append(html);

                if ($.fn.DataTable.isDataTable('#lms_' + counter + '_table')) {
                    $('#lms_' + counter +'_table').DataTable().clear().destroy();
                }
    
               var t = $('#lms_' + counter + '_table').DataTable({
                    mark: true,
                    "columnDefs": [ {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    } ],
                    "order": [[ 1, 'asc' ]]
                } );
         
                t.on( 'order.dt search.dt', function () {
                    t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();

                counter++;
            });
        }

    });
}

function ej_titles(keyword, sys, section){

    if ($.fn.DataTable.isDataTable("#ejournal_title_table")) {
        $('#ejournal_title_table').DataTable().clear().destroy();
    }
    
    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/ejournal',
        async: false,
        data: { 'search' : search_arr },
        success: function (response){
           var html = '';
           _global_ejournal = response.length;
            
           $.each(response, function(key, val){
                html += '<tr> \
                         <td></td> \
                         <td>' + val.art_title +'</td> \
                         <td>' + val.art_author + '</td> \
                         <td>' + moment(val.date_created).format("MMM DD, YYYY") +'</td> \
                         </tr>';
           });

           var cls = (response.length > 0) ? 'dark' : 'light';
                var results = (response.length > 0) ? response.length + ' result/s found' : 'No results found';
                var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';

      
           $('.ej_title_count').empty().append(badge);

            if(response.length > 0){
                var highlight = new RegExp(keyword, 'gi');
                var html = html.replace(highlight, '<strong><u>' + keyword + '</u></strong>');
            }
            
           $('#ejournal_titles tbody').empty().append(html);
           
            var t = $('#ejournal_title_table').DataTable({
                    mark: true,
                    "columnDefs": [ {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    } ],
                    "order": [[ 1, 'asc' ]]
                } );
        
                t.on( 'order.dt search.dt', function () {
                    t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();
        }
    });
}

function ej_authors(keyword, sys, section){

    if ($.fn.DataTable.isDataTable("#ejournal_author_table")) {
        $('#ejournal_author_table').DataTable().clear().destroy();
    }

    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/ejournal',
        async: false,
        data: { 'search' : search_arr },
        success: function (response){
            var html;
            _global_ejournal += response.length;
            
           $.each(response, function(key, val){
                html += '<tr> \
                         <td></td> \
                         <td>' + val.art_title +'</td> \
                         <td>' + val.art_author + '</td> \
                         <td>' + moment(val.date_created).format("MMM DD, YYYY") +'</td> \
                         </tr>';
           });

           var cls = (response.length > 0) ? 'dark' : 'light';
                var results = (response.length > 0) ? response.length + ' result/s found' : 'No results found';
                var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';

           $('.ej_author_count').empty().append(badge);

           if(response.length > 0){
               var highlight = new RegExp(keyword, 'gi');
               var html = html.replace(highlight, '<strong><u>' + keyword + '</u></strong>');
           }
           
           $('#ejournal_authors tbody').empty().append(html);

            var t = $('#ejournal_author_table').DataTable({
                    mark: true,
                    "columnDefs": [ {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    } ],
                    "order": [[ 1, 'asc' ]]
                } );
        
                t.on( 'order.dt search.dt', function () {
                    t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();
        }
    });
}

function bris_projects_ov(keyword, sys, section){

    if ($.fn.DataTable.isDataTable("#bris_project_table")) {
        $('#bris_project_table').DataTable().clear().destroy();
    }

    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/bris',
        async: false,
        data: { 'search' : search_arr },
        success: function (response){
           var html;
           var proposal;
           var proposal_label;
           _global_bris = response.length;
            

           $.each(response, function(key, val){

            
            var proponent = (val.proponent == null || val.proponent == 0) ? '-' : coordinator[val.proponent];
            var status = (val.status == null) ? '-' : val.status;


            proposal = (val.prp == 1) ? 'text-muted' : '';
            proposal_label = (val.prp == 1) ? '<small><span class="badge badge-secondary">PROPOSAL</span></small>' : '';

            html += '<tr  class="' + proposal + '"> \
            <td></td> \
            <td>' + val.title + ' ' + proposal_label +'</td> \
            <td>' + proponent + '</td> \
            <td>' + status+'</td> \
            <td>' + moment(val.date_created).format("MMM DD, YYYY") +'</td> \
            </tr>';

           });

           var cls = (response.length > 0) ? 'dark' : 'light';
                var results = (response.length > 0) ? response.length + ' result/s found' : 'No results found';
                var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';

           $('.bris_proj_count').empty().append(badge);

           if(response.length > 0){
               var highlight = new RegExp(keyword, 'gi');
               var html = html.replace(highlight, '<strong><u>' + keyword + '</u></strong>');
           }
           
           $('#bris_projects tbody').empty().append(html);
           
            var t = $('#bris_project_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );

            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
}

function get_coordinator(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'GET',
        url: APP_URL + '/bris/coor',
        async: false,
        success: function (response){
            // console.log(response);
            $.each(response, function(key, val){
                // user = val.usr_name;
                coordinator[val.usr_id] = val.usr_name;
            });
        }
    });

    // console.log(coordinator);
    // return (user == undefined) ? '-' : user;
}

function bris_programs_ov(keyword, sys, section){

    if ($.fn.DataTable.isDataTable("#bris_program_table")) {
        $('#bris_program_table').DataTable().clear().destroy();
    }

    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/bris',
        async: false,
        data: { 'search' : search_arr },
        success: function (response) {
        
            var html;
            var proposal;
            _global_bris += response.length;
            

            $.each(response, function(key, val){
                var proponent = (val.proponent == null || val.proponent == 0) ? '-' : val.proponent;
                var status = (val.status == null) ? '-' : val.status;


                proposal = (val.prp == 1) ? 'text-muted' : '';
                proposal_label = (val.prp == 1) ? '<small><span class="badge badge-secondary">PROPOSAL</span></small>' : '';

                html += '<tr  class="' + proposal + '"> \
                <td></td> \
                <td>' + val.title + ' ' + proposal_label +'</td> \
                <td>' + proponent + '</td> \
                <td>' + status+'</td> \
                <td>' + moment(val.date_created).format("MMM DD, YYYY") +'</td> \
                </tr>';
           });

           var cls = (response.length > 0) ? 'dark' : 'light';
                var results = (response.length > 0) ? response.length + ' result/s found' : 'No results found';
                var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';


           $('.bris_prog_count').empty().append(badge);

           if(response.length > 0){
               var highlight = new RegExp(keyword, 'gi');
               var html = html.replace(highlight, '<strong><u>' + keyword + '</u></strong>');
           }
           
           $('#bris_programs tbody').empty().append(html);
           
            var t = $('#bris_program_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );

            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
}

function bris_proposals_ov(keyword, sys, section){
    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall',
        async: false,
        data: { 'search' : search_arr },
        success: function (response) {

            // console.log(response);

            // return false;
        //     var html;
            

        //    $.each(response, function(key, val){
        //         html += '<tr>'+
        //         // <td></td>'+
        //                 '<td>'+val.TITLE+'</td>'+
        //                 '<td>'+val.pp_last_name+'</td>'+
        //                 '<td>'+val.pp_first_name+'</td>'+
        //                 '<td>'+val.pp_middle_name+'</td>'+
        //                 '<td>'+val.pp_contact+'</td>'+
        //                 '<td>'+val.pp_email+'</td>'+
        //                 '<td>'+val.mpr_gen_specialization+'</td>'+
        //                 '</tr>';
        //    });

        //    $('.memis_spec_count').empty();
        //    $('#memis_specializations tbody').empty();

        //    var badge = (response.length > 0) ? 'primary' : 'secondary';
        //    $('.memis_spec_count').append('<span class="badge badge-' + badge + '">'+ response.length + ' matche(s)' + '</span>');
        //    $('#memis_specializations tbody').append(html);
        }
    });
}

function memis_specializations(keyword, sys, section){

    
    if ($.fn.DataTable.isDataTable("#memis_spec_table")) {
        $('#memis_spec_table').DataTable().clear().destroy();
    }

    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/memis/spec',
        async: false,
        data: { 'search' : search_arr },
        success: function (response) {
            _global_memis = response.length;
            var html;
            

           $.each(response, function(key, val){
               var region = (val.REGION == null) ? '-' : val.REGION;
               var province = (val.PROVINCE== null) ? '-' : val.PROVINCE;
               var city = (val.CITY == null) ? '-' : val.CITY;
                html += '<tr>\
                <td></td> \
                        <td>'+ val.TITLE+'</td>\
                        <td>'+ val.pp_last_name +'</td>\
                        <td>'+ val.pp_first_name +'</td>\
                        <td>'+ val.pp_middle_name +'</td>\
                        <td>'+ val.sex +'</td>\
                        <td>'+ val.pp_contact +'</td>\
                        <td>'+ val.pp_email +'</td>\
                        <td>'+ val.mpr_gen_specialization +'</td>\
                        <td>'+ region +'</td>\
                        <td>'+ province +'</td>\
                        <td>'+ city +'</td>\
                        </tr>';
           });


           var cls = (response.length > 0) ? 'dark' : 'light';
           var results = (response.length > 0) ? response.length + ' result/s found' : 'No results found';
           var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';


           $('.memis_spec_count').empty().append(badge);             

           if(response.length > 0){
               
               var highlight = new RegExp(keyword, 'gi');
               var html = html.replace(highlight, '<strong><u>' + keyword + '</u></strong>');
           }
           
           $('#memis_spec_table tbody').empty().append(html);

           var t = $('#memis_spec_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
     
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });

    
}

function memis_members(keyword, sys, section){

    if ($.fn.DataTable.isDataTable("#memis_members_table")) {
        $('#memis_members_table').DataTable().clear().destroy();
    }
    
    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/memis/memb',
        async: false,
        data: { 'search' : search_arr },
        success: function (response) {
           var html;
           _global_memis += response.length;

           $.each(response, function(key, val){
                html += '<tr> \
                <td></td> \
                        <td>' + val.TITLE + '</td> \
                        <td>' + val.pp_last_name + '</td> \
                        <td>' + val.pp_first_name + '</td> \
                        <td>' + val.pp_middle_name + '</td> \
                        <td>' + val.sex + '</td>\
                        <td>' + val.pp_contact + '</td> \
                        <td>' + val.pp_email + '</td> \
                        <td>' + val.REGION + '</td> \
                        <td>' + val.PROVINCE + '</td> \
                        <td>' + val.CITY + '</td> \
                        <td>' + val.adr_brgy + '</td> \
                        </tr>';


            
           });

           var cls = (response.length > 0) ? 'dark' : 'light';
           var results = (response.length > 0) ? response.length + ' result/s found' : 'No results found';
           var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';
        
           $('.memis_mem_count').empty().append(badge);

           if(response.length > 0){
               var highlight = new RegExp(keyword, 'gi');
               var html = html.replace(highlight, '<strong><u>' + keyword + '</u></strong>');
           }
           
           $('#memis_members tbody').empty().append(html);

           var t = $('#memis_members_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
     
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
}

function memis_awards(keyword, sys, section){

    if ($.fn.DataTable.isDataTable("#memis_awards_table")) {
        $('#memis_awards_table').DataTable().clear().destroy();
    }
    
    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/memis/awa',
        async: false,
        data: { 'search' : search_arr },
        success: function (response) {
            
            _global_memis += response.length;
            
            var html;
            
           $.each(response, function(key, val){
                html += '<tr>\
                <td></td> \
                        <td>'+ val.TITLE +'</td>\
                        <td>'+ val.pp_last_name +'</td>\
                        <td>'+ val.pp_first_name +'</td>\
                        <td>'+ val.pp_middle_name +'</td>\
                        <td>'+ val.sex +'</td>\
                        <td>'+ val.div_number +'</td>\
                        <td>'+ val.awa_year +'</td>\
                        <td>'+ val.awa_citation +'</td>\
                        </tr>';
           });


           var cls = (response.length > 0) ? 'dark' : 'light';
           var results = (response.length > 0) ? response.length + ' result/s found' : 'No results found';
           var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';


           $('.memis_awa_count').empty().append(badge);             

           if(response.length > 0){
               
               var highlight = new RegExp(keyword, 'gi');
               var html = html.replace(highlight, '<strong><u>' + keyword + '</u></strong>');
           }
           
           $('#memis_awards_table tbody').empty().append(html);

           var t = $('#memis_awards_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
     
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
}

function memis_gbs(keyword, sys, section){

    if ($.fn.DataTable.isDataTable("#memis_gbs_table")) {
        $('#memis_gbs_table').DataTable().clear().destroy();
    }
    
    var search_arr = {};

    search_arr['keyword'] = keyword;
    search_arr['sys'] = sys;
    search_arr['filter'] = section;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        method: 'POST',
        url: APP_URL + '/search/overall/memis/gb',
        async: false,
        data: { 'search' : search_arr },
        success: function (response) {
            
            _global_memis += response.length;
            
            var html;
            
           $.each(response, function(key, val){
               

            var present  = (val.ph_to == 'Present') ? 'Present' : moment(val.ph_to).format("MMM DD, YYYY");
                html += '<tr>\
                <td></td> \
                        <td>'+ val.TITLE +'</td>\
                        <td>'+ val.pp_last_name +'</td>\
                        <td>'+ val.pp_first_name +'</td>\
                        <td>'+ val.pp_middle_name +'</td>\
                        <td>'+ val.sex +'</td>\
                        <td>'+ val.pos_name +'</td>\
                        <td>'+ moment(val.ph_from).format("MMM DD, YYYY") + ' - ' + present +'</td>\
                        <td>'+ val.ph_remarks +'</td>\
                        </tr>';
           });


           var cls = (response.length > 0) ? 'dark' : 'light';
           var results = (response.length > 0) ? response.length + ' result/s found' : 'No results found';
           var badge = '<span class="badge badge-' + cls + '">'+ results + '</span>';


           $('.memis_gb_count').empty().append(badge);             

           if(response.length > 0){
               
               var highlight = new RegExp(keyword, 'gi');
               var html = html.replace(highlight, '<strong><u>' + keyword + '</u></strong>');
           }
           
           $('#memis_gbs_table tbody').empty().append(html);

           var t = $('#memis_gbs_table').DataTable({
                mark: true,
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
     
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }
    });
}

function download(id){

    // console.log(id);

    $.ajax({
        method: 'GET',
        url: APP_URL + '/lms/download_file',
        // data: { 'id' : id },
        async: false,
        success: function (response) {
        }
    });
}

function memis_graph(val){
    // window.location('/memis');
}

// show data on clicking bar chart
function show_table_ejournal(data){
 alert(data);      
}

function verify_feedback(){

    var jqXHR = $.ajax({
        type: "GET",
        url: APP_URL + "/verify_feedback",
        async: false,
        crossDomain: true,
    });

    var stat = jqXHR.responseText.replace(/\"/g, '');
// console.log(stat);
    if(stat == 0){
        $('#feedbackModal').modal('toggle');
    }else{
        window.location = APP_URL + '/logout';
    }
}

function view_feedbacks(){

    if ($.fn.DataTable.isDataTable("#feedback_table")) {
        $('#feedback_table').DataTable().clear().destroy();
    }

    var fbChart;  

    var fb_labels = [];
    var fb_ui = [];
    var fb_ux = [];
    var fb_bgcolors = ['red', 'yellow', 'green'];

    $.ajax({
        method: 'GET',
        url: APP_URL + '/feedbacks_chart',
        async: false,
        success: function (response) {
        $.each(response, function(key, val){
            fb_ui.push(val.UI);
            fb_ux.push(val.UX);
            fb_labels.push(val.rate_description);
        });
        }
    });

    fb_title = 'Feedbacks';
    var ui_bar = document.getElementById('fb_ui_chart').getContext('2d');
    
    fbChart = new Chart(ui_bar, {
        type: 'bar',
        data: {
            labels: fb_labels,
            datasets: [{
                label: 'User Interface',
                data: fb_ui,
                backgroundColor: fb_bgcolors,
                borderColor: 'white',
                borderWidth: 1
            }],
        },
        options: {
            layout: { padding: {left:10}},
            title : {
                display: true,
                text: 'User Interface',
                fontSize: 14,
            },
            legend: {
                display: false, 
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    var ux_bar = document.getElementById('fb_ux_chart').getContext('2d');
    
    fbChart = new Chart(ux_bar, {
        type: 'bar',
        data: {
            labels: fb_labels,
            datasets: [{
                label: 'User Experience',
                data: fb_ux,
                backgroundColor: fb_bgcolors,
                borderColor: 'white',
                borderWidth: 1
            }],
        },
        options: {
            layout: { padding: {left:10}},
            title : {
                display: true,
                text: 'User Experience',
                fontSize: 14,
            },
            legend: {
                display: false, 
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    var html;

    $.ajax({
        method: 'GET',
        url: APP_URL + '/feedbacks',
        async: false,
        success: function (response) {
        $.each(response, function(key, val){
            $('#feedback_table').append('<tr><td></td> \
                                             <td>' + val.name + '</td> \
                                             <td>' + val.UI + '</td> \
                                             <td>' + val.fb_suggest_ui + '</td> \
                                             <td>' + val.UX + '</td> \
                                             <td>' + val.fb_suggest_ux + '</td> \
                                             <td>' + moment(val.created_at).format("MMM DD, YYYY") + '</td> \
                                        </tr>');
        });
        }
    });

    var t = $('#feedback_table').DataTable({
        mark: true,
        lengthMenu: [5, 10, 20, 50, 100],
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
    } );
 
    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

    $.ajax({
        method: 'POST',
        url: APP_URL + '/update_feedbacks',
        async: false,
        success: function (response) {
        }
    });

    
    $('.fb_notif .close').click();
}

function per_province(name, id){
    $('#perProvince .modal-title').text(name);

    var per_prov_bar_chart , per_prov_pie_chart;
    var per_prov_labels = [];
    var per_prov_total = [];
    var per_prov_bgcolors = [];
    var per_prov_title;

    $.ajax({
        method: 'POST',
        url: APP_URL + '/memis/pp',
        async: false,
        data: { 'id' : id },
        success: function (response) {
        $.each(response, function(key, val){
            per_prov_total.push(val.total);
            per_prov_labels.push(val.label);
            per_prov_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
        });
        }
    });

    // per_prov_title = 'Per province';
    // var bar = document.getElementById('per_prov_bar_chart').getContext('2d');
    
    // per_prov_bar_chart = new Chart(bar, {
    //     type: 'horizontalBar',
    //     data: {
    //         labels: per_prov_labels,
    //         datasets: [{
    //             label: per_prov_title,
    //             data: per_prov_total,
    //             backgroundColor: per_prov_bgcolors,
    //             borderColor: 'white',
    //             borderWidth: 1
    //         }],
    //     },
    //     options: {
    //         title : {
    //             display: true,
    //             text: per_prov_title,
    //             fontSize: 14,
    //         },
    //         legend: {
    //             display: false, 
    //         },
    //         scales: {
    //             yAxes: [{
    //                 ticks: {
    //                     beginAtZero: true
    //                 }
    //             }]
    //         }
    //     }
    // });

}


function chart_info(id){
        var source = '';
        var title = '';
        var desc = '';
    if(id == 1){
         source = APP_URL + "/storage/images/charts/basicbar.jpeg";
         title = 'Basic Bar Chart';
         desc = 'Bar chart showing horizontal columns. This chart type is often beneficial for smaller screens, as the user can scroll through the data vertically, and axis labels are easy to read.';
         desc += '<br/><br/><strong class="text-danger">This chart will show if any filter is selected.</strong>';
    }else if(id == 2){
         source = APP_URL + "/storage/images/charts/pie.jpeg";
         title = 'Pie Chart';
         desc = 'Pie charts are very popular for showing a compact overview of a composition or comparison. While they can be harder to read than column charts, they remain a popular choice for small datasets.';
         desc += '<br/><br/><strong class="text-danger">This chart will show if any filter is selected.</strong>';
    }else if(id == 3){
         source = APP_URL + "/storage/images/charts/stackedbar.jpeg";
         title = 'Stacked Bar Chart';
         desc = 'Chart showing stacked horizontal bars. This type of visualization is great for comparing data that accumulates up to a sum.';
         desc += '<br/><br/><strong class="text-danger">This chart will show if any two(2) filter selected ALL in the options.</strong>';
    }else if(id == 4){
         source = APP_URL + "/storage/images/charts/column.jpeg";
         title = 'Column Chart';
         desc = 'A basic column chart compares rainfall values between four cities. Tokyo has the overall highest amount of rainfall, followed by New York. The chart is making use of the axis crosshair feature, to highlight months as they are hovered over.';
         desc += '<br/><br/><strong class="text-danger">This chart will show if any filter not selected ALL in the options and time-related filters (Year/Month/Quarterly).</strong>';
    }else if(id == 5){
        source = APP_URL + "/storage/images/charts/stacked_column.jpg";
        title = 'Stacked-Column Chart';
        desc = 'Chart showing stacked columns for comparing quantities. Stacked charts are often used to visualize data that accumulates to a sum. This chart is showing data labels for each individual section of the stack.';
        desc += '<br/><br/><strong class="text-danger">This chart will show if any filter not selected ALL in the options and time-related filters (Year/Month/Quarterly).</strong>';
  
    }else{
        source = APP_URL + "/storage/images/charts/graph.jpg";
        title = 'Parts of a Graph';
    }

    $('#chart_modal img').attr("src",  source);
    $('#chart_modal .modal-title').text(title);
    $('#chart_modal img').attr("src",  source);
    $('#chart_modal .card-text').html(desc);
    $('#chart_modal').modal('toggle');

}

function checkStrength(password){
 
    //initial strength
    var strength = 0
 
    //if the password length is less than 6, return message.
    if (password.length < 6) {
        $('#result').removeClass()
        $('#result').addClass('badge badge-secondary')
        $('.result').removeClass('hidden');
        $('.result').addClass('pt-3')
        return 'Too short'
    }
 
    //length is ok, lets continue.
 
    //if length is 8 characters or more, increase strength value
    if (password.length == 8) strength += 1
 
    //if password contains both lower and uppercase characters, increase strength value
    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))  strength += 1
 
    //if it has numbers and characters, increase strength value
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))  strength += 1 
 
    //if it has one special character, increase strength value
    if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))  strength += 1
 
    //if it has two special characters, increase strength value
    if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,",%,&,@,#,$,^,*,?,_,~])/)) strength += 1

    
    if (password.length > 15 && password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,",%,&,@,#,$,^,*,?,_,~])/)) strength += 1
 
    //now we have calculated strength value, we can return messages
 
    //if value is less than 2
    if (strength < 2 ) {
        $('#result').removeClass()
        $('#result').addClass('badge badge-info')
        $('.result').removeClass('hidden');
        $('.result').addClass('pt-3')
        return 'Weak'
    } else if (strength == 2 ) {
        $('#result').removeClass()
        $('#result').addClass('badge badge-warning')
        $('.result').removeClass('hidden');
        $('.result').addClass('pt-3')
        return 'Good'
    } else if (strength == 3) {
        $('#result').removeClass()
        $('#result').addClass('badge badge-success')
        $('.result').removeClass('hidden');
        $('.result').addClass('pt-3')
        return 'Strong'
    } else {
        $('#result').removeClass()
        $('#result').addClass('badge badge-danger')
        $('.result').removeClass('hidden');
        $('.result').addClass('pt-3')
        return 'Very Strong'
    }
}

function click_overall(id, filter){

    // alert(id + ' ' + filter);
    if ($.fn.DataTable.isDataTable("#member_table")) {
        $('#member_table').DataTable().clear().destroy();
    }
// console.log(id);
    $('#member_modal .modal-title').text();
    $('#member_modal').modal('toggle');
    $('#member_table thead').empty();
    $('#member_table tfoot').empty();
    $('#member_table tbody').empty();

    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    var add_region_header = '';
    var add_awards_header = '';
    var add_gb_header = '';
    var add_default_header = '';
    var add_division_header = '';

    // if(filter == 'default_division'){
        add_default_header = '<th>Contact</th><th>Email</th>';
    // }else if(filter == 'default_region'){
    //     add_region_header = '<th>Province</th><th>Town/City</th>';
    // }else if(filter == 'default_category'){
    //     add_default_header = '<th>Contact</th><th>Email</th>';
    // }else if(filter == 'default_status'){
    //     add_default_header = '<th>Contact</th><th>Email</th>';
    // }else if(filter == 'default_sex'){
    //     add_default_header= '<th>Contact</th><th>Email</th>';
    // }else if(filter == 'default_country'){
    // }else if(filter == 'default_age'){
    // }else if(filter == 'default_province'){
    // }else if(filter == 'default_city'){
    // }else if(filter == 'default_age'){
    // }else if(filter == 'default_educ'){
    // }else{

    // }

    var data = $('#generate_chart_form').serializeArray();
    data.push({name: "id", value: id});
    // console.log(data);
    $.ajax({
        method: 'POST',
        url: APP_URL + '/memis/bar_graph_by_id',
        async: false,
        data:  data,
        datatype: 'json',
        success: function (response) {
// console.log(response);
            if(response.length > 0){

                var head = '<tr><th></th><th> Title </th><th> Last Name </th> \
                <th> First Name </th> \
                <th> Middle Name </th> \
                <th> Sex </th> \
                ' + add_default_header + ' \
                ' + add_awards_header + ' \
                ' + add_region_header + ' \
                ' + add_division_header + ' \
                ' + add_gb_header + ' \
                </tr>';
    
                $('#member_table thead').append(head);
                $('#member_table tfoot').append(head);
    
                var body = '';  var uni_year = [];
                $.each(response, function(key, val){
    
                    var present  = (val.ph_to == 'Present') ? 'Present' : moment(val.ph_to).format("MMM DD, YYYY");
                    var add_region_field = (filter == 2) ? '<td>' + val.PROVINCE +'</td><td>' + val.CITY +'</td>' : '';
    
                    var add_awards_field =  (filter == 7) ? '<td> Division ' + val.div_number +'</td> \
                                                             <td>' + val.awa_year +'</td> \
                                                             <td>' + val.awa_title +' | '+ val.awa_giving_body+'</td> \
                                                             <td>' + ((val.awa_citation == '' ) ? 'Not available' : val.awa_citation) + '</td>' 
                                                             : '';
    
                    var add_gb_field = (filter == 8) ? '<td>' + moment(val.ph_from).format("MMM DD, YYYY") + ' - ' + present + '</td> \
                                                       <td>' + val.ph_remarks + '</td>' : '';
                    var add_default_field = (add_default_header != '') ? '<td>' + val.pp_contact + '</td><td>' + val.pp_email + '</td>' : '';
                    var add_division_field = (add_division_header != '') ? '<td>' + 'Division ' + val.div_number + '</td>' : '';
                    
                    // if(filter == 7){
                    //     awa_per_year.push(val.awa_year);
                    // }
    
                    body += '<tr><td></td> \
                                <td>' + val.title_name + '</td> \
                                <td>' + val.pp_last_name + '</td> \
                                <td>' + val.pp_first_name + '</td> \
                                <td>' + val.pp_middle_name + '</td> \
                                <td>' + val.sex + '</td> \
                                ' + add_default_field + '\
                                ' + add_awards_field + '\
                                ' + add_region_field + '\
                                ' + add_division_field + '\
                                ' + add_gb_field + '\
                            </tr>';
                    
                });

                $('#member_table tbody').append(body);

                var t = $('#member_table').DataTable({
                    
                    mark: true,
                    "columnDefs": [ {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    } ],
                });
         
                t.on( 'order.dt search.dt', function () {
                    t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();
                
            }
        }
    });
}

function get_drilldown(id){

    var drilldown = [];
    var data;
    $.ajax({
        method: 'POST',
        url: APP_URL + '/memis/drilldown/region',
        async: false,
        data:  $('#generate_chart_form').serialize() + "&par1="+ id,
        datatype: 'json',
        success: function (response) {

            $.each(response, function(key, val){
                drilldown.push([val.label, val.total]);

            });
            // console.log(JSON.stringify(drilldown));

        }
    });

     data = JSON.stringify(drilldown);
     return data;
    
    
    
}

function memis_generate_chart(chart){
        var default_x = $('input[type=radio]:checked').attr('id');
    
        $('.no-data-found').remove();
        bar_sub_title = [];
        $("#generate_chart_form option:selected").each(function() {
            if($(this).val() > 0){
                bar_sub_title.push($(this).text());
            }
        });

        var selections = [];
        var filter_counter = 0;
        
        $("#generate_chart_form select:not(#memis_start_year,#memis_end_year) option:selected").each(function() {
            selections.push($(this).val());
        });

        for (let i = 0; i < selections.length; i++) {
            if (selections[i] != '0') filter_counter++;
        }


        if(filter_counter == 0){
            $('.chart_filter_alert').removeAttr('hidden').hide().fadeIn('slow');
        }else{
            var total = 0;

            if(chart == 1){ // bar chart
                
                
                chart_numbers = 1;
                $('#chart_numbers').prop('checked', true); // Unchecks it
                $('#chart_numbers').change(); // Unchecks it
                chart_orientation = 1;
                $('#chart_orientation').prop('checked', true); // Unchecks it
                $('#chart_orientation').change(); // Unchecks it
                chart_rendered = 1;
    
                var memis_labels = [];
                var memis_total = [];
                var memis_bgcolors = [];
                var memis_ids = [];
                var memis_drill_data = [];
                var memis_series_drill = [];

                var start = $('#memis_start_year').val();
                var end = $('#memis_end_year').val();
                if(start > 0 && end > 0){
                    bar_main_title = category_title + '(' + start + '-' + end + ')';
                }

                $.ajax({
                    method: 'POST',
                    url: APP_URL + '/memis/bar_graph',
                    async: false,
                    data:  $('#generate_chart_form').serialize(),
                    datatype: 'json',
                    success: function (response) {
                        if(response.length > 0){
                            $.each(response, function(key, val){   
                                memis_total.push(parseInt(val.total));
                                memis_labels.push(val.label);
                                memis_bgcolors.push('#800000');
                                memis_ids.push(val.bar_id);
                                total += parseInt(val.total);
    
                                  
                                memis_drill_data.push({
                                    name:val.label,
                                    y: val.total,
                                    drilldown: val.label
                                })
    
                            });
    
                            
                        }else{
                            $('.no-data-found').remove();
                            $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
                            <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
                          </div>').hide().fadeIn();
                        }
                    }
                });
    
                bar_labels = memis_labels;
                bar_total = total;
               
                    exeChart = new Highcharts.chart('container'+chart, {
                        chart: {
                            type: 'bar',
                            events: {
                            load: function() {
                                var chart = this,
                                barsLength = chart.series[0].data.length;
                        
                                chart.update({
                                chart: {
                                    height: 100 + (50 * barsLength)
                                }
                                }, true, false, false);
                            }
                            }
                        },
                        title: {
                            text: bar_main_title
                        },
                        subtitle: {
                            text: 'Source: http://execom.nrcp.dost.gov.ph/'
                        },
                        xAxis: {
                            categories: memis_labels,
                            title: {
                                text: null
                            },
                            labels: {
                                style: {
                                    fontSize:'14px'
                                }
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Total Members (' + total + ')',
                                align: 'high'
                            },
                            labels: {
                                overflow: 'justify'
                            }
                        },
                        plotOptions: {
                            bar: {
                                dataLabels:{
                                    enabled:true,
                                    formatter:function() {
                                        var pcnt = (this.y / total) * 100;
                                        return this.y + '(' + Highcharts.numberFormat(pcnt) + '%)';
                                    }
                                } 
                            },
                            series: {
                                colorByPoint: true,
                                colors: memis_bgcolors,
                                pointWidth: '30',
                                point: {
                                    events: {
                                        click: function () {
                                            click_overall(memis_ids[parseInt(this.index)], default_x);
                                        }
                                    }
                                }
                            }
                        },
                        legend: {
                            layout: 'vertical',
                            x: -40,
                            y: 80,
                            floating: true,
                            shadow: true
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Members',
                            data: memis_total,
                        }]
                    });
                
                
    
                
            }else if(chart == 2){ // pie chart
    
                var memis_labels = [];
                var memis_total = [];
                var memis_bgcolors = [];
                var total = 0;
                var memis_ids = [];

                var start = $('#memis_start_year').val();
                var end = $('#memis_end_year').val();
                if(start > 0 && end > 0){
                    bar_main_title = category_title + '(' + start + '-' + end + ')';
                }
                
                $.ajax({
                    method: 'POST',
                    url: APP_URL + '/memis/bar_graph',
                    async: false,
                    data:  $('#generate_chart_form').serialize(),
                    datatype: 'json',
                    success: function (response) {
    
                        if(response.length > 0){
                            $.each(response, function(key, val){   
                                
                                memis_labels.push({
                                    name: val.label, 
                                    y: parseFloat(val.total),
                                });
    
                                total += val.total;
    
                                memis_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
                                memis_ids.push(val.bar_id);
            
                            });
                
                        }else{
                            $('.no-data-found').remove();
                            $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
                            <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
                          </div>').hide().fadeIn();
                        }
                    }
                });
    
                Highcharts.chart('container'+chart, {
                    chart: {
                      plotBackgroundColor: null,
                      plotBorderWidth: null,
                      plotShadow: false,
                      type: 'pie',
                      marginBottom: 50 
                    },
                    title: {
                      text: bar_main_title
                    },
                    subtitle: {
                        text: 'Source: http://execom.nrcp.dost.gov.ph/'
                    },
                    tooltip: {
                      pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
                    },
                    accessibility: {
                      point: {
                        valueSuffix: '%'
                      }
                    },
                    plotOptions: {
                      pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                          enabled: true,
                          format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        },
                        point: {
                            events: {
                                click: function () {
                                    click_overall(memis_ids[parseInt(this.index)], default_x);
                                }
                          }
                        }
                      }
                    },    
                    credits: {
                        text: 'Total Members (' + total + ')',
                        position: {
                            align: 'right',
                        },
                        style: {
                            fontSize: '9pt', // you can style it!,
                            // color: '#ffffff',
                        }
                    },
                    colors: memis_bgcolors,
                    series: [{
                      name: '',
                      data: memis_labels
                    }]
                  });
            }else if(chart == 3){ // stacked chart
                
                chart_numbers = 1;
                $('#chart_numbers').prop('checked', true); // Unchecks it
                $('#chart_numbers').change(); // Unchecks it
                chart_orientation = 1;
                $('#chart_orientation').prop('checked', true); // Unchecks it
                $('#chart_orientation').change(); // Unchecks it
                chart_rendered = 1;
                // var default_label = $('input[name=radio_default]:checked').attr('id');
                // var chart_height = (default_label == 'default_category') ? '' : '';
    
    
                var memis_labels = [];
                var memis_bgcolors = [];
                var total = 0;

                
    
                $.ajax({
                    method: 'POST',
                    url: APP_URL + '/memis/stack_graph',
                    async: false,
                    data:  $('#generate_chart_form').serialize(),
                    datatype: 'json',
                    success: function (response) {
                        // console.log(response);
                        if(response.length > 0){
                            if(stacked_bar_exemption == 1){stacked_bar_y = [];} // y axis label is based from php query result
                            
                            var i = 0 ;
                            for(i; i < response.length;i++){
                                $.each(response[i], function(key, val){   
                                            
                                    var memis_total = [];
                                    $.each(val, function(k,v){
                                        
                                        if(v.length > 0){
    
                                            $.each(v, function(x, y){
                                                memis_total.push(y.total);
                                                if(stacked_bar_exemption == 1){stacked_bar_y.push(y.country_name);}
                                                total += y.total;
                                            });
                    
                                            memis_labels.push({
                                                name:k,
                                                data:memis_total
                                            })
    
                                            memis_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
                                        }
                                    });
                                });
                            }
    
                            if(stacked_bar_exemption == 1){
                                uniqueCountry = stacked_bar_y.filter(function(item, i, stacked_bar_y) {
        
                                    
                                    return i == stacked_bar_y.indexOf(item);
                                
                                });
                                uniqueCountry.sort();
                                
                                stacked_bar_y = [];
        
                                $.each(uniqueCountry.sort(), function(key, val){
                                    
                                    stacked_bar_y.push(val);
                                });
                            }
    
                        }else{
                            $('.no-data-found').remove();
                            $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
                            <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
                          </div>').hide().fadeIn();
                        }
                    }
                });
    
                
                exeChart = new Highcharts.chart('container'+chart, {
                    chart: {
                      type: 'bar',
                      events: {
                        load: function() {
                          var chart = this,
                            barsLength = chart.series[0].data.length;
                  
                          chart.update({
                            chart: {
                              height: 150 + (70 * stacked_bar_y.length)
                            }
                          }, true, false, false);
                        }
                      }
                    },
                    title: {
                      text: bar_main_title
                    },
                    xAxis: {
                      categories: stacked_bar_y,
                      title: {
                          text: 'Total Members (' + total + ')',
                          align: 'high'
                      },
                      labels: {
                          style: {
                              fontSize:'14px'
                          }
                      }
                    },
                    yAxis: {
                      min: 0,
                      title: {
                        text: bar_sub_title
                      }
                    },
                    legend: {
                      reversed: true
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y} ({point.percentage:.2f}%)<br/>'
                    },
                    plotOptions: {
                      series: {
                        stacking: 'normal',
                        pointWidth: '30',
                        dataLabels: {
                            enabled: true,
                            formatter: function () {
                                return this.y + ' (' + Math.round(100 * this.y / this.total) + '%)';
                            },
                        },
                      }
                    },
                    colors :  memis_bgcolors,
                    series: memis_labels
                  });
    
                  chart_rendered = 1;
            }else if(chart == 4){ // column chart
                chart_numbers = 1;
                $('#chart_numbers').prop('checked', true); // Unchecks it
                $('#chart_numbers').change(); // Unchecks it
                chart_orientation = 2;
                $('#chart_orientation').prop('checked', false); // Unchecks it
                $('#chart_orientation').change(); // Unchecks it
                chart_rendered = 1;
    
                var memis_labels = [];
                var memis_total = [];
                var y_total = [];
                var total = 0;
                
    
                
                var start = $('#memis_start_year').val();
                var end = $('#memis_end_year').val();
                if(start > 0 && end > 0){
                    bar_main_title = category_title + '(' + start + '-' + end + ')';
                    stacked_bar_y = [];
                    for (start; start <= end; start++) {
                        stacked_bar_y.push(start);
                    }
    
                }
    
                var period = $('#memis_period').val();
                var month = 0, quarter = 0, semestral = 0;
                var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                var quarters = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Qurter'];
                var sems = ['1st Semestral', '2nd Semestral'];
                if(period == 1){
                    stacked_bar_y = [];
                    for (month; month <  12; month++) {
                        stacked_bar_y.push(months[month]);
                    }
                    
                    var p_year = (period_year > 0) ? ' '+ period_year : '';
                    
                    bar_main_title = category_title + '(Monthly' + p_year + ')';
                }else if(period == 2){
                    stacked_bar_y = [];
                    for (quarter; quarter <  4; quarter++) {
                        stacked_bar_y.push(quarters[quarter]);
                    }
                    bar_main_title = category_title + '(Quarterly' + p_year + ')';
                }else if(period == 3){
                    stacked_bar_y = [];
                    for (semestral; semestral <  2; semestral++) {
                        stacked_bar_y.push(sems[semestral]);
                    }
                    bar_main_title = category_title + '(Semestral' + p_year + ')';
                }
    
    
                $.ajax({
                    method: 'POST',
                    url: APP_URL + '/memis/column_graph',
                    async: false,
                    data:  $('#generate_chart_form').serialize(),
                    datatype: 'json',
                    success: function (response) {
                        // console.log(response);
                        if(response.length > 0){
                            
                            
                            var i = 0 ;
                            for(i; i < response.length;i++){
                                $.each(response[i], function(key, val){   
                                            
                                    var memis_total = [];
                                    $.each(val, function(k,v){
                                        
                                        if(v.length > 0){
    
                                            $.each(v, function(x, y){
                                                    memis_total.push(y.total);
                                                    total += y.total;
                                            });
    
    
                                            
                                            memis_labels.push({
                                                name:k,
                                                data:memis_total
                                            })
    
                                        }
    
                                    });
    
                                    
                                });
                            }
                        
                            chart_rendered = 1;
                        }else{
                            $('.no-data-found').remove();
                            $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
                            <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
                          </div>').hide().fadeIn();
                        }
                    }
                });
    
                exeChart = new Highcharts.chart('container'+chart, {
                    chart: {
                      type: 'column',
                      events: {
                        load: function() {
                          var chart = this,
                            barsLength = chart.series[0].data.length;
                  
                          chart.update({
                            chart: {
                              height: 700
                            }
                          }, true, false, false);
                        }
                      }
                    },
                    title: {
                      text: bar_main_title
                    },
                    xAxis: {
                      categories: stacked_bar_y
                    },
                    yAxis: {
                      min: 0,
                      title: {
                        text: bar_sub_title
                      }
                    },
                    tooltip: {
                        formatter: function() {
                            var s = '<b>'+ this.x +'</b>', sum = 0;
                                 
                            $.each(this.points, function(i, point) {
                                sum += point.y;
                            });     
    
                            $.each(this.points, function(i, point) {
                                s += '<br/><span style="color:{series.color};padding:0">'+ point.series.name +'</span>: '+
                                    point.y + '(' + (( point.y / sum) * 100).toFixed(2) + '%)';
    
                            });                
                            s += '<br/>Total: '+sum               
                            return s;
                        },
                        shared: true,
                        useHTML: true
                    },
                    legend: {
                      reversed: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 1,
                            borderWidth: 0,
                            pointWidth: '30',
                            dataLabels: {
                                enabled: true,
                                formatter:function() {
                                    return this.y ;
                                }
                            }
                        }
                    },
                    series: memis_labels,
                  });
                              
            }else if(chart == 5){ // stack column chart
                
                chart_numbers = 1;
                $('#chart_numbers').prop('checked', true); // Unchecks it
                $('#chart_numbers').change(); // Unchecks it
                chart_orientation = 2;
                $('#chart_orientation').prop('checked', false); // Unchecks it
                $('#chart_orientation').change(); // Unchecks it
                chart_rendered = 1;
    
                var memis_labels = [];
                var memis_total = [];
                var memis_bgcolors = [];
                var y_total = [];
                var total = 0;
                
    
                
                var start = $('#memis_start_year').val();
                var end = $('#memis_end_year').val();
                if(start > 0 && end > 0){
                    
                    bar_main_title = category_title + '(' + start + '-' + end + ')';
                    stacked_bar_y = [];
                    for (start; start <= end; start++) {
                        stacked_bar_y.push(start);
                    }
    
                }
                var period = $('#memis_period').val();
                var month = 0, quarter = 0, semestral = 0;
                var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                var quarters = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Qurter'];
                var sems = ['1st Semestral', '2nd Semestral'];
                var period_year = $('#memis_year').val();
    
                if(period == 1){
                    stacked_bar_y = [];
                    for (month; month <  12; month++) {
                        stacked_bar_y.push(months[month]);
                    }
                    
                    var p_year = (period_year > 0) ? period_year : '';
                    
                    bar_main_title = category_title + '(Monthly ' + p_year + ')';
                }else if(period == 2){
                    stacked_bar_y = [];
                    for (quarter; quarter <  4; quarter++) {
                        stacked_bar_y.push(quarters[quarter]);
                    }
                    bar_main_title = category_title + '(Quarterly ' + p_year + ')';
                }else if(period == 3){
                    stacked_bar_y = [];
                    for (semestral; semestral <  2; semestral++) {
                        stacked_bar_y.push(sems[semestral]);
                    }
                    bar_main_title = category_title + '(Semestral ' + p_year + ')';
                }
    
    
                $.ajax({
                    method: 'POST',
                    url: APP_URL + '/memis/stack_column_graph',
                    async: false,
                    data:  $('#generate_chart_form').serialize(),
                    datatype: 'json',
                    success: function (response) {
                        if(response.length > 0){
                        
                            var i = 0 ;
                            for(i; i < response.length;i++){
                                $.each(response[i], function(key, val){   
                                    var memis_total = [];
                                    $.each(val, function(k,v){
                                        if(v.length > 0){
    
                                            $.each(v, function(x, y){
                                                    memis_total.push(y.total);
                                                    total += y.total;
                                            });
    
                                            memis_labels.push({
                                                name:k,
                                                data:memis_total,
                                                stack: 'STACK'
                                            })
    
                                            memis_bgcolors.push('#000000'.replace(/0/g, function(){return (~~(Math.random()*16)).toString(16);}));
    
                                        }
    
                                    });
                                });
                            }
                        
                            chart_rendered = 1;
                        }else{
                            $('.no-data-found').remove();
                            $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
                            <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
                          </div>').hide().fadeIn();
                        }
                    }
                });
    
                exeChart = new Highcharts.chart('container'+chart, {
    
                    chart: {
                        type: 'column',
                        events: {
                          load: function() {
                            var chart = this,
                              barsLength = chart.series[0].data.length;
                    
                            chart.update({
                              chart: {
                                height: 700
                              }
                            }, true, false, false);
                          }
                        }
                    },
                
                    title: {
                        text: bar_main_title
                    },
                
                    xAxis: {
                        categories: stacked_bar_y
                    },
                    yAxis: {
                      min: 0,
                      title: {
                        text: bar_sub_title
                      }
                    },
                    tooltip: {
                        formatter: function () {
                            return '<b>' + this.x + '</b><br/>' +
                                this.series.name + ': ' + this.y + '<br/>' +
                                'Total: ' + this.point.stackTotal;
                        }
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                            pointPadding: 1,
                            borderWidth: 0,
                            pointWidth: '30',
                            dataLabels: {
                                enabled: true,
                                formatter:function() {
                                    return this.y ;
                                }
                            }
                        }
                    },
                    colors :  memis_bgcolors,
                    series: memis_labels
                });
                         
            }else{ // drilldown bar chart
                
                
                    chart_numbers = 1;
                    $('#chart_numbers').prop('checked', true); // Unchecks it
                    $('#chart_numbers').change(); // Unchecks it
                    chart_orientation = 1;
                    $('#chart_orientation').prop('checked', true); // Unchecks it
                    $('#chart_orientation').change(); // Unchecks it
                    chart_rendered = 1;
        
                    var memis_labels = [];
                    var memis_total = [];
                    var memis_bgcolors = [];
                    var memis_ids = [];
                    var memis_drill_data = [];
                    var memis_series_drill = [];
                    $.ajax({
                        method: 'POST',
                        url: APP_URL + '/memis/bar_graph',
                        async: false,
                        data:  $('#generate_chart_form').serialize(),
                        datatype: 'json',
                        success: function (response) {
                            if(response.length > 0){
                                $.each(response, function(key, val){   
                                    memis_total.push(parseInt(val.total));
                                    memis_labels.push(val.label);
                                    memis_bgcolors.push('#800000');
                                    memis_ids.push(val.bar_id);
                                    total += parseInt(val.total);
        
                                      
                                    memis_drill_data.push({
                                        name:val.label,
                                        y: val.total,
                                        drilldown: val.label
                                    })
        
                                });
                                // console.log(memis_drill_data);
                                
                            }else{
                                $('.no-data-found').remove();
                                $('.no_data_available').after('<div class="alert alert-warning mt-4 no-data-found text-center" role="alert"> \
                                <span class="fas fa-exclamation-triangle font-weight-bold"></span> NO DATA AVAILABLE. \
                              </div>').hide().fadeIn();
                            }
                        }
                    });
        
                    bar_labels = memis_labels;
                    bar_total = total;
                   
                    
        
                        var y = 0;
                        $.each(memis_labels, function(key, val){
                            y++;
                            
                        
                            memis_series_drill.push({
                                name:val,
                                id: val,
                                data:  JSON.parse(get_drilldown(y))
                            })
                        });

        
                        exeChart = new Highcharts.chart('container'+chart, {
                            chart: {
                                type: 'bar',
                                events: {
                                load: function() {
                                    var chart = this,
                                    barsLength = chart.series[0].data.length;
                            
                                    chart.update({
                                    chart: {
                                        height: 100 + (50 * barsLength)
                                    }
                                    }, true, false, false);
                                }
                                }
                            },
                            title: {
                                text: bar_main_title
                            },
                            subtitle: {
                                text: 'Source: http://execom.nrcp.dost.gov.ph/'
                            },
                            accessibility: {
                                announceNewData: {
                                    enabled: true
                                }
                            },
                            xAxis: {
                                // categories: memis_labels,
                                type: 'category',
                                title: {
                                    text: null
                                },
                                labels: {
                                    style: {
                                        fontSize:'14px'
                                    }
                                }
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Total Members (' + total + ')',
                                      align: 'high'
                                },
                                labels: {
                                    overflow: 'justify'
                                }
                            },
                            
                            plotOptions: {
                                series: {
                                    events: {
                                        click: function(event) {
                                            if (exeChart.drillUpButton) {
                                                // alert(event.point.index + ' ' + event.point.name) //todo
                                           } 
                                        }
                                      },
                                    dataLabels:{
                                        enabled:true,
                                        formatter:function() {
                                            var pcnt = (this.y / total) * 100;
                                            return this.y + '(' + Highcharts.numberFormat(pcnt) + '%)';
                                        }
                                    }
                                },
                            },
                            tooltip: {
                                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}<br/>'
                            },
                            legend: {
                                layout: 'vertical',
                                x: -40,
                                y: 80,
                                floating: true,
                                shadow: true
                            },
                            credits: {
                                enabled: false
                            },
                            series: [
                                {
                                    name: "Regions",
                                    colorByPoint: true,
                                    data: memis_drill_data
                                }
                            ],
                            drilldown: { series : memis_series_drill}
                        });
                        
                       
                    
        
                    
                
            }
        }

    

      

}