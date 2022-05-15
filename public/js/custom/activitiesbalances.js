/*
Name: 			localaccountsbalances.js
Written by: 	KABUYE HASHIM MUNIIRU
ORMS Version: 	1.0
Description:    Handle all the requests and behaviors of localaccountsbalances.blade.php
*/

$(document).ready(function () {
    // Initialise the ajax headers to have the CSRF laravel token
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN':  $("meta[name='csrf-token']").attr('content')}
    }); // end ajax header initialisation

    dtInit(); // Initialise the dataTable

    

    $('#fetchBalancesBtn').on('click', function (e) {
        e.preventDefault();                       
        if($('#fetchBalanceForm').valid())
        {
            
        }
        if(e.detail !== 2)
        {       
            if($('#fetchBalanceForm').valid())
            {
                $('#printActivitiesBalances').addClass('hidden');
                $('#printActivitiesBalancesB').addClass('hidden');
                getActivityBalancesForTerm();
    
            }
            else{
                $('#printActivitiesBalances').addClass('hidden');
                $('#printActivitiesBalancesB').addClass('hidden');
            }
        }
        
        
    });

    $('#fetchSectionBalancesBtn').on('click', function (e) {
        e.preventDefault();                       
        if($('#fetchBalanceFormBySection').valid())
        {
            //getSectionTermFeesBalance();
        }
       
            
      
        
    });

    

    $('#terms').change(function (e) { 
        e.preventDefault();

        if($('#terms').val() != '')
        {
            getActivities($('#terms').val());
            
        }
        else{
            
        }
       
        
    });

    $('#classes').change(function (e) { 
        e.preventDefault();

        if($('#classes').val() != '')
        {
            getStreams($('#classes').val());
            
        }
        else{
            
        }
       
        
    });

    // Click event for the print 
    $('#printActivitiesBalances').click(function (e) {
        if(e.detail !== 2)
        {       
                         
            printActivitiesBalances();
        }         
       
    });// end click event

    // Click event for the print 
    $('#printActivitiesBalancesB').click(function (e) {
        if(e.detail !== 2)
        {       
                         
            printActivitiesBalancesB();
        }         
       
    });// end click event

    

});

function printActivitiesBalances()
{
    

    var tbl = $('#accountsBalancesTable').clone();
    //tbl.find('tr th:nth-child(5), tr td:nth-child(5)').remove();

    // Put footer at the end
    var foot = tbl.find('tfoot');
    var last = foot.find('tr');
    tbl.find('tfoot').remove();
    tbl.append(last);

    var doc = new jsPDF();  

    var pageSize = doc.internal.pageSize;
            var pageWidth = pageSize.width ? pageSize.width : pageSize.getWidth();
            var center = pageWidth / 2;
   
    doc.setTextColor(headColor[0], headColor[1], headColor[2]);
    doc.setFontSize(headFontSize);
    doc.text(schoolName, center, schoolNameY, 'center');
    doc.setDrawColor(headColor[0], headColor[1], headColor[2]);
    doc.text($('#balanceHead').html(), center, 32, 'center');
    doc.text($('#balanceSubHead').html(), center, 40, 'center');
    doc.text($('#balanceSubHead2').html(), center, 47, 'center');
    doc.setLineWidth(1);
    doc.line(15, 53, 195, 53);
    doc.setFontSize(11);
    

    
    // It can parse html:
    doc.autoTable({
        html: tbl.get(0),
        startY: 56,
        styles: {
            lineColor: [0, 0, 0],
            lineWidth: 0.2,
            fontSize: 9
        },
        headStyles: {
            fillColor: [0, 0, 0],              
        },
        footStyles: {
            fillColor: [0, 0, 0],              
        },
        bodyStyles: {                  
            textColor: [0, 0, 0],
        },      
        willDrawCell: function(data) {
            if (data.row.section === 'body') {
                console.log(data.row.raw);
                var obj = data.row.raw;
                if (obj.classList.contains('has-unpaid-balance')) {
                    //doc.setTextColor(231, 76, 60); // Red
                    doc.setFontStyle('bold');
                }
            }
        },
        
    });

    
    
    doc.save('ActivitiesBalance_'+ $('#balanceSubHead').html() +  displayDate(new Date())  +'.pdf');
    
}


