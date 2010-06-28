$(document).ready(function() {
    
    var list = '';    
    var vars=[], hash;
    if(window.location.href.indexOf('?') > -1)  {
        var hashes = window.location.href.slice(window.location.href.indexOf('?')+1).split('&');

        //for(var i = 0; i < hashes.length; i++) {
        hash = hashes[0].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
            
        list = hash[1];
    } else {
			list = 'books';
    }
    
    openList(list);
	  set_watermark();	
    load_lists();
    setup_list();
    
    $("#list-tags").hide();
    $("#email-list").hide();
    $("#email").val('');
        
    $("#add-table-row").click(function() {
        /**
        var datarow = {id:'', title:'', author:'', publisher:''};
        var su = jQuery("#list-table").jqGrid('addRowData', 0, datarow);
        */
        jQuery("#list-table").jqGrid('editGridRow', 'new', {height:200, reloadAfterSubmit:false});
        
    });
    
    $("#del-table-row").click(function() {
        var gr = jQuery("#list-table").jqGrid('getGridParam', 'selarrrow'); //'selrow'
        //alert(gr);
        if(gr != null ) {
        	jQuery("#list-table").jqGrid('delGridRow', gr, {reloadAfterSubmit:false});        
        	// call php to delete row from here.
        	deleteListItem(list, gr);
      }
    });
    
    $("#save-table-changes").click(function() {
        
        //if($("#username :selected").val() != 0) {
            var xmldata = Array();
            var num = jQuery('#list-table').jqGrid('getGridParam', 'records');
            //alert(num);
            for(var i =0; i < num; i++) {
                xmldata[i] = jQuery('#list-table').jqGrid('getRowData', i+1);
            }
            //saveFile($("#username :selected").text(), $("#listname :selected").text(), xmldata);
            saveFile("dyi", "books", xmldata);
    });
    
    $("#del-list").click(function() {
    	
    	if(list != 'books' && list != 'music') {
    		deleteList(list);
				location.href="list.html";
			} else {
				alert('You cannot delete default list');
			}
    });
    
    $("#share-list").click(function() {
       $("#email-list").slideDown();
    });
    
    $("#cancel-email").click(function() {
        $("#email-list").slideUp();
        $("#email").val('');
    });
    
    $("#email").keyup(function() {
       var address = $("#email").val();

       if(address != 0) {
          if (isValidEmailAddress(address)) {
              $("#validStatus").css({"background-image": "url('images/validYes.png')"});
          } else {
             $("#validStatus").css({"background-image": "url('images/validNo.png')"});               
          }
       } else {
           $("#validStatus").css({"background-image":"none"});
       }        
    });

    $("#send-email").click(function() {
        var address = $("#email").val();
        
        if(address != 0) {
        if (isValidEmailAddress(address)) {
            $("#validStatus").css({"background-image": "url('images/validYes.png')"});           
            emailList('dyi', 'books', address);  //need to update
            
        } else {
            $("#validStatus").css({"background-image": "url('images/validNo.png')"});
        }
        } else {
            $("#validStatus").css({"background-image":"none"});
        }
    });
    
    $("#search-submit").click(function(e) {
        e.preventDefault();
        var category = $("#search-select").val();
        var keywords = $("#search-text").val();
	
				var link = "home.html"+ '?cat=' + category + '&keywords=' + keywords + '&page=1';
        location.href=link;
    });
    
});

function deleteListItem(list, item) {

    $.getJSON(
        "data/deleteListItems.php",
        {list:list, items: item},
        function(json) {
            if(json.status == 'FAILED') {
                alert(json.statusmsg);
            } else {
                //do nothing.
                //alert('success!');
            }
        }
    )
}

function openList(lname) {
    
    var setName = "dyi";//$("#username :selected").text();
    var listName = lname;//$("#listname :selected").text();

    //use jqgrid plugin instead
    var lastsel;
    jQuery("#list-table").GridUnload();
    jQuery("#list-table").jqGrid({
    url:'data/getJSONFile.php?set='+setName+'&list='+listName,
    	datatype: "json",
    	colNames:['id', 'title','author','price', 'rating'],
    	colModel:[
	   	{name:'id',index:'id',width:40, editable:false,editoptions:{readonly:true,size:10}},
    		{name:'title',index:'title',width:400, editable:false},
    		{name:'author',index:'author', width:150, editable:false},
    		{name:'price', index:'price', width:60, editable:false},
				{name:'rating', index:'rating', width:60, editable:false}
    	],
    	rowNum:15,
    	width: 720,
        height: 350,
    	rowList:[15,30,45],
    	pager:jQuery('#pager1'),
    	sortname:'id',
        recordpos: 'left',
    	viewrecords: true,
    	sortorder: "desc",
        multiselect: true,
        multiboxonly: true,
      editurl: 'data/dummy.php',   	
    	caption:listName,
    	});
    	jQuery("#list-table").jqGrid('navGrid', '#pager1',{edit:false, add:false,del:false, position:'right'});
}


function saveFile(uname, lname, data) {
    $.getJSON(
        "data/saveFile.php",
        {uname:uname, lname:lname, data:data},
        function(json) {
            if(json.status == 'FAILED') {
                alert(json.statusmsg);
            }
        });
}


function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}

function emailList(uname, lname, address) {
    $.getJSON(
        "data/emailFile.php",
        {uname:uname, lname:lname, addr:address},
        function(json) {
            if(json.status == 'FAILED') {
                alert("Sending email failed.");
            } else {
                alert(json.sent);
            }
        }
    );	
}

function deleteList(lname) {    	
   
	$.getJSON(
 		"data/removeList.php",
 		{uname: 'dyi', lname: lname},
 		function (json){
 			if (json.status == 'FAILED') {
  				alert(json.statusmsg);
 			} else {
  				load_lists();
 			}
  	});
}