var Theme = {
    currentRequest: null,

    disableTabStateRestore:false,

    selectLiveSearchAutoStart:0,

    socsupplies: {},

    reconsupplies: {},

    ajaxsupply: false,

    init:function( $ ) {
        this._initHamburgerMenu();
        //this._initMobileMenu();
        this._initScrollTop( $ );
        this.initSelectpicker();
        this.initResponsiveTables();
        this._initTabsStateFromHash( $ );
        this.mainNav($);
        this.miscScripts($);
        this.deleteButtons($);
        this.modalButton($);
        this.initAjaxSearchDash($);
        this.initDateFilter($);
        this.reportDateFitler($);
        this.departmentSections($);
        this.mobileChecker($);
        this.selectDepartment($);
        this.releaseScripts($);
        this.filterShow($);
        this.socReportGenerate($);
        this.reconReportGenerate($);
        this.processSOCsupplies($);
        this.checkDeptSelect($);

        $( '[data-toggle="tooltip"]' ).tooltip();

        this.FormValidationHelper.init();

        $('.counter').counterUp({
            delay: 10,
            time: 1000
        });
    },

    checkDeptSelect: function($){

        if($('#useraccid').length > 0){
            const deptuser = {
                'NURSING': 7,
                'LABORATORY': 6,
                'PHARMACY': 4,
                'HOUSEKEEPING': 8,
                'MAINTENANCE': 8,
                'RADIOLOGY': 5,
                'BUSINESS OFFICE': 9,
                'INFORMATION / TRIAGE': 10,
                'PHYSICAL THERAPY': 14,
                'KONSULTA PROGRAM': 11,
                'CLINIC A': 12,
                'CLINIC B': 12,
                'CLINIC C': 12,
                'CLINIC D': 12,
                'PHILHEALTH - KP': 11,
                'PHILHEALTH - ASC': 7,
                'PHILHEALTH - CLINIC A': 12,
                'DSWD': 10
            };

            var v = $('#useraccid').val();

            for (const key in deptuser) {
                if (deptuser.hasOwnProperty(key)) {
                    if (deptuser[key] === parseInt(v)) {
                        
                    }else{
                        $('div[data-name="department"] select option[value="'+key+'"]').remove();
                        console.log(key);
                    }
                }
            }
            $('#acf-field_63f4a94f212d3').trigger('change');
            Theme.checkDepartment($);
        }
    },

    filterShow: function($){
        if($('.filter-show__item').length > 0){
            $('.filter-show__item').click(function(){
                var c = $(this).find('input');
                var cl = c.attr('data-src');

                if(c.is(':checked')){
                    $('.'+cl).show();
                }else{
                    $('.'+cl).hide();
                }
            });
        }
    },

    releaseScripts:function($){
        $('#acf-field_63e9e505537a8').change(function(){
            var supid = $('#acf-field_63e9e505537a8').val();

            $.ajax ({
                url: $('#ajax-url').val(),
                type: 'POST',
                dataType: 'JSON',
                data: {
                    // the value of data.action is the part AFTER 'wp_ajax_' in
                    // the add_action ('wp_ajax_xxx', 'yyy') in the PHP above
                    action: 'load_release_data',
                    // ANY other properties of data are passed to your_function()
                    // in the PHP global $_REQUEST (or $_POST in this case)
                    sup: supid,
                    },
                beforeSend : function()   {           

                },
                success: function (resp) {
                        console.log(resp);

                        $('.res-date').text(resp.data.purchased_date);
                        $('.res-room').text(resp.data.section);
                       
                    },
                error: function (xhr, ajaxOptions, thrownError) {
                    // this error case means that the ajax call, itself, failed, e.g., a syntax error
                    // in your_function()
                    console.log('Request failed: ' + thrownError.message) ;
                    
                    },
            });
        });
    },

    selectDepartment: function($){
        $('#select-department:not(.recon-dept)').change(function(){
            Theme.reloadPatientDashContents($, $('.search-ajax').val(), $('#select-department').val());
        });
    },

    mobileChecker: function($) {
        if (Theme.isMobile()) {
            $('body').addClass('in-mobile');
        } else {
            $('body').removeClass('in-mobile');
        }
    },

    isMobile: function() {
        if (jQuery(window).width() <= 992) {
            return true;
        } else {
            return false;
        }
    },

    departmentSections: function($){
        Theme.checkDepartment($);
        
        $('#acf-field_63f4a94f212d3').change(function(){
            Theme.checkDepartment($);
        });
    },

    checkDepartment: function($){
        var v = $('#acf-field_63f4a94f212d3').val();
        
            $('#acf-field_64086f91f709d option').hide();
            $('#acf-field_63f315281c018 option[value="Reagent"]').hide();
        
            if(v == "NURSING"){
                $('#acf-field_64086f91f709d option[value="Treatment Room (Clinic A)"]').show();
                $('#acf-field_64086f91f709d option[value="Ambulatory Surgery Center (ASC)"]').show();
        
                $('#acf-field_64086f91f709d').val('Treatment Room (Clinic A)');
            }
        
            if(v == "LABORATORY"){
                $('#acf-field_64086f91f709d option[value="Clinical Chemistry"]').show();
                $('#acf-field_64086f91f709d option[value="Immunology"]').show();
                $('#acf-field_64086f91f709d option[value="Histopathology"]').show();
                $('#acf-field_64086f91f709d option[value="Clinical Microscopy"]').show();
                $('#acf-field_64086f91f709d option[value="Hematology"]').show();
        
                $('#acf-field_64086f91f709d').val('Clinical Chemistry');
                $('#acf-field_63f315281c018 option[value="Reagent"]').show();
            }
        
            if(v == "PHARMACY"){
                $('#acf-field_64086f91f709d option[value="Medical Supplies"]').show();
                $('#acf-field_64086f91f709d option[value="Medicines"]').show();
                $('#acf-field_64086f91f709d option[value="Goods"]').show();
        
                $('#acf-field_64086f91f709d').val('Medical Supplies');
            }
        
            if(v == "HOUSEKEEPING"){
                $('#acf-field_64086f91f709d option[value="Comfort Rooms"]').show();
                $('#acf-field_64086f91f709d option[value="Janitor’s Closet"]').show();
                $('#acf-field_64086f91f709d option[value="Autoclave Room"]').show();
        
                $('#acf-field_64086f91f709d').val('Comfort Rooms');
            }
        
            if(v == "MAINTENANCE"){
                $('#acf-field_64086f91f709d option[value="Transport Vehicle"]').show();
                $('#acf-field_64086f91f709d option[value="Septic Vault"]').show();
                $('#acf-field_64086f91f709d option[value="Generator "]').show();
                $('#acf-field_64086f91f709d option[value="Water Tank System"]').show();
                $('#acf-field_64086f91f709d option[value="Solar"]').show();
                $('#acf-field_64086f91f709d option[value="CCTV"]').show();
        
                $('#acf-field_64086f91f709d').val('Transport Vehicle');
            }
    },

    printReport: function($){
        var loc = ($('#section-list option:selected').attr('data-val') != "all")?$('#section-list option:selected').val():'All Locations';
        var subloc = ($('#subsection-list option:selected').attr('data-val') != "all")?$('#subsection-list option:selected').val():'All Sub Sections';

        var mywindow = window.open('', 'PRINT', 'height=600,width=1200');

        mywindow.document.write('<html><head><title>' + $('#filter-data').attr('data-title')  + '</title>');
        mywindow.document.write('<style>*{font-family: Arial, Helvetica, sans-serif}body{max-width: 1120px;margin: 25px auto}table{border-collapse: collapse;width: 100%;margin-bottom: 15px}table td,table th{border: 1px solid #000;text-align: left;padding: 2px 7px;font-size: 14px}table thh2{text-transform: uppercase;font-size: 18px}h3{font-size: 26px}h1{font-size: 18px;text-transform: uppercase;font-weight: 700}.report__result-header{font-size: 16px;background-color: #000;color: #fff;padding: 5px 15px;margin-bottom: 15px}.actual-field{border: none}.pfooter tr td {text-align:center;}.pfooter,.pfooter td {border:none;}.pfooter .name span {border-top: 1px solid #000;padding: 6px 15px;}.pfooter .name {padding-top: 35px;}#section-list,#subsection-list{display:none;}</style>');
        mywindow.document.write('</head><body>');
        mywindow.document.write('<button onclick="window.print();">Print Report</button>');

        if(loc){
            mywindow.document.write('<h2>Location: ' + loc + '</h2>');
        }

        if(subloc){
            mywindow.document.write('<h2>Sub Section: ' + subloc + '</h2>');
        }

        mywindow.document.write(document.getElementById('report__result').innerHTML);
        mywindow.document.write('<table class="pfooter"><tr><td>Prepared By</td><td>Received By</td></tr><tr><td class="name"><span>'+$('#preparedby').val()+'</span></td><td class="name"><span>Mary Angelie Buñi Atupan</span></td></tr><tr><td>&nbsp;</td><td>Clinic Manager</td></tr></table>');
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        //mywindow.print();
        //mywindow.close();

        return true;
    },

    socReportGenerate: function($){
        if($('.soc-report').length > 0){
            Theme.initialReportDateFitler($, $('.soc-report').attr('dfrom'), $('.soc-report').attr('dto'));
        }
    },

    processSOCsupplies: function($){
        if($('.supplies-json').length > 0){
            var supjson = $('.supplies-json').text();
            supjson = JSON.parse(supjson);
            
            Theme.processBatch($, supjson);
        }
    },

    mergeAndSumJSON: function(json1, json2) {
        const obj1 = JSON.parse(json1);
        const obj2 = JSON.parse(json2);
      
        // Helper function to check if a value is numeric
        const isNumeric = (value) => !isNaN(parseFloat(value)) && isFinite(value);
      
        // Helper function to handle summing numeric values recursively
        const sumValues = (value1, value2) => {
          if (Array.isArray(value1) && Array.isArray(value2)) {
            return value1.map((item, index) => sumValues(item, value2[index]));
          } else if (typeof value1 === 'object' && typeof value2 === 'object') {
            const mergedObj = { ...value1 };
            for (const key in value2) {
              if (value2.hasOwnProperty(key)) {
                mergedObj[key] = key in value1 ? sumValues(value1[key], value2[key]) : value2[key];
              }
            }
            return mergedObj;
          } else if (isNumeric(value1) && isNumeric(value2)) {
            return parseFloat(value1) + parseFloat(value2);
          } else if (isNumeric(value1)) {
            return parseFloat(value1);
          } else if (isNumeric(value2)) {
            return parseFloat(value2);
          } else {
            return value2; // Return value2 if neither value is numeric
          }
        };
      
        // Merge the objects and auto-sum the numeric values recursively
        const mergedObj = sumValues(obj1, obj2);
      
        // Convert the merged object back to a JSON string
        const mergedJSON = JSON.stringify(mergedObj);
        return mergedJSON;
    },

    processBatch: function($, supjson){
        var totalRecords = Object.keys(supjson).length;
        var currentRecord = 0; // Start with the first record
        var batchSize = 1; // Set the batch size to 1 record initially
        var batchInc = 5; // Set the batch size to + 5 each time

        Theme.socsupplies = {}; // clear records

        function processNextBatch() {
            // Calculate the end index for the current batch
            if(batchSize > 50){
                batchSize = 50;
            }else{
                batchSize += batchInc;
            }

            console.log(batchSize);

            var endRecord = Math.min(currentRecord + batchSize, totalRecords);

            var batchData = {};
            // Extract the records for the current batch
            for (var i = currentRecord; i < endRecord; i++) {
                var recordKey = Object.keys(supjson)[i];
                batchData[recordKey] = supjson[recordKey];
            }

            // Calculate progress for the current batch
            var progress = Math.min(((currentRecord / totalRecords) * 100).toFixed(2), 100);
            $("#progress").css("width", progress + "%").text(progress + "%");

            var to = $('.date-to').val();
            var ttodate =  (to.length == 0)?$('#report__result').attr('dto'):to;
            
            // Make the AJAX request with the current batch data
            Theme.ajaxsupply = $.ajax({
                url: $('#ajax-url').val(),
                type: 'POST',
                dataType: 'JSON',
                data: {
                    action: 'batch_process_supplies', // Corrected the action name
                    batchData: batchData, // Pass the data for the current batch
                    to: ttodate
                },
                beforeSend : function()   {           
                   $('.report__result').addClass('overlay');
                },
                success: function(response) {
                    if (response.success) {

                        var bdata = response.data;
                        // Process successful, update the UI or do something with the response

                        currentRecord += batchSize; // Move to the next batch

                        Theme.socsupplies = JSON.parse(Theme.mergeAndSumJSON(JSON.stringify(bdata), JSON.stringify(Theme.socsupplies)));

                        if (currentRecord < totalRecords) {
                            // If there are more records, continue with the next batch
                            processNextBatch();
                        } else {
                            var from = $('.date-from').val();
                            var to = $('.date-to').val();


                            var tfromdate = (from.length == 0)?$('#report__result').attr('dfrom'):from;
                            var ttodate =  (to.length == 0)?$('#report__result').attr('dto'):to;

                            $.ajax ({
                                url: $('#ajax-url').val(),
                                type: 'POST',
                                dataType: 'JSON',
                                data: {
                                    // the value of data.action is the part AFTER 'wp_ajax_' in
                                    // the add_action ('wp_ajax_xxx', 'yyy') in the PHP above
                                    action: 'load_soc_report',
                                    // ANY other properties of data are passed to your_function()
                                    // in the PHP global $_REQUEST (or $_POST in this case)
                                    fromdate: tfromdate,
                                    todate: ttodate,
                                    suppdata: Theme.socsupplies
                                    },
                                success: function (resp) {
                                       if(resp.success){
                                            $('.report__result').html(resp.data);
                                            $('.filter-show__item input').prop('checked', true);
                                       }
                    
                                       $('.report__result').removeClass('overlay');
                                       // All records processed
                                       $("#progress").css("width", "100%").text("100%");
                                    },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    // this error case means that the ajax call, itself, failed, e.g., a syntax error
                                    // in your_function()
                                    console.log('Request failed: ' + thrownError.message) ;
                                    $('.report__result').removeClass('overlay');
                                    },
                            });
                        }
                    } else {
                        // Handle the error
                        $("#result").append(response.data + "<br>");
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX error
                    console.error("Error processing batch:", error);
                    //$("#result").append("An error occurred while processing the batch.<br>");
                }
            });
        }

        $('.report__filter a.btn').click(function(){
            var from = $('.date-from').val();
            var to = $('.date-to').val();

            if(from.length == 0 || to.length == 0){
                return false;
            }

            Theme.ajaxsupply.abort();
            console.log('Request aborted');
        });

        // Start processing the first batch
        processNextBatch();
    },

    reconReportGenerate: function($){
        if($('.init-recon-report').length > 0){
            var aid = false;

            if($('#author-id').length > 0){
                aid = $('#author-id').val();
            }

            Theme.initialReportDateFitler($, $('.init-recon-report').attr('dfrom'), $('.init-recon-report').attr('dto'), aid);
        }
    },

    initialReportDateFitler: function($, dfrom, dto, aid = false){
        var from = dfrom;
        var to = dto;
        var d = false;
        var inc = 'all';
        var exp = 'all';


        if($('.recon-dept').length > 0){
            d = $('.recon-dept').val();
        }

        if(($('.income-cat').length > 0) && ($('.expense-cat').length > 0)){
            inc = $('.income-cat').val();
            exp = $('.expense-cat').val();
        }


        $.ajax ({
            url: $('#ajax-url').val(),
            type: 'POST',
            dataType: 'JSON',
            data: {
                // the value of data.action is the part AFTER 'wp_ajax_' in
                // the add_action ('wp_ajax_xxx', 'yyy') in the PHP above
                action: 'load_' + $('#filter-data').attr('data-report'),
                // ANY other properties of data are passed to your_function()
                // in the PHP global $_REQUEST (or $_POST in this case)
                fromdate: from,
                todate: to,
                dept: d,
                incomecat: inc,
                expensecat: exp,
                author: aid
                },
            beforeSend : function()   {           
                $('.report__result').addClass('overlay');
            },
            success: function (resp) {
                    console.log(resp);
                    if(resp.success){
                        $('.report__result').html(resp.data);
                        $('.filter-show__item input').prop('checked', true);
                    }
                    
                    if($('#filter-data').attr('data-report') == "reconciliation_report"){
                        if($('.supplies-json-recon').length > 0){
                            var supjson = $('.supplies-json-recon').text();
                            supjson = JSON.parse(supjson);
                            
                            Theme.reconBatchProcess($, supjson);
                        }
                    }else{
                        $('.report__result').removeClass('overlay');
                        Theme.actualCountCalculator($);
                        Theme.sectionFilter($);
                        Theme.reconTotal($);
                        Theme.recalculateReconTotal($);
                        Theme.sortRecon($);
                    }
                },
            error: function (xhr, ajaxOptions, thrownError) {
                // this error case means that the ajax call, itself, failed, e.g., a syntax error
                // in your_function()
                console.log('Request failed: ' + thrownError.message) ;
                $('.report__result').removeClass('overlay');
                },
        });

        Theme.actualCountCalculator($);
    },

    reconBatchProcess: function($, supjson){
        if($('.supplies-json-recon').length > 0){
            console.log(supjson);       
            console.log(Object.keys(supjson).length);            
            Theme.processBatchRecon($, supjson);
        }
    },

    processBatchRecon:function($, supjson){
        var totalRecords = Object.keys(supjson).length;
        var currentRecord = 0; // Start with the first record
        var batchSize = 1; // Set the batch size to 1 record initially
        var batchInc = 5; // Set the batch size to + 5 each time

        Theme.reconsupplies = {}; // clear records

        function processNextBatchRecon() {
            // Calculate the end index for the current batch
            if(batchSize > 50){
                batchSize = 50;
            }else{
                batchSize += batchInc;
            }


            var endRecord = Math.min(currentRecord + batchSize, totalRecords);

            var batchData = {};
            // Extract the records for the current batch
            for (var i = currentRecord; i < endRecord; i++) {
                var recordKey = Object.keys(supjson)[i];
                batchData[recordKey] = supjson[recordKey];
            }

            console.log(batchData);

            // Calculate progress for the current batch
            var progress = Math.min(((currentRecord / totalRecords) * 100).toFixed(2), 100);
            $("#progress").css("width", progress + "%").text(progress + "%");

            var to = $('.date-to').val();
            var from = $('.date-from').val();
            var ttodate =  (to.length == 0)?$('#report__result').attr('dto'):to;
            var tfromdate =  (from.length == 0)?$('#report__result').attr('dfrom'):from;
            
            // Make the AJAX request with the current batch data
            Theme.ajaxsupply = $.ajax({
                url: $('#ajax-url').val(),
                type: 'POST',
                dataType: 'JSON',
                data: {
                    action: 'batch_process_supplies_recon', // Corrected the action name
                    batchData: batchData, // Pass the data for the current batch
                    to: ttodate,
                    from: tfromdate
                },
                beforeSend : function()   {           
                   $('.report__result').addClass('overlay');
                },
                success: function(response) {
                    if (response.success) {

                        var bdata = response.data;
                        // Process successful, update the UI or do something with the response

                        currentRecord += batchSize; // Move to the next batch

                        Theme.reconsupplies = JSON.parse(Theme.mergeAndSumJSON(JSON.stringify(bdata), JSON.stringify(Theme.reconsupplies)));
                        console.log(Theme.reconsupplies);

                        if (currentRecord < totalRecords) {
                            // If there are more records, continue with the next batch
                            processNextBatchRecon();
                        } else {
                            var from = $('.date-from').val();
                            var to = $('.date-to').val();


                            var tfromdate = (from.length == 0)?$('#report__result').attr('dfrom'):from;
                            var ttodate =  (to.length == 0)?$('#report__result').attr('dto'):to;

                            $.ajax ({
                                url: $('#ajax-url').val(),
                                type: 'POST',
                                dataType: 'JSON',
                                data: {
                                    // the value of data.action is the part AFTER 'wp_ajax_' in
                                    // the add_action ('wp_ajax_xxx', 'yyy') in the PHP above
                                    action: 'render_recon_output',
                                    // ANY other properties of data are passed to your_function()
                                    // in the PHP global $_REQUEST (or $_POST in this case)
                                    fromdate: tfromdate,
                                    todate: ttodate,
                                    suppdata: Theme.reconsupplies
                                    },
                                success: function (resp) {
                                    console.log(resp);
                                       if(resp.success){
                                            $('.report__result').html(resp.data);
                                            $('.filter-show__item input').prop('checked', true);
                                       }
                    
                                       $('.report__result').removeClass('overlay');
                                       // All records processed
                                       $("#progress").css("width", "100%").text("100%");

                                        Theme.actualCountCalculator($);
                                        Theme.sectionFilter($);
                                        Theme.reconTotal($);
                                        Theme.recalculateReconTotal($);
                                        Theme.sortRecon($);
                                    },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    // this error case means that the ajax call, itself, failed, e.g., a syntax error
                                    // in your_function()
                                    console.log('Request failed: ' + thrownError.message) ;
                                    $('.report__result').removeClass('overlay');
                                    },
                            });
                        }
                    } else {
                        // Handle the error
                        $("#result").append(response.data + "<br>");
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX error
                    console.error("Error processing batch:", error);
                    //$("#result").append("An error occurred while processing the batch.<br>");
                }
            });

            Theme.recalculateReconTotal($);
        }

        $('.report__filter a.btn').click(function(){
            var from = $('.date-from').val();
            var to = $('.date-to').val();

            if(from.length == 0 || to.length == 0){
                return false;
            }

            Theme.ajaxsupply.abort();
            console.log('Request aborted');
        });

        // Start processing the first batch
        processNextBatchRecon();
    },

    sortRecon: function($){
        $('#report__result table').each(function() {
            var $table = $(this);
            var $tbody = $table.find('tbody');
        
            $tbody.sort(function(a, b) {
                var aName = $(a).data('name').toUpperCase();
                var bName = $(b).data('name').toUpperCase();
                return (aName < bName) ? -1 : (aName > bName) ? 1 : 0;
            }).appendTo($table);
        });
    },

    reconTotal: function($){
        if($('.recon-total').length > 0){
            var totp = 0;

            $('.report__result tbody tr:visible').each(function(){
                var q = $(this).find('.row-actual-count input').val();
                var p = $(this).find('.row-price').attr('data-val');

                var t = parseInt(q) * parseFloat(p);
                totp += t;
            });

            let lossdata = $('.sup-loss span').attr('data-val');
            let loss = totp - parseFloat(lossdata);
            let np = loss.toFixed(2);
            let str = parseFloat(np).toLocaleString("en-US");
            

            $('.recon-total span').html("&#8369 " + str);
        }

        if($('.sup-total').length > 0){
            var totp2 = 0;

            $('.report__result tbody.count-supplies tr:visible').each(function(){
                var q2 = $(this).find('.row-actual-count input').val();
                var p2 = $(this).find('.row-price').attr('data-val');

                var t2 = parseInt(q2) * parseFloat(p2);
                
                totp2 += t2;
                
            });

            let np2 = totp2.toFixed(2);
            let str2 = parseFloat(np2).toLocaleString("en-US");

            $('.sup-total span').html("&#8369 " + str2);
        }

        Theme.checkExpired($);
    },

    checkExpired: function($){
        // Get today's date
        const today = new Date();

        // Select all visible and non-empty .filter-exp <td> elements
        $("td.filter-exp:visible").filter(function() {
            // Get the text inside the td and trim any extra spaces
            const dateText = $.trim($(this).html());

            // Check if the <td> has a valid date
            if (dateText !== "") {
                // Parse the date in the format MM/DD/YYYY
                const cellDate = new Date(dateText);

                // Check if the parsed date is valid
                if (!isNaN(cellDate.getTime())) {
                    // Calculate the time difference in milliseconds
                    const timeDifference = cellDate.getTime() - today.getTime();
                    
                    // Convert time difference to days
                    const daysDifference = timeDifference / (1000 * 3600 * 24);
                    
                    // Select all <td> elements in the current row (parent <tr>)
                    const allTdsInRow = $(this).closest("tr").find("td");
                    
                    if (daysDifference <= 180) {
                        allTdsInRow.css({
                            "font-weight": "bold"
                        });
                    }
                }
            }
        });

    },

    recalculateReconTotal: function($){
        $('#section-list, #subsection-list').change(function(){
            Theme.reconTotal($);
        });
    },

    sectionFilter: function($){
        if($('#section-list').length > 0){
            $('#subsection-list').insertAfter('.report__result h1');
            $('#subsection-list').hide();
            
            $('#section-list').insertAfter('.report__result h1');

            $('#section-list').change(function(){
                var v = $(this).val();

                $('tr[data-section]').hide();
                $('tr[data-section="'+ v +'"]').show();

                if(v == "Select Room Section"){
                    $('tr[data-section]').show();
                }

                if(v != "Ambulatory Surgery Center (ASC)"){
                    $('#subsection-list').hide();
                }else{
                    $('#subsection-list').show();
                    $('#subsection-list').trigger('change');
                }
            });

            $('#subsection-list').change(function(){
                var v = $(this).val();

                $('tr[data-subsection]').hide();
                $('tr[data-subsection="'+ v +'"]').show();

                if(v == "Select Sub Section"){
                    $('tr[data-section="Ambulatory Surgery Center (ASC)"]').show();
                }
            });
        }
    },

    reportDateFitler: function($){
        $('.print-btn').click(function(e){
            e.preventDefault();
            
            if($('#preparedby').val().length == 0){
                alert('Kindly place a value in the Prepared By Field.');
                return false;
            }

            Theme.printReport($);
        });

        $('#filter-data a.btn').click(function(e){
            e.preventDefault();
            Theme.socsupplies = {};
            
            var from = $('.date-from').val();
            var to = $('.date-to').val();
            var d = false;
            var inc = 'all';
            var exp = 'all';

            if(from.length == 0 || to.length == 0){
                return false;
            }

            if($('.recon-dept').length > 0){
                d = $('.recon-dept').val();
            }

            if(($('.income-cat').length > 0) && ($('.expense-cat').length > 0)){
                inc = $('.income-cat').val();
                exp = $('.expense-cat').val();
            }


            $.ajax ({
                url: $('#ajax-url').val(),
                type: 'POST',
                dataType: 'JSON',
                data: {
                    // the value of data.action is the part AFTER 'wp_ajax_' in
                    // the add_action ('wp_ajax_xxx', 'yyy') in the PHP above
                    action: 'load_' + $('#filter-data').attr('data-report'),
                    // ANY other properties of data are passed to your_function()
                    // in the PHP global $_REQUEST (or $_POST in this case)
                    fromdate: from,
                    todate: to,
                    dept: d,
                    incomecat: inc,
                    expensecat: exp,
                    suppdata: Theme.socsupplies
                    },
                beforeSend : function()   {           
                   $('.report__result').addClass('overlay');
                },
                success: function (resp) {
                        console.log(resp);
                       if(resp.success){
                            $('.report__result').html(resp.data);
                            $('.filter-show__item input').prop('checked', true);

                            Theme.processSOCsupplies($); //soc

                            //recon
                            if($('.supplies-json-recon').length > 0){
                                var supjson = $('.supplies-json-recon').text();
                                supjson = JSON.parse(supjson);
                                
                                Theme.reconBatchProcess($, supjson);
                            }
                       }

                       if($('.supplies-json').length == 0){
                        $('.report__result').removeClass('overlay');
                       }

                        Theme.actualCountCalculator($);
                        Theme.sectionFilter($);
                        Theme.reconTotal($);
                        Theme.recalculateReconTotal($);
                        Theme.sortRecon($);
                    },
                error: function (xhr, ajaxOptions, thrownError) {
                    // this error case means that the ajax call, itself, failed, e.g., a syntax error
                    // in your_function()
                    console.log('Request failed: ' + thrownError.message) ;
                    $('.report__result').removeClass('overlay');
                    },
            });
        });

        Theme.actualCountCalculator($);
    },

    numberWithCommas: function(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },

    actualCountCalculator: function($){
        $('.row-actual-count .actual-field').change(function(){
            var v = parseFloat($(this).val()).toFixed(2);
            var p = parseFloat($(this).parents('tr').find('.row-price').attr('data-val')).toFixed(2);
            var o = parseFloat($(this).parents('tr').find('.orig-count').attr('data-val')).toFixed(2);
            
            var np = (v * p);

            let n = np;
            let str = n.toLocaleString("en-US");
            
            $(this).parents('tr').find('.row-variance').html(v - o);
            $(this).parents('tr').find('.row-total').html("&#8369 " + str);
            $(this).attr('value', $(this).val());

            Theme.reconTotal($);
        });
    },

    initDateFilter: function($){
        $( ".date-from" ).datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
              $( ".date-to" ).datepicker( "option", "minDate", selectedDate );
            }
          });

          $( ".date-to" ).datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
              $( ".date-from" ).datepicker( "option", "maxDate", selectedDate );
            }
          });
    },

    modalButton: function($){
        $('.edit-item').click(function(e){
            e.preventDefault();

            var b = $(this).attr('item-id');
            var params = {
                'id': $(this).attr('item-id'), 
                'form': $('.custom-post__add-form').attr('form-id')
            };

            $.ajax ({
                url: $('#ajax-url').val(),
                type: 'POST',
                dataType: 'JSON',
                data: {
                    // the value of data.action is the part AFTER 'wp_ajax_' in
                    // the add_action ('wp_ajax_xxx', 'yyy') in the PHP above
                    action: 'edit_item',
                    p: params
                    // ANY other properties of data are passed to your_function()
                    // in the PHP global $_REQUEST (or $_POST in this case)
                    },
                beforeSend : function()    {           
    
                },
                success: function (resp) {
                    console.log(resp);
                        if(resp.success){
                            $('.modal__content').html(resp.data);
                            $('.modal-container').css('display','flex');   
                            $('.modal__close').unbind();
                            $('.modal__close').click(function(){
                                location.reload();
                                //$('.modal-container').hide();
                            });

                            $('.modal-container').click(function(e){
                                if (e.target == this) {
                                    location.reload();
                                    //$('.modal-container').hide();
                                }
                            });

                            acf.do_action('append', $('.modal__content .acf-form'));
                            Theme.miscScripts($);
                        }
                    },
                error: function (xhr, ajaxOptions, thrownError) {
                    // this error case means that the ajax call, itself, failed, e.g., a syntax error
                    // in your_function()
                    console.log('Request failed: ' + thrownError.message) ;
                    },
            });
        });
    },

    deleteButtons: function($){
        $('.delete-item').click(function(e){
            e.preventDefault();
            Theme.modalDialog($, $(this).attr('href'), $(this).attr('title'));
        });
    },

    modalDialog: function($, url, title){
        $('<div></div>').appendTo('body')
        .html('<div><h2>'+title+'</h2></div>')
        .dialog({
            modal: true, title: 'Please Confirm', zIndex: 10000, autoOpen: true,
            width: 'auto', resizable: false,
            buttons: {
                Yes: function () {
                    $( location ).attr("href", url);
                    $(this).dialog("close");
                },
                No: function () {                                                                 
                    $(this).dialog("close");
                }
            },
            close: function (event, ui) {
                $(this).remove();
            }
        });
    },

    initAjaxSearchDash: function($){
        var ajaxfield = $('.search-ajax');

        Theme.currentRequest = null;

        ajaxfield.keyup(function(){
            var dept = ($('#select-department').length > 0)?$('#select-department').val():false;

            if($(this).val().length > 0){
                Theme.reloadPatientDashContents($, $(this).val(), dept);
            }else{
                Theme.reloadPatientDashContents($, false, dept);
            }
        });
    },

    reloadPatientDashContents: function($, search=false, d=false){
        Theme.currentRequest = $.ajax ({
            url: $('#ajax-url').val(),
            type: 'POST',
            dataType: 'JSON',
            data: {
                // the value of data.action is the part AFTER 'wp_ajax_' in
                // the add_action ('wp_ajax_xxx', 'yyy') in the PHP above
                action: 'load_items_per_search',
                // ANY other properties of data are passed to your_function()
                // in the PHP global $_REQUEST (or $_POST in this case)
                search : search,
                pt: $('.custom-post__list').attr('data-pt'),
                dept: d
                },
            beforeSend : function()    {           
                if(Theme.currentRequest != null) {
                    Theme.currentRequest.abort();
                }

               Theme.initShowOverlay($);
            },
            success: function (resp) {
                    console.log(resp);
                   if(resp.success){
                        $('.custom-post__list').html(resp.data);
                        Theme.deleteButtons($);
                        Theme.modalButton($);
                        Theme.initResponsiveTables();
                   }

                   Theme.removeOverlay($);
                },
            error: function (xhr, ajaxOptions, thrownError) {
                // this error case means that the ajax call, itself, failed, e.g., a syntax error
                // in your_function()
                console.log('Request failed: ' + thrownError.message) ;
                Theme.removeOverlay($);
                },
        });
    },

    initShowOverlay: function($){
        $('.custom-post__list').addClass('overlay');
    },

    removeOverlay: function($){
        $('.custom-post__list').removeClass('overlay');
    },

    miscScripts: function($){
        $('.acf-field-date-picker').each(function(){
            var t = $(this).find('.acf-label label').text();
            $(this).find('.acf-input .input').attr('placeholder', t);
        });
        
        $('.acf-form .acf-form-submit a.acf-button').click(function(e){
            e.preventDefault();

            $(this).parents('.acf-form').submit();
        });
        
        $('.label-custom').remove();

        $('.acf-field input[type="text"], .acf-field input[type="number"]').each(function(){
            var pl = $(this).attr('placeholder');
            $('<div class="label-custom" style="display:none;">'+pl+'</div>').prependTo($(this).parent());

            if($(this).val() != ""){
                $(this).parent().find('.label-custom').show();
            }else{
                $(this).parent().find('.label-custom').hide();
            }
        });

        $('.acf-field input[type="text"], .acf-field input[type="number"]').change(function(){
            if($(this).val() != ""){
                $(this).parent().find('.label-custom').show();
            }else{
                $(this).parent().find('.label-custom').hide();
            }
        });
    },

    mainNav: function($){
        $('.main-nav.disabled a').attr('href', '#');

        $('.main-nav.disabled a').click(function(e){
            e.preventDefault();
        });

        if(Theme.isMobile()){
            $('#navMenu').removeClass('active');
            $('.overall-menu').removeClass('expanded');
            $('.overall-menu__logo-outside').show();
            $('.login-status').hide();
            $('.page-breadcrumbs').css('margin', '80px 0 0 60px');
            $('.layout-container').css('margin', '15px 0 0 60px');
        }

        $('#navMenu').click(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                $('.overall-menu').removeClass('expanded');
                $('.overall-menu__logo-outside').show();
                $('.login-status').hide();
                $('.page-breadcrumbs').css('margin', '80px 0 0 60px');
                $('.layout-container').css('margin', '15px 0 0 60px');
            }else{
                $(this).addClass('active');
                $('.overall-menu').addClass('expanded');
                $('.overall-menu__logo-outside').hide();
                $('.login-status').show();
                $('.page-breadcrumbs').css('margin', '80px 0 0 270px');
                $('.layout-container').css('margin', '15px 0 0 270px');
            }
        });

        $('#navigation > li > a').attr('href','#');

        $('#navigation > li > a').click(function(e){
            e.preventDefault();

            var url = $(this).parent().find('.sub-menu li:first-child a').attr('href');

            window.location.href = url;
        });

        $('.um-right.um-half a').click(function(e){
            e.preventDefault();

            $('.login-status__forms-login').hide();
            $('.login-status__forms-register').show();
        });

        if($('.login-status__forms-register .um-field-error').length > 0) {
            $('.login-status__forms-login').hide();
            $('.login-status__forms-register').show();
        }

        $('.container.layout-container').css('max-width', ($(window).width() - 270)+'px');

        $(window).resize(function(){
            $('.container.layout-container').css('max-width', ($(window).width() - 270)+'px');
        });
    },

    _initHamburgerMenu: function() {
        var menuTrigger = jQuery('#hamburger-menu'),
            bottomLayer = jQuery('.bottom_layer'),
            mainNav = jQuery('.main-nav-header');

        menuTrigger.on('click', function(e){
            e.preventDefault();

            if ( menuTrigger.hasClass('hamburger-menu-close') ) {
                menuTrigger.removeClass('hamburger-menu-close').addClass('hamburger-menu-open');
                bottomLayer.css('visibility', 'visible');
                //mainNav.show('fast');
            } else {
                menuTrigger.removeClass('hamburger-menu-open').addClass('hamburger-menu-close');
                bottomLayer.css('visibility', 'hidden');
                //mainNav.hide('fast');
            }
        });
    },

    initSVGanimation: function( config ) {
        // For option list check https://github.com/maxwellito/vivus
        var cfg = jQuery.extend( {
                svgSelector:'',
            }, config || {}),
            svgCfg = {
                type: 'oneByOne',
                duration: 60,
                animTimingFunction: Vivus.EASE
            };

        if ( ! cfg.svgSelector ) {
            return null;
        }

        if ( cfg.vivusOptions ) {
            jQuery.extend( svgCfg, cfg.vivusOptions );
        }

        var noWinNoFee = new Vivus(cfg.svgSelector, svgCfg);
    },

    initGoogleMap: function( cfg ) {
        if ( 'undefined' == typeof( cfg ) ) {
            return;
        }

        var mapElement = document.getElementById( cfg.element_id );

        if ( ! mapElement ) {
            return;
        }

        var jMap = jQuery( mapElement );
        jMap.height( cfg.height );

        if ( cfg.full_width ) {
            var onResizeHandler = function() {
                jMap.width( jQuery( window ).outerWidth() )
                    .offset( { left:0 } );
                if ( map ) {
                    //google.maps.event.trigger(map, 'resize');
                    if ( mapLang ) {
                        map.setCenter( mapLang );
                    }
                }
            };
            onResizeHandler();
            jQuery( window ).on( 'resize', onResizeHandler );
        }

        var mapLang = new google.maps.LatLng( parseFloat( cfg.coordinates[0] ), parseFloat( cfg.coordinates[1] ) ),
            map = new google.maps.Map( mapElement, {
                scaleControl: true,
                center: mapLang,
                zoom: cfg.zoom,
                mapTypeId: cfg.MapTypeId || google.maps.MapTypeId.ROADMAP,
                scrollwheel: false
            }),
            marker = new google.maps.Marker({
                map: map,
                position: map.getCenter()
            });

        // Registers map instance in _inited_maps collection.
        if ( ! this._inited_maps ) this._inited_maps = {};
        this._inited_maps[cfg.element_id] = map;

        if ( cfg.address ) {
            var infowindow = new google.maps.InfoWindow();
            infowindow.setContent( cfg.address );
            google.maps.event.addListener( marker, 'click', function() {
                infowindow.open( map, marker );
            });
        }

        // Fix display map in bootstrap tabs and accordion.
        if ( cfg.is_reset_map_fix_for_bootstrap_tabs_accrodion ) {
            jQuery( document ).on( 'shown.bs.collapse shown.bs.tab', '.panel-collapse, a[data-toggle="tab"]', function() {
                google.maps.event.trigger( map, 'resize' );
                map.setCenter( mapLang );
            });
        }
    },

    initStickyHeader: function() {
        var doc = jQuery( document ),
            headerIsSticky = false,
            headerWrap = jQuery( '.header-wrap' ),
            headerInfo = headerWrap.find( '.header__info' ),
            headerBacklog = headerWrap.find( '.header-wrap__bundle' ),
            headerWrapClassSticky = 'header-wrap--sticky-header',
            stickyBreakpoint = null,
            switchHeightDelay = 0,
            calculateHeaderInfo = function(){
                stickyBreakpoint = headerInfo.outerHeight() + switchHeightDelay;

                headerBacklog.css({
                    'min-height': headerWrap.find( '.header__content-wrap' ).outerHeight() + 'px',
                    'margin-top': stickyBreakpoint + 'px'
                });
            };
        setTimeout( calculateHeaderInfo, 10 );
        jQuery( window ).on( 'resize', calculateHeaderInfo );

        doc.on( 'scroll', function() {
            var newState = doc.scrollTop() > stickyBreakpoint;
            if ( newState != headerIsSticky ) {
                headerIsSticky = newState;
                if ( newState ) {
                    headerWrap.addClass( headerWrapClassSticky );
                } else {
                    headerWrap.removeClass( headerWrapClassSticky );
                }
            }
        });
    },

    initResponsiveTables: function() {
        jQuery(".page-single__content table").each(function(){
            jQuery(this).addClass('table');
            jQuery(this).wrapAll('<div class="table-responsive" />');
        });
        jQuery("#taxes-pay-table").each(function(){
            jQuery(this).addClass('table');
            jQuery(this).wrapAll('<div class="table-responsive" />');
        });
    },

    initFocusButtonContents: function() {
        var self = this;
        jQuery(".focus-button__item-link-content").on( "click", function(e) {
            e.preventDefault();
            console.log('clicked');
            var wrapperId = jQuery( this ).attr('href');
            jQuery(".focus-button__item").removeClass('active');
            jQuery(this).closest( ".focus-button__item" ).addClass('active');
            //jQuery('.page-single').hide();
            //jQuery('#focus-buttons__content-wrapper').show();
            jQuery( '.focus-buttons__content-wrap').hide();
            jQuery( wrapperId ).show();
            if ( jQuery( window ).width() > 768 ) {
                self._scrollToElementHelper( wrapperId, 200);
            } else {
                self._scrollToElementHelper( wrapperId, 140);
            }
            
        });
    },

    initFocusButtonScrollToContent: function( activated ) {
        var self = this;
        if ( activated ) {
            jQuery( document ).ready(function() {
                console.log( "ready!" );
                var wrapperId = jQuery( '.focus-buttons__content');
                if ( jQuery( window ).width() > 768 ) {
                    self._scrollToElementHelper( wrapperId, 200);
                } else {
                    self._scrollToElementHelper( wrapperId, 140);
                }
            });
        }
    },

    _scrollToElementHelper: function( wrapperId, offsetFix) {
        jQuery( 'html, body' ).animate({
                scrollTop: jQuery( wrapperId ).offset().top - offsetFix
        }, 2000);
    },

    /**
     * Initialization modile menu.
     * @use jquery.slicknav.js, slicknav.css
     * @return void
     */
    _initMobileMenu:function() {
        var mainBtn,
            closeClass = 'slicknav_btn--close',
            itemClass = 'slicknav_item',
            itemOpenClass = 'slicknav_item--open';

        jQuery( '#navigation' ).slicknav({
            label:'',
            prependTo:'.header__content',
            openedSymbol: '',
            closedSymbol: '',
            allowParentLinks:true,
            beforeOpen: function( target ) {
                if ( target.length ) {
                    if ( target[0] == mainBtn ) {
                        target.addClass( closeClass );
                    }else if ( target.hasClass( itemClass ) ) {
                        target.addClass( itemOpenClass );
                    }
                }
            },
            beforeClose: function( target ) {
                if( target.length ){
                    if( target[0] == mainBtn ) {
                        target.removeClass( closeClass );
                    }else if( target.hasClass( itemClass ) ) {
                        target.removeClass( itemOpenClass );
                    }
                }
            }
        });

        mainBtn = jQuery( '.slicknav_btn' );
        mainBtn = mainBtn.length ? mainBtn[0] : null;
    },

    /**
     * Initialization custom select box.
     *
     * @use bootstrap.min.js, bootstrap-select.min.js, bootstrap-select.min.css
     * @param String|jQuery elements
     * @return void
     */
    initSelectpicker: function( elements ) {
        var self = this,
            collection = elements ? jQuery( elements ) : null;

        if ( null === collection ) {
            collection = jQuery( 'select.selectpicker' )
                .add( '.widget select' ); // Use .add() to add select elements.
        }

        if ( ! collection  || collection.length < 1 ) {
            return false;
        }

        var liveAutoStartLimit =  this.selectLiveSearchAutoStart;
        if ( liveAutoStartLimit > 0 ) {
            collection.each(function() { // .not('[data-live-search]')
                if ( this.children.length >= liveAutoStartLimit  && ! this.hasAttribute( 'data-live-search' ) ) {
                    jQuery( this ).attr( 'data-live-search', true );
                }
            });
        }

        collection.each(function() {
                // var el = jQuery( this );
                // if ( el.attr('multiple') ) {
                //     el.selectpicker({selectedTextFormat:'static'});
                // } else {
                //     el.selectpicker();
                // }
                self._fixSelectpickerEmptyClass( this );
                //self._fixSelectpickerMultiSelectedText( this );
            })
            .on( 'change', function() {
                self._fixSelectpickerEmptyClass( this );
            }
        );
    },

    _fixSelectpickerEmptyClass:function( node ) {
        var el = jQuery( node ),
            isSelectpicker = el.data( 'selectpicker' ) ? true : false;
        if ( ! isSelectpicker ) {
            return;
        }
        var emptyClass = 'selectpicker--empty';
        if ( el.val() ) {
            el.selectpicker( 'setStyle', emptyClass, 'remove' );
        } else {
            el.selectpicker( 'setStyle', emptyClass, 'add' );
        }
    },

    _fixSelectpickerMultiSelectedText:function( node ) {
        var el = jQuery( node );
        if ( ! isSelectpicker ) {
            return;
        }

        if ( el.hasAttr('multiple') ) {
            el.selectpicker( {selectedTextFormat:'static'} );
        }
    },

    _initTabsStateFromHash:function( $ ) {
        if ( this.disableTabStateRestore || ! document.location.hash ) {
            return;
        }
        var hash = document.location.hash;
        if ( hash.search( 'accordion' ) < 0 ) {
            var tabLink = $( '.nav-tabs a[href="' + hash + '"]' );
            if ( tabLink.length ) {
                tabLink.tab( 'show' );
            }
        } else {
            var accordionLink = $( '.accordion__item a[href="' + hash + '"]' );
            if ( accordionLink.length ) {
                accordionLink.trigger( 'click' );
            }
        }
    },

    _makeDatepickerConfig:function( customOptions ) {
        if ( window.ThemeSDDatepickerCfg ) {
            return jQuery.extend( {}, window.ThemeSDDatepickerCfg, customOptions || {} );
        }
        return customOptions;
    },

    initResizeHandler: function( config ) {
        var cfg = jQuery.extend( {
            deviceType:'desktop',
            }, config || {});
        _sliderResizeHandler = function(){
            var windowWidth = jQuery( window ).width();
            var deviceType = cfg.deviceType;

            if (windowWidth >= 992) {
                deviceType = 'desktop';
            } else {
                deviceType = 'mobile';
            }

            var isNewValue = cfg.deviceType != deviceType;

            if ( isNewValue ) {
                if ( isNewValue ) {
                    cfg.deviceType = deviceType;
                    switch (cfg.deviceType) {
                        case 'desktop':
                            jQuery('#eem-slider').hide();
                            jQuery('#dee-slider').show();
                            break;
                        case 'mobile':
                            jQuery('#dee-slider').hide();
                            jQuery('#eem-slider').show();
                            break;
                    }
                }
            }
        };
        jQuery( window ).on( 'resize', _sliderResizeHandler );//.trigger('resize');
    },

    /**
     * Create swiper sliders.
     *
     * @param numSlides config
     */
    makeSwiper: function( config ) {
        var cfg = jQuery.extend( {
            containerSelector:'',
            slidesNumber:4,
            navPrevSelector:'',
            navNextSelector:'',
            sliderElementSelector:'.swiper-slider',
            slideSelector: '.swiper-slide',
            widthToSlidesNumber:function( windowWidth, slidesPerView ) {
                var result = slidesPerView;
                if (windowWidth > 992) {

                } else if(windowWidth > 768) {
                    //result = Math.max(3, Math.ceil(slidesPerView / 2));
                    result = Math.ceil(slidesPerView / 2);
                } else if (windowWidth > 670) {
                    result = 2;
                } else {
                    result = 1;
                }

                return result;
            }
        }, config || {} );
        if ( ! cfg.containerSelector ) {
            return null;
        }

        var numSlides = cfg.slidesNumber,
            container = jQuery( cfg.containerSelector ),
            sliderElement = container.find( cfg.sliderElementSelector ),
            realSlidesNumber = sliderElement.find( cfg.slideSelector ).length,
            swiperCfg = {
                slidesPerView: numSlides,
                spaceBetween: 30,
                loop: numSlides < realSlidesNumber
                //,loopedSlides: 0
            };
        if ( cfg.swiperOptions ) {
            jQuery.extend( swiperCfg, cfg.swiperOptions );
        }

        var swiper = new Swiper( sliderElement, swiperCfg ),
            navButtons = null,
            naviPrev = null,
            naviNext = null;
        if( cfg.navPrevSelector ) {
            naviPrev = container.find( cfg.navPrevSelector );
            if ( naviPrev.length ) {
                naviPrev.on( 'click', function( e ) {
                    e.preventDefault();
                    swiper.slidePrev();
                });
                navButtons = jQuery( naviPrev );
            }
        }
        if ( cfg.navNextSelector ) {
            naviNext = container.find( cfg.navNextSelector );
            if ( naviNext.length ) {
                naviNext.on( 'click', function( e ) {
                    e.preventDefault();
                    swiper.slideNext();
                });
                navButtons = navButtons ? navButtons.add( naviNext ) : jQuery( naviNext );
            }
        }

        var isFirstCall = true,
        _resizeHandler = function(){
            var slidesPerView = numSlides;

            if ( cfg.widthToSlidesNumber && 'function' == typeof cfg.widthToSlidesNumber ) {
                slidesPerView = cfg.widthToSlidesNumber( jQuery( window ).width(), numSlides );
            }

            var isNewValue = swiper.params.slidesPerView != slidesPerView;

            if ( isFirstCall || isNewValue ) {
                if ( isNewValue ) {
                    swiper.params.slidesPerView = slidesPerView;
                    swiper.update();
                }

                if ( navButtons ) {
                    if ( slidesPerView < realSlidesNumber && realSlidesNumber > 1 ) {
                        navButtons.show();
                    } else {
                        navButtons.hide();
                    }
                }
                if ( isFirstCall ) {
                    isFirstCall = false;
                }
            }
        };
        jQuery( window ).on( 'resize', _resizeHandler );//.trigger('resize');
        _resizeHandler();
    },

    /**
     * Create slideshows.
     *
     * @param numSlides config
     */
    makeSlider: function( config ) {
        var cfg = jQuery.extend( {
            sliderSelector:'',
            slideTransitionType:'',
            nextSelector:'',
        }, config || {}),
        sliderCfg = {
            autoplay: true,
            dots: false,
            arrows: false,
            infinite: true,
            speed: 500,
            fade: true,
            cssEase: 'linear',
            slidesToShow: 1,
            slidesToScroll: 1,
        };
        if ( ! cfg.sliderSelector && ! cfg.slideTransitionType ) {
            return null;
        }

        if ( cfg.sliderOptions ) {
            jQuery.extend( sliderCfg, cfg.sliderOptions );
        }

        var slider = jQuery( cfg.sliderSelector ).slick( sliderCfg );

        if ( 'click' === cfg.slideTransitionType ) {
            jQuery( cfg.nextSelector ).on( 'click', function(){
                slider.slick('slickNext');
            });
        }
    },

    /**
     * Modify slideshow delay.
     *
     * @param numSlides config
     */
    //modifySliderDelay: function( slider, currentSlide, imagePauses ) {
    //    var self = this;
    //    var cfg = jQuery.extend( {
    //        mode: 'horizontal',
    //        infiniteLoop: true,
    //        auto: false,
    //        autoStart: false,
    //        autoDirection: 'next',
    //        autoHover: true,
    //        autoControls: false,
    //        pager: true,
    //        pagerType: 'full',
    //        controls: true,
    //        captions: true,
    //        speed: 500,
    //        startSlide: startSlide,
    //        onSlideAfter: function($el,oldIndex, newIndex){
    //            self( slider, currentSlide, imagePauses[newIndex] );
    //        }
    //    }, config || {});
    //
    //    setTimeout( slider.goToNextSlide(), imagePauses[currentSlide] );
    //
    //    slider.reloadSlider( cfg );
    //},

    initParallax: function( selector ) {
        if ( ! selector ) {
            selector = '.parallax-image';
        }

        jQuery( selector ).each(function() {
            var element = jQuery( this ),
                speed = element.data( 'parallax-speed' );
            element.parallax( "50%", speed ? speed : 0.4 );
        });
    },

    _initScrollTop: function( $ ) {
        var document = $( 'body, html' ),
            link = $( '.footer__arrow-top' ),
            windowHeight = $( window ).outerHeight(),
            documentHeight = $( document ).outerHeight();

        if( windowHeight >= documentHeight ) {
            link.hide();
        }

        link.on( 'click', function( e ) {
            e.preventDefault();

            document.animate({
                scrollTop: 0
            }, 800 );
        });
    },

    init_faq_question_form: function( formSelector ) {
        var form = jQuery( formSelector ),
            formContent = jQuery( '.form-block__content' ),
            formElMsgSuccess = jQuery( '.form-block__validation-success' );

        if ( form.length < 1 ) {
            return;
        }

        var noticeWrapper = form.find( '.form-block__validation-error' ),
            resetFormErrors = function() {
                form.find( '.field-error-msg' ).remove();
                noticeWrapper.html( '' );
            };

        Theme.FormValidationHelper.initTooltip();

        form.on( 'submit', function( e ) {
            //e.preventDefault();
            var dataArray = form.serializeArray(),
                formData = {};

            jQuery.each( dataArray, function( i, item ) {
                formData[item.name] = item.value;
            });

            jQuery.ajax({
                url: form.attr( 'action' ),
                data: formData,
                method:'POST',
                error:function( responseXHR ) {
                    var res = responseXHR.responseJSON ? responseXHR.responseJSON : {};
                    resetFormErrors();
                    Theme.FormValidationHelper.formReset( formSelector );

                    if ( res.field_errors ) {
                        jQuery.each( res.field_errors, function( fieldKey, message ) {
                            var el = form.find( '[name*="[' + fieldKey + ']"]' );
                            el.tooltip( 'destroy' );
                            setTimeout(function() {
                                Theme.FormValidationHelper.initTooltip( el );
                                Theme.FormValidationHelper.itemMakeInvalid( el, message );
                            }, 200 );
                        });
                    }

                    if ( res.error ) {
                        noticeWrapper.html( '<i class="fa fa-exclamation-triangle"></i>' + res.error );
                    }
                },
                success:function( res ) {
                    resetFormErrors();
                    Theme.FormValidationHelper.formReset( formSelector );
                    if ( res.message ) {
                        formContent.fadeOut( 400, function() {
                            formElMsgSuccess.html( res.message );
                        });
                    }
                    if ( res.success ) {
                        form[0].reset();
                    }
                }
            });

            return false;
        });
    },

    /**
     * Initilize sharrre buttions.
     * @param  object config
     * @return void
     */
    initSharrres: function( config ) {
        if ( ! config || typeof config != 'object' || ! config.itemsSelector ) {
            //throw 'Parameters error.';
            return;
        }

        var curlUrl = config.urlCurl ? config.urlCurl : '',
            elements = jQuery( config.itemsSelector );

        if ( elements.length < 1 ) {
            return;
        }

        var initSharreBtn = function() {
            var el = jQuery( this ),
                url = el.parent().data( 'urlshare' ),
                imageUrl = el.parent().data( 'imageshare' ),
                curId = el.data( 'btntype' ),
                curConf = {
                    urlCurl: curlUrl,
                    enableHover: false,
                    enableTracking: true,
                    url: ( '' !== url ) ? url : document.location.href,
                    share: {},
                    buttons: {
                        pinterest: {
                            media: imageUrl
                        },
                        //vk: {
                        //    image: imageUrl
                        //}
                    },
                    click: function( api, options ) {
                        api.simulateClick();
                        api.openPopup( curId );
                    }
                };

            curConf.share[curId] = true;
            el.sharrre( curConf );
        };
        elements.each( initSharreBtn );

        // To prevent jumping to the top of page on click event.
        setTimeout(function() {
            jQuery( 'a.share,a.count', config.itemsSelector ).attr( 'href', 'javascript:void(0)' );
        }, 1500 );
    },

    /**
     * Initilize Search Form in popup.
     * @use jquery.magnific-popup.min.js magnific-popup.css
     * @return void
     */
    initSerchFormPopup: function( config ) {
        var classHide = 'search-form-popup--hide',
            cfg = jQuery.extend({
                placeholder_text: 'Type in your request...'
            }, config || {});

        jQuery( '.popup-search-form' ).magnificPopup({
            type: 'inline',
            preloader: false,
            focus: '#s',
            //closeMarkup: '<button title="%title%" type="button" class="mfp-close"><i class="fa fa-times"></i></button>',
            showCloseBtn: false,
            removalDelay: 500, // Delay removal by X to allow out-animation.
            fixedContentPos: false,
            callbacks: {
                beforeOpen: function() {
                    this.st.mainClass = this.st.el.attr( 'data-effect' );
                },
                open: function() {
                    this.content.removeClass( classHide );
                    jQuery( '.mfp-close' ).on( 'click', function() {
                        jQuery.magnificPopup.close();
                    });
                },
                close: function() {
                    this.content.addClass( classHide );
                }
            },
            midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
        });

        if ( cfg.placeholder_text ) {
            jQuery( '.search-form-popup' )
                .find( '.search-field' )
                .attr( 'placeholder', cfg.placeholder_text );
        }
    },

};