function printActivitiesBalancesB()
{
    

    var tbl = $('#accountsBalancesTable').clone();
    tbl.find('tr th:nth-child(4), tr td:nth-child(4), tr th:nth-child(5), tr td:nth-child(5)').remove();

    // Put footer at the end
    var foot = tbl.find('tfoot');
    var last = foot.find('tr');
    tbl.find('tfoot').remove();
    

    var doc = new jsPDF();  

    var pageSize = doc.internal.pageSize;
            var pageWidth = pageSize.width ? pageSize.width : pageSize.getWidth();
            var center = pageWidth / 2;
   
    doc.setTextColor(headColor[0], headColor[1], headColor[2]);
    doc.setFontSize(headFontSize);
    doc.text(schoolName, center, schoolNameY, 'center');
    doc.setDrawColor(headColor[0], headColor[1], headColor[2]);
    doc.text($('#balanceHead').html(), center, 32, 'center');
    doc.text($('#balanceSubHead').html(), center, 43, 'center');
    //doc.text($('#testSubHead2').html(), center, 47, 'center');
    doc.setLineWidth(1);
    doc.line(15, 53, 195, 53);
    doc.setFontSize(11);
    doc.setTextColor(100);

    
    // It can parse html:
    doc.autoTable({
        html: tbl.get(0),
        startY: 56,
        styles: {
            lineColor: [0, 0, 0],
            lineWidth: 0.2,
            fontSize: 9
        },
        headStyles: {
            fillColor: [0, 0, 0],              
        },
        footStyles: {
            fillColor: [0, 0, 0],              
        },
        bodyStyles: {                  
            textColor: [0, 0, 0],
        },       
        willDrawCell: function(data) {
            if (data.row.section === 'body') {
                console.log(data.row.raw);
                var obj = data.row.raw;
                if (obj.classList.contains('has-unpaid-balance')) {
                    //doc.setTextColor(231, 76, 60); // Red
                    doc.setFontStyle('bold');
                }
            }
        },
        
    });

    
    
    doc.save('ActivitiesBalance_'+ $('#balanceSubHead').html() +  displayDate(new Date())  +'.pdf');
    
}

function dtInit()
{
    var t = $('#accountsBalancesTable').DataTable({
        lengthMenu: [[  -1], [  'All']]
    });
    
    
    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

    
    
}


function getActivities(termid)
{
    termid = parseInt(termid);
    $.ajax({
        type: "post",
        url: "/adminhome/finance/activities/getactivitiesbyterm",
        data: { term_id: termid},
        dataType: "json",
        success: function (response, status, code) {
             activityList = JSON.parse(response.data);
            console.log(activityList);
             $('#activities').empty();                                                  
            $.each(activityList, function(key, val){
                
                var activity = '<option value="'+ val.activityid +'"  data-activitydate="'+ val.deadline +'">'+ val.activity +'</option>';
                $('#activities').append(activity);
            });
            
            return true;
        },
        error: function (error) {
            alert('Error '+ error);
              
        }
    });
}

function getStreams(classid)
{
    classid = parseInt(classid);
    $.ajax({
        type: "post",
        url: "/adminhome/classes/class/getstreamsbyclass",
        data: { class_id: classid},
        dataType: "json",
        success: function (response, status, code) {
             streamList = JSON.parse(response.data);
            console.log(streamList);
             $('#streams').empty();
             $('#streams').append('<option value="'+ 0 +'">All Streams</option>');                         
            $.each(streamList, function(key, val){
                
                var stream = '<option value="'+ val.classandstreamid +'">'+ val.classstream +'</option>';
                $('#streams').append(stream);
            });
            
            return true;
        },
        error: function (error) {
            alert('Error '+ error);
              
        }
    });
}

function getDate(data)
{

    if(data)
    {
        return String($.datepicker.formatDate('yy-mm-dd', new Date(data)));
    }
    else{
        return null;
    }
}

function displayDate(data)
{
    if(data)
    {
        return $.datepicker.formatDate('D, dd M yy', new Date(data));
    }
    else{
        return null;
    }
}

