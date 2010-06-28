function createList(uname, lname) {
	alert('create list');
	
    $.getJSON(
      "data/createList.php",
      {uname: uname, lname: lname},
      function(json) {
      	if(json.status == 'OK') {
      		
      		load_lists();
      	} else  {
      		alert(json.statusmsg);
      	}
      });
}

function load_lists() {	 
		var listNames = new Array();
		
		$.getJSON(
			"data/getListNames.php",
			{set:'dyi'},
			function(json) {
				if(json.status == 'FAILED') {
					alert(json.statusmsg);
				} else {
					//alert(json.opts);
					$("#black-lists").children().remove();
					
					$.each (json.opts, function(i, lname) {

			    	var id = lname + '-list';      
			    	listNames[i] = lname;      
    				
    				$("#black-lists").append('<li id='+ id + '><a href="list.html?list='+ lname +'">' + lname + '</a></li>');    				
					});					
				}
			});
			
			return listNames;
}

function set_watermark() {
	    $("#search-text").each(function() {
			var default_value = "keywords";
			if($(this).val() == default_value) {
        	$(this).addClass('default-text');
			} else {
				$(this).removeClass('default-text');
			}
			
			$(this).focus(function() {
        	if($(this).val() == default_value) {
        		$(this).val("");
			$(this).removeClass('default-text');
		}
	});
			
	$(this).blur(function() {
		if($(this).val() == '') {
			$(this).val(default_value);
			$(this).addClass('default-text');
			}
     	});
    });

    
    $("#search-text").keyup(function(event) {
			if(event.keyCode == 13) {
         $("#search-submit").click();				
      }
    });
}

function setup_list() {
	  $("#add-list-dialog").dialog({autoOpen:false, overlay:{opacity:0.5, background:"black"}});    
    $("button").button();
    
    $("#add-list-link").click(function() {
  		$("#add-list-dialog").dialog("open");
    		return false;
    });

    $("#add-list-dialog input[type=radio]").change(function() {
        
        if ($("#add-list-dialog input[type=radio]:checked").val() == 'public') {
            $("#list-tags").fadeOut();
        }
        else if ($("#add-list-dialog input[type=radio]:checked").val() == 'private') {
            $("#list-tags").fadeIn();
        }
        else {}
    });
    
    $("#create-list-button").click(function() {
        if($("#new-list-name").val() == '') {
            alert('list name is emtpy');
            return false;
        }
        
        var uname = 'dyi'; //to be updated.
        var lname = $("#new-list-name").val();
        
        createList(uname, lname);
        
        $("#add-list-dialog").dialog('close');
    });
    
    $("#cancel-list-button").click(function() {
        $("#add-list-dialog").dialog('close');
    });            
}