/**
 * Gallery plugin.
 * Enables filtering and pagination functionalities.
 *
 * @param {jQuery|selector} container
 * @param {Oject}           config
 */
Theme.Gallery = function( container, config ) {
    if ( config ) {
        jQuery.extend( this, config );
    }

    this.cnt = jQuery( container );

    this._init();
};

Theme.Gallery.prototype = {

    paginationSl: '.pagination',
    imagesContainerSl:'.gallery__items',
    filterButtonsSl: '.gallery__navigation a',
    filterButtonActionClass: 'gallery__navigation__item-current',
    animationClass: 'animated',
    _jPager:null,

    /**
     * Settings for jPages plugin
     *
     * @see initPagination
     * @type {Object}
     */
    paginationConfig:{
        // container: '#galleryContatiner1 .gallery__items',
        perPage: 9,
        animation:'fadeIn',
        previous: '',
        next: '',
        minHeight: false
    },

    getPagerEl:function() {
        return this.paginationSl ? this.cnt.find( this.paginationSl ) : null;
    },

    getImagesContEl:function() {
        return this.cnt.find( this.imagesContainerSl );
    },

    /**
     * Initilize gallery.
     * @use jquery.swipebox.js, swipebox.css, jPages.js
     *
     * @return void
     */
    _init: function( contSelector ) {
        if ( this.cnt.length < 1 ) {
            // throw 'configuration error';
            return;
        }

        this.cnt.find( '.swipebox' ).swipebox({
            useSVG: true,
            hideBarsDelay: 0,
            loopAtEnd: true
        });

        this._initPagination();
        this._initFilter();
    },

    /**
     * Initilize gallery pagination.
     *
     * @use jPages.js
     * @return void
     */
    _initPagination:function() {
        var paginationEl = this.getPagerEl();

        if ( ! paginationEl || paginationEl.length < 1 ) {
            return;
        }

        if ( this._jPager ) {
            this._jPager.jPages( 'destroy' );
        }

        this._jPager = paginationEl.jPages(
            jQuery.extend({
                    container : this.getImagesContEl()
                },
                this.paginationConfig
            )
        );
    },

    /**
     * Initilize gallery filter.
     * @param container selector, wrap gallery
     * @param filterButtons selector
     * @return void
     */
    _initFilter:function( container, filterButtons ) {
        var filterButtonsEl = this.filterButtonsSl ? this.cnt.find( this.filterButtonsSl ) : null;
        if ( ! filterButtonsEl && ! filterButtonsEl.length ) {
            return;
        }

        var self = this,
            items = this.getImagesContEl().children();

        /**
         * Items animation use jPages animation, when pagination off.
         */
        var _itemsAnimation = function() {
            if( self._jPager ) {
                return;
            }

            var customAnimationClass = self.paginationConfig.animation;
            if( ! customAnimationClass ) {
                return;
            }

            var animationClasses = self.animationClass + ' ' + customAnimationClass;
            items.addClass( animationClasses );
            setTimeout( function() {
                items.removeClass( animationClasses );
            }, 600 );
        };

        _itemsAnimation();

        filterButtonsEl.on( 'click', function( e ) {
            e.preventDefault();
            var idFilter = jQuery( this ).data( 'filterid' ),
                btnActiveClass = self.filterButtonActionClass;

            filterButtonsEl.parent()
                .removeClass( btnActiveClass );

            jQuery( this ).parent()
                .addClass( btnActiveClass );

            if( ! idFilter ) {
                idFilter = 'all';
            }

            var filtered = idFilter == 'all' ? items : items.filter( '[data-filterid*="' + idFilter + '"]' ),
                needShow = filtered,// filtered.filter(':not(:visible)'),
                needHide = items.not( filtered );//.filter(':visible');

            if ( ! needShow.length && ! needHide.length ) {
                return; // Nothing to do.
            }

            _itemsAnimation();

            needHide.hide();
            needShow.show();

            if ( self._jPager ) {
                self._initPagination();
            }
        });
    }
};