// Go to the selected term to fetch theBalances
function getActivityBalancesForTerm()
{   
    var term = parseInt($('#terms').val());
    var activity = parseInt($('#activities').val());
    var clasz = parseInt($('#classes').val()); 
    var stream = parseInt($('#streams').val());
    
    $.ajax({
        type: "post",
        url: "/adminhome/finance/activities/balances/getbalances",
        data: {
            term_id: term,
            activity_id: activity,
            class_id: clasz,
            class_and_stream_id: stream
                        
        },            
        dataType: "json",
        success: function (response, status, code) {
            $('#printActivitiesBalances').removeClass('hidden');
            $('#printActivitiesBalancesB').removeClass('hidden');
            console.log(response);
            var stringed = JSON.stringify(response.data.data)
            // First destroy the DataTable Before creating a new one
            $('#accountsBalancesTable').DataTable().destroy();
            var stream;
            if($('#streams').val() == 0)
            {
                stream = '';
            }
            else{
                stream = $('#streams :selected').text();
            }
            $('#balanceSubHead2').html($('#classes :selected').text() + ' ' + stream + ' '
                                 + $('#terms :selected').text() + ' | ' + displayDate($('#activities :selected').attr('data-activitydate')));
            $('#balanceSubHead').html($('#activities :selected').text());
            
            
             // Create a new dataTable and assign it to a variable t
             var t = $("#accountsBalancesTable").DataTable({
                data: JSON.parse(stringed),
                columns: [
                    { data: null },
                    { data: 'name'},
                    { data: 'class'},                                        
                    { data: 'fee', render: function(d){return  getCurrency(d);}},
                    { data: 'paid', render: function(d){return  getCurrency(d);}},
                    { data: 'balance', render: function(d, type, all){
                        return  getCurrency(d);                            
                        }
                    },
                    { data: null , render: function(d, type, all){
                        percentagePaid = parseInt((all.paid / all.fee) * 100);
                        percentageBalance = parseInt(100 - percentagePaid)
                        return  'Paid: ' + percentagePaid + '%. Balance ' + percentageBalance + '%';
                        }
                    }                                       
                ],
                columnDefs: [ {
                    targets: 0,
                    order: [[ 1, 'asc' ]]
                  } ],                  
                    lengthMenu: [[  -1], [  'All']] ,                            
                createdRow: function(row, data, dataIndex){
                    if(data.balance == null || data.balance > 0 )
                    {
                        $(row).addClass('has-unpaid-balance');
                    }
                },
                footerCallback: function ( row, data, start, end, display ) {
                    var api = this.api();
                    nb_cols = api.columns().nodes().length;
                    var j = 3;
                    var fee;
                    var paid;
                    var balance;
                    while(j < nb_cols-1){
                        var pageTotal = api
                    .column( j, { page: 'allPages'} )
                    .data()
                    .reduce( function (a, b) {
                        return Number(a) + Number(b);
                    }, 0 );
                    // Update footer
                    $( api.column( j ).footer() ).html(getCurrency(pageTotal));
                    switch (j) {
                        case 3:
                            fee = pageTotal;
                            break;
                        case 4:
                            paid = pageTotal;
                            break;
                        case 5:
                            balance = pageTotal;
                            break;                    
                    }
                    
                    
                        j++;
                    }

                    var percentage = function(){
                        percentagePaid = parseInt((paid / fee) * 100);
                        percentageBalance = parseInt(100 - percentagePaid)
                        return  'Paid: ' + percentagePaid + '%. Balance ' + percentageBalance + '%';
                        }
                        $( api.column( 6 ).footer() ).html(percentage);
                } 
                 
            });

            // Use var t to assign a counter column onto the table
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        },
        error: function (error) {
            console.log(error);
            $('#res').append(error.responseText);
            alert("No Response From Server");
        }
        
    });
}


function getSectionTermFeesBalance()
{   
    var section = parseInt($('#sections').val());    
    var term = parseInt($('#termsforsections').val());
    $.ajax({
        type: "post",
        url: "/adminhome/finance/schoolfees/classbalances/getbalanceofsection",
        data: {
            section_id: section,
            term_id: term
        },            
        dataType: "json",
        success: function (response, status, code) {
            console.log(response);
            var stringed = JSON.stringify(response.data.data)
            // First destroy the DataTable Before creating a new one
            $('#schoolFeesBalancesTable').DataTable().destroy();
            
            $('#balanceSubHead').html($('#sections :selected').text() + ' ' + $('#termsforsections :selected').text());
            
             // Create a new dataTable and assign it to a variable t
             var t = $("#schoolFeesBalancesTable").DataTable({
                data: JSON.parse(stringed),
                columns: [
                    { data: null },
                    { data: 'name'},
                    { data: 'class'},
                    { data: 'fees', render: function(d){return  getCurrency(d);}},
                    { data: 'paid', render: function(d){return  getCurrency(d);}},
                    { data: 'balance', render: function(d, type, all){
                        return  getCurrency(d);                            
                        }
                    },
                    { data: null , render: function(d, type, all){
                        percentagePaid = parseInt((all.paid / all.fees) * 100);
                        percentageBalance = parseInt(100 - percentagePaid)
                        return  'Paid: ' + percentagePaid + '%. Balance ' + percentageBalance + '%';
                        }
                    }                                       
                ],
                columnDefs: [ {
                    targets: 0,
                    order: [[ 1, 'asc' ]]
                  } ],                
                createdRow: function(row, data, dataIndex){
                    if(data.balance == null || data.balance > 0 )
                    {
                        $(row).addClass('has-unpaid-balance');
                    }
                },
                footerCallback: function ( row, data, start, end, display ) {
                    var api = this.api();
                    nb_cols = api.columns().nodes().length;
                    var j = 3;
                    var fees;
                    var paid;
                    var balance;
                    while(j < nb_cols-1){
                        var pageTotal = api
                    .column( j, { page: 'allPages'} )
                    .data()
                    .reduce( function (a, b) {
                        return Number(a) + Number(b);
                    }, 0 );
                    // Update footer
                    $( api.column( j ).footer() ).html(getCurrency(pageTotal));
                    switch (j) {
                        case 3:
                            fees = pageTotal;
                            break;
                        case 4:
                            paid = pageTotal;
                            break;
                        case 5:
                            balance = pageTotal;
                            break;                    
                    }
                    
                    
                        j++;
                    }

                    var percentage = function(){
                        percentagePaid = parseInt((paid / fees) * 100);
                        percentageBalance = parseInt(100 - percentagePaid)
                        return  'Paid: ' + percentagePaid + '%. Balance ' + percentageBalance + '%';
                        }
                        $( api.column( 6 ).footer() ).html(percentage);
                } 
                 
            });

            // Use var t to assign a counter column onto the table
            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        },
        error: function (error) {
            console.log(error);
            $('#res').append(error.responseText);
            alert("No Response From Server");
        }
        
    });
}