/**
 * Form validation helper.
 * @use bootstrap.min.js, bootstrap-custom.css
 */
Theme.FormValidationHelper = {
    options: {
        itemsValidationClass: 'form-validation-item',
        emailValidationRegex: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
    },

    errors: {
        requiredField: 'Fill in the required field.',
        emailInvalid: 'Email invalid.',
    },

    init: function() {
        this.initTooltip(
            jQuery( '.' + this.options.itemsValidationClass )
        );
        this.initContactForm7CustomValidtion();
    },

    /**
     * Initialization tooltips.
     * @param selector|jQuery items
     * @return void
     */
    initTooltip: function( items ) {
        if ( 'string' == typeof items ) {
            items = jQuery( items );
        }else if ( 'undefined' == typeof items ) {
            items = jQuery( '.' + this.options.itemsValidationClass );
        }

        if ( items.length < 1 ) {
            return items;
        }

        items
            .tooltip({
                trigger: 'manual',
                animation: true,
                delay: 0
            })
            .on( 'focus', function() {
                jQuery( this ).tooltip( 'destroy' );
            });
        return items;
    },

    /**
     * Form items hide tooltip.
     * @param selector wrap
     * @return void
     */
    formReset: function( wrap ) {
        var wrap = jQuery( wrap );

        if( 0 == wrap.length ) {
            return null;
        }

        wrap.find( '.' + this.options.itemsValidationClass )
            .tooltip( 'destroy' )
            .attr( 'data-original-title', '' )
            .attr( 'title', '' );
    },

    /**
     * Item show tooltip with error.
     * @parm jQuery object item
     * @parm  string title
     * @return void
     */
    itemMakeInvalid: function( item, title ) {
        item
            .attr( 'data-original-title', title )
            .tooltip( 'show' );
    },

    /**
     * Validation items.
     * @param object items
     * @return integer errors count
     */
    itemsValidation: function( items ) {
        var self = this,
            errorsCount = 0;

        jQuery.each( items, function( i, item ) {
            var item = jQuery( item ),
                itemVal = item.val(),
                itemName = item.attr( 'name' ),
                itemType = item.attr( 'type' );

            if( ! itemVal.trim() ) {
                errorsCount++;
                self.itemMakeInvalid( item, ( 'undefined' != self.errors[ 'requiredField' ] ? self.errors[ 'requiredField' ] : '' ) );
            } else if( 'email' == itemType || 'email' == itemName || item.hasClass( 'yks-mc-input-email-address' ) ) { // Change to mailchimp for wp.
                if( ! self.options.emailValidationRegex.test( itemVal ) ) {
                    errorsCount++;
                    self.itemMakeInvalid( item, ( 'undefined' != self.errors[ 'emailInvalid' ] ? self.errors[ 'emailInvalid' ] : '' ) );
                }
            }
        });

        return errorsCount;
    },

    /**
     * Initialization custom validation for plugin contact form 7.
     * @return void
     */
    initContactForm7CustomValidtion: function() {
        var self = this,
            wrapForm = jQuery( '.wpcf7' ),
            itemsValidationClass = this.options.itemsValidationClass;

        wrapForm.each(function() {
            var wrapFromId = jQuery( this ).attr( 'id' ),
                wrapFormEl = jQuery( '#' + wrapFromId );

            if( wrapFormEl.length < 1 ) {
                return;
            }

            var items = wrapFormEl
                .find( '.wpcf7-validates-as-required' )
                .addClass( itemsValidationClass );

            self.initTooltip( items );

            wrapFormEl.find( 'form' ).on( 'ajaxComplete', function( e ) {
                self.initTooltip( items );
                jQuery( this ).find( '.wpcf7-not-valid' ).each(function( i, item ) {
                    var item = jQuery( item ),
                        itemErrorText = item.siblings( '.wpcf7-not-valid-tip' ).text();

                    switch( itemErrorText ){
                        case 'Please fill in the required field.':
                            itemErrorText = 'undefined' != self.errors[ 'requiredField' ] ? self.errors[ 'requiredField' ] : '';
                            break;
                        case 'Email address seems invalid.':
                            itemErrorText = 'undefined' != self.errors[ 'emailInvalid' ] ? self.errors[ 'emailInvalid' ] : '';
                            break;
                    }

                    self.itemMakeInvalid( item, itemErrorText );
                });
            });
        });
    },

    /**
     * Initialization custom validation for plugin Easy MailChimp Forms.
     *
     * @param selector wrapFormId
     * @return void
     */
    initMailChimpCustomValidtion: function( wrapFormId ) {
        var self = this,
            itemsValidationClass = this.options.itemsValidationClass,
            wrapForm = jQuery( '#' + wrapFormId );

        if ( wrapForm.length < 1 ) {
            return;
        }

        var items = wrapForm.find( '.yks-require, input[required="required"]' )
            .addClass( itemsValidationClass );

        this.initTooltip( items );

        wrapForm.find( 'form' )
            .find( '[type="submit"], [type="image"]' )
            .on( 'click', function( e ) {
                self.initTooltip( items );
                if ( self.itemsValidation( items ) > 0 ) {
                    e.preventDefault();
                }
            });
    },

    /**
     * Initialization custom validation for forms.
     *
     * @param  selector wrapFormId
     * @return void
     */
    initValidationForm: function( wrapFormId ) {
        var self = this,
            itemsValidationClass = this.options.itemsValidationClass,
            wrapForm = jQuery( '#' + wrapFormId );

        if ( 0 == wrapForm.length ) {
            return;
        }

        this.initTooltip(
            wrapForm.find( '.' + this.options.itemsValidationClass )
        );

        wrapForm.find( 'form' ).on( 'submit', function( e ) {

            // e.preventDefault();
            self.formReset( wrapForm );

            var items = wrapForm.find( '.' + itemsValidationClass ),
                formErrors = 0;

            formErrors = self.itemsValidation( items ) ;

            // validation success
            if( 0 == formErrors ) {
                //TODO complete
            }
        });
    }
};

Theme.formatter = {
    configs:{},

    setConfig:function( format, cfg ) {
        this.configs[format] = cfg;
    },

    formatMoney:function( amount ) {
        var cfg = jQuery.extend({
            //mask: '{amount}',
            decimal_separator: '.',
            thousand_separator: ',',
            decimals: 2
        }, this.configs.money[ 'money' ] ? this.configs[ 'money' ] : {});

        var formatted = this.formatNumber( amount, cfg.decimals, 3, cfg.thousand_separator, cfg.decimal_separator );

        if ( cfg.mask ) {
            var completed = cfg.mask.replace( '{amount}', formatted );
            if ( completed != cfg.mask ) {
                return completed;
            }
        }

        return formatted;
    },

    formatNumber: function( number, decimals, th, thSep, decSep ) {
        var re = '\\d(?=(\\d{' + ( th || 3 ) + '})+' + ( decimals > 0 ? '\\D' : '$' ) + ')',
            number = parseFloat(number);
            num = number.toFixed( Math.max( 0, ~~decimals ) );

        return ( decSep ? num.replace( '.', decSep ) : num ).replace( new RegExp( re, 'g' ), '$&' + ( thSep || ',' ) );
    },

    /**
     * Allows format strings with %s and %d placeholders.
     *
     * @return String
     */
    sprintf:function() {
        var args = arguments,
            string = args[0],
            i = 1;

        return string.replace( /%((%)|s|d)/g, function( m ) {
            // m is the matched format, e.g. %s, %d
            var val = null;
            if ( m[2] ) {
                val = m[2];
            } else {
                val = args[i];
                switch ( m ) {
                    case '%d':
                        val = parseFloat( val );
                        if ( isNaN( val ) ) val = 0;
                        break;
                }
                i++;
            }
            return val;
        });
    },

    time:function( timeIn24Hours, format ) {
        if ( ! format || 'hh:ii' == format ) {
            return timeIn24Hours;
        }

        var parts = timeIn24Hours.split( ':' ),
            result = format.replace( 'ii', parts[1] ),
            h = parseInt( parts[0], 10 ),
            is12HoursFormat = format.search( 'A' ) >= 0,
            is12HoursFormatLowercase = format.search( 'a' ) >= 0,
            newHourValue = h;


        if ( is12HoursFormat || is12HoursFormatLowercase ) {
            var suffix = h >= 12 ? ' PM' : ' AM';
            result = result.replace( is12HoursFormatLowercase ? 'a' : 'A', is12HoursFormatLowercase ? suffix.toLowerCase() : suffix );
            if ( newHourValue >= 12 ) {
                newHourValue -= 12;
            }
            if ( 0 == newHourValue ) {
                newHourValue = 12;
            }
        }

        if ( format.search( 'hh' ) >= 0 ) {
            result = result.replace( 'hh', ( newHourValue < 10 ? '0' : '' ) + newHourValue );
        } else {
            result = result.replace( 'h', newHourValue );
        }

        return result;
    }
};

jQuery(function( $ ) {
    Theme.init( $ );
